<h4><?php echo _t("Upload Torrent") ?></h4>
<?php
$acl = new Acl(USER_ID);

$this->setTitle("Upload Torrent");

if (isset($_POST['upload'])) {
    try {

        if ($_POST['secure_input'] != $_SESSION['secure_token'])
            throw new Exception("Wrong secured token");


        if (!isset($_FILES["file"]) && !isset($_FILES['nfo']) && !isset($_POST['type']))
            throw new Exception("Missing Form data.");

        if (empty($_FILES["file"]['name']))
            throw new Exception('No Torrent file was selected.');
        if (empty($_FILES["nfo"]['name']))
            throw new Exception('No NFO file was selected.');


        if (empty($_POST['type']))
            throw new Exception("Missing category");

        $file = $_FILES["file"];
        $filename = $file['name'];
        $tmpname = $file["tmp_name"];

        $nfo = $_FILES["nfo"];
        $nfoname = $nfo["name"];

        if (!preg_match('/^(.+)\.torrent$/si', $filename, $matches))
            throw new Exception("Thats not a torrent file...");

        $torrentName = $matches[1];

        if (!preg_match('/^(.+)\.nfo$/si', $nfoname, $matches))
            throw new Exception("Thats not a nfo file...");

        if (isset($_FILES['nfo']) && !empty($_FILES['nfo']['name'])) {
            $nfofile = $_FILES['nfo'];
            if ($nfofile['size'] == 0)
                throw new Exception("NFO file size to small.");
            if ($nfofile['size'] > 65535)
                throw new Exception("NFO file size to big.");
            $nfofilename = $nfofile['tmp_name'];
            if (@!is_uploaded_file($nfofilename))
                throw new Exception("NFO file upload failed.");

            $nfo = str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename));
        }
        $nfo = nfostrip($nfo);
        $dict = Bcode::bdec_file($tmpname, filesize($tmpname));
        if (!isset($dict))
            throw new Exception("torrent file not benc coded");
        list($ann, $info) = Bcode::dict_check($dict, "announce(string):info");

        $tmaker = (isset($dict['value']['created by']) && !empty($dict['value']['created by']['value'])) ? $dict['value']['created by']['value'] : "Unknown";

        list($dname, $plen, $pieces) = Bcode::dict_check($info, "name(string):piece length(integer):pieces(string)");

        if (strlen($pieces) % 20 != 0)
            throw new Exception("somthing bad happend. and we dont know what.");

        $filelist = array();
        $totallen = Bcode::dict_get($info, "length", "integer");
        if (isset($totallen)) {
            $filelist[] = array($dname, $totallen);
            $type = "single";
        } else {
            $flist = Bcode::dict_get($info, "files", "list");
            if (!isset($flist))
                throw new Exception("somthing bad happend. and we dont know what.");
            if (!count($flist))
                throw new Exception("somthing bad happend. and we dont know what.");

            $totallen = 0;
            foreach ($flist as $fn) {
                list($ll, $ff) = Bcode::dict_check($fn, "length(integer):path(list)");
                $totallen += $ll;
                $ffa = array();
                foreach ($ff as $ffe) {
                    if ($ffe["type"] != "string")
                        throw new Exception("somthing bad happend. and we dont know what.");
                    $ffa[] = $ffe["value"];
                }
                if (!count($ffa))
                    throw new Exception("somthing bad happend. and we dont know what.");
                $ffe = implode("/", $ffa);
                $filelist[] = array($ffe, $ll);
            }
            $type = "multi";
        }

        $dict['value']['info']['value']['private'] = Bcode::bdec('i1e');
        unset($dict['value']['announce-list']);
        unset($dict['value']['nodes']);
        $dict = Bcode::bdec(Bcode::benc($dict));
        list($ann, $info) = Bcode::dict_check($dict, "announce(string):info");

        $infohash = sha1($info["string"]);

        unset($info);



        $db = new DB("torrents");
        $db->select("torrent_save_as = '$filename'");
        if ($db->numRows())
            throw new Exception("Torrent allready exists");
        $db = new DB("torrents");
        $db->setColPrefix("torrent_");
        $id = uniqid(true);
        $db->id = $id;
        $db->info_hash = $infohash;
        $db->name = $torrentName;
        $db->filename = $filename;
        $db->save_as = $filename;
        $db->search_text = searchfield("$torrentName $dname");
        $db->nfo = $nfo;
        $db->size = $totallen;
        $db->added = time();
        $db->type = $type;
        $db->userid = USER_ID;
        $db->numfiles = count($filelist);
        $db->category = $_POST['type'];
        $db->youtube = $_POST['youtube'];
        $db->imdb = $_POST['imdb'];
        $db->freeleech = isset($_POST['freeleech']) ? 1 : 0;
        $db->insert();
        $fp = fopen(PATH_TORRENTS . "$id.torrent", "w");
        if ($fp) {
            @fwrite($fp, Bcode::benc($dict), strlen(Bcode::benc($dict)));
            fclose($fp);

            $db->query("INSERT INTO {PREFIX}torrents_files (file_torrent, file_name, file_size) VALUES " . file_list($filelist, $id));

            if (isset($_POST['imdb']) && !empty($_POST['imdb']))
                $link = $_POST['imdb'];
            else
                $link = $descr;

            $m = preg_match("/tt\\d{7}/", $link, $ids);
            if ($m) {
                $link = "http://www.imdb.com/title/" . $ids[0];

                $db = new DB("torrents");
                $db->torrent_imdb = $link;
                $db->update("torrent_id = '" . $db->escape($id) . "'");

                preg_match("#tt(?P<imdbId>[0-9]{7,7})#", $link, $matches);
                if (count($matches) == 0)
                    continue;
                $thenumbers = $matches['imdbId'];
                include(PATH_LIBRARY . "imdb/imdb.class.php");
                $movie = new imdb($thenumbers);
                $movieid = $thenumbers;
                $movie->setid($movieid);
                $gen = $movie->genres();
                $plotoutline = $movie->plotoutline();
                $mvrating = $movie->rating();
                $photo_url = $movie->photo_localurl();
                $db = new DB("torrents_imdb");
                $db->setColPrefix("imdb_");
                $db->torrent = $id;
                $db->genres = implode(", ", $gen);
                $db->plot = $plotoutline;
                $db->title = $movie->title();
                $db->image = $photo_url;
                $db->rating = $mvrating;
                $db->insert();
            }

            header("location: " . page("torrent", "details", "", "", "", "id=" . $id));
        } else {
            $db = new DB("torrents");
            $db->delete("torrent_id = $id");
            throw new Exception("Could not upload torrent file. please contact staff.");
        }
    } Catch (Exception $e) {
        echo error(_t($e->getMessage())) . "<br />";
    }
}
try {

    if (!$acl->uploader)
        throw new Exception("You are not an uploader. Access denied!");

    $tpl = new Template($this->path . "tpl/");
    $tpl->build("upload.php");
} Catch (Exception $e) {
    echo error(_t($e->getMessage())) . "<br />";
}
?>
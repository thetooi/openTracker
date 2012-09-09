<?php

include("../../../init.php");

if (!empty($_FILES)) {
    $tempFile = $_FILES['Filedata']['tmp_name'];
    $targetPath = str_replace("//", "/", PATH_ROOT . $_REQUEST['folder'] . '/');

    $ext = end(explode(".", $_FILES['Filedata']['name']));
    do {
        $file = uniqid(true);
        $targetFile = $targetPath . $file . "." . $ext;
    } while (file_exists($targetFile));
    echo $targetFile;
    $u = move_uploaded_file($tempFile, $targetFile);
    if ($u) {
        $db = new DB("files");
        $db->setColPrefix("file_");
        $db->id = $file;
        $db->name = $_FILES['Filedata']['name'];
        $db->added = time();
        $db->size = filesize($targetFile);
        $db->userid = USER_ID;
        $db->insert();
        echo str_replace("/files/", "", $_POST['folder']);
        echo "File Uploaded!";
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
                $image = new Image;
                $image->load($targetFile);
                if ($image->getWidth() > 1024) {
                    $image->resizeToWidth("1024");
                }
                if ($image->getHeight() > 768) {
                    $image->resizeToHeight("768");
                }
                $image->save($targetFile);
                $image = new Image;
                $image->load($targetFile);
                $db = new DB("files");
                $db->setColPrefix("file_");
                $db->width = $image->getWidth();
                $db->height = $image->getHeight();
                $db->update("file_id = '" . $file . "'");
                //$image->addWatermark();
                //$image->save($targetFile);
                break;
        }
    } else {
        var_dump($_FILES['Filedata']);
        echo "Error!";
    }
    echo $targetFile;
}
?>

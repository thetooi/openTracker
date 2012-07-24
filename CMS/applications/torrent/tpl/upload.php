<form method="post" enctype="multipart/form-data" action="<?php echo page("torrent", "upload"); ?>">
    <input type="hidden" name="secure_input" value="<?php echo $_SESSION['secure_token_last'] ?>">
    <div class="col_100 align_center">
        <?php
        $cat = new DB("categories");
        $cat->setColPrefix("category_");
        $cat->select();
        while ($cat->nextRecord()) {
            $sel = isset($_GET['c' . $cat->id]) ? " CHECKED" : "";
            ?>
            <div class="col_5 align_center paddings">
                <label for="cat_<?php echo $cat->id ?>"><img src="images/categories/<?php echo $cat->icon; ?>" alt="<?php echo $cat->name; ?>" /><br />
                    <input type="radio" name="type" id="cat_<?php echo $cat->id; ?>" value="<?php echo $cat->id ?>" />
                </label>
            </div>
        <?php }
        ?>
    </div>
    <div class="col_100">
        <div class="col_100">
            <div class="col_15 align_right paddings"><b><?php echo _t("Announce url"); ?></b></div>
            <div class="col_70 paddings"><?php echo CMS_URL . "announce.php"; ?></div>
        </div>
        <div class="col_100">
            <div class="col_15 align_right paddings"><b><?php echo _t("Torrent file"); ?></b></div>
            <div class="col_75 paddings"><input type="file" name="file" /></div>
        </div>
        <div class="col_100">
            <div class="col_15 align_right paddings"><b><?php echo _t("NFO"); ?></b></div>
            <div class="col_75 paddings"><input type="file" name="nfo" /></div>
        </div>
        <div class="col_100">
            <div class="col_15 align_right paddings"><b><?php echo _t("IMDB-link"); ?></b></div>
            <div class="col_75 paddings"><input type="text" name="imdb" size="80"><br>
                <span class="small">(<?php echo _t("Link shall only be pointed to valid imdb") ?>)
                    <br><?php echo _t("Example") ?>: http://www.imdb.com/title/tt1201607/</span></div>
        </div>
        <div class="col_100">
            <div class="col_15 align_right paddings"><b><?php echo _t("Youtube-link"); ?></b></div>
            <div class="col_75 paddings"><input type="text" name="youtube" size="80"><br>
                <span class="small">(<?php echo _t("Link shall only be pointed to trailer at Youtube."); ?>)
                    <br><?php echo _t("Example") ?>: http://www.youtube.com/watch?v=9iEQdTFb2Rw"</span></div>
        </div>
        <div class="col_100">
            <div class="col_15 align_right paddings"><b><?php echo _t("Additional"); ?></b></div>
            <label><input type="checkbox" name="freeleech" /><?php echo _t("Freeleech"); ?></label>
        </div>
        <div class="col_100">
            <div class="col_15 align_right paddings"><input type="submit" name="upload" value="<?php echo _t("Upload Torrent"); ?>"></div>
        </div>
    </div>
</form>
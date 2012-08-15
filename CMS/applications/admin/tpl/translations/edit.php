<?php

/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */

if(!defined("INCLUDED"))
    die("Access denied");

?>

<script type="text/javascript">
    $(document).ready(function(){
        $(".autosave").focusout(function(){
            var id = $(this).attr("name").replace("trans_", "");
            var text = $(this).attr("rel");
            var translates = $(this).val();
        
            var lang_id = $("#lang_id").val();
        
        
            text = encodeURIComponent(text);
            translates = encodeURIComponent(translates);
            $.ajax({
                type: "POST",
                url: PATH_APP+"tpl/translations/ajax.php",
                data: "action=save&text="+text+"&translation="+translates+"&language="+lang_id,
                success: function(msg){
                    msg = parseAjaxMsg(msg);
                    
                }
            });
        });
    });
    
    $(document).ready(function(){
        $("#flag").change(function(){
            var val = $(this).val();
            $("#flag_image").attr("src", "images/flags/"+val);
        });
        
        var val = $("#flag").val();
        $("#flag_image").attr("src", "images/flags/"+val);
    });
</script>

<?php
try {

    $db = new DB("system_languages");
    $db->select("language_id = '" . $db->escape($this->lang_id) . "'");

    if (!$db->numRows())
        throw new Exception("No language installed.");

    if (isset($_POST['save'])) {
        try {

            if (empty($_POST['name']) || empty($_POST['id']))
                throw new Exception("missing data");

            $db = new DB("system_languages");
            $db->language_id = $_POST['id'];
            $db->language_name = $_POST['name'];
            $db->language_flag = $_POST['flag'];
            $db->update("language_id = '" . $db->escape($this->lang_id) . "'");
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $db = new DB("system_languages");
    $db->select("language_id = '" . $db->escape($this->lang_id) . "'");
    $db->nextRecord();
    $flags = makefilelist(PATH_IMAGES . "flags", ".|..|index.html", true);
    ?>
    <h4><?php echo _t("Editing Translation") ?> <?php echo $db->language_name ?></h4><br />
    <a href="<?php echo page("admin", "translations", "export", $this->lang_id) ?>">
        <span class="btn blue"><?php echo _t("Export Language") ?></span>
    </a>
    <br /><br />

    <form method="post">
        <table>
            <tr><td><?php echo _t("Name") ?></td><td><input type="text" name="name" value="<?php echo $db->language_name; ?>"></td></tr>
            <tr><td><?php echo _t("Id") ?></td><td><input type="text" name="id" value="<?php echo $db->language_id ?>"></td></tr>
            <tr><td><?php echo _t("Flag") ?></td><td><img id="flag_image" src="images/flags/<?php echo $db->language_flag; ?>"><select id="flag" name="flag"><?php echo makefileopts($flags, $db->language_flag) ?></select></td></tr>
            <tr><td><input type="submit" name="save" value="<?php echo _t("Save") ?>"></td></tr>
        </table>
    </form>

    <?php
    $db = new DB("translation");
    $db->setSort("translation_phrase ASC");
    $db->select("translation_lang_id='en'");
    $i = 1;
    echo "<table>";
    $s = 1;
    while ($db->nextRecord()) {

        $trans = new DB("translation");
        $trans->select("translation_phrase = '" . $db->escape($db->translation_phrase) . "' AND translation_lang_id='" . $this->lang_id . "'");
        $trans->nextRecord();
        if ($s == 1) {
            echo "<tr>";
        }
        echo "<td style='padding: 4px;' valign='top'><input type='hidden' name='id[]' value='$i' />";
        echo "<input type='hidden' name='text_$i' value='" . htmlentities($db->translation_phrase, ENT_QUOTES, "UTF-8") . "' />$db->translation_phrase<br />
            <input type='text' name='trans_$i' class='autosave' rel='" . htmlentities($db->translation_phrase, ENT_QUOTES, "UTF-8") . "' value='" . (($trans->translation_phrase_translated != "") ? $trans->translation_phrase_translated : "") . "' size=40 />
            <br /><div style='display: none;' id='msg_" . $i . "'><font color='green'>Saved!</font></div>
             ";
        echo "</td>";

        if ($s == 2) {
            $s = 0;
            echo "</tr>";
        }
        $s++;
        $i++;
    }

    echo "<input type='hidden' name='language' id='lang_id' value='" . $this->lang_id . "' />";
    echo "</table>";
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>

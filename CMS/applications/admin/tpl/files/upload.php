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
if (!defined("INCLUDED"))
    die("Access denied");

try {

    $max_filesize = ini_get('upload_max_filesize') * 1024 * 1024;
    ?>
    <script src='<?php echo CMS_URL ?>/siteadmin/javascript/uploadify/swfobject.js' type='text/javascript'></script>
    <script src='<?php echo CMS_URL ?>/siteadmin/javascript/uploadify/jquery.uploadify.v2.1.4.min.js' type='text/javascript'></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#file_upload').uploadify({
                'uploader'  : '<?php echo CMS_URL ?>/siteadmin/javascript/uploadify/uploadify.swf',
                'script'    : '<?php echo CMS_URL ?>/siteadmin/javascript/uploadify/uploadify.php',
                'cancelImg' : '<?php echo CMS_URL ?>/siteadmin/javascript/uploadify/cancel.png',
                'folder'    : '/files/',
                'auto'      : false,
                'queueID'		: 'faQueue',
                'multi'       : true,
                'sizeLimit' : <?php echo $max_filesize; ?>,
                                                                                                                                                                                                                                                                                                
                'onComplete' : function (event, queueID, fileObj, response, data) {
                    console.log(response);  
                },
                                                                                                                                                                                                                                                                                                
                'onAllComplete' : function(event,data) {
                    window.location = "<?php echo page("admin", "files") ?>";
                },
                'onError'     : function (event,ID,fileObj,errorObj) {
                    alert(errorObj.type + ' Error: ' + errorObj.info);
                }
            });
        });
    </script>
    <div class="col_50"> 
        <p><input id="file_upload" type="file" name="file_upload" />
        <div id="status-message">Choose files to upload<br />
            Max size <?php echo (int) ini_get('upload_max_filesize'); ?>MB
        </div>
        <div class="col_100" id="selectedFiles">
            <div id="faQueue"></div>
        </div></p>
    </div>
    <div class="col_30">
        <fieldset>
            <legend>Upload</legend>
            <input type="submit" value="Start Upload" onClick="javascript:$('#file_upload').uploadifyUpload()" />
        </fieldset>
    </div>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>

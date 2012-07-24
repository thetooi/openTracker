<?php
$folders = array(
    "Configs" => PATH_CONFIGS,
    "Torrents" => PATH_TORRENTS,
    "Avatars" => PATH_ROOT . "images/avatars/",
    "IMDB" => PATH_ROOT . "images/imdb/",
);
?>

<h4>Files check</h4>

<table width="100%">
    <?php
    $i = 1;
    $continue = true;

    foreach ($folders as $folder => $path) {
        if ($i == 1)
            echo "<tr>";

        if (!is_writable($path))
            $continue = false;

        $mode = (is_writable($path) ? "<font color=green>Writable</font>" : "<font color=red>Not Writable</font>");
        ?>
        <td><?php echo "<b>" . $folder . "</b><br /> Status: $mode"; ?></td>
        <?
        if ($i == 4) {
            echo "</tr>";
        }

        $i++;
    }
    ?>   
</table>

<?php
if ($continue) {
    ?>
    <br /><br /><a href="?action=step2"><span class="btn blue big">CONTINUE</span></a><br /><br />
<?php } ?>

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

    $acl = new Acl(USER_ID);
    
    if(!$acl->Access("b"))
        throw new Exception("Access denied, you must be power user or higher to use the bonus store.");
    $this->sidebar = true;
    ?>
    <h4><?php echo _t("Bonus Store") ?></h4>
    <?php
    if (isset($_POST['buy'])) {
        try {
            $acl = new Acl(USER_ID);

            if (!isset($_POST['product']))
                throw new Exception("Missing product id");

            $product = $_POST['product'];

            $db = new DB("bonus");
            $db->setColPrefix("bonus_");
            $db->select("bonus_id = '" . $db->escape($product) . "'");

            if (!$db->numRows())
                throw new Exception("Invalid bonus id");

            $db->nextRecord();
            $product_cost = (int) $db->cost;

            $user = new DB();
            if ((int) $acl->bonus < $product_cost)
                throw new Exception("Not enought points");

            $data = $db->data;
            switch ($db->type) {
                case "1":
                    if ($acl->downloaded < $data)
                        throw new Exception("Not enough downloaded data to remove");
                    $user->query("UPDATE {PREFIX}users SET user_downloaded = user_downloaded - $data WHERE user_id = '" . $user->escape(USER_ID) . "'");
                    break;

                case "2":
                    $user->query("UPDATE {PREFIX}users SET user_uploaded = user_uploaded + $data WHERE user_id = '" . $user->escape(USER_ID) . "'");
                    break;

                case "3":
                    $user->query("UPDATE {PREFIX}users SET user_invites = user_invites + $data WHERE user_id = '" . $user->escape(USER_ID) . "'");
                    break;
            }
            $user->query("UPDATE {PREFIX}users SET user_bonus = user_bonus - $product_cost WHERE user_id = '" . $user->escape(USER_ID) . "'");
            echo notice(_t("Your purchase was made successfully"));
        } Catch (Exception $e) {
            echo error(_t($e->getMessage()));
        }
    }

    $acl = new Acl(USER_ID);
    ?>
    <table class="forum" width="100%" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <td class="border-bottom border-right"><?php echo _t("Store item"); ?></td>
                <td class="border-bottom border-right"><?php echo _t("Costs"); ?></td>
                <td class="border-bottom"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $db = new DB("bonus");
            $db->setColPrefix("bonus_");
            $db->setSort("bonus_sort ASC");
            $db->select();
            while ($db->nextRecord()) {
                $buy_button = ((int) $acl->bonus < (int) $db->cost) ? _t("Not enough points") : "<input type='submit' name='buy' value='" . _t("Buy") . "' />"
                ?>

                <tr>
                    <td class="border-bottom border-right">
                        <h4><?php echo $db->title; ?></h4>
                        <?php echo htmlformat($db->description, true); ?>
                    </td>
                    <td class="border-bottom border-right"><?php echo $db->cost; ?></td>
                    <td class="border-bottom">
                        <form method="POST">
                            <input type="hidden" name="product" value="<?php echo $db->id ?>" />
                            <?php echo $buy_button; ?>
                        </form>
                    </td>
                </tr>

                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
} Catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>

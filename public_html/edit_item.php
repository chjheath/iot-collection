<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = $_GET['cid'];
    $iid = $_GET['id'];

    $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($cid, $_SESSION['uid']));

    $isOwnerOfCollection = FALSE;

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection && !isset($_POST['submit_form'])) {
        Header("Location:account.php");
    } else {
        //Retrieve old data

        $stmt = $con->prepare("SELECT ItemName, ItemValue FROM collection_items WHERE ItemID = ?");
        $stmt->execute(array($iid));

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $oldName = $res['ItemName'];
        $oldValue = $res['ItemValue'];
        $visibilityOptions = '';

        $stmt->closeCursor();
    
        $newName = '';
        $newValue = '';
    
        $nameError = '';
        $valueError = '';
        $nameOK = FALSE;
    
        if (isset($_POST['submit_form'])) {
    
            if (isset($_POST['item_name'])) {
                $itemName = trim($_POST['item_name']);
                $itemName = cleanQuery($itemName);

                if (strlen($itemName) <= 25) {
                    $nameOK = TRUE;
                }
            } else {
                $nameError = '<p style="color:red; font-weight:bold">You must enter an item name.</p>';
            }

            if (isset($_POST['item_value'])) {
                $itemValue = $_POST['item_value'];

                //No need to clean this as only numbers are allowed anyways.

                $valueOK = TRUE;
            } else {
                $valueError = '<p style="color:red; font-weight: bold">You must enter an item value.</p>';
            }

            if ($nameOK && $valueOK) {
                $stmt = $con->prepare("UPDATE collection_items SET ItemName = ?, ItemValue = ? WHERE CollectionID = ? AND ItemID = ?");
                $stmt->execute(array($itemName, $itemValue, $_POST['coll_id'], $_POST['item_id']));
                $stmt->closeCursor();

                Header("Location:view_collection.php?id=".$_POST['coll_id']);
            }
    
        }
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit item</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Edit your item.</h1>
        <form action="edit_item.php" method="post">

            <input type="hidden" name="coll_id" value="<?php print $cid?>">
            <input type="hidden" name="item_id" value="<?php print $iid?>">

            <label for="item_name">Enter item name <?php print $nameError?></label>
            <input type="text" name="item_name" value="<?php print $oldName?>">

            <br/><br/>

            <label for="item_value">Enter item value (in USD) <?php print $valueError?></label>
            <input type="number" name="item_value" value="<?php print $oldValue?>">

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Add item</button>
        </form>
    </body>
</html>
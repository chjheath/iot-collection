<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = $_GET['id'];

    $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($cid, $_SESSION['uid']));

    $isOwnerOfCollection = FALSE;

    $itemName = '';
    $itemValue = 0;

    $nameOK = FALSE;
    $valueOK = FALSE;

    $nameError = '';
    $valueError = '';

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection) {
        Header("Location:account.php");
    } else {
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
                $stmt = $con->prepare("INSERT INTO collection_items VALUES (NULL, ?, ?, '', ?, 'Yes')");
                $stmt->execute(array($itemName, $itemValue, $_POST['coll_id']));
                $stmt->closeCursor();

                $stmt = $con->prepare("SELECT COUNT(*) as c FROM collection_items WHERE CollectionID = ?");
                $stmt->execute(array($_POST['coll_id']));
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $idx = $res['c'];
                $stmt->closeCursor();

                //Header("Location:choose_image?id=".$_POST['coll_id'].'&item='.$idx);
            }
        }
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add item to your collection</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Add an item to your collection.</h1>
        <form action="add_item.php" method="post">

            <input type="hidden" name="coll_id" value="<?php print $cid?>">

            <label for="item_name">Enter item name <?php print $nameError?></label>
            <input type="text" name="item_name">

            <br/><br/>

            <label for="item_value">Enter item value (in USD) <?php print $valueError?></label>
            <input type="number" name="item_value">

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Add item</button>
        </form>
    </body>
</html>
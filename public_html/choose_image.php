<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = '';
    $iid = '';

    if (isset($_GET['id'])) {
        $cid = $_GET['id'];
        $cid = trim($cid);
        $cid = cleanQuery($cid);
    }

    if (isset($_GET['item'])) {
        $iid = $_GET['item'];
        $iid = trim($iid);
        $iid = cleanQuery($iid);
    }

    $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($cid, $_SESSION['uid']));

    $isOwnerOfCollection = FALSE;

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    $stmt = $con->prepare("SELECT ItemID FROM collection_items WHERE CollectionID = ?");
    $stmt->execute(array($cid));

    while($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($res['ItemID'] == $iid) {
            break;
        } else {
            $stmt->closeCursor();
            Header("Location:account.php");
        }
    }

    if (!$isOwnerOfCollection && !isset($_POST['submit_form'])) {
        Header("Location:account.php");
    } else if (isset($_POST['submit_form'])) {
        initiate_pi($_SESSION['uid'], $_POST['coll_id'], $_POST['item_id']);
    }
    

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add image to your item</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Add an image to your item</h1>
        <form action="choose_image.php" method="post">

            <input type="hidden" name="coll_id" value="<?php print $cid?>">
            <input type="hidden" name="item_id" value="<?php print $iid?>">

            <h2>Steps:</h2>
            <ul>
                <li>1. Please turn on the Raspberry Pi.</li>
                <li>2. Click Continue</li>
            </ul>

            <button type="submit" name="submit_form" class="button">Continue</button>
        </form>
    </body>
</html>
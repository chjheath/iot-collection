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
        move_uploaded_file($_FILES['img_upl']["tmp_name"], "images/".basename($_FILES["img_upl"]["item_"].$_POST['item_id'].".png"));

        $url = "images/".basename($_FILES["img_upl"]["item_"].$_POST['item_id'].".png");

        $stmt = $con->prepare("UPDATE collection_items SET ItemImage = ? WHERE ItemID = ?");
        $stmt->execute(array($url, $_POST['item_id']));
        $stmt->closeCursor();

        Header("Location:index.php");
    }
    

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Finish adding image to your item</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Finish adding image to your item</h1>
        <form action="finalize_image.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="coll_id" value="<?php print $cid?>">
            <input type="hidden" name="item_id" value="<?php print $iid?>">

            <h2>Steps:</h2>
            <ul>
                <li>1. Place collectible on platform.</li>
                <li>2. Take picture.</li>
                <li>3. Click Finish</li>
            </ul>

            <br/><br/>

            <label for="img_upl">Choose Image From Files</label>
            <input type="file" id="img_upl" name="img_upl" accept="image/png, image/gif, image/jpeg">

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Finish</button>
        </form>
    </body>
</html>
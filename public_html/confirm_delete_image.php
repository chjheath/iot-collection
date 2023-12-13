<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = $_GET['cid'];
    $iid = $_GET['item'];

    $cid = trim($cid);
    $cid = cleanQuery($cid);

    $iid = trim($iid);
    $iid = cleanQuery($iid);

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
    
        if (isset($_POST['submit_form'])) {
           Header("Location:delete_image.php?cid=".$_POST['coll_id'].'&item='.$_POST['item_id']);
        }
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirm Removal</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Are you absolutely sure you want to delete this image? This <p style="color:red; font-weight:bold;">CANNOT</p> be reversed.</h1>
        <form action="confirm_delete_image.php" method="post">

            <input type="hidden" name="coll_id" value="<?php print $cid?>">
            <input type="hidden" name="item_id" value="<?php print $iid?>">

            <br/><br/>

            <button type="submit" class="delete" name="submit_form">Confirm Delete</button>
        </form>
    </body>
</html>
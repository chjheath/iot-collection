<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid']) && !isset($_GET['access'])) {
        if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
            Header("Location:account.php"); //redirect to account page if already logged in.
        }
    }

    $isAdmin = FALSE;

    if (isset($_GET['access']) && (!isset($_SESSION['aid']) && !isset($_SESSION['sid']))) {
        Header("Location:view_collection.php?id=".$_GET['id']);
    } else {
        $isAdmin = TRUE;
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = $_GET['id'];
    $cid = trim($cid);
    $cid = cleanQuery($cid);

    $id = '';

    if (isset($_SESSION['uid'])) {
        $id = $_SESSION['uid'];
    } else {
        $stmt = $con->prepare("SELECT UserID FROM collections WHERE CollectionID = ?");
        $stmt->execute(array($cid));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $res['UserID'];

        $stmt->closeCursor();
    }

    $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($cid, $id));

    $isOwnerOfCollection = FALSE;

    if (!$isAdmin) {
        $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'YES'");
        $stmt->execute(array($cid, $id));

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $isOwnerOfCollection = TRUE;
        }
    } else {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection && !isset($_POST['submit_form'])) {
        Header("Location:account.php");
    } else {
    
        if (isset($_POST['submit_form'])) {
           Header("Location:delete_collection.php?id=".$_POST['coll_id']);
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
        <h1>Are you absolutely sure you want to delete this collection? This <p style="color:red; font-weight:bold;">CANNOT</p> be reversed.</h1>
        <form action="confirm_delete_coll.php" method="post">

            <input type="hidden" name="coll_id" value="<?php print $cid?>">

            <br/><br/>

            <button type="submit" class="delete" name="submit_form">Confirm Delete</button>
        </form>
    </body>
</html>
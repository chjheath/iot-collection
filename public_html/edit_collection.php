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

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection && !isset($_POST['submit_form'])) {
        Header("Location:account.php");
    } else {
        //Retrieve old data

        $stmt = $con->prepare("SELECT CollectionName, Visibility FROM collections WHERE CollectionID = ?");
        $stmt->execute(array($cid));

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $oldName = $res['CollectionName'];
        $oldVisibility = $res['Visibility'];
        $visibilityOptions = '';

        $stmt->closeCursor();

        //Fill in old data

        if ($oldVisibility == 'Public') {
            $visibilityOptions = '<option value="Public" selected>Yes</option>
                                  <option value="Private">No</option>';
        } else {
            $visibilityOptions = '<option value="Public">Yes</option>
                                  <option value="Private" selected>No</option>';
        }
    
        $newName = '';
        $newVisibility = '';
    
        $nameError = '';
        $nameOK = FALSE;
    
        if (isset($_POST['submit_form'])) {
    
            if (isset($_POST['coll_name'])) {
                $newName = trim($_POST['coll_name']);
                $newName = cleanQuery($newName);

                if (strlen($newName) <= 25) {
                    $nameOK = TRUE;
                } else {
                    Header("Location:edit_collection.php?id=".$_POST['coll_id']);
                    $nameError = '<p style="color: red; font-weight: bold">Your collection name can only be 25 or less characters.</p>';
                }

            } else {
                Header("Location:edit_collection.php?id=".$_POST['coll_id']);
                $nameError = '<p style="color: red; font-weight: bold">You must enter a name</p>';
            }

            $newVisibility = trim($_POST['coll_visibility']);
            $newVisibility = cleanQuery($newVisibility);

            if ($nameOK) {
                $stmt = $con->prepare("UPDATE collections SET CollectionName = ?, Visibility = ? WHERE CollectionID = ?");
                $stmt->execute(array($newName, $newVisibility, $_POST['coll_id']));
                $stmt->closeCursor();

                Header("Location:account.php");
            }
    
        }
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit your collection</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Edit your collection.</h1>
        <form action="edit_collection.php" method="post">

            <input type="hidden" name="coll_id" value="<?php print $cid?>">

            <label for="coll_name">Enter Collection Name <?php print $nameError?></label>
            <input type="text" name="coll_name" value="<?php print $oldName?>">

            <br/><br/>

            <label for="coll_visibility">Do you want your collection to be viewable by others?</label>

            <select name="coll_visibility">
                <?php print $visibilityOptions?>
            </select>

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Submit changes</button>
        </form>
    </body>
</html>
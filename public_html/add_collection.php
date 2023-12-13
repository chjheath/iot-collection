<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $collectionName = '';
    $collectionVisibility = '';

    $nameError = '';
    $nameOK = FALSE;

    if (isset($_POST['submit_form'])) {

        if (isset($_POST['coll_name'])) {
            $collectionName = trim($_POST['coll_name']);
            $collectionName = cleanQuery($collectionName);

            if (strlen($collectionName) <= 25) {
                $nameOK = TRUE;
            } else {
                $nameError = '<p style="color: red; font-weight: bold">Your collection name can only be 25 or less characters.</p>';
            }
        } else {
            $nameError = '<p style="color: red; font-weight: bold">You must enter a name</p>';
        }

        $collectionVisibility = trim($_POST['coll_visibility']);
        $collectionVisibility = cleanQuery($collectionVisibility);

        if ($nameOK) {
            $stmt = $con->prepare("INSERT INTO collections VALUES (NULL, ?, ?, ?, 'Yes')");
            $stmt->execute(array($collectionName, $collectionVisibility, $_SESSION['uid']));
            $stmt->closeCursor();

            Header("Location:account.php");
        }
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add New Collection</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Fill in the form below to create a new collection. You will be able to select an image after creation.</h1>
        <form action="add_collection.php" method="post">

            <label for="coll_name">Enter Collection Name <?php print $nameError?></label>
            <input type="text" name="coll_name">

            <br/><br/>

            <label for="coll_visibility">Do you want your collection to be viewable by others?</label>

            <select name="coll_visibility">
                <option value="Public">Yes</option>
                <option value="Private">No</option>
            </select>

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Create new collection</button>
        </form>
    </body>
</html>
<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['sid']) && !$isset($_SESSION['aid'])) {
        Header("Location:index.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $id = trim($id);
        $id = cleanQuery($id);
    } else {
        Header("Location:admin_page.php");
    }

    if (isset($_POST['submit_form'])) {
        Header("Location:delete_user.php?id=".$_POST['user_id']);
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
        <h1>Are you absolutely sure you want to delete this user? This <p style="color:red; font-weight:bold;">CANNOT</p> be reversed.</h1>
        <form action="confirm_delete_user.php" method="post">

            <input type="hidden" name="user_id" value="<?php print $id?>">

            <br/><br/>

            <button type="submit" class="delete" name="submit_form">Confirm Delete</button>
        </form>
    </body>
</html>
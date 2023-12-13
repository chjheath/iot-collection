<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['sid']) && !isset($_SESSION['aid'])) {
        Header("Location:index.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    //Variables

    $oldPass = '';
    $newPass = '';
    $confirmedPass = '';

    $oldPassOK = FALSE;
    $newPassOK = FALSE;
    $passesMatch = FALSE;

    $oldError = '';
    $newError = '';
    $confirmError = '';
    $matchError = '';
    $oldWrongError = '';

    $id = '';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $id = trim($id);
        $id = cleanQuery($id);
    } else if (isset($_POST['user_id'])) {
        $id = $_POST['user_id'];
        $id = trim($id);
        $id = cleanQuery($id);
    }

    //Handle submission

    if (isset($_POST['submit_form'])) { //user submits form

        //Pull In New

        if (isset($_POST['new_pass'])) {
            $newPass = trim($_POST['new_pass']);
            $newPass = cleanQuery($newPass);
        } else {
            $newError = '<p style="color:red; font-weight:bold">You must enter a new password.</p>';
        }

        //Pull In Confirmed Pass

        if (isset($_POST['conf_new'])) {
            $confirmedPass = trim($_POST['conf_new']);
            $confirmedPass = cleanQuery($confirmedPass);
        } else {
            $confirmError = '<p style="color:red; font-weight:bold">You must confirm their new password.</p>';
        }

        //Verify Both passwords meet requirements & match

        if (validateQuery($newPass, 'pass')) {
            $newPassOK = TRUE;
        } else {
            $newError = '<p style="color:red; font-weight:bold">This password does not meet the requirements (10+ characters with at least one letter & one number).</p>';
        }

        if ($newPassOK) {
            if ($newPass == $confirmedPass) {
                $passesMatch = TRUE;
            } else {
                $matchError = '<p style="color:red; font-weight:bold">Their new passwords must match!</p>';
            }
        }

        //Update

        if ($passesMatch) {
            //Hash New Password

            $newPass = password_hash($newPass, PASSWORD_DEFAULT);

            $stmt = $con->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
            $stmt->execute(array($newPass, $id));
            $stmt->closeCursor();

            Header("Location:admin_page.php");
        }

    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Change user password</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Change password.</h1>
        <form action="admin_change_user_pwd.php" method="post">

            <input type="hidden" name="user_id" value="<?php print $id?>">

            <label for="new_pass">Enter New Password <?php print $newError?> </label>
            <input type="password" name="new_pass">

            <br/><br/>

            <label for="conf_new">Confirm New Password <?php print $confirmError?> <?php print $matchError?></label>
            <input type="password" name="conf_new">

            <br/><br/>

            <button type="submit" class="button" name="submit_form">Submit changes</button>
        </form>
    </body>
</html>
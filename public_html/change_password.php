<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:login.php"); //redirect to account page if already logged in.
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

    //Handle submission

    if (isset($_POST['submit_form'])) { //user submits form

        //Pull In Old

        if (isset($_POST['old_pass'])) {
            $oldPass = trim($_POST['old_pass']);
            $oldPass = cleanQuery($oldPass);
        } else {
            $oldError = '<p style="color:red; font-weight:bold">You must enter your current password.</p>';
        }

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
            $confirmError = '<p style="color:red; font-weight:bold">You must confirm your new password.</p>';
        }

        //Verify Both passwords meet requirements & match

        if (validateQuery($newPass, 'pass')) {
            $newPassOK = TRUE;
        } else {
            $newError = '<p style="color:red; font-weight:bold">Your password does not meet the requirements (10+ characters with at least one letter & one number).</p>';
        }

        if ($newPassOK) {
            if ($newPass == $confirmedPass) {
                $passesMatch = TRUE;
            } else {
                $matchError = '<p style="color:red; font-weight:bold">Your new passwords must match!</p>';
            }
        }

        //Verify old password & update.

        if ($passesMatch) {
            //Verify old password.

            $stmt = $con->prepare("SELECT Email FROM users WHERE UserID = ?");
            $stmt->execute(array($_SESSION['uid']));
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            $email = $res['Email'];

            $stmt->closeCursor();

            if (validateLogin($email, $oldPass)) {

                //Hash New Password

                $newPass = password_hash($newPass, PASSWORD_DEFAULT);

                $stmt = $con->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
                $stmt->execute(array($newPass, $_SESSION['uid']));
                $stmt->closeCursor();

                Header("Location:account.php");
            } else {
                $oldWrongError = '<p style="color:red; font-weight:bold">Your old password is incorrect.</p>';
            }
        }

    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Change Password</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Change password.</h1>
        <form action="change_password.php" method="post">

            <label for="old_pass">Enter Current Password <?php print $oldError?><?php print $oldWrongError?></label>
            <input type="password" name="old_pass">

            <br/><br/>

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
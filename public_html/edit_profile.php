<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:login.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    //Fill data

    $stmt = $con->prepare("SELECT Email, Username FROM users WHERE UserID = ?");
    $stmt->execute(array($_SESSION['uid']));
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $currentEmail = $res['Email'];
    $currentUser = $res['Username'];

    $stmt->closeCursor(); //closing original stmt request

    //Variables

    $isChangingEmail = FALSE;
    $isChangingUser = FALSE;

    $emailError = '';
    $userError = '';

    $newEmail = '';
    $newUser = '';

    $emailOK = FALSE;
    $userOK = FALSE;

    //Handle submission

    if (isset($_POST['submit_form'])) { //user submits form

        //Email

        if (isset($_POST['user_email'])) {
            $newEmail = trim($_POST['user_email']);
            $newEmail = cleanQuery($newEmail);
        }

        //Username

        if (isset($_POST['user_name'])) {
            $newUser = trim($_POST['user_name']);
            $newUser = cleanQuery($newUser);
        }

        if ($newEmail != $currentEmail) {
            $isChangingEmail = TRUE;
        }

        if ($newUser != $currentUser) {
            $isChangingUser = TRUE;
        }

        //Validating

        $emailAvailable = validateQuery($newEmail, 'email');
        $userAvailable = validateQuery($newUser, 'user');

        if ($emailAvailable && $newEmail != '') {
            $emailOK = TRUE;
        } else {
            $emailError = '<p style="color:red; font-weight:bold">Invalid e-mail. Either taken or empty entry.</p>';
        }

        if ($userAvailable && $newUser != '') {
            $userOK = TRUE;
        } else {
            $userError = '<p style="color:red; font-weight:bold">Invalid username. Either taken or empty entry.</p>';
        }

        //Updating

        if ($isChangingEmail && $isChangingUser) { //changing both email & username

            if ($emailOK && $userOK) {
                $stmt = $con->prepare("UPDATE users SET Email = ?, Username = ? WHERE UserID = ?");
                $stmt->execute(array($newEmail, $newUser, $_SESSION['uid']));
                $stmt->closeCursor();

                Header("Location:account.php");
            }

        } else if (!$isChangingEmail && $isChangingUser) { //changing only username

            if ($userOK) {
                $stmt = $con->prepare("UPDATE users SET Username = ? WHERE UserID = ?");
                $stmt->execute(array($newUser, $_SESSION['uid']));
                $stmt->closeCursor();

                Header("Location:account.php");
            }

        } else if ($isChangingEmail && !$isChangingUser) { //changing only email

            if ($emailOK) {
                $stmt = $con->prepare("UPDATE users SET Email = ? WHERE UserID = ?");
                $stmt->execute(array($newEmail, $_SESSION['uid']));
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
        <title>Edit Profile</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Edit account details.</h1>
        <form action="edit_profile.php" method="post">

            <label for="user_email">Edit E-mail <?php print $emailError?></label>
            <input type="email" name="user_email" value="<?php print $currentEmail?>">

            <br/><br/>

            <label for="user_name">Edit Username <?php print $userError?></label>
            <input type="text" name="user_name" value="<?php print $currentUser?>">

            <br/><br/>

            <button type="submit" class="button" name="submit_form">Submit changes</button>
        </form>
    </body>
</html>
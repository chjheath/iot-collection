<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    } else if (isset($_SESSION['aid']) || isset($_SESSION['sid'])) {
        Header("Location:admin_page.php");
    }

    include "header.php";

    require_once 'util/util.php';

    $loginName = '';
    $loginPass = '';

    $invalidLoginError = '';

    $loginOK = FALSE;

    if (isset($_POST['submit_form'])) {

        if (isset($_POST['user_email'])) {
            $loginName = trim($_POST['user_email']);
            $loginName = cleanQuery($loginName);
        }

        if (isset($_POST['user_pass'])) {
            $loginPass = trim($_POST['user_pass']);
            $loginPass = cleanQuery($loginPass);
        }

        if (validateLogin($loginName, $loginPass)) {
            $loginOK = TRUE;
        } else {
            $invalidLoginError = '<p style="color:red; font-weight:bold">Invalid login.</p>';
        }

        if ($loginOK) {
            global $con;
            $stmt = '';

            $stmt = $con->prepare("SELECT UserID FROM users WHERE Email = ?");
            $stmt->execute(array($loginName));

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['uid'] = $res['UserID'];

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
        <title>Login</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Login to your account. Not a user? <a href="login_admin.php">Click here</a></h1>
        <?php print $invalidLoginError?>
        <form action="login.php" method="post">
            <label for="user_email">Enter e-mail address</label>
            <input type="text" name="user_email"/>

            <br/><br/>

            <label for="user_pass">Enter password</label>
            <input type="password" name="user_pass"/>

            <br/><br/>

            <button type="submit" class="button" name="submit_form">Login</button>
        </form>
    </body>
</html>
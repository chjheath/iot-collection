<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $loginName = '';
    $loginPass = '';

    $invalidLoginError = '';

    $loginOK = FALSE;

    if (isset($_POST['submit_form'])) {

        if (isset($_POST['admin_name'])) {
            $loginName = trim($_POST['admin_name']);
            $loginName = cleanQuery($loginName);
        }

        if (isset($_POST['admin_pass'])) {
            $loginPass = trim($_POST['admin_pass']);
            $loginPass = cleanQuery($loginPass);
        }

        if (validateAdminLogin($loginName, $loginPass)) {
            $loginOK = TRUE;
        } else {
            $invalidLoginError = '<p style="color:red; font-weight:bold">Invalid login.</p>';
        }

        if ($loginOK) {
            global $con;
            $stmt = '';

            $stmt = $con->prepare("SELECT AdminID, AccessLevel FROM admins WHERE Username = ?");
            $stmt->execute(array($loginName));

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($res['AccessLevel'] == 'Low') {
                $_SESSION['aid'] = $res['AdminID'];
            } else if ($res['AccessLevel'] == 'High') {
                $_SESSION['sid'] = $res['AdminID'];
            }

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
        <title>Login</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Login to your account.</h1>
        <?php print $invalidLoginError?>
        <form action="login_admin.php" method="post">
            <label for="admin_name">Enter username</label>
            <input type="text" name="admin_name"/>

            <br/><br/>

            <label for="admin_pass">Enter password</label>
            <input type="password" name="admin_pass"/>

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Login</button>
        </form>
    </body>
</html>
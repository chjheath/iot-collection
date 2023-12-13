<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    include "header.php";

    require_once 'util/util.php';

    $userOK = FALSE;
    $passOK = FALSE;

    $u_user = '';
    $u_pass = '';
    $conf_u_pass = '';
    $u_salt = '';
    $access_level = '';

    $userError = '';
    $passError = '';
    $confirmPassError = '';

    if (isset($_POST['submit_form'])) {

        //Username validation
        if (isset($_POST['user_name'])) {
            $u_user = trim($_POST['user_name']);
            $u_user = cleanQuery($u_user);

            if (validateQuery($u_user, 'admin')) {
                $userOK = TRUE;
            } else {
                $userError = '<p style="color:red">Invalid username or it is already taken.</p>';
            }
        } else {
            $userError = '<p style="color:red">Enter a username.</p>';
        }

        //Password validation & encryption (encryption added later)

        if (isset($_POST['user_pass']) && isset($_POST['conf_user_pass'])) {
            $u_pass = trim($_POST['user_pass']);
            $u_pass = cleanQuery($u_pass);

            $conf_u_pass = trim($_POST['conf_user_pass']);
            $conf_u_pass = cleanQuery($conf_u_pass);

            if (validateQuery($u_pass, 'pass') && validateQuery($conf_u_pass, 'pass')) {
                if ($u_pass == $conf_u_pass) {
                    $passOK = TRUE;
                } else {
                    $confirmPassError = '<p style="color:red">Passwords do not match.</p>';
                }
            } else {
                $passError = '<p style="color:red">Password does not meet requirements.</p>';
            }
        } else {
            $passError = '<p style="color:red">Enter a password.</p>';
        }

        //Checking if terms are agreed to

        if (isset($_POST['access_level'])) {
            $access_level = 'High';
        } else {
            $access_level = 'Low';
        }

        //Submission

        if ($userOK && $passOK) {
            // Pass Hash & Verif. from https://www.geeksforgeeks.org/how-to-secure-hash-and-salt-for-php-passwords/
            
            $u_pass = password_hash($u_pass, PASSWORD_DEFAULT);

            $stmt = $con->prepare("INSERT INTO admins VALUES (NULL, ?, ?, ?, ?)");
            if ($stmt->execute(array($u_user, $u_pass, $access_level, 'Yes'))) {
                $stmt->closeCursor();

                //resetting variables

                $u_user = '';
                $u_pass = '';
                $conf_u_pass = '';

                //Redirect
                Header("Location:admin_page.php");
            } else {
                //Redirect back to form
                Header("Location:add_admin.php");
            }
        }
        
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register admin</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Register a new admin account</h1>
        <form action="add_admin.php" method="post">
            <label for="user_name">Enter username <?php print $userError?></label>
            <input type="text" name="user_name"/>

            <br/><br/>

            <label for="user_pass">Enter password <?php print $passError?></label>
            <input type="password" name="user_pass"/>

            <br/><br/>

            <label for="conf_user_pass">Confirm password <?php print $confirmPassError?></label>
            <input type="password" name="conf_user_pass"/>

            <br/><br/>

            <label for="access_level">Give this admin high level access?</label>
            <input type="checkbox" name="access_level"/>

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Add Admin</button>
        </form>
    </body>
</html>
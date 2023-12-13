<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $emailOK = FALSE;
    $userOK = FALSE;
    $passOK = FALSE;
    $agreedToTerms = FALSE;

    $u_email = '';
    $u_user = '';
    $u_pass = '';
    $conf_u_pass = '';
    $u_salt = '';

    $emailError = '';
    $userError = '';
    $passError = '';
    $confirmPassError = '';

    if (isset($_POST['submit_form'])) {

        //Email validation (modified from course-project code)
        if (isset($_POST['user_email'])) {
            if (filter_input (INPUT_POST, 'user_email', FILTER_VALIDATE_EMAIL)) {
                $u_email = trim($_POST['user_email']);
                $u_email = cleanQuery($u_email);

                if (validateQuery($u_email, 'email')) {
                    $emailOK = TRUE;
                } else {
                    $emailError = '<p style="color:red">Invalid e-mail or it is already taken.</p>';
                }
            } else {
                $emailError = '<p style="color:red">Enter an e-mail.</p>';
            }
        }

        //Username validation
        if (isset($_POST['user_name'])) {
            $u_user = trim($_POST['user_name']);
            $u_user = cleanQuery($u_user);

            if (validateQuery($u_user, 'user')) {
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

        if (isset($_POST['agreeToTerms'])) {
            $agreedToTerms = TRUE;
        }

        //Submission

        if ($userOK && $emailOK && $passOK) {
            // Pass Hash & Verif. from https://www.geeksforgeeks.org/how-to-secure-hash-and-salt-for-php-passwords/
            
            $u_pass = password_hash($u_pass, PASSWORD_DEFAULT);

            $stmt = $con->prepare("INSERT INTO users VALUES (NULL, ?, ?, ?, ?)");
            if ($stmt->execute(array($u_email, $u_user, $u_pass, 'Yes'))) {
                $stmt->closeCursor();

                //resetting variables

                $u_email = '';
                $u_user = '';
                $u_pass = '';
                $conf_u_pass = '';

                //Redirect
                Header("Location:login.php");
            } else {
                //Redirect back to form
                Header("Location:register.php");
            }
        }
        
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Register a new account</h1>
        <form action="register.php" method="post">
            <label for="user_email">Enter e-mail address <?php print $emailError?></label>
            <input type="email" name="user_email"/>

            <br/><br/>

            <label for="user_name">Enter username <?php print $userError?></label>
            <input type="text" name="user_name"/>

            <br/><br/>

            <label for="user_pass">Enter password <?php print $passError?></label>
            <input type="password" name="user_pass"/>

            <br/><br/>

            <label for="conf_user_pass">Confirm password <?php print $confirmPassError?></label>
            <input type="password" name="conf_user_pass"/>

            <br/><br/>

            <label for="agreeToTerms">Click to agree to terms</label>
            <input type="checkbox" name="agreeToTerms" required/>

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Register</button>
        </form>
    </body>
</html>
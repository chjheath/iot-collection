<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    include "header.php";

    require_once 'util/util.php';

    $userOK = FALSE;
    $isChangingUser = FALSE;

    $admin_id = $_GET['id'];
    $admin_id = trim($admin_id);
    $admin_id = cleanQuery($admin_id);

    $errorName = '';

    if (isset($_GET['error'])) {
        $errorName = $_GET['error'];
        $errorName = trim($errorName);
        $errorName = cleanQuery($errorName);

        if ($errorName == 'invalid_name') {
            $userError = '<p style="color:red">Invalid username or it is already taken.</p>';
        }
    }

    $u_user = '';
    $access_level = '';

    $userError = '';

    $oldUser = '';
    $oldAccess = '';

    //Retrieve & fill old data

    $stmt = $con->prepare("SELECT Username, AccessLevel FROM admins WHERE AdminID = ? AND Active = 'Yes'");
    $stmt->execute(array($admin_id));
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $oldUser = $res['Username'];
    
    if ($res['AccessLevel'] == 'High') {
        $oldAccess = 'checked';
    }

    if (isset($_POST['submit_form'])) {

        //Username validation
        if (isset($_POST['user_name'])) {
            if ($oldUser != $_POST['user_name']) {
                $isChangingUser = TRUE;
            }

            $u_user = trim($_POST['user_name']);
            $u_user = cleanQuery($u_user);

            if ($isChangingUser) {
                if (validateQuery($u_user, 'admin')) {
                    $userOK = TRUE;
                } else {
                    $userError = '<p style="color:red">Invalid username or it is already taken.</p>';
                }
            }
        } else {
            $userError = '<p style="color:red">Enter a username.</p>';
        }

        //Checking if terms are agreed to

        if (isset($_POST['access_level'])) {
            $access_level = 'High';
        } else {
            $access_level = 'Low';
        }

        //Submission

        if ($userOK) {
            $stmt = $con->prepare("UPDATE admins SET Username = ?, AccessLevel = ? WHERE AdminID = ? AND Active = 'Yes'");
            if ($stmt->execute(array($u_user, $access_level, $_POST['post_admin_id']))) {
                $stmt->closeCursor();

                //resetting variables

                $u_user = '';

                //Redirect
                Header("Location:admin_page.php");
            } else {
                //Redirect back to form
                Header("Location:admin_page.php");
            }
        } else {
            Header("Location:edit_admin.php?id=".$_POST['post_admin_id']."&error=invalid_name");
        }
        
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit admin</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>Edit an admin account</h1>
        <form action="edit_admin.php" method="post">

            <input type="hidden" value="<?php print $admin_id?>" name="post_admin_id"/>

            <label for="user_name">Enter username <?php print $userError?></label>
            <input type="text" name="user_name" value="<?php print $oldUser?>"/>

            <br/><br/>

            <label for="access_level">Give this admin high level access?</label>
            <input type="checkbox" name="access_level" <?php print $oldAccess?>/>

            <br/><br/>

            <button type="submit" name="submit_form" class="button">Submit changes</button>
        </form>
    </body>
</html>
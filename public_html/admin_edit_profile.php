<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:login.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $id = '';

    if (isset($_SESSION['sid'])) {
        $id = $_SESSION['sid'];
    } else if (isset($_SESSION['aid'])) {
        $id = $_SESSION['aid'];
    }

    //Fill data

    $stmt = $con->prepare("SELECT Username FROM admins WHERE AdminID = ?");
    $stmt->execute(array($id));
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $currentUser = $res['Username'];

    $stmt->closeCursor(); //closing original stmt request

    //Variables

    $isChangingUser = FALSE;

    $userError = '';

    $newUser = '';

    $userOK = FALSE;

    //Handle submission

    if (isset($_POST['submit_form'])) { //user submits form

        //Username

        if (isset($_POST['user_name'])) {
            $newUser = trim($_POST['user_name']);
            $newUser = cleanQuery($newUser);
        }

        if ($newUser != $currentUser) {
            $isChangingUser = TRUE;
        }

        //Validating

        $userAvailable = validateQuery($newUser, 'admin');

        if ($userAvailable && $newUser != '') {
            $userOK = TRUE;
        } else {
            $userError = '<p style="color:red; font-weight:bold">Invalid username. Either taken or empty entry.</p>';
        }

        //Updating

        if ($isChangingUser) {
            if ($userOK) {
                $stmt = $con->prepare("UPDATE admins SET Username = ? WHERE AdminID = ?");
                $stmt->execute(array($newUser, $id));
                $stmt->closeCursor();
    
                Header("Location:admin_page.php");
            } else {
                $userError = '<p style="color:red; font-weight:bold">Invalid username. Either taken or empty entry.</p>';
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
        <form action="admin_edit_profile.php" method="post">

            <label for="user_name">Edit Username <?php print $userError?></label>
            <input type="text" name="user_name" value="<?php print $currentUser?>">

            <br/><br/>

            <button type="submit" class="button" name="submit_form">Submit changes</button>
        </form>
    </body>
</html>
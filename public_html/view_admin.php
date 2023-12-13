<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['sid'])) {
        if (isset($_SESSION['aid'])) {
            Header("Location:admin_page.php");
        } else {
            Header("Location:index.php");
        }
    }
    

    include "header.php";

    require_once 'util/util.php';

    $id = '';
    $user = '';
    $access = '';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $id = trim($id);
        $id = cleanQuery($id);
    }

    $stmt = $con->prepare("SELECT Username, AccessLevel FROM admins WHERE AdminID = ? AND Active = 'Yes'");
    $stmt->execute(array($id));
    
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $user = $res['Username'];
    $access = $res['AccessLevel'];

    $stmt->closeCursor();

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View admin</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>View account</h1>

        <h3>Username: <?php print $user?></h3>
        <h3>Access Level: <?php print $access?></h3>
    </body>
</html>
<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    include "header.php";

    require_once 'util/util.php';

    $id = '';
    $u_email = '';
    $u_user = '';
    $coll_count = '';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $id = trim($id);
        $id = cleanQuery($id);
    }

    $stmt = $con->prepare("SELECT Email, Username FROM users WHERE UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($id));
    
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $u_email = $res['Email'];
    $u_user = $res['Username'];

    $stmt->closeCursor();

    $stmt = $con->prepare("SELECT COUNT(CollectionID) as count FROM collections WHERE UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($id));
    
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $coll_count = $res['count'];

    $stmt->closeCursor();

    $link = '';

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View user</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>View account</h1>

        <h3>Email: <?php print $u_email?></h3>
        <h3>Username: <?php print $u_user?></h3>
        <h3>Collections: <?php print $coll_count?></h3>
    </body>
</html>
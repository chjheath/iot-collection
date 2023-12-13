<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['sid']) && !isset($_SESSION['aid'])) {
        Header("Location:index.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $id = trim($id);
        $id = cleanQuery($id);
    } else {
        Header("Location:admin_page.php");
    }

    $stmt = $con->prepare("UPDATE users SET Active = ? WHERE UserID = ?");
    $stmt->execute(array('No', $id));

    $stmt->closeCursor();

    Header("Location:admin_page.php");

?>
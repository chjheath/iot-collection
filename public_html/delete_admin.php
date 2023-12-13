<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['sid'])) {
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

    $stmt = $con->prepare("UPDATE admins SET Active = ? WHERE AdminID = ?");
    $stmt->execute(array('No', $id));

    $stmt->closeCursor();

    Header("Location:admin_page.php");

?>
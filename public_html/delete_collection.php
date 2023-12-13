<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid']) && !isset($_GET['access'])) {
        if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
            Header("Location:account.php"); //redirect to account page if already logged in.
        }
    }

    $isAdmin = FALSE;

    if (isset($_GET['access']) && (!isset($_SESSION['aid']) && !isset($_SESSION['sid']))) {
        Header("Location:view_collection.php?id=".$_GET['id']);
    } else {
        $isAdmin = TRUE;
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = $_GET['id'];

    $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($cid, $_SESSION['uid']));

    $isOwnerOfCollection = FALSE;

    if (!$isAdmin) {
        $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'YES'");
        $stmt->execute(array($cid, $_SESSION['uid']));

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $isOwnerOfCollection = TRUE;
        }
    } else {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection) {
        Header("Location:account.php");
    } else {
        $stmt = $con->prepare("UPDATE collections SET Active = ? WHERE CollectionID = ?");
        $stmt->execute(array('No', $cid));

        $stmt->closeCursor();

        Header("Location:account.php");
    }

?>
<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = $_GET['cid'];
    $iid = $_GET['item'];

    $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($cid, $_SESSION['uid']));

    $isOwnerOfCollection = FALSE;

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection) {
        Header("Location:account.php");
    } else {
        $stmt = $con->prepare("UPDATE collection_items SET Active = ? WHERE CollectionID = ? AND ItemID = ?");
        $stmt->execute(array('No', $cid, $iid));

        $stmt->closeCursor();

        Header("Location:account.php");
    }

?>
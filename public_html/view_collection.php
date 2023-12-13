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
    $cid = trim($cid);
    $cid = cleanQuery($cid);

    $isOwnerOfCollection = FALSE;
    $hasSubmitted = FALSE;
    $isPrivate = FALSE;
    
    $totalValue = 0;

    if (!$isAdmin) {
        $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'YES'");
        $stmt->execute(array($cid, $_SESSION['uid']));

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $isOwnerOfCollection = TRUE;
        }
    } else {
        $isOwnerOfCollection = TRUE;
    }

    $stmt = $con->prepare("SELECT Visibility FROM collections WHERE CollectionID = ?");
    $stmt->execute(array($cid));
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $vis = $res['Visibility'];

    if ($res['Visibility'] == 'Private') {
        $isPrivate = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection && $isPrivate && !$isAdmin) {
        Header("Location:collections.php");
    } else {
        //Retrieve old data

        $stmt = $con->prepare("SELECT CollectionName, Visibility, UserID FROM collections WHERE CollectionID = ? AND Active = 'Yes'");
        $stmt->execute(array($cid));

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $postedByName = userIDToName($res['UserID']);
        $oldName = $res['CollectionName'];
        $oldVisibility = $res['Visibility'];

        $stmt->closeCursor();

        if ($oldName == '' || $oldVisibility == '') { //does not exist
            Header("Location:collections.php");
        }

        $stmt = $con->prepare("SELECT COUNT(*) as c FROM collection_items WHERE CollectionID = ? AND Active = 'Yes'");
        $stmt->execute(array($cid));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $count = $res["c"];

        $stmt->closeCursor();

        # Insert collections

        $cards = '';

        $stmt = $con->prepare("SELECT * FROM collection_items WHERE CollectionID = ? AND Active = 'Yes'"); //update later
        $stmt->execute(array($cid));

        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $collID = $res['CollectionID'];
            $itemID = $res['ItemID'];
            $itemName = $res['ItemName'];
            $itemValue = $res['ItemValue'];
            $itemImage = $res['ItemImage'];

            $cards = $cards.'<div class="column">
                                    <div class="box">
                                        <div class="container">
                                            <br/>
                                            <img src="'.$itemImage.'">
                                            <h2 style="text-align:center">'.$itemName.'</h2>
                                            <p class="description">Value: $'.$itemValue.'</p>
                                        </div>
                                    </div>
                                </div>';

            $totalValue = $totalValue + $itemValue;
        }

        $stmt->closeCursor();
    
        if (isset($_POST['create-new'])) {
            $hasSubmitted = TRUE;
            Header("Location:add_item.php?id=".$_POST['coll_id']);
        }
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View your collection</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <h1>View this collection</h1>
        <hr/>
        <form action="view_collection.php" method="post">
            <input type="hidden" name="coll_id" value="<?php print $cid?>">

            <h3>Posted By: <?php print $postedByName?></h3>
            <h3>Name: <?php print $oldName?></h3>
            <h3>Visibility: <?php print $oldVisibility?></h3>
            <h3>Total Value: $<?php print $totalValue?></h3>

            <br/><br/>

            <div class="row">
                <?php print $cards?>
            </div>
        </form>
    </body>
</html>
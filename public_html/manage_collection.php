<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['uid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $cid = $_GET['id'];

    $stmt = $con->prepare("SELECT CollectionID FROM collections WHERE CollectionID = ? AND UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($cid, $_SESSION['uid']));

    $isOwnerOfCollection = FALSE;
    $hasSubmitted = FALSE;

    if (isset($_POST['create-new'])) {
        $hasSubmitted = TRUE;
        Header("Location:add_item.php?id=".$_POST['coll_id']);
    }
    
    $totalValue = 0;

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $isOwnerOfCollection = TRUE;
    }

    $stmt->closeCursor();

    if (!$isOwnerOfCollection && !$hasSubmitted) {
        Header("Location:account.php");
    } else {
        //Retrieve old data

        $stmt = $con->prepare("SELECT CollectionName, Visibility FROM collections WHERE CollectionID = ?");
        $stmt->execute(array($cid));

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $oldName = $res['CollectionName'];
        $oldVisibility = $res['Visibility'];

        $stmt->closeCursor();

        $stmt = $con->prepare("SELECT COUNT(*) as c FROM collection_items WHERE CollectionID = ? AND Active = 'Yes'");
        $stmt->execute(array($cid));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $count = $res["c"];

        $stmt->closeCursor();

        # Insert collections

        $create = '<div class="column" style="margin-right:-308px">
                        <div class="box" style="width:50%">
                            <div class="container">
                                <p><button type="submit" class="button full-width new" name="create-new">+</button></p>
                            </div>
                        </div>
                    </div>';

        $cards = '';

        if ($count == 0) {
            $cards = '<div class="column" style="margin-right:-308px">
                        <div class="box" style="width:50%">
                            <div class="container">
                                <p><button type="submit" class="button full-width new" name="create-new">+</button></p>
                            </div>
                        </div>
                    </div>';
        } else {
            $stmt = $con->prepare("SELECT * FROM collection_items WHERE CollectionID = ? AND Active = ?"); //update later
            $stmt->execute(array($cid, 'Yes'));

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
                                                <p><a href="edit_item.php?cid='.$collID.'&id='.$itemID.'" class="edit full-width">Edit</a></p>
                                                <p><a href="choose_image.php?id='.$collID.'&item='.$itemID.'" class="button half-width">Choose Image</a><a href="confirm_delete_image.php?cid='.$collID.'&item='.$itemID.'" class="delete half-width">Remove Image</a></p>
                                                <p><a name="delete" href="confirm_delete_item.php?cid='.$collID.'&item='.$itemID.'" class="delete full-width">Delete</a></p>
                                            </div>
                                        </div>
                                    </div>';

                $totalValue = $totalValue + $itemValue;
            }

            $stmt->closeCursor();
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
        <h1>View your collection</h1>
        <hr/>
        <form action="manage_collection.php" method="post">
            <input type="hidden" name="coll_id" value="<?php print $cid?>">

            <h3>Name: <?php print $oldName?></h3>
            <h3>Visibility: <?php print $oldVisibility?></h3>
            <h3>Total Value: $<?php print $totalValue?></h3>

            <br/><br/>

            <div class="row">
                <h1>Add new item</h1>
                <?php print $create?>
            </div>

            <hr/>
                
            <div class="row">
                <h1>Items</h1>
                <?php print $cards?>
            </div>
        </form>
    </body>
</html>
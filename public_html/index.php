<?php

    //from course project CSCI-N 342

    session_start();

    include "header.php";

    # Insert collections

    $cards = '';
    $count = 0;

    $stmt = $con->prepare("SELECT * FROM collections WHERE Active = ? AND Visibility = ?"); //update later
    $stmt->execute(array('Yes', 'Public'));

    while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($count == 5) {
            break;
        }
        $collID = $res['CollectionID'];
        $title = $res['CollectionName'];
        $userID = $res['UserID'];

        $cards = $cards.'<div class="column">
                            <div class="box">
                                <div class="container">
                                    <br/>
                                    <h2 style="text-align:center">'.$title.'</h2>
                                    <p class="description">Posted by: '.userIDToName($userID).'</p>
                                    <p><a href="view_collection.php?id='.$collID.'" class="button full-width">View</a></p>
                                </div>
                            </div>
                        </div>';
        
        $count = $count + 1;
    }

    $stmt->closeCursor();

?>



<!doctype html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Collection Capstone Project</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>

        <div id="collections">
            <form action="collections.php" method="post">
                <h2>Check these 5 collections out</h2>
                <hr/>

                <div class="row">
                    <?php print $cards?>
                </div>
            </form>
        </div>
    </body>
</html>
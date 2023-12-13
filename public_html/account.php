<?php

    //from course project CSCI-N 342

    session_start();

    include "header.php";
    
    if (!isset($_SESSION['uid'])) {
        Header("Location:login.php");
    }

    $stmt = $con->prepare("SELECT Email, Username FROM users WHERE UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($_SESSION['uid']));
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $email = $res['Email'];
    $username = $res['Username'];

    $email = "<p style='color:red; margin-left:25px;'>".$email."</p>";
    $username = "<p style='color:red; margin-left:25px;'>".$username."</p>";

    $stmt->closeCursor();

    $stmt = $con->prepare("SELECT COUNT(*) as c FROM collections WHERE UserID = ? AND Active = 'Yes'");
    $stmt->execute(array($_SESSION['uid']));
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
        $stmt = $con->prepare("SELECT * FROM collections WHERE UserID = ? AND Active = ?"); //update later
        $stmt->execute(array($_SESSION['uid'], 'Yes'));

        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $collID = $res['CollectionID'];
            $title = $res['CollectionName'];
            $visibility = $res['Visibility'];

            $cards = $cards.'<div class="column">
                                <div class="box">
                                    <div class="container">
                                        <br/>
                                        <h2 style="text-align:center">'.$title.'</h2>
                                        <p class="description">Visibility: '.$visibility.'</p>
                                        <p><a href="manage_collection.php?id='.$collID.'" class="button full-width">View</a></p>
                                        <p><a href="edit_collection.php?id='.$collID.'" class="edit full-width">Edit</a></p>
                                        <p><a name="delete" href="confirm_delete_coll.php?id='.$collID.'" class="delete full-width">Delete</a></p>
                                    </div>
                                </div>
                            </div>';
        }

        $stmt->closeCursor();
    }

    if (isset($_POST['create-new'])) {
        Header("Location:add_collection.php");
    }
?>



<!doctype html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Account</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <br/>
        <div id="profileInfo">
            <h2>View & edit profile information - <a href="edit_profile.php">Edit Profile</a> - <a href="change_password.php">Change Password</a></h2>
            <hr/>

            <p>Your current e-mail address: <?php print $email?></p>
            <p>Your current username: <?php print $username?></p>
            <hr/>
        </div>

        <br/>

        <div id="collections">
            <form action="account.php" method="post">
                <h2>View & edit collections - Total collections: <?php print $count?></h2>
                <hr/>

                <div class="row">
                    <h1>Create new collection</h1>
                    <?php print $create?>
                </div>

                <hr/>
                
                <div class="row">
                    <h1>Active collections</h1>
                    <?php print $cards?>
                </div>
            </form>
        </div>

    </body>
</html>
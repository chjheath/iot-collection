<?php

    //from course project CSCI-N 342

    session_start();

    include "header.php";

    $username = '';
    $access = '';
    $highTiles = '';
    
    if (isset($_SESSION['sid'])) {
        $highTiles = '<div class="column">
                        <div class="box">
                            <div class="container">
                                <br/>
                                <h2 style="text-align:center">Admins</h2>
                                <p class="description">Access: High</p>
                                <p><a href="add_admin.php" class="add full-width">Add</a></p>
                                <p><a href="choose.php?method=view_admin" class="button full-width">View</a></p>
                                <p><a href="choose.php?method=edit_admin" class="edit full-width">Edit</a></p>
                                <p><a href="choose.php?method=change_pwd_admin" class="change full-width">Change Password</a></p>
                                <p><a href="choose.php?method=remove_admin" class="delete full-width">Remove</a></p>
                            </div>
                        </div>
                    </div>';
    }

    if (!isset($_SESSION['sid']) && !isset($_SESSION['aid'])) {
        Header("Location:index.php");
    }

    $stmt = $con->prepare("SELECT Username, AccessLevel FROM admins WHERE AdminID = ? AND Active = 'Yes'");
    if (isset($_SESSION['aid'])) {
        $stmt->execute(array($_SESSION['aid']));
    } else if (isset($_SESSION['sid'])) {
        $stmt->execute(array($_SESSION['sid']));
    }

    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $username = $res['Username'];
    $access = $res['AccessLevel'];

    $username = "<p style='color:red; margin-left:25px;'>".$username."</p>";
    $access = "<p style='color:red; margin-left:25px;'>".$access."</p>";
?>



<!doctype html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Main Page</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <br/>
        <div id="profileInfo">
            <h2>View & edit profile information - <a href="admin_edit_profile.php">Edit Profile</a> - <a href="admin_change_pwd.php">Change Password</a></h2>
            <hr/>

            <p>Your current username: <?php print $username?></p>
            <p>Your current access level: <?php print $access?></p>
            <hr/>
        </div>

        <br/>

        <div id="collections">
            <form action="admin_page.php" method="post">
                <div class="row">
                    <div class="column">
                        <div class="box">
                            <div class="container">
                                <br/>
                                <h2 style="text-align:center">Users</h2>
                                <p class="description">Access: Low, High</p>
                                <p><a href="choose.php?method=view_user" class="button full-width">View</a></p>
                                <p><a href="choose.php?method=edit_user" class="edit full-width">Edit</a></p>
                                <p><a href="choose.php?method=change_pwd_user" class="change full-width">Change Password</a></p>
                                <p><a href="choose.php?method=remove_user" class="delete full-width">Remove</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="column">
                        <div class="box">
                            <div class="container">
                                <br/>
                                <h2 style="text-align:center">Collections</h2>
                                <p class="description">Access: Low, High</p>
                                <p><a href="choose.php?method=view_coll" class="button full-width">View</a></p>
                                <p><a href="choose.php?method=remove_coll" class="delete full-width">Remove</a></p>
                            </div>
                        </div>
                    </div>

                    <?php print $highTiles?>
                </div>
            </form>
        </div>

    </body>
</html>
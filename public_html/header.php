<?php

    //from course project CSCI-N 342

    require_once 'util/db_connect.php';
    require_once 'util/util.php';

    $loginBar = '<li style="float:right">
                    <a href="register.php">Register</a>
                 </li>
                 <li style="float:right">
                    <a href="login.php">Login</a>
                 </li>';
    $navBar = '<li>
                    <a href="index.php">Home</a>
               </li>';

    if (isset($_SESSION['uid'])) {
        $navBar = $navBar.'<li>
                                <a href="collections.php">Find Collections</a>
                         </li>';
        $loginBar = '<li style="float:right">
                        <a href="logout.php">Logout</a>
                     </li>
                     <li style="float:right">
                         <a href="account.php">Account</a>
                     </li>
                    ';
    } else if (isset($_SESSION['aid']) || isset($_SESSION['sid'])) {
        $navBar = '<li>
                    <a href="index.php">Home</a>
                </li>';
        $loginBar = '<li style="float:right">
                        <a href="logout.php">Logout</a>
                     </li>
                    <li style="float:right">
                        <a href="admin_page.php">Admin Page</a>
                    </li>';
    }

?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <ul>
            <?php print $navBar?>
            <?php print $loginBar?>
            
        </ul>

        <br/>
    </body>
</html>
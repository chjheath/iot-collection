<?php

    //pieces & ideas from course project CSCI-N 342

    session_start();

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:account.php"); //redirect to account page if already logged in.
    }

    include "header.php";

    require_once 'util/util.php';

    $method = '';
    $redirectuserTo = '';

    if (isset($_GET['method'])) {
        $method = trim($_GET['method']);
        $method = cleanQuery($method);
    } else {
        if (!isset($_POST['submit_form'])) {
            Header("Location:admin_page.php");
        }
    }

    $choices = '';

    if ($method == 'view_user') {
        $method_type = 'users to view';

        $stmt = $con->prepare("SELECT UserID, Username FROM users WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['UserID'].'">ID: '.$res['UserID'].', Username: '.$res['Username'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'view_user.php';
    } else if ($method == 'edit_user') {
        $method_type = 'users to edit';

        $stmt = $con->prepare("SELECT UserID, Username FROM users WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['UserID'].'">ID: '.$res['UserID'].', Username: '.$res['Username'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'edit_user.php';
    } else if ($method == 'change_pwd_user') {
        $method_type = 'users to change password';

        $stmt = $con->prepare("SELECT UserID, Username FROM users WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['UserID'].'">ID: '.$res['UserID'].', Username: '.$res['Username'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'admin_change_user_pwd.php';
    }else if ($method == 'remove_user') {
        $method_type = 'users to remove';

        $stmt = $con->prepare("SELECT UserID, Username FROM users WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['UserID'].'">ID: '.$res['UserID'].', Username: '.$res['Username'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'confirm_delete_user.php';
    } else if ($method == 'view_coll') {
        $method_type = 'collections to view';

        $stmt = $con->prepare("SELECT CollectionID, CollectionName, UserID, Visibility FROM collections WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['CollectionID'].'">ID: '.$res['CollectionID'].', Name: '.$res['CollectionName'].', Posted By: '.userIDToName($res['UserID']).', '.$res['Visibility'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'view_collection.php';
    } else if ($method == 'remove_coll') {
        $method_type = 'collections to remove';

        $stmt = $con->prepare("SELECT CollectionID, CollectionName, UserID, Visibility FROM collections WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['CollectionID'].'">ID: '.$res['CollectionID'].', Name: '.$res['CollectionName'].', Posted By: '.userIDToName($res['UserID']).', '.$res['Visibility'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'confirm_delete_coll.php';
    } else if ($method == 'view_admin') {
        if (!isset($_SESSION['sid'])) {
            Header("Location:admin_page.php");
        }

        $method_type = 'admins to view';

        $stmt = $con->prepare("SELECT AdminID, Username, AccessLevel FROM admins WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['AdminID'].'">ID: '.$res['AdminID'].', Username: '.$res['Username'].', Access Level: '.$res['AccessLevel'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'view_admin.php';
    } else if ($method == 'edit_admin') {
        if (!isset($_SESSION['sid'])) {
            Header("Location:admin_page.php");
        }

        $method_type = 'admins to edit';

        $stmt = $con->prepare("SELECT AdminID, Username, AccessLevel FROM admins WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['AdminID'].'">ID: '.$res['AdminID'].', Username: '.$res['Username'].', Access Level: '.$res['AccessLevel'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'edit_admin.php';
    } else if ($method == 'remove_admin') {
        if (!isset($_SESSION['sid'])) {
            Header("Location:admin_page.php");
        }

        $method_type = 'admins to remove';

        $stmt = $con->prepare("SELECT AdminID, Username, AccessLevel FROM admins WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['AdminID'].'">ID: '.$res['AdminID'].', Username: '.$res['Username'].', Access Level: '.$res['AccessLevel'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'confirm_delete_admin.php';
    } else if ($method == 'change_pwd_admin') {
        if (!isset($_SESSION['sid'])) {
            Header("Location:admin_page.php");
        }

        $method_type = 'admins to change password';

        $stmt = $con->prepare("SELECT AdminID, Username, AccessLevel FROM admins WHERE Active = 'Yes'");
        $stmt->execute(array());
        
        while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $choices = $choices.'<option value="' .$res['AdminID'].'">ID: '.$res['AdminID'].', Username: '.$res['Username'].', Access Level: '.$res['AccessLevel'].'</option>';
        }

        $stmt->closeCursor();

        $redirectuserTo = 'edit_admin_change_pwd.php';
    } else {
        //Header("Location:admin_page.php");
    }

    if (isset($_POST['submit_form'])) {
        //echo "Location:".$link.'?id='.$_POST['choices'].'&access=admin';
        if ($method == 'view_coll' || $method == 'remove_coll' || $method == 'change_pwd_user') {
            Header("Location:".$_POST['redir']."?id=".$_POST['choices']."&access=admin");
        } else {
            Header("Location:".$_POST['redir']."?id=".$_POST['choices']);
        }
    }

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Choose from the following</title>

        <!-- Load Stylesheets -->

        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>

    <body>
        <form action="choose.php" method="post">

            <input type="hidden" name='redir' value="<?php print $redirectuserTo?>">

            <label for="choose">Choose one of the following <?php print $method_type?></label>

            <br/><br/>

            <select name="choices" style="text-align:center">
                <?php print $choices?>
            </select>

            <br/><br/>

            <button type="submit" class="button" name="submit_form">Continue</button>
        </form>
    </body>
</html>
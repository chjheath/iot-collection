<?php

    session_start();

    session_destroy(); //end session

    Header("Location:index.php");

?>


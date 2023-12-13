<?php

    $host = 'REDACTED';
    $user = 'REDACTED';
    $pass = 'REDACTED';

    try {
        $con = new PDO ("mysql:host=$host;dbname=REDACTED", $user, $pass);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

?>
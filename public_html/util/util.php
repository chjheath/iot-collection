<?php

    //Validate e-mail, username, password, code (slightly modified from CSCI-N 342 course project code)
    function validateQuery($query, $method) {
        global $con;

        $stmt = '';
        $query = trim($query);
        $len = strlen($query);

        if ($method == 'email') { //check if email exists
            $stmt = $con->prepare("SELECT COUNT(*) as foundMatch FROM users WHERE Email = ?");
            $stmt->execute(array($query));
        } else if ($method == 'user') { //check if user exists
            $stmt = $con->prepare("SELECT COUNT(*) as foundMatch FROM users WHERE Username = ?");
            $stmt->execute(array($query));
        } else if ($method == 'admin') { //check if user exists
            $stmt = $con->prepare("SELECT COUNT(*) as foundMatch FROM admins WHERE Username = ?");
            $stmt->execute(array($query));
        } else if ($method == 'both') {
            if (isEmail($query)) {
                if (validateQuery($query, 'email')) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if (validateQuery($query, 'user')) {
                    return true;
                } else {
                    return false;
                }
            }
        } else if ($method == 'pass') { //check if pass meets req.
            if(($len >= 10) && (containsNum($query) && containsLetters($query))) {
                return true; //password meets requirements (at least 10 chars. + contains letter & number)
            } else {
                return false; //password does not meet requirements
            }
        } else if ($method == 'code') { //check if generated code meets requirements
            if ($len == 50 && (containsLetters($query) && containsNum($query))) {
                return true; //given code matches formast
            } else {
                return false; //given code does not match format
            }
        } else { //invalid method
            return false;
        }

        if ($method != 'code' && $method != 'pass') {
            $matches = $stmt->fetch(PDO::FETCH_OBJ);
            $count = $matches->foundMatch;

            if ($count == 1) { //found
                $stmt->closeCursor();
                return false;
            } else {
                $stmt->closeCursor();
                return true;
            }
        }
    }

    function initiate_pi($user, $collection, $item) {
        //Send info to Rasp. Pi

        Header("Location:finalize_image.php?id=".$collection."&item=".$item);
    }

    function userIDToName($user) {
        global $con;

        $stmt = $con->prepare("SELECT Username FROM users WHERE UserID = ?");
        $stmt->execute(array($user));

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $user = $res['Username'];

        $stmt->closeCursor();

        return $user;
    }

    //Validate login (slightly modified from CSCI-N 342 course project code)
    function validateLogin($login_name, $pass) {
        global $con;

        $stmt = '';
        $login_name = trim($login_name);
        $len = strlen($login_name);

        $stmt = $con->prepare("SELECT Email, Password FROM users WHERE Email = ? AND Active = 'Yes'");
        $stmt->execute(array($login_name));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Pass Hash & Verif. from https://www.geeksforgeeks.org/how-to-secure-hash-and-salt-for-php-passwords/

        if ($result && password_verify($pass, $result['Password']) == 1) { // https://stackoverflow.com/a/60821796
            $stmt->closeCursor();
            return true;
        } else {
            $stmt->closeCursor();
            return false;
        }
    }

    //Validate admin login (slightly modified from CSCI-N 342 course project code)
    function validateAdminLogin($login_name, $pass) {
        global $con;

        $stmt = '';
        $login_name = trim($login_name);
        $len = strlen($login_name);

        $stmt = $con->prepare("SELECT Username, Password FROM admins WHERE Username = ? AND Active = 'Yes'");
        $stmt->execute(array($login_name));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Pass Hash & Verif. from https://www.geeksforgeeks.org/how-to-secure-hash-and-salt-for-php-passwords/

        if ($result && password_verify($pass, $result['Password']) == 1) { // https://stackoverflow.com/a/60821796
            $stmt->closeCursor();
            return true;
        } else {
            $stmt->closeCursor();
            return false;
        }
    }

    //(taken directly from ch13_functions/inc/util.php)

    function cleanQuery($query) {
        $search = array(
    		'@<script[^>]*?>.*?</script>@si',   // Strip out anything between the javascript tags
    		'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    		'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
        );

        $query = preg_replace($search, '', $query);

        $query = htmlspecialchars($query, ENT_QUOTES);

        return $query;
    }

    //Generate random code (slightly modified from ch13_functions/inc/util.php)

    function generateCode() {
        $code = '';

        for ($x = 0; $x < 50; $x++) {
            $rand = mt_rand(1,35);

            if ($rand > 26) {
                $rand -= 26;

                $code = $code.$rand;
            } else {
                $code = $code.toChar($rand);
            }
        }

        return $code;
    }

    //Contains # (slightly modified from ch13_functions/inc/util.php)

    function containsNum($query) {
        $foundNum = FALSE;

        $len = strlen($query);
        $query = trim($query);
        $queryChars = str_split($query);

        for ($x = 0; $x < $len; $x++) {
            if (preg_match("/[0-9]/", $queryChars[$x])) {
                $foundNum = TRUE;
                break;
            }
        }

        if ($foundNum) {
            return true;
        } else {
            return false;
        }
    }

    //Contains letter (slightly modified from ch13_functions/inc/util.php)

    function containsLetters($query) {
        $foundLet = FALSE;

        $len = strlen($query);
        $query = trim($query);
        $queryChars = str_split($query);

        for ($x = 0; $x < $len; $x++) {
            if (preg_match("/[A-Za-z]/", $queryChars[$x])) {
                $foundLet = TRUE;
                break;
            }
        }

        if ($foundLet) {
            return true;
        } else {
            return false;
        }
    }

    //Contains @ (slightly modified from similar functions in code)

    function isEmail($query) {
        $foundSymbol = FALSE;

        $len = strlen($query);
        $query = trim($query);
        $queryChars = str_split($query);

        for ($x = 0; $x < $len; $x++) {
            if ($queryChars[$x] == '@') {
                $foundSymbol = TRUE;
                break;
            }
        }

        if ($foundSymbol) {
            return true;
        } else {
            return false;
        }
    }

    function toChar($digit) { //(taken directly from ch13_functions/inc/util.php)
        $char = "";
        switch ($digit){
               case 1: $char = "A"; break;
               case 2: $char = "B"; break;
               case 3: $char = "C"; break;
               case 4: $char = "D"; break;
               case 5: $char = "E"; break;
               case 6: $char = "F"; break;
               case 7: $char = "G"; break;
               case 8: $char = "H"; break;
               case 9: $char = "I"; break;
               case 10: $char = "J"; break;
               case 11: $char = "K"; break;
               case 12: $char = "L"; break;
               case 13: $char = "M"; break;
               case 14: $char = "N"; break;
               case 15: $char = "O"; break;
               case 16: $char = "P"; break;
               case 17: $char = "Q"; break;
               case 18: $char = "R"; break;
               case 19: $char = "S"; break;
               case 20: $char = "T"; break;
               case 21: $char = "U"; break;
               case 22: $char = "V"; break;
               case 23: $char = "W"; break;
               case 24: $char = "X"; break;
               case 25: $char = "Y"; break;
               case 26: $char = "Z"; break;
               default: "A";
        }
        return $char;
    }
?>
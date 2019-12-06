<?php
function errorAlert($errno, $errstr, $errfile, $errline, $errcontext) {
    echo "
    Error Information: <br><hr>
    Page: <b>" . $_SERVER['REQUEST_URI'] . "</b><br>
    Error: <b> $errstr </b><br>
    Broken File: <b> $errfile </b><br>
    Line: <b> $errline </b><br>
    ";
    die();
}
set_error_handler("errorAlert");

// Log Function
function logAction($action, $user) {
    global $pdo;
    global $time;
    global $us_date;

    $sql_log = "INSERT INTO logs (action, username, timestamp) VALUES (:action, :username, :timestamp)";
    $stmt_log = $pdo->prepare($sql_log);
    $stmt_log->bindValue(':action', $action);
    $stmt_log->bindValue(':username', $user);
    $stmt_log->bindValue(':timestamp', $us_date . ' ' . $time);
    $result_log = $stmt_log->execute();
}

function truncate_string($string, $maxlength, $extension) {

    // Set the replacement for the "string break" in the wordwrap function
    $cutmarker = "**cut_here**";

    // Checking if the given string is longer than $maxlength
    if (strlen($string) > $maxlength) {

        // Using wordwrap() to set the cutmarker
        // NOTE: wordwrap (PHP 4 >= 4.0.2, PHP 5)
        $string = wordwrap($string, $maxlength, $cutmarker);

        // Exploding the string at the cutmarker, set by wordwrap()
        $string = explode($cutmarker, $string);

        // Adding $extension to the first value of the array $string, returned by explode()
        $string = $string[0] . $extension;
    }

    // returning $string
    return $string;
}

function time_php2sql($unixtime){
    return gmdate("Y-m-d H:i:s", $unixtime);
}

function demoAlert () {
    global $demo;
    if ($demo === true) {
        echo '<div id="demoAlert"><div class="alert alert-info" role="alert">You are viewing this software in a limited demo version. Some features may be locked or removed in this demo version. To buy the full version, and host the software yourself, please vist <a href="https://discord.io/HydridSystems">https://discord.io/HydridSystems</a>. Typos and other issues will be fixed before release.</div></div>';
    }
}

function demoBlock () {
    echo '<div id="demoAlert"><div class="alert alert-danger" role="alert">Sorry, this feature has been blocked in demo mode for safety reasons. Please purchase the full version for access.</div></div>';
}

function getUserIP() {
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function notify($errortype, $errormsg, $location) {
    $_SESSION["errortype"] = $errortype;
    $_SESSION["errormsg"] = $errormsg;
    header('Location: '.$location.'');
    exit();
}
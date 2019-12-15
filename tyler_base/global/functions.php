<?php

function discordAlert($message) {
    global $wh;
    //=======================================================================
    // Create new webhook in your Discord channel settings and copy&paste URL
    //=======================================================================
    $webhookurl = $wh;
    //=======================================================================
    // Compose message. You can use Markdown
    //=======================================================================
    $json_data = array(
        'content' => "$message",
        'username' => "ezApps"
    );
    $make_json = json_encode($json_data);
    $ch = curl_init($webhookurl);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $make_json);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    return $response;
}

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
        echo '<div id="demoAlert"><div class="alert alert-info" role="alert">'.locale('demoalert').'</div></div>';
    }
}

function demoBlock () {
    echo '<div id="demoAlert"><div class="alert alert-danger" role="alert">'.locale('demoblock').'</div></div>';
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

function apps() {
    global $version;
    if(!isset($_COOKIE['apps'])) {
        $json = file_get_contents("https://raw.githubusercontent.com/ezAppsDev/Application-System/master/version.json");
        $curVer = json_decode($json);
        $newVer = $curVer->version;
        if ($newVer > $version) {
            phpAlert(locale('outdated'));
        } else {
            setcookie("apps", 'set', time()+60);
        }
    }
}

function locale($term) {
    if (LOCALE) {
        $localeSetting = 'en';
        switch (LOCALE) {
            case 'es':
                $localeSetting = 'es';
                break;
            case 'fr':
                $localeSetting = 'fr';
                break;
        }
        include 'tyler_base/locales/'.$localeSetting.'.php';
        $result = $localeDictionary[$term];
        if ($result) {
            return $result;
        } else {
            return '[Locale Error]';
        }
    } else {
        return '[Locale Error]';
    }


}

// Log Function
function logger($action) {
    global $pdo;
    global $time;
    global $datetime;
    global $user_ip;

    $a = nl2br($action);

    $log    = "INSERT INTO logs (user, datetime, ip, action) VALUES (?,?,?,?)";
    $log    = $pdo->prepare($log);
    $log    = $log->execute([$_SESSION['user_id'], $datetime, $user_ip, $a]);
}

// Automated app format checker
// function formatCheck ($id) {
//     global $pdo;
    
//     $sql = "SELECT * FROM applicants WHERE id = ?";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([$id]);
//     $app = $stmt->fetch(PDO::FETCH_ASSOC);
//     if(strpos($_SESSION['applying_for_format'], $app['format']) === false){
//         // notify('success', '1', DOMAIN.'/apply');
//         $sql = "UPDATE applicants SET status = ? WHERE id = ?";
//         $pdo->prepare($sql)->execute(['DENIED', $id]);
//     }
// } 
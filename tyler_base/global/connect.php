<?php
// MySQL Settings
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "app_system");
require_once 'functions.php';

// Do Not Edit Below --- SERIOUSLY DON'T TOUCH THIS STUFF.
$pdoOptions = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    PDO::ATTR_EMULATE_PREPARES => false
);
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, $pdoOptions);
}

catch(Exception $e) {
    die('<strong>Fatal Error while connecting to the database. Please refresh and try again, or check back later!</strong>');
}

if(!ini_get('allow_url_fopen')) {
    die('allow_url_fopen is disabled. Please enable it in your host\'s php settings.');
}
?>

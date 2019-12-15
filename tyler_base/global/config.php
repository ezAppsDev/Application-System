<?php
$version = '1.1.1';
$assets_ver = '01';
require_once 'functions.php';

//EDIT THIS TO YOUR DOMAIN. DO NOT INCLUDE A TRAILING SLASH
$domain = 'http://localhost';
define("DOMAIN", $domain);

// REGULAR USERS SHOULD NOT TOUCH THIS. DEVELOPER ONLY.
$demo = false;

//Global Config
$sql_gconfig = "SELECT * FROM settings WHERE id = ?";
$stmt_gconfig = $pdo->prepare($sql_gconfig);
$stmt_gconfig->execute(['1']);
$configRow = $stmt_gconfig->fetch(PDO::FETCH_ASSOC);

$config['name'] = $configRow['name'];
$config['theme'] = $configRow['theme'];
$wh = $configRow['discord_webhook'];
$config['app_accept_message'] = $configRow['app_accept_message'];
$webhook['app_created'] = $configRow['wh_app_created'];
$webhook['app_accepted'] = $configRow['wh_app_accepted'];
$webhook['app_declined'] = $configRow['wh_app_declined'];

apps();
$user_ip = getUserIP();
$date = date('Y-m-d');
$year = date('Y');
$us_date = date_format(date_create_from_format('Y-m-d', $date) , 'm/d/Y');
$time = date('h:i:s', time());
$datetime = $us_date . ' ' . $time;
$doul = $_SERVER['HTTP_HOST'];
$message = '';
$sqltimestamp = date('Y-m-d H:i:s', time());

require 'user/userInfo.php';
require "notify.php";
?>
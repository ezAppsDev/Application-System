<?php
	require 'tyler_base/global/connect.php';
	require 'tyler_base/global/config.php';
	session_name('ezApps');
	session_start();
	session_unset();
	session_destroy();
    header('Location: '.DOMAIN.'/login');
?>
<?php
session_name('ezApps');
session_start();
require '../tyler_base/global/connect.php';
require '../tyler_base/global/config.php';
$page['name'] = locale('linkdiscaccount');

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$client_id = "CLIENT_ID";
$client_secret = "CLIENT_SECRET";

if (isset($_GET["error"])) {
    notify('danger', locale('discordapierror'), DOMAIN.'/index');
} elseif (isset($_GET["code"])) {
	$redirect_uri = DOMAIN.'/3rdparty/discord.php';
	$token_request = "https://discordapp.com/api/oauth2/token";
	$token = curl_init();
	curl_setopt_array($token, array(
		CURLOPT_URL => $token_request,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => array(
			"grant_type" => "authorization_code",
			"client_id" => $client_id,
			"client_secret" => $client_secret,
			"redirect_uri" => $redirect_uri,
			"code" => $_GET["code"]
		)
	));
	curl_setopt($token, CURLOPT_RETURNTRANSFER, true);
	$resp = json_decode(curl_exec($token));
	curl_close($token);
	if (isset($resp->access_token)) {
		$access_token = $resp->access_token;
		$info_request = "https://discordapp.com/api/users/@me";
		$info = curl_init();
		curl_setopt_array($info, array(
			CURLOPT_URL => $info_request,
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$access_token}"
			) ,
			CURLOPT_RETURNTRANSFER => true
		));
		$user = json_decode(curl_exec($info));
        curl_close($info);

        $_SESSION['discord_id']   = $user->id;
        $_SESSION['access_token']   = $access_token;
		$_SESSION['user_avatar'] = $user->avatar;
		
		$sql = "UPDATE users SET discord_id = ?, avatar = ? WHERE id = ?";
		$result = $pdo->prepare($sql)->execute([$_SESSION['discord_id'], 'https://cdn.discordapp.com/avatars/'.$_SESSION['discord_id'].'/'.$_SESSION['user_avatar'].'.png', $_SESSION['user_id']]); 	

		if ($result) {
			notify('success', locale('discordlinkagesuccess'), DOMAIN.'/index');
		} else {
			notify('danger', locale('discordlinkagerror'), DOMAIN.'/index');
		}		
	} else {
		header("Location: https://discordapp.com/oauth2/authorize?client_id=$client_id&response_type=code&scope=identify");
        exit();
	}
} else {
    header("Location: https://discordapp.com/oauth2/authorize?client_id=$client_id&response_type=code&scope=identify");
    exit();
}
?>
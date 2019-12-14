<?php
if(!isset($_SESSION['user_id'])) {
    //If they are NOT logged 
    $lp['loggedIn'] = false;
    define("loggedIn", $lp['loggedIn']);
    if (isset($page['name']) && $page['name'] <> 'Login') {
        header('Location: '.DOMAIN.'/login');
    }
}  else {
    //If they ARE logged in
    $lp['loggedIn'] = true;
	define("loggedIn", $lp['loggedIn']);
    // Get User Data
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userRow === false) {
        header('Location: '.DOMAIN.'/login');
        exit();
    }

    // Define variables
    $user['id'] = $userRow['id'];
    $user['display_name'] = $userRow['display_name'];
    $user['joined'] = $userRow['joined'];
    $user['usergroup'] = $userRow['usergroup'];
    $user['discord_id'] = $userRow['discord_id'];

    require_once 'userGroups.php';
}
?>

<?php
$group['access'] = false;

// Pull the usergroup from the database
$sql1_gp             = "SELECT * FROM usergroups WHERE id = ?";
$stmt1_gp            = $pdo->prepare($sql1_gp);
$stmt1_gp->execute([$user['usergroup']]);
$groupRow = $stmt1_gp->fetch(PDO::FETCH_ASSOC);

if ($stmt1_gp->rowCount() < 0) {
    // Checks if the users usergroup is valid, if it is not, they are assigned to the default group
    $sql_iug = "UPDATE users SET usergroup=? WHERE user_id=?";
    $stmt_iug = $pdo->prepare($sql_iug);
    $stmt_iug->execute(['1', $_SESSION['user_id']]);
}

// Define variables
$group['id'] = $groupRow['id'];
$group['name'] = $groupRow['name'];
$group['access'] = $groupRow['access'];
$group['super_admin'] = $groupRow['super_admin'];
$group['app_management'] = $groupRow['app_management'];

define("access", $group['access']);
define("super_admin", $group['super_admin']);
define("app_management", $group['app_management']);

if (access === 'false') {
    session_unset();
    session_destroy();
    notify('danger', 'You do not have access to this system.', DOMAIN.'/login?banned');
}
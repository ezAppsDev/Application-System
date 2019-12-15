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
$group['view_apps'] = $groupRow['view_apps'];
$group['review_apps'] = $groupRow['review_apps'];
$group['view_users'] = $groupRow['view_users'];
$group['view_usergroups'] = $groupRow['view_usergroups'];
$group['edit_users'] = $groupRow['edit_users'];
$group['edit_usergroups'] = $groupRow['edit_usergroups'];

define("access", $group['access']);
define("super_admin", $group['super_admin']);
define("view_apps", $group['view_apps']);
define("review_apps", $group['review_apps']);
define("view_users", $group['view_users']);
define("view_usergroups", $group['view_usergroups']);
define("edit_users", $group['edit_users']);
define("edit_usergroups", $group['edit_usergroups']);

if (access === 'false') {
    session_unset();
    session_destroy();
    notify('danger', locale('bannedsystem'), DOMAIN.'/login?banned');
}
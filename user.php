<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = 'User Profile';

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

if (isset($_GET['id'])) {
    $id = strip_tags($_GET['id']);

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        notify('danger', 'That user does not exist.', DOMAIN.'/index');
    } else {
        $_SESSION['profile_user_id'] = $user['id'];
        $_SESSION['profile_display_name'] = $user['display_name'];
        $_SESSION['profile_joined'] = $user['joined'];
        $_SESSION['profile_usergroup'] = $user['usergroup'];
        $_SESSION['profile_discord_id'] = $user['discord_id'];
        $_SESSION['profile_avatar'] = $user['avatar'];

        $_SESSION['profile_owner'] = 'false';

        if ($_SESSION['user_id'] === $user['id']) {
            $_SESSION['profile_owner'] = 'true';
        }

        $getUGDB = "SELECT name FROM `usergroups` WHERE id = ?"; 
        $getUGDB = $pdo->prepare($getUGDB); 
        $getUGDB->execute([$_SESSION['profile_usergroup']]); 
        $usersgroupDB = $getUGDB->fetch();

        $_SESSION['profile_usergroup_name'] = $usersgroupDB['name'];

        $dbCount['total_profile_apps'] = $pdo->query('select count(*) from applicants WHERE user='.$user['id'])->fetchColumn();

    }
}

//Update user settings
if (isset($_POST['updateUserSettings'])) {
    //Sanitize
    $newPass     = strip_tags($_POST['newPass']);

    if (strlen($newPass) < 8) {
        notify('danger', 'Your password must be longer than 8 characters.', DOMAIN.'/user?id='.$_SESSION['profile_user_id']);
    } elseif (!preg_match("#[0-9]+#", $newPass)) {
        notify('danger', 'Your password must include at least one number.', DOMAIN.'/user?id='.$_SESSION['profile_user_id']);
    } elseif (!preg_match("#[a-zA-Z]+#", $newPass)) {
        notify('danger', 'Your password must include at least one letter.', DOMAIN.'/user?id='.$_SESSION['profile_user_id']);
    } else {
        $passwordHash = password_hash($newPass, PASSWORD_BCRYPT, array("cost" => 12));
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$passwordHash, $_SESSION['user_id']]);
        notify('success', 'Settings updated.', DOMAIN.'/user?id='.$_SESSION['profile_user_id']);    
    }
}

//Admin Update user settings
if (isset($_POST['updateAdminUserSettings'])) {
    //Sanitize
    $usergroup     = strip_tags($_POST['usergroup']);

    $sql = "UPDATE users SET usergroup = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$usergroup, $_SESSION['profile_user_id']]);
    notify('success', 'User updated.', DOMAIN.'/user?id='.$_SESSION['profile_user_id']); 
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require 'tyler_base/page/header.php'; ?>
</head>

<body>
    <?php require 'tyler_base/page/nav.php'; ?>
    <?php require 'tyler_base/page/s-nav.php'; ?>
    <div class="lime-container">
        <div class="lime-body">
            <div class="container-fluid">
                <div id="ezaMsg"><?php if (isset($message)) { echo $message; } ?></div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="profile-cover"></div>
                        <div class="profile-header">
                            <div class="profile-img">
                                <?php if($_SESSION['profile_avatar'] === NULL): ?>
                                    <img src="<?php echo DOMAIN; ?>/assets/themes/<?php echo $config['theme']; ?>/images/avatars/placeholder.png">
                                <?php else: ?>
                                    <img src="<?php echo $_SESSION['profile_avatar']; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="profile-name">
                                <h3><?php echo $_SESSION['profile_display_name']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">About</h5>
                                <ul class="list-unstyled profile-about-list">
                                    <li><i class="material-icons">calendar_today</i><span>Joined:
                                            <?php echo $_SESSION['profile_joined']; ?></span></li>
                                    <li><i class="material-icons">group_add</i><span>Usergroup:
                                            <?php echo $_SESSION['profile_usergroup_name']; ?></span></li>
                                    <li><i class="material-icons">account_box</i><span>Discord ID:
                                            <?php echo $_SESSION['profile_discord_id']; ?></span></li>
                                    <?php if($_SESSION['profile_owner'] === 'true' || super_admin === 'true'): ?>
                                    <hr>
                                    <?php if(super_admin === 'true' && edit_users === 'false'): ?>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#adminUserSettings">Admin</button>
                                    <!-- Admin User Settings Modal -->
                                    <div class="modal fade" id="adminUserSettings" tabindex="-1" role="dialog"
                                        aria-labelledby="adminUserSettings" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="adminUserSettings">Editing User</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <i class="material-icons">close</i>
                                                    </button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                <label class="col-form-label"
                                                                        for="display_name">Display Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="display_name" value="<?php echo $_SESSION['profile_display_name']; ?>"
                                                                        disabled>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-form-label"
                                                                        for="usergroup">Usergroup</label>
                                                                    <div class="form-group">
                                                                        <select name="usergroup" id="usergroup"
                                                                            class="form-control custom-select" required>
                                                                            <option
                                                                                value="<?php echo $_SESSION['profile_usergroup']; ?>"
                                                                                selected>
                                                                                <?php echo $_SESSION['profile_usergroup_name']; ?>
                                                                            </option>
                                                                            <?php
                                                                            $getAllUsergroupsDB = "SELECT * FROM usergroups where id != ? ";
                                                                            $getAllUsergroupsDB = $pdo->prepare($getAllUsergroupsDB);
                                                                            $getAllUsergroupsDB->execute([$_SESSION['profile_usergroup']]);
                                                                            $AllUsergroupsDB = $getAllUsergroupsDB->fetchAll(PDO::FETCH_ASSOC);
                                                                            
                                                                            foreach ($AllUsergroupsDB as $usergroupDB) {
                                                                                echo '<option value="'.$usergroupDB['id'].'">'.$usergroupDB['name'].'</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="updateAdminUserSettings"
                                                            class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; 
                                if($_SESSION['profile_owner'] === 'true'): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal"
                                        data-target="#userSettings">Settings</button>
                                    <!-- User Settings Modal -->
                                    <div class="modal fade" id="userSettings" tabindex="-1" role="dialog"
                                        aria-labelledby="userSettings" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="userSettings">Settings</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <i class="material-icons">close</i>
                                                    </button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                <label class="col-form-label"
                                                                        for="display_name">Display Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="display_name" value="<?php echo $_SESSION['profile_display_name']; ?>"
                                                                        disabled>
                                                                </div>
                                                                <div class="form-group">
                                                                <label class="col-form-label"
                                                                        for="newPass">New Password</label>
                                                                    <input type="password" class="form-control"
                                                                        name="newPass" id="newPass"
                                                                        placeholder="New Password..." autocomplete="new-password" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="updateUserSettings"
                                                            class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;
                                endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <?php if($dbCount['total_profile_apps'] === 0): ?>
                            <div class="alert alert-warning m-b-lg" role="alert">
                                <?php echo $_SESSION['profile_display_name']; ?> hasn't applied for anything yet!
                            </div>
                        <?php else: ?>
                            <?php 
                        $getUserAppliedDB = "SELECT * FROM applicants WHERE user = ? ORDER BY created DESC";
                        $getUserAppliedDB = $pdo->prepare($getUserAppliedDB);
                        $getUserAppliedDB->execute([$_SESSION['profile_user_id']]);
                        $userAppliedDB = $getUserAppliedDB->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($userAppliedDB as $appliedDB) { 
                            $getAppInfoDB = "SELECT name FROM `applications` WHERE id = ?"; 
                            $getAppInfoDB = $pdo->prepare($getAppInfoDB); 
                            $getAppInfoDB->execute([$appliedDB['app']]); 
                            $appInfoDB = $getAppInfoDB->fetch();?>
                        <div class="card">
                            <div class="card-body">
                                <div class="post">
                                    <div class="post-header">
                                        <?php if($_SESSION['profile_avatar'] === NULL): ?>
                                            <img src="<?php echo DOMAIN; ?>/assets/themes/<?php echo $config['theme']; ?>/images/avatars/placeholder.png">
                                        <?php else: ?>
                                            <img src="<?php echo $_SESSION['profile_avatar']; ?>">
                                        <?php endif; ?>
                                        <div class="post-info">
                                            <span
                                                class="post-author"><?php echo $_SESSION['profile_display_name']; ?></span><br>
                                            <span class="post-date"><?php echo $appliedDB['created']; ?></span>
                                        </div>
                                    </div>
                                    <div class="post-body">
                                        <p>Created an application for "<?php echo $appInfoDB['name']; ?>"
                                            <br />Current Status: <?php echo $appliedDB['status']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php require 'tyler_base/page/copyright.php'; ?>
    </div>

    <?php require 'tyler_base/page/footer.php'; ?>
</body>

</html>
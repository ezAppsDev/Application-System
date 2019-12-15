<?php
session_name('ezApps');
session_start();
require ('../../../../tyler_base/global/connect.php');
require ('../../../../tyler_base/global/config.php');
$id = strip_tags($_GET['id']);
$sql = "SELECT * FROM usergroups WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$ugInfo = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['editing_group'] = $id;
$_SESSION['editing_group_name'] = $ugInfo['name'];
$_SESSION['editing_group_access'] = $ugInfo['access'];
$_SESSION['editing_group_super_admin'] = $ugInfo['super_admin'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title></title>
</head>

<body>
    <?php if(super_admin === 'false' && edit_usergroups === 'false'): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger m-b-lg" role="alert">
                <?php echo locale('noperms'); ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <form method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="app_name"><?php echo locale('usergroupname'); ?></label>
                    <input type="text" class="form-control" name="group_name" id="group_name" placeholder="<?php echo locale('usergroupname'); ?>"
                        value="<?php echo $ugInfo['name']; ?>" required>
                </div>
                <div class="divider"></div>
                <div class="form-group">
                    <input type="checkbox" name="perm_access" id="perm_access"
                        <?php if ($ugInfo['access'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_access"><?php echo locale('access'); ?> <i class="fas fa-user-lock" data-toggle="tooltip" data-placement="right" title="<?php echo locale('supadminonly'); ?>"></i></label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="perm_super_admin" id="perm_super_admin"
                        <?php if ($ugInfo['super_admin'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_super_admin"><?php echo locale('supadmin'); ?> <i class="fas fa-user-lock" data-toggle="tooltip" data-placement="right" title="<?php echo locale('supadminonly'); ?>"></i></label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="perm_view_apps" id="perm_view_apps"
                        <?php if ($ugInfo['view_apps'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_view_apps"><?php echo locale('viewapps'); ?></label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="perm_review_apps" id="perm_review_apps"
                        <?php if ($ugInfo['review_apps'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_review_apps"><?php echo locale('reviewapps'); ?></label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="perm_view_users" id="perm_view_users"
                        <?php if ($ugInfo['view_users'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_view_users"><?php echo locale('viewusers'); ?></label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="perm_view_usergroups" id="perm_view_usergroups"
                        <?php if ($ugInfo['view_usergroups'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_view_usergroups"><?php echo locale('viewusergroups'); ?></label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="perm_edit_users" id="perm_edit_users"
                        <?php if ($ugInfo['edit_users'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_edit_users"><?php echo locale('editusers'); ?></label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="perm_edit_usergroups" id="perm_edit_usergroups"
                        <?php if ($ugInfo['edit_usergroups'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_edit_usergroups"><?php echo locale('editusergroups'); ?> <i class="fas fa-user-lock" data-toggle="tooltip" data-placement="right" title="<?php echo locale('supadminonly'); ?>"></i></label>
                </div>
            </div>
        </div>
        <hr>
        <button type="submit" name="updateUsergroup" class="btn btn-primary btn-md float-right mb-3"><?php echo locale('update'); ?></button>
    </form>
    <?php endif; ?>
</body>

</html>
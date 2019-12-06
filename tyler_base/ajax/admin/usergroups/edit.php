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

$_SESSION['editing_group_app_management'] = $ugInfo['app_management'];

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title></title>
</head>

<body>
    <form method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="app_name">Group Name</label>
                    <input type="text" class="form-control" name="group_name" id="group_name" placeholder="Group Name" value="<?php echo $ugInfo['name']; ?>" required>
                </div>
                <hr>
                <div class="form-group">
                    <input type="checkbox" name="perm_access" id="perm_access" <?php if ($ugInfo['access'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_access">Access</label>

                    <input type="checkbox" name="perm_super_admin" id="perm_super_admin" <?php if ($ugInfo['super_admin'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_super_admin">Super Admin</label>

                    <input type="checkbox" name="perm_app_management" id="perm_app_management" <?php if ($ugInfo['app_management'] === 'true') {echo 'checked';} ?>>
                    <label class="label" for="perm_app_management">App Management</label>
                </div>
            </div>
        </div>
        <hr>
        <button type="submit" name="updateUsergroup" class="btn btn-primary btn-md float-right mb-3">Update</button>
    </form>
</body>

</html>
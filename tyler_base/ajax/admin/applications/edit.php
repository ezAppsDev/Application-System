<?php
session_name('ezApps');
session_start();
require ('../../../../tyler_base/global/connect.php');
require ('../../../../tyler_base/global/config.php');

$appID = strip_tags($_GET['appID']);

$sql = "SELECT * FROM applications WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$appID]);
$appInfo = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['editing_app'] = $appID;
$_SESSION['editing_app_name'] = $appInfo['name'];
$_SESSION['editing_app_status'] = $appInfo['status'];
$_SESSION['editing_app_format'] = $appInfo['format'];
$_SESSION['editing_app_desc'] = $appInfo['description'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title></title>
</head>

<body>
    <?php if(super_admin === 'false'): ?>
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
                    <label for="app_name"><?php echo locale('appformatname'); ?></label>
                    <input type="text" class="form-control" name="app_name" id="app_name" placeholder="<?php echo locale('appformatname'); ?>" value="<?php echo $appInfo['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="app_status"><?php echo locale('appstatus'); ?></label>
                    <select class="form-control" name="app_status" id="app_status" required>
                        <option value="<?php echo $appInfo['status']; ?>" selected><?php echo $appInfo['status']; ?> (<?php echo locale('current'); ?>)</option>
                        <option value="OPEN"><?php echo locale('open'); ?></option>
                        <option value="CLOSED"><?php echo locale('closed'); ?></option>
                        <option value="ON-HOLD"><?php echo locale('onhold'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="app_format"><?php echo locale('appdescription'); ?></label>
                    <textarea class="form-control" rows="4" name="app_desc" id="app_desc" placeholder="<?php echo locale('appdescription'); ?>" required><?php echo $appInfo['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="app_format"><?php echo locale('appformat'); ?></label>
                    <textarea class="form-control" rows="4" name="app_format" id="app_format" placeholder="<?php echo locale('appformat'); ?>" required><?php echo $appInfo['format']; ?></textarea>
                </div>
            </div>
        </div>
        <hr>
        <button type="submit" name="deleteApp" onclick="if(confirm('<?php echo locale('deleteappconfirm'); ?>')){}else{return false;};" class="btn btn-danger btn-md float-left mb-3"><?php echo locale('delete'); ?></button>
        <button type="submit" name="updateApp" class="btn btn-primary btn-md float-right mb-3"><?php echo locale('update'); ?></button>
    </form>
    <?php endif; ?>
</body>

</html>
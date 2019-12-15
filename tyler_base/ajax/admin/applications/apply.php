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

$_SESSION['applying_for'] = $appID;
$_SESSION['applying_for_name'] = $appInfo['name'];
$_SESSION['applying_for_status'] = $appInfo['status'];
$_SESSION['applying_for_format'] = $appInfo['format'];
$_SESSION['applying_for_desc'] = $appInfo['description'];
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
            <?php if($appInfo['status'] === "CLOSED"): ?>
                <div class="alert alert-danger m-b-lg" role="alert">
                    <?php echo locale('appclosed'); ?>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="app_name"><?php echo locale('appformatname'); ?></label>
                    <input type="text" class="form-control" value="<?php echo $appInfo['name']; ?>" required disabled>
                </div>
                <?php if($appInfo['description'] <> NULL || $appInfo['description'] <> ""): ?>
                    <div class="alert alert-info m-b-lg" role="alert">
                        <strong><?php echo locale('appdescription'); ?>:</strong><hr>
                        <?php echo nl2br($appInfo['description']); ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="app_format"><?php echo locale('appformat'); ?></label>
                    <textarea class="form-control" rows="12" name="app_format" id="app_format" placeholder="<?php echo locale('appformat'); ?>" required><?php echo $appInfo['format']; ?></textarea>
                </div>
                <label><i><?php echo locale('autodenialnote'); ?></i></label>
            <?php endif; ?>
            </div>
        </div>
        <hr>
        <button type="submit" name="applyApp" class="btn btn-primary btn-md float-right mb-3"><?php echo locale('submitapp'); ?></button>
    </form>
</body>

</html>
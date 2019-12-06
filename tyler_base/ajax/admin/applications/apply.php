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
                    This application is currently closed.
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="app_name">Application Name</label>
                    <input type="text" class="form-control" value="<?php echo $appInfo['name']; ?>" required disabled>
                </div>
                <div class="form-group">
                    <label for="app_format">Application Format</label>
                    <textarea class="form-control" rows="12" name="app_format" id="app_format" placeholder="Application Format" required><?php echo $appInfo['format']; ?></textarea>
                </div>
                <label><i>Not following the format may result in an automatic denial.</i></label>
            <?php endif; ?>
            </div>
        </div>
        <hr>
        <button type="submit" name="applyApp" class="btn btn-primary btn-md float-right mb-3">Submit Application</button>
    </form>
</body>

</html>
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
                    <label for="app_name">Application Name</label>
                    <input type="text" class="form-control" name="app_name" id="app_name" placeholder="Application Name" value="<?php echo $appInfo['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="app_status">Application Status</label>
                    <select class="form-control" name="app_status" id="app_status" required>
                        <option value="<?php echo $appInfo['status']; ?>" selected><?php echo $appInfo['status']; ?> (Current)</option>
                        <option value="OPEN">OPEN</option>
                        <option value="CLOSED">CLOSED</option>
                        <option value="ON-HOLD">ON-HOLD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="app_format">Application Format</label>
                    <textarea class="form-control" rows="4" name="app_format" id="app_format" placeholder="Application Format" required><?php echo $appInfo['format']; ?></textarea>
                </div>
            </div>
        </div>
        <hr>
        <button type="submit" name="deleteApp" onclick="if(confirm('Are you sure you would like to delete this application format? All applications submitted will be lost, and can not be recovered.')){}else{return false;};" class="btn btn-danger btn-md float-left mb-3">Delete</button>
        <button type="submit" name="updateApp" class="btn btn-primary btn-md float-right mb-3">Update</button>
    </form>
</body>

</html>
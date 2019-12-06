<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = 'Site Settings';

if (!loggedIn) {
    header('Location: /login');
    exit();
}

//Check if they're staff and have permissions
if (super_admin === 'false') {
    notify('danger', 'You do not have access to that part of the site.', '/index');
}

//Update settings
if (isset($_POST['updateSettings'])) {
    $site_name  = strip_tags($_POST['site_name']);
    $app_accepted  = strip_tags(nl2br($_POST['app_accepted']));

    $sql = "UPDATE settings SET name = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$site_name, $_SESSION['app_id']]); 

    $sql = "UPDATE settings SET app_accept_message = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$app_accepted, $_SESSION['app_id']]); 

    notify('success', 'Settings updated.', '/admin/settings');
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
            <div class="container">
            <div id="ezaMsg"><?php if (isset($message)) { echo $message; } ?></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">ezApps Settings</h5>
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="site_name">Website Name / Community Name</label>
                                                <input type="text" class="form-control" name="site_name" id="site_name" value="<?php echo $config['name']; ?>" placeholder="Website Name / Community Name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="app_accepted">Application Accepted Message</label>
                                                <textarea class="form-control" rows="4" name="app_accepted" id="app_accepted" placeholder="Application Accepted Message" required><?php echo $config['app_accept_message']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" name="updateSettings" class="btn btn-success">Update Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require 'tyler_base/page/copyright.php'; ?>
    </div>
    <?php require 'tyler_base/page/footer.php'; ?>
</body>

</html>
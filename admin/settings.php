<?php
session_name('ezApps');
session_start();
require '../tyler_base/global/connect.php';
require '../tyler_base/global/config.php';
$page['name'] = locale('sitesettings');

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

//Check if they're staff and have permissions
if (super_admin === 'false') {
    notify('danger', locale('accessdenied'), '/index');
}

//Update settings
if (isset($_POST['updateSettings'])) {
    $site_name  = strip_tags($_POST['site_name']);
    $webhook  = strip_tags($_POST['webhook']);
    $app_accepted  = strip_tags(nl2br($_POST['app_accepted']));

    if (isset($_POST['theme'])) {
        $theme  = strip_tags($_POST['theme']);
    }

    $sql = "UPDATE settings SET name = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$site_name, '1']);
    
    if ($webhook <> NULL) {
        $sql = "UPDATE settings SET discord_webhook = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$webhook, '1']);
    } else {
        $sql = "UPDATE settings SET discord_webhook = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([NULL, '1']);    
    }

    $sql = "UPDATE settings SET app_accept_message = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$app_accepted, '1']); 

    if (isset($_POST['wh_app_created'])) { //Is checked
        $sql = "UPDATE settings SET wh_app_created = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', '1']);
    } else { //Is not checked
        $sql = "UPDATE settings SET wh_app_created = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', '1']);
    }
    sleep(2);
    if (isset($_POST['wh_app_accepted'])) { //Is checked
        $sql = "UPDATE settings SET wh_app_accepted = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', '1']);
    } else { //Is not checked
        $sql = "UPDATE settings SET wh_app_accepted = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', '1']);
    }

    if (isset($_POST['wh_app_declined'])) { //Is checked
        $sql = "UPDATE settings SET wh_app_declined = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', '1']);
    } else { //Is not checked
        $sql = "UPDATE settings SET wh_app_declined = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', '1']);
    }

    if (isset($theme)) {
        if ($theme <> $config['theme']) {
            $sql = "UPDATE settings SET theme = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$theme, '1']);
        }
    }
    
    logger(locale('updatedsitesettings'));
    notify('success', locale('settingsupdated'), DOMAIN.'/admin/settings');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../tyler_base/page/header.php'; ?>
</head>

<body>
    <?php require '../tyler_base/page/nav.php'; ?>
    <?php require '../tyler_base/page/s-nav.php'; ?>
    <div class="lime-container">
        <div class="lime-body">
            <div class="container">
            <div id="ezaMsg"><?php if (isset($message)) { echo $message; } ?></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo locale('appsettings'); ?></h5>
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="site_name"><?php echo locale('appname'); ?></label>
                                                <input type="text" class="form-control" name="site_name" id="site_name" value="<?php echo $config['name']; ?>" placeholder="<?php echo locale('appname'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="webhook"><?php echo locale('discordwebhook'); ?> <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#discordWebhookSettings"><?php echo locale('settings'); ?></button></label>
                                                <input type="text" class="form-control" name="webhook" id="webhook" value="<?php echo $wh; ?>" placeholder="<?php echo locale('blanktodisable'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="discordWebhookSettings" tabindex="-1" role="dialog"
                                        aria-labelledby="discordWebhookSettings" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="discordWebhookSettings"><?php echo locale('whentosend'); ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <i class="material-icons">close</i>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <input type="checkbox" name="wh_app_created" id="wh_app_created" <?php if ($webhook['app_created'] === 'true') {echo 'checked';} ?>>
                                                                <label class="label" for="wh_app_created"><?php echo locale('appscreated'); ?></label>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="checkbox" name="wh_app_accepted" id="wh_app_accepted" <?php if ($webhook['app_accepted'] === 'true') {echo 'checked';} ?>>
                                                                <label class="label" for="wh_app_accepted"><?php echo locale('appsaccepted'); ?></label>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="checkbox" name="wh_app_declined" id="wh_app_declined" <?php if ($webhook['app_declined'] === 'true') {echo 'checked';} ?>>
                                                                <label class="label" for="wh_app_declined"><?php echo locale('appsdeclined'); ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="app_accepted"><?php echo locale('appacceptedmessage'); ?></label>
                                                <textarea class="form-control" rows="4" name="app_accepted" id="app_accepted" placeholder="<?php echo locale('appacceptedmessage'); ?>" required><?php echo $config['app_accept_message']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="theme"><?php echo locale('theme'); ?></label>
                                                <select class="form-control custom-select" id="theme" name="theme">
                                                    <option value="<?php echo $config['theme']; ?>" selected disabled><?php echo $config['theme']; ?> (<?php echo locale('current'); ?>)</option>
                                                    <option value="default"><?php echo locale('default'); ?></option>
                                                    <option value="dark"><?php echo locale('dark'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" name="updateSettings" class="btn btn-success"><?php echo locale('updatesettings'); ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require '../tyler_base/page/copyright.php'; ?>
    </div>
    <?php require '../tyler_base/page/footer.php'; ?>
</body>

</html>
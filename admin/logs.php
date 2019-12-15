<?php
session_name('ezApps');
session_start();
require '../tyler_base/global/connect.php';
require '../tyler_base/global/config.php';
$page['name'] = locale('viewlogs');

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

//Check if they're staff and have permissions
if (super_admin === 'false') {
    notify('danger', locale('accessdenied'), DOMAIN.'/index');
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
                                <h5 class="card-title"><?php echo locale('logs'); ?></h5>
                                <div class="alert alert-info m-b-lg" role="alert">
                                    <?php echo locale('logsalert'); ?>
                                </div>
                                <form method="POST" id="logsLookup">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="action"><?php echo locale('action'); ?></label>
                                                <input type="text" class="form-control" name="action" id="action"
                                                    placeholder='<?php echo locale('searchinfo'); ?>'>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user_id"><?php echo locale('id'); ?></label>
                                                <input type="text" class="form-control" name="user_id" id="user_id"
                                                    placeholder='<?php echo locale('id'); ?>'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" name="login" class="btn btn-link float-right"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                                <div class="divider"></div>
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
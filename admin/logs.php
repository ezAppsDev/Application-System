<?php
session_name('ezApps');
session_start();
require '../tyler_base/global/connect.php';
require '../tyler_base/global/config.php';
$page['name'] = 'View Logs';

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

//Check if they're staff and have permissions
if (super_admin === 'false') {
    notify('danger', 'You do not have access to that part of the site.', DOMAIN.'/index');
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
                                <h5 class="card-title">Logs</h5>
                                <div class="alert alert-info m-b-lg" role="alert">
                                    In order to prevent database crashes we can not display all logs. Instead, Please
                                    search for a term such as "Comment On", "Updated", etc along with the User's ID for
                                    the most accurate results.
                                </div>
                                <form method="POST" id="logsLookup">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="action">Action</label>
                                                <input type="text" class="form-control" name="action" id="action"
                                                    placeholder='Search for a term such as "Comment On", "Updated", etc'>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user_id">User ID</label>
                                                <input type="text" class="form-control" name="user_id" id="user_id"
                                                    placeholder='User ID'>
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
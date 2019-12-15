<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = locale('login');

if (isset($_GET['banned'])) {
    notify('danger', locale('banned'), DOMAIN.'/login');
}

if (isset($_POST['login'])) {
    $display_name     = strip_tags($_POST['display_name']);
    $password  = strip_tags($_POST['password']);

    $sql = "SELECT * FROM users WHERE display_name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$display_name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user === false) {
        notify('danger', locale('accountnotfound'), DOMAIN.'/login');
    } else {
        $validPassword = password_verify($password, $user['password']);
        if ($validPassword) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = time();
            logger('Logged In');
            header('Location: index');
        } else {
            notify('danger', locale('wrongpassword'), DOMAIN.'/login');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require 'tyler_base/page/header.php'; ?>
    </head>
    <body class="login-page err-500">
        <div class="container">
            <div class="login-container">
                <div class="row">
                    <div class="col-lg-4 col-md-5 col-sm-9 lfh">
                        <div class="card login-box">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo locale('login'); ?></h5>
                                <?php demoAlert(); ?>
                                <div id="ezaMsg"><?php if (isset($message)) { echo $message; } ?></div>
                                <form method="post">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="display_name" id="display_name" placeholder="<?php echo locale('displayname'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="<?php echo locale('password'); ?>">
                                    </div>
                                    <button type="submit" name="login" class="btn btn-primary"><?php echo locale('login'); ?></button>
                                    <a href="./create-account" class="btn btn-secondary float-right"><?php echo locale('createaccount'); ?></a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php require 'tyler_base/page/footer.php'; ?>
    </body>
</html>
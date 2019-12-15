<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = locale('createaccount');

if (isset($_POST['register'])) {
    //Sanitize
    $display_name     = strip_tags($_POST['display_name']);
    $password  = strip_tags($_POST['password']);
    $passwordc  = strip_tags($_POST['passwordc']);

    if (isset($_POST['ageCheck'])) {
        if ($password <> $passwordc) {
            notify('danger', locale('passnotmatch'), DOMAIN.'/create-account');
        } elseif (strlen($password) < 8) {
            notify('danger', locale('longerthan8'), DOMAIN.'/create-account');
        } elseif (!preg_match("#[0-9]+#", $password)) {
            notify('danger', locale('needsnumber'), DOMAIN.'/create-account');
        } elseif (!preg_match("#[a-zA-Z]+#", $password)) {
            notify('danger', locale('needsletter'), DOMAIN.'/create-account');
        } else {
            $dbCount['total_users'] = $pdo->query('select count(*) from users')->fetchColumn();

            if ($dbCount['total_users'] === 0) {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));
                $sql          = "INSERT INTO users (display_name, password, joined, usergroup) VALUES (?,?,?,?)";
                $stmt         = $pdo->prepare($sql);
                $result_user   = $stmt->execute([$display_name, $passwordHash, $us_date, '2']);
                if ($result_user) {
                    notify('success', locale('accountcreated'), DOMAIN.'/login');
                }
            } else {
                $sql       = "SELECT COUNT(display_name) AS num FROM users WHERE display_name = ?";
                $stmt      = $pdo->prepare($sql);
                $stmt->execute([$display_name]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['num'] > 0) {
                    notify('danger', locale('dntaken'), DOMAIN.'/create-account');
                } else {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));
                    $sql          = "INSERT INTO users (display_name, password, joined) VALUES (?,?,?)";
                    $stmt         = $pdo->prepare($sql);
                    $result_user   = $stmt->execute([$display_name, $passwordHash, $us_date]);
                    if ($result_user) {
                        notify('success', locale('accountcreated'), DOMAIN.'/login');
                    }
                }
            }
            
        }
    } else {
        notify('danger', locale('tickthebox'), DOMAIN.'/create-account');
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
                                <h5 class="card-title"><?php echo locale('createaccount'); ?></h5>
                                <?php demoAlert(); ?>
                                <div id="ezaMsg"><?php if (isset($message)) { echo $message; } ?></div>
                                <form method="post">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="display_name" id="displayName" placeholder="<?php echo locale('displayname'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="<?php echo locale('password'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" name="passwordc" id="passwordc" placeholder="<?php echo locale('confirmpassword'); ?>" required>
                                    </div>
                                    <div class="custom-control custom-checkbox form-group">
                                        <input type="checkbox" class="custom-control-input" name="ageCheck" id="ageCheck" required>
                                        <label class="custom-control-label" for="ageCheck"><?php echo locale('ageagree'); ?></label>
                                    </div>
                                    <button type="submit" name="register" class="btn btn-primary"><?php echo locale('finishcreation'); ?></button>
                                    <a href="./login" class="btn btn-secondary float-right"><?php echo locale('login'); ?></a>
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
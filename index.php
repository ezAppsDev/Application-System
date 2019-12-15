<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = locale('home');

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

$dbCount['total_apps'] = $pdo->query('select count(*) from applicants')->fetchColumn();
$dbCount['total_users'] = $pdo->query('select count(*) from users')->fetchColumn();
$dbCount['my_apps'] = $pdo->query('select count(*) from applicants WHERE user="'.$_SESSION['user_id'].'"')->fetchColumn();
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
                <?php if($wh <> NULL && $user['discord_id'] === NULL): ?>
                <div class="alert alert-info m-b-lg" role="alert">
                   <?php echo locale('nodiscordidlinked'); ?>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo locale('totalapplications'); ?></h5>
                                <h2 class="float-right"><?php echo $dbCount['total_apps']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo locale('totalusers'); ?></h5>
                                <h2 class="float-right"><?php echo $dbCount['total_users']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo locale('myapps'); ?></h5>
                                <?php if($dbCount['my_apps'] === 0): ?>
                                <div class="alert alert-warning m-b-lg" role="alert">
                                    <?php echo locale('noapps'); ?>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="15%"> <?php echo locale('id'); ?></th>
                                                <th scope="col" width="30%"> <?php echo locale('application'); ?></th>
                                                <th scope="col" width="25%"> <?php echo locale('status'); ?></th>
                                                <th scope="col" width="20%"> <?php echo locale('applied'); ?></th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $getApplicationsDB = "SELECT * FROM applicants WHERE user=?";
                                                $getApplicationsDB = $pdo->prepare($getApplicationsDB);
                                                $getApplicationsDB->execute([$_SESSION['user_id']]);
                                                $appsDB = $getApplicationsDB->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                foreach ($appsDB as $appDB) {
                                                    $getSpecAppDB = "SELECT id,name FROM applications WHERE id=?";
                                                    $getSpecAppDB = $pdo->prepare($getSpecAppDB);
                                                    $getSpecAppDB->execute([$appDB['app']]);
                                                    $appiDB = $getSpecAppDB->fetch();

                                                    echo '<tr><td>'.$appDB['id'].'</td>';
                                                    echo '<td>'.$appiDB['name'].'</td>';
                                                    if ($appDB['status'] === 'PENDING') {
                                                        echo '<td><span class="badge badge-warning">'.locale('pending').'</span></td>';
                                                    } elseif ($appDB['status'] === 'DENIED') {
                                                        echo '<td><span class="badge badge-danger">'.locale('denied').'</span></td>';
                                                    } elseif ($appDB['status'] === 'ACCEPTED') {
                                                        echo '<td><span class="badge badge-success">'.locale('accepted').'</span></td>';
                                                    }
                                                    echo '<td>'.$appDB['created'].'</td>';
                                                    echo '<td><a class="btn btn-primary btn-sm" href="./app?id='.$appDB['id'].'" role="button">'.locale('view').'</a></td></tr>';
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
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
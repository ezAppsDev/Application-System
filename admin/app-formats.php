<?php
session_name('ezApps');
session_start();
require '../tyler_base/global/connect.php';
require '../tyler_base/global/config.php';
$page['name'] = locale('appformats');

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

//Check if they're staff and have permissions
if (super_admin === 'false' && view_apps === 'false') {
    notify('danger', locale('accessdenied'), DOMAIN.'/index');
}

//Check if any applications exist
$dbCount['app_formats'] = $pdo->query('select count(*) from applications')->fetchColumn();

//Create application format
if (isset($_POST['createAppFormat'])) {
    //Sanitize
    $app_name     = strip_tags($_POST['app_name']);
    $app_format  = strip_tags(nl2br($_POST['app_format']));

    $checkAppName = "SELECT COUNT(name) AS num FROM applications WHERE name = ?";
    $checkAppName = $pdo->prepare($checkAppName);
    $checkAppName->execute([$app_name]);
    $can_result = $checkAppName->fetch(PDO::FETCH_ASSOC);
    if ($can_result['num'] > 0) {
        notify('danger', locale('appnameinuse'), DOMAIN.'/admin/app-formats');
    } else {
        $sql1          = "INSERT INTO applications (name, format, created) VALUES (?,?,?)";
        $stmt1         = $pdo->prepare($sql1);
        $result_ac   = $stmt1->execute([$app_name, $app_format, $us_date]);
        if ($result_ac) {
            // $sql2 = "SELECT id FROM applications WHERE name = ?";
            // $stmt2 = $pdo->prepare($sql2);
            // $stmt2->execute([$app_name]);
            // $appID = $stmt2->fetchColumn();
            // DONT TOUCH THIS CODE.
            // $sql3 = "ALTER TABLE `usergroups` ADD `app_manage_$appID` ENUM('true','false') NOT NULL DEFAULT 'false'";
            // $stmt3 = $pdo->prepare($sql3);
            // $stmt3->execute();
            logger(locale('appformatcreatednotif').' - "'.$app_name.'"');
            notify('success', locale('appformatcreatedalert'), DOMAIN.'/admin/app-formats');
        }
    }
}

//Edit application
if (isset($_POST['updateApp'])) {
    //Sanitize
    $app_name     = strip_tags($_POST['app_name']);
    $app_status     = strip_tags($_POST['app_status']);
    $app_desc  = strip_tags(nl2br($_POST['app_desc']));
    $app_format  = strip_tags(nl2br($_POST['app_format']));

    if ($_SESSION['editing_app_name'] <> $app_name) {
        $checkAppName = "SELECT COUNT(name) AS num FROM applications WHERE name = ?";
        $checkAppName = $pdo->prepare($checkAppName);
        $checkAppName->execute([$app_name]);
        $can_result = $checkAppName->fetch(PDO::FETCH_ASSOC);
        if ($can_result['num'] > 0) {
            notify('danger', locale('appnameinuse'), DOMAIN.'/admin/app-formats');
        } else {
            $sql = "UPDATE applications SET name = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$app_name, $_SESSION['editing_app']]); 
        }
    }

    if ($_SESSION['editing_app_status'] <> $app_status) {
        $sql = "UPDATE applications SET status = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$app_status, $_SESSION['editing_app']]); 
    }
    sleep(2);
    if ($_SESSION['editing_app_desc'] <> $app_desc) {
        $sql = "UPDATE applications SET description = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$app_desc, $_SESSION['editing_app']]); 
    }

    if ($_SESSION['editing_app_format'] <> $app_format) {
        $sql = "UPDATE applications SET format = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$app_format, $_SESSION['editing_app']]); 
    }

    logger(locale('editedappfromat').' - "'.$app_name.'" ('.$_SESSION['editing_app'].')');
    notify('success', locale('appformatupdated'), DOMAIN.'/admin/app-formats');
}

//Delete app format
if (isset($_POST['deleteApp'])) {
    //First we need to delete all of the applications
    $sql = "DELETE FROM applicants WHERE app = ?";
    $pdo->prepare($sql)->execute([$_SESSION['editing_app']]);
    
    //Now delete the format
    $sql = "DELETE FROM applications WHERE id = ?";
    $pdo->prepare($sql)->execute([$_SESSION['editing_app']]); 

    logger(locale('deletedappformatnotif').' - "'.$_SESSION['editing_app_name'].'" ('.$_SESSION['editing_app'].')');

    notify('success', locale('deletedappformatalert'), DOMAIN.'/admin/app-formats');
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
                                <h5 class="card-title"><?php echo locale('appformats'); ?>
                                <button type="button" class="btn btn-success btn-sm float-right mb-3" data-toggle="modal" data-target="#addApplicationModal">+ <?php echo locale('new'); ?></button></h5>
                                
                                <!-- Create App Format Modal -->
                                <div class="modal fade" id="addApplicationModal" tabindex="-1" role="dialog" aria-labelledby="addApplicationModal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addApplicationModal"><?php echo locale('newappformat'); ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="app_name" id="app_name" placeholder="<?php echo locale('appformatname');?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea class="form-control" rows="4" name="app_format" id="app_format" placeholder="<?php echo locale('appformat');?>" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo locale('cancel');?></button>
                                                    <button type="submit" name="createAppFormat" class="btn btn-primary"><?php echo locale('create');?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if($dbCount['app_formats'] === 0): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning m-b-lg" role="alert">
                                            <?php echo locale('noappformatsyet'); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="15%"><?php echo locale('id'); ?></th>
                                                <th scope="col" width="30%"><?php echo locale('name'); ?></th>
                                                <th scope="col" width="25%"><?php echo locale('status'); ?></th>
                                                <th scope="col" width="20%"><?php echo locale('totalapplied'); ?></th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $getApplicationsDB = "SELECT * FROM applications";
                                                $getApplicationsDB = $pdo->prepare($getApplicationsDB);
                                                $getApplicationsDB->execute();
                                                $appsDB = $getApplicationsDB->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                foreach ($appsDB as $appDB) {
                                                    $dbCount['total_applied'] = $pdo->query('select count(*) from applicants WHERE app='.$appDB['id'])->fetchColumn();
                                                    echo '<tr><td>'.$appDB['id'].'</td>';
                                                    echo '<td>'.$appDB['name'].'</td>';
                                                    if ($appDB['status'] === 'OPEN') {
                                                        echo '<td><span class="badge badge-success">'.locale('open').'</span></td>';
                                                    } elseif ($appDB['status'] === 'CLOSED') {
                                                        echo '<td><span class="badge badge-danger">'.locale('closed').'</span></td>';
                                                    } elseif ($appDB['status'] === 'ON-HOLD') {
                                                        echo '<td><span class="badge badge-warning">'.locale('onhold').'</span></td>';
                                                    }
                                                    echo '<td>'.$dbCount['total_applied'].'</td>';
                                                    echo '<td><a class="btn btn-primary btn-sm openAppEditorModal" href="javascript:void(0);" data-href="'.DOMAIN.'/tyler_base/ajax/admin/applications/edit.php?appID='.$appDB['id'].'" role="button">'.locale('edit').'</a></td></tr>';
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

                <!-- Edit App Format Modal -->
                <div class="modal fade" id="AppEditorModal" tabindex="-1" role="dialog" aria-labelledby="AppEditorModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="AppEditorModal"><?php echo locale('editingapp'); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="material-icons">close</i>
                                </button>
                            </div>
                            <div id="openAppEditorModalBody" class="modal-body">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require '../tyler_base/page/copyright.php'; ?>
    </div>

    <?php require '../tyler_base/page/footer.php'; ?>
    <script type="text/javascript">
    $(document).ready(function() {
      $('.openAppEditorModal').on('click',function(){
          var dataURL = $(this).attr('data-href');
          $('#openAppEditorModalBody.modal-body').load(dataURL,function(){
              $('#AppEditorModal').modal({show:true});
          });
      });
    });
    </script>
</body>

</html>
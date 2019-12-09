<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = 'Application Formats';

if (!loggedIn) {
    header('Location: /login');
    exit();
}

//Check if they're staff and have permissions
if (super_admin === 'false') {
    if (app_management === 'false') {
        notify('danger', 'You do not have access to that part of the site.', '/index');
    }
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
        notify('danger', 'Application name already in-use.', '/admin/formats');
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
            notify('success', 'Application format created! Please ensure you set what groups can manage these applications in usergroup management.', '/admin/formats');
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
            notify('danger', 'Application name already in-use.', '/admin/formats');
        } else {
            $sql = "UPDATE applications SET name = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$app_name, $_SESSION['editing_app']]); 
        }
    }

    if ($_SESSION['editing_app_status'] <> $app_status) {
        $sql = "UPDATE applications SET status = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$app_status, $_SESSION['editing_app']]); 
    }

    if ($_SESSION['editing_app_desc'] <> $app_desc) {
        $sql = "UPDATE applications SET description = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$app_desc, $_SESSION['editing_app']]); 
    }

    if ($_SESSION['editing_app_format'] <> $app_format) {
        $sql = "UPDATE applications SET format = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$app_format, $_SESSION['editing_app']]); 
    }

    notify('success', 'Application format updated.', '/admin/formats');
}

//Delete app format
if (isset($_POST['deleteApp'])) {
    $sql = "DELETE FROM applications WHERE id = ?";
    $pdo->prepare($sql)->execute([$_SESSION['editing_app']]); 

    notify('success', 'Application format deleted.', '/admin/formats');
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
                                <h5 class="card-title">Application Formats 
                                <button type="button" class="btn btn-success btn-sm float-right mb-3" data-toggle="modal" data-target="#addApplicationModal">+ New</button></h5>
                                
                                <!-- Create App Format Modal -->
                                <div class="modal fade" id="addApplicationModal" tabindex="-1" role="dialog" aria-labelledby="addApplicationModal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addApplicationModal">New Application Format</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="app_name" id="app_name" placeholder="Application Name" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea class="form-control" rows="4" name="app_format" id="app_format" placeholder="Application Format" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="createAppFormat" class="btn btn-primary">Create</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if($dbCount['app_formats'] === 0): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning m-b-lg" role="alert">
                                            You haven't created any application formats yet.
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="15%">ID</th>
                                                <th scope="col" width="30%">Name</th>
                                                <th scope="col" width="25%">Status</th>
                                                <th scope="col" width="20%">Total Applied</th>
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
                                                    echo '<tr><td>'.$appDB['id'].'</td>';
                                                    echo '<td>'.$appDB['name'].'</td>';
                                                    if ($appDB['status'] === 'OPEN') {
                                                        echo '<td><span class="badge badge-success">OPEN</span></td>';
                                                    } elseif ($appDB['status'] === 'CLOSED') {
                                                        echo '<td><span class="badge badge-danger">CLOSED</span></td>';
                                                    } elseif ($appDB['status'] === 'ON-HOLD') {
                                                        echo '<td><span class="badge badge-warning">ON-HOLD</span></td>';
                                                    }
                                                    echo '<td>NULL</td>';
                                                    echo '<td><a class="btn btn-primary btn-sm openAppEditorModal" href="javascript:void(0);" data-href="../../../tyler_base/ajax/admin/applications/edit.php?appID='.$appDB['id'].'" role="button">Edit</a></td></tr>';
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
                                <h5 class="modal-title" id="AppEditorModal">Editing Application</h5>
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
        <?php require 'tyler_base/page/copyright.php'; ?>
    </div>

    <?php require 'tyler_base/page/footer.php'; ?>
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
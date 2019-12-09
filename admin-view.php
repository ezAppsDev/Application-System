<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = 'View Applications';

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

//Check if they're staff and have permissions
if (super_admin === 'false') {
    if (app_management === 'false') {
        notify('danger', 'You do not have access to that part of the site.', DOMAIN.'/index');
    }
}

//Check if any applications exist
$dbCount['applicants'] = $pdo->query('select count(*) from applicants')->fetchColumn();
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
                                <h5 class="card-title">Submitted Applications</h5>
                                
                                <?php if($dbCount['applicants'] === 0): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning m-b-lg" role="alert">
                                            No applications have been recieved yet!
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                    <table id="appsTable" class="table">
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
                                                $getApplicationsDB = "SELECT * FROM applicants";
                                                $getApplicationsDB = $pdo->prepare($getApplicationsDB);
                                                $getApplicationsDB->execute();
                                                $appsDB = $getApplicationsDB->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                foreach ($appsDB as $appDB) {
                                                    $getSpecAppDB = "SELECT id,name FROM applications WHERE id=?";
                                                    $getSpecAppDB = $pdo->prepare($getSpecAppDB);
                                                    $getSpecAppDB->execute([$appDB['app']]);
                                                    $appiDB = $getSpecAppDB->fetch();

                                                    echo '<tr><td>'.$appDB['id'].'</td>';
                                                    echo '<td>'.$appiDB['name'].'</td>';
                                                    if ($appDB['status'] === 'PENDING') {
                                                        echo '<td><span class="badge badge-warning">PENDING</span></td>';
                                                    } elseif ($appDB['status'] === 'DENIED') {
                                                        echo '<td><span class="badge badge-danger">DENIED</span></td>';
                                                    } elseif ($appDB['status'] === 'ACCEPTED') {
                                                        echo '<td><span class="badge badge-success">ACCEPTED</span></td>';
                                                    }
                                                    echo '<td>'.$appDB['created'].'</td>';
                                                    echo '<td><a class="btn btn-primary btn-sm" href="./app?id='.$appDB['id'].'" role="button">View</a></td></tr>';
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
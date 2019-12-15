<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = 'Apply';

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

//Check if any applications exist
$dbCount['app_formats'] = $pdo->query('select count(*) from applications WHERE status="OPEN"')->fetchColumn();

//Apply
if (isset($_POST['applyApp'])) {
    //Sanitize
    $app_format  = strip_tags(nl2br($_POST['app_format']));

    if ($webhook['app_created'] === 'true') {
        if ($user['discord_id'] <> NULL) {
            discordAlert($user['display_name'] . ' (<@' . $user['discord_id'] . '>) created an application on ' . $datetime);
        } else {
            discordAlert($user['display_name'] . ' created an application on ' . $datetime);
        }        
    }

    // if ($app_format > $_SESSION['applying_for_format']){
    //     notify('success', '1', DOMAIN.'/apply');
    // } else {
    //     notify('success', '2', DOMAIN.'/apply');
    // }

    $sql1          = "INSERT INTO applicants (user, app, created, format) VALUES (?,?,?,?)";
    $stmt1         = $pdo->prepare($sql1);
    $result_ac   = $stmt1->execute([$_SESSION['user_id'], $_SESSION['applying_for'] , $datetime, $app_format]);
    if ($result_ac) {
        $rediID = $pdo->lastInsertID();
        if(stripos($_SESSION['applying_for_format'], $app_format) !== false){
            $sql = "UPDATE applicants SET status = ?, denial_reason = ? WHERE id = ?";
            $pdo->prepare($sql)->execute(['DENIED', '[AUTOMATIC] Failure to follow format.<hr><strong>Declined by System</strong>', $rediID]);
        }
        notify('success', 'Your application has been submitted! Please allow us at least 48 hours for proper review. You may check on the status of yor application at any time on your home page.', DOMAIN.'/app?id='.$rediID);
    }
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
                                <h5 class="card-title">Available Applications</h5>
                                
                                <?php if($dbCount['app_formats'] === 0): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning m-b-lg" role="alert">
                                            No application formats are opened currently. Please check back later!
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="90%"></th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $getApplicationsDB = "SELECT * FROM applications WHERE status='OPEN'";
                                                $getApplicationsDB = $pdo->prepare($getApplicationsDB);
                                                $getApplicationsDB->execute();
                                                $appsDB = $getApplicationsDB->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                foreach ($appsDB as $appDB) {
                                                    echo '<tr><td>'.$appDB['name'].'</td>';
                                                    echo '<td><a class="btn btn-success btn-sm openApplyModal" href="javascript:void(0);" data-href="'.DOMAIN.'/tyler_base/ajax/admin/applications/apply.php?appID='.$appDB['id'].'" role="button">Apply</a></td></tr>';
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
                <div class="modal fade" id="openApplyModal" tabindex="-1" role="dialog" aria-labelledby="openApplyModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="openApplyModal">Applying</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="material-icons">close</i>
                                </button>
                            </div>
                            <div id="openApplyModalBody" class="modal-body">
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
      $('.openApplyModal').on('click',function(){
          var dataURL = $(this).attr('data-href');
          $('#openApplyModalBody.modal-body').load(dataURL,function(){
              $('#openApplyModal').modal({show:true});
          });
      });
    });
    </script>
</body>

</html>
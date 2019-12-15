<?php
session_name('ezApps');
session_start();
require '../tyler_base/global/connect.php';
require '../tyler_base/global/config.php';
$page['name'] = locale('usergroupmanage');

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

if (super_admin === 'false' && view_usergroups === 'false') {
    notify('danger', locale('accessdenied'), DOMAIN.'/index');
}

//Create new group 
if (isset($_POST['createNewGroup'])) {
    //Sanitize
    $name     = strip_tags($_POST['name']);

    $checkGroupName = "SELECT COUNT(name) AS num FROM usergroups WHERE name = ?";
    $checkGroupName = $pdo->prepare($checkGroupName);
    $checkGroupName->execute([$name]);
    $can_result = $checkGroupName->fetch(PDO::FETCH_ASSOC);
    if ($can_result['num'] > 0) {
        notify('danger', locale('usergroupnameinuse'), DOMAIN.'/admin/usergroups');
    } else {
        $sql1          = "INSERT INTO usergroups (name) VALUES (?)";
        $stmt1         = $pdo->prepare($sql1);
        $result_ac   = $stmt1->execute([$name]);
        if ($result_ac) {
            logger(locale('createdusergroup').' - '.$name.'');
            notify('success', locale('usergroupcreated'), DOMAIN.'/admin/usergroups');
        }
    }
}

//Edit group
if (isset($_POST['updateUsergroup'])) {
    //Sanitize
    $group_name     = strip_tags($_POST['group_name']);

    if ($_SESSION['editing_group_name'] <> $group_name) {
        $checkGroupName = "SELECT COUNT(name) AS num FROM usergroups WHERE name = ?";
        $checkGroupName = $pdo->prepare($checkGroupName);
        $checkGroupName->execute([$name]);
        $can_result = $checkGroupName->fetch(PDO::FETCH_ASSOC);
        if ($can_result['num'] > 0) {
            notify('danger', locale('usergroupnameinuse'), DOMAIN.'/admin/usergroups');
        } else {
            $sql = "UPDATE usergroups SET name = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$group_name, $_SESSION['editing_group']]); 
        }
    }

    if (super_admin === 'true') {	
        if (isset($_POST['perm_access'])) { //Is checked	
            $sql = "UPDATE usergroups SET access = ? WHERE id = ?";	
            $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);	
        } else { //Is not checked	
            $sql = "UPDATE usergroups SET access = ? WHERE id = ?";	
            $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);	
        }	
    	
        if (isset($_POST['perm_super_admin'])) { //Is checked	
            $sql = "UPDATE usergroups SET super_admin = ? WHERE id = ?";	
            $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);	
        } else { //Is not checked	
            $sql = "UPDATE usergroups SET super_admin = ? WHERE id = ?";	
            $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);	
        }	
        if (isset($_POST['perm_edit_usergroups'])) { //Is checked	
            $sql = "UPDATE usergroups SET edit_usergroups = ? WHERE id = ?";	
            $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);	
        } else { //Is not checked	
            $sql = "UPDATE usergroups SET edit_usergroups = ? WHERE id = ?";	
            $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);	
        }
    }

    if (isset($_POST['perm_view_apps'])) { //Is checked
        $sql = "UPDATE usergroups SET view_apps = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);
    } else { //Is not checked
        $sql = "UPDATE usergroups SET view_apps = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);
    }

    if (isset($_POST['perm_review_apps'])) { //Is checked
        $sql = "UPDATE usergroups SET review_apps = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);
    } else { //Is not checked
        $sql = "UPDATE usergroups SET review_apps = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);
    }

    if (isset($_POST['perm_view_users'])) { //Is checked
        $sql = "UPDATE usergroups SET view_users = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);
    } else { //Is not checked
        $sql = "UPDATE usergroups SET view_users = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);
    }

    if (isset($_POST['perm_view_usergroups'])) { //Is checked
        $sql = "UPDATE usergroups SET view_usergroups = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);
    } else { //Is not checked
        $sql = "UPDATE usergroups SET view_usergroups = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);
    }

    if (isset($_POST['perm_edit_users'])) { //Is checked
        $sql = "UPDATE usergroups SET edit_users = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', $_SESSION['editing_group']]);
    } else { //Is not checked
        $sql = "UPDATE usergroups SET edit_users = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', $_SESSION['editing_group']]);
    }

    logger(locale('editedusergroupnotif').' - '.$group_name.' ('.$_SESSION['editing_group'].')');
    notify('success', locale('usergroupupdated').' <strong>'.locale('permslockednote').'</strong>', DOMAIN.'/admin/usergroups');
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
                                <h5 class="card-title"><?php echo locale('usergroups'); ?>
                                    <button type="button" class="btn btn-success btn-sm float-right mb-3"
                                        data-toggle="modal" data-target="#addUsergroupModal">+ <?php echo locale('new'); ?></button></h5>

                                <!-- Create App Format Modal -->
                                <div class="modal fade" id="addUsergroupModal" tabindex="-1" role="dialog"
                                    aria-labelledby="addUsergroupModal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addUsergroupModal"><?php echo locale('usergroups'); ?>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="name"
                                                                    id="name" placeholder="<?php echo locale('usergroupname'); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal"><?php echo locale('cancel'); ?></button>
                                                    <button type="submit" name="createNewGroup"
                                                        class="btn btn-primary"><?php echo locale('create'); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" width="45%"><?php echo locale('id'); ?></th>
                                                <th scope="col" width="45%"><?php echo locale('name'); ?></th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $getUsergroupsDB = "SELECT * FROM usergroups";
                                                $getUsergroupsDB = $pdo->prepare($getUsergroupsDB);
                                                $getUsergroupsDB->execute();
                                                $usergroupsDB = $getUsergroupsDB->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                foreach ($usergroupsDB as $usergroupDB) {
                                                    echo '<tr><td>'.$usergroupDB['id'].'</td>';
                                                    echo '<td>'.$usergroupDB['name'].'</td>';
                                                    echo '<td><a class="btn btn-primary btn-sm openGroupEditorModal" href="javascript:void(0);" data-href="'.DOMAIN.'/tyler_base/ajax/admin/usergroups/edit.php?id='.$usergroupDB['id'].'" role="button">'.locale('edit').'</a></td></tr>';
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Usergroup Modal -->
                <div class="modal fade" id="GroupEditorModal" tabindex="-1" role="dialog"
                    aria-labelledby="GroupEditorModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="GroupEditorModal"><?php echo locale('editingusergroup'); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="material-icons">close</i>
                                </button>
                            </div>
                            <div id="openGroupEditorModalBody" class="modal-body">
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
        $('.openGroupEditorModal').on('click', function() {
            var dataURL = $(this).attr('data-href');
            $('#openGroupEditorModalBody.modal-body').load(dataURL, function() {
                $('#GroupEditorModal').modal({
                    show: true
                });
            });
        });
    });
    </script>
</body>

</html>
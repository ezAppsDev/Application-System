<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = 'Viewing App';

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}

if (isset($_GET['id'])) {
    $id = strip_tags($_GET['id']);

    $sql = "SELECT * FROM applicants WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($app === false) {
        notify('danger', 'That application does not exist.', DOMAIN.'/index');
    } else {
        $_SESSION['app_id'] = $id;
        $_SESSION['app_user'] = $app['user'];
        $_SESSION['app_type'] = $app['app'];
        $_SESSION['app_status'] = $app['status'];
        $_SESSION['app_denial_reason'] = $app['denial_reason'];
        $_SESSION['app_accepted_by'] = $app['accepted_by'];
        $_SESSION['app_created'] = $app['created'];
        $_SESSION['app_format'] = $app['format'];

        $getSpecAppDB = "SELECT * FROM applications WHERE id=?";
        $getSpecAppDB = $pdo->prepare($getSpecAppDB);
        $getSpecAppDB->execute([$_SESSION['app_type']]);
        $appiDB = $getSpecAppDB->fetch(PDO::FETCH_ASSOC);

        $_SESSION['app_i_name'] = $appiDB['name'];

        $getAppUserInfo = "SELECT * FROM users WHERE id=?";
        $getAppUserInfo = $pdo->prepare($getAppUserInfo);
        $getAppUserInfo->execute([$_SESSION['app_user']]);
        $appuDB = $getAppUserInfo->fetch(PDO::FETCH_ASSOC);

        $_SESSION['app_u_name'] = $appuDB['display_name'];
        $_SESSION['app_u_usergroup'] = $appuDB['usergroup'];

        $getAppUserGroupInfo = "SELECT id,name FROM usergroups WHERE id=?";
        $getAppUserGroupInfo = $pdo->prepare($getAppUserGroupInfo);
        $getAppUserGroupInfo->execute([$_SESSION['app_u_usergroup']]);
        $appugDB = $getAppUserGroupInfo->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['app_ug_name'] = $appugDB['name'];

        if ($_SESSION['app_user'] <> $_SESSION['user_id']) {
            if (super_admin === 'false') {
                if (app_management === 'false') {
                    notify('danger', 'You do not have access to that part of the site.', DOMAIN.'/index');
                }
            }
        }
    }
}

//Add comment
if (isset($_POST['addComment'])) {
    //Sanitize
    $comment  = strip_tags(nl2br($_POST['comment']));

    $sql1          = "INSERT INTO applicant_comments (aid, user, created, msg) VALUES (?,?,?,?)";
    $stmt1         = $pdo->prepare($sql1);
    $result_ac   = $stmt1->execute([$_SESSION['app_id'], $_SESSION['user_id'], $datetime, $comment]);
    if ($result_ac) {
        logger('Commented on an application - Application ID: '.$_SESSION['app_id'].' <br />Comment: '.$comment.'');
        notify('success', 'Comment added.', DOMAIN.'/app?id='.$_SESSION['app_id']);
    }
}

//Accept app
if (isset($_POST['acceptApp'])) {
    $sql = "UPDATE applicants SET status = ? WHERE id = ?";
    $pdo->prepare($sql)->execute(['ACCEPTED', $_SESSION['app_id']]); 

    $sql = "UPDATE applicants SET accepted_by = ? WHERE id = ?";
    $pdo->prepare($sql)->execute(['<hr><strong>Accepted by '.$user['display_name'].' (ID: '.$_SESSION['user_id'].')</strong>', $_SESSION['app_id']]); 

    logger('Accepted an application - Application ID: '.$_SESSION['app_id'].'');
    notify('success', 'Application Accepted', DOMAIN.'/app?id='.$_SESSION['app_id']);
}

//Decline app
if (isset($_POST['declineApp'])) {
    $denial_reason  = strip_tags(nl2br($_POST['denial_reason']));

    $sql = "UPDATE applicants SET status = ? WHERE id = ?";
    $pdo->prepare($sql)->execute(['DENIED', $_SESSION['app_id']]); 

    $sql = "UPDATE applicants SET denial_reason = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$denial_reason . '<hr><strong>Declined by '.$user['display_name'].' (ID: '.$_SESSION['user_id'].')</strong>', $_SESSION['app_id']]); 

    logger('Declined an application - Application ID: '.$_SESSION['app_id'].'');
    notify('success', 'Application Denied', DOMAIN.'/app?id='.$_SESSION['app_id']);
}

//Check if a comment command is in the url
if (isset($_GET['c'])) {
    $c = strip_tags($_GET['c']);

    //Make sure they're staff
    if (super_admin === 'false') {
        if (app_management === 'false') {
            notify('danger', 'You do not have access to that part of the site.', DOMAIN.'/index');
        }
    }

    //If the comment needs to be hidden
    if (isset($_GET['hide']) && strip_tags($_GET['hide']) === 'true') {
        $sql = "UPDATE applicant_comments SET hidden = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', $c]);
        logger('Hid a comment - Application ID: '.$_SESSION['app_id'].' ... Comment ID: '.$c.'');
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
                <?php if($_SESSION['app_status'] === 'ACCEPTED'): ?>
                    <div class="alert alert-success m-b-lg" role="alert">
                        <?php echo $config['app_accept_message']; 
                        echo $_SESSION['app_accepted_by']; ?>
                    </div>
                <?php elseif ($_SESSION['app_status'] === 'DENIED'): ?>
                    <div class="alert alert-danger m-b-lg" role="alert">
                        <?php echo nl2br($_SESSION['app_denial_reason']); ?>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mail-container">
                                    <div class="mail-header">
                                        <div class="mail-title">
                                            <?php echo $_SESSION['app_i_name']; ?> Application - ID: <?php echo $_SESSION['app_id']; ?>
                                        </div>
                                        <div class="mail-actions">
                                            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#replyModal">Comment</button>
                                        </div>
                                        <!-- Reply Modal -->
                                        <div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModal" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="replyModal">Adding Comment</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <i class="material-icons">close</i>
                                                        </button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <textarea class="form-control" rows="4" name="comment" id="comment" placeholder="Comment..." required></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="addComment" class="btn btn-primary">Add</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mail-info">
                                        <div class="mail-author">
                                            <img src="./assets/images/avatars/placeholder.png" alt="">
                                            <div class="mail-author-info">
                                                <span class="mail-author-name"><?php echo $_SESSION['app_u_name']; ?></span>
                                                <span class="mail-author-address"><?php echo $_SESSION['app_ug_name']; ?></span>
                                            </div>
                                        </div>
                                        <div class="mail-other-info">
                                            <span><?php echo $_SESSION['app_created']; ?></span>
                                        </div>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="mail-text">
                                        <p><?php echo nl2br($_SESSION['app_format']); ?></p>
                                    </div>
                                    <hr>
                                    <div class="mail-actions">
                                        <form method="post" class="form-inline">
                                            <?php if($_SESSION['app_status'] === 'PENDING'): ?>
                                                <button type="submit" name="acceptApp" class="btn btn-success mr-2">Accept</button>
                                            <?php else: ?>
                                                <button class="btn btn-success mr-2" disabled>Accept</button>
                                            <?php endif; ?>
                                            <?php if($_SESSION['app_status'] === 'PENDING') :?>
                                                <button type="button" data-toggle="modal" data-target="#declineApp" class="btn btn-danger">Decline</button>
                                            <?php else: ?>
                                                <button class="btn btn-danger" disabled>Decline</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                $getCommentsDB = "SELECT * FROM applicant_comments WHERE aid = ?";
                $getCommentsDB = $pdo->prepare($getCommentsDB);
                $getCommentsDB->execute([$_SESSION['app_id']]);
                $commentsDB = $getCommentsDB->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($commentsDB as $commentDB) {
                    $getReplyUserInfo = "SELECT * FROM users WHERE id=?";
                    $getReplyUserInfo = $pdo->prepare($getReplyUserInfo);
                    $getReplyUserInfo->execute([$commentDB['user']]);
                    $replyuDB = $getReplyUserInfo->fetch(PDO::FETCH_ASSOC);

                    $getReplyUserGroupInfo = "SELECT id,name FROM usergroups WHERE id=?";
                    $getReplyUserGroupInfo = $pdo->prepare($getReplyUserGroupInfo);
                    $getReplyUserGroupInfo->execute([$replyuDB['usergroup']]);
                    $replyugDB = $getReplyUserGroupInfo->fetch(PDO::FETCH_ASSOC);
                    ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                            <?php if($commentDB['hidden'] === 'true'): ?>
                                <div class="alert alert-info m-b-lg" role="alert">
                                    <i>This reply has been hidden by an admin.</i>
                                </div>
                            <?php else: ?>
                                <div class="mail-container">
                                    <div class="mail-header">
                                        <div class="mail-title">
                                            Comment
                                        </div>
                                        <?php if(super_admin === 'true'): ?>
                                            <div class="mail-actions">
                                                <a class="btn btn-danger btn-sm" href="<?php echo $_SERVER['REQUEST_URI']; ?>&c=<?php echo $commentDB['id']; ?>&hide=true" role="button">Hide</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mail-info">
                                        <div class="mail-author">
                                            <img src="./assets/images/avatars/placeholder.png" alt="">
                                            <div class="mail-author-info">
                                                <span class="mail-author-name"><?php echo $replyuDB['display_name']; ?></span>
                                                <span class="mail-author-address"><?php echo $replyugDB['name']; ?></span>
                                            </div>
                                        </div>
                                        <div class="mail-other-info">
                                            <span><?php echo $commentDB['created']; ?></span>
                                        </div>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="mail-text">
                                        <p><?php echo nl2br($commentDB['msg']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php if($_SESSION['app_status'] === 'PENDING'): ?>
                    <!-- Decline App Modal -->
                    <div class="modal fade" id="declineApp" tabindex="-1" role="dialog" aria-labelledby="declineApp" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="declineApp">Reason for denial</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <i class="material-icons">close</i>
                                    </button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <textarea class="form-control" rows="4" name="denial_reason" id="denial_reason" placeholder="Denial Reason" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" name="declineApp" class="btn btn-danger">Decline</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <?php require 'tyler_base/page/copyright.php'; ?>
    </div>
    <?php require 'tyler_base/page/footer.php'; ?>
</body>

</html>
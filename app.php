<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = locale('viewingapp');

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
        notify('danger', locale('appnotexist'), DOMAIN.'/index');
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
            if (super_admin === 'false' && view_apps === 'false') {
                notify('danger', locale('accessdenied'), DOMAIN.'/index');
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
        logger(locale('newcommentnotif').': '.$_SESSION['app_id'].' <br />'.locale('comment').': '.$comment.'');
        notify('success', locale('commentadded'), DOMAIN.'/app?id='.$_SESSION['app_id']);
    }
}

//Accept app
if (isset($_POST['acceptApp'])) {
    $sql = "UPDATE applicants SET status = ? WHERE id = ?";
    $pdo->prepare($sql)->execute(['ACCEPTED', $_SESSION['app_id']]); 

    $sql = "UPDATE applicants SET accepted_by = ? WHERE id = ?";
    $pdo->prepare($sql)->execute(['<hr><strong>'.locale('acceptedby').' '.$user['display_name'].' ('.locale('id').': '.$_SESSION['user_id'].')</strong>', $_SESSION['app_id']]); 

    if ($webhook['app_accepted'] === 'true') {
        $whUI = "SELECT id,display_name,discord_id FROM users WHERE id = ?";
        $whUI = $pdo->prepare($whUI);
        $whUI->execute([$_SESSION['app_user']]);
        $whUI = $whUI->fetch(PDO::FETCH_ASSOC);
        
        if ($whUI['discord_id'] <> NULL) {
            discordAlert($whUI['display_name'] . ' (<@' . $whUI['discord_id'] . '>)\'s '.$_SESSION['app_i_name'].' '.locale('appacceptedalert'));
        }
    }

    logger($_SESSION['user_id'] . ' '.locale('appacceptednotif').': '.$_SESSION['app_id'].'');
    notify('success', locale('appaccepted'), DOMAIN.'/app?id='.$_SESSION['app_id']);
}

//Decline app
if (isset($_POST['declineApp'])) {
    $denial_reason  = strip_tags(nl2br($_POST['denial_reason']));

    $sql = "UPDATE applicants SET status = ? WHERE id = ?";
    $pdo->prepare($sql)->execute(['DENIED', $_SESSION['app_id']]); 

    $sql = "UPDATE applicants SET denial_reason = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$denial_reason . '<hr><strong>'.locale('deniedby').': '.$user['display_name'].' ('.locale('id').': '.$_SESSION['user_id'].')</strong>', $_SESSION['app_id']]); 

    if ($webhook['app_declined'] === 'true') {
        $whUI = "SELECT id,display_name,discord_id FROM users WHERE id = ?";
        $whUI = $pdo->prepare($whUI);
        $whUI->execute([$_SESSION['app_user']]);
        $whUI = $whUI->fetch(PDO::FETCH_ASSOC);
        
        if ($whUI['discord_id'] <> NULL) {
            discordAlert($whUI['display_name'] . ' (<@' . $whUI['discord_id'] . '>)\'s '.$_SESSION['app_i_name'].' '.locale('appdeniedalert'));
        }
    }

    logger($_SESSION['user_id'] . locale('appdeniednotif').': '.$_SESSION['app_id'].'');
    notify('success', locale('appdenied'), DOMAIN.'/app?id='.$_SESSION['app_id']);
}

//Check if a comment command is in the url
if (isset($_GET['c'])) {
    $c = strip_tags($_GET['c']);

    //Make sure they're staff
    if (super_admin === 'false') {
        notify('danger', locale('accessdenied'), DOMAIN.'/index');
    }

    //If the comment needs to be hidden
    if (isset($_GET['hide']) && strip_tags($_GET['hide']) === 'true') {
        $sql = "UPDATE applicant_comments SET hidden = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['true', $c]);
        logger(locale('commenthidnotif').': '.$_SESSION['app_id'].' ... '.locale('comment').locale('id').': '.$c.'');
        notify('success', locale('commenthidden'), DOMAIN.'/app?id='.$_SESSION['app_id']);
    } elseif (isset($_GET['hide']) && strip_tags($_GET['hide']) === 'false') {
        $sql = "UPDATE applicant_comments SET hidden = ? WHERE id = ?";
        $pdo->prepare($sql)->execute(['false', $c]);
        logger(locale('commentunhidnotif').': '.$_SESSION['app_id'].' ... '.locale('comment').locale('id').': '.$c.'');
        notify('success', locale('commentunhidden'), DOMAIN.'/app?id='.$_SESSION['app_id']);
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
                                            <?php echo $_SESSION['app_i_name']; ?> <?php echo locale('application').' - '.locale('id'); ?>:
                                            <?php echo $_SESSION['app_id']; ?>
                                        </div>
                                        <div class="mail-actions">
                                            <button type="button" class="btn btn-secondary" data-toggle="modal"
                                                data-target="#replyModal"><?php echo locale('comment'); ?>:</button>
                                        </div>
                                        <!-- Reply Modal -->
                                        <div class="modal fade" id="replyModal" tabindex="-1" role="dialog"
                                            aria-labelledby="replyModal" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="replyModal"><?php echo locale('addingcomment'); ?></h5>
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
                                                                        <textarea class="form-control" rows="4"
                                                                            name="comment" id="comment"
                                                                            placeholder="Comment..."
                                                                            required></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal"><?php echo locale('cancel'); ?></button>
                                                            <button type="submit" name="addComment"
                                                                class="btn btn-primary"><?php echo locale('add'); ?></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mail-info">
                                        <div class="mail-author">
                                            <img src="<?php echo DOMAIN; ?>/assets/themes/<?php echo $config['theme']; ?>/images/avatars/placeholder.png" alt="">
                                            <div class="mail-author-info">
                                                <span
                                                    class="mail-author-name"><?php echo $_SESSION['app_u_name']; ?></span>
                                                <span
                                                    class="mail-author-address"><?php echo $_SESSION['app_ug_name']; ?></span>
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
                                    <?php if (super_admin === 'true' || review_apps === 'true'): ?>
                                    <div class="divider"></div>
                                    <div class="mail-actions">
                                        <form method="post" class="form-inline">
                                            <?php if($_SESSION['app_status'] === 'PENDING'): ?>
                                            <button type="submit" name="acceptApp"
                                                class="btn btn-success mr-2"><?php echo locale('accept'); ?></button>
                                            <?php else: ?>
                                            <button class="btn btn-success mr-2" disabled><?php echo locale('accept'); ?></button>
                                            <?php endif; ?>
                                            <?php if($_SESSION['app_status'] === 'PENDING') :?>
                                            <button type="button" data-toggle="modal" data-target="#declineApp"
                                                class="btn btn-danger"><?php echo locale('decline'); ?></button>
                                            <?php else: ?>
                                            <button class="btn btn-danger" disabled><?php echo locale('decline'); ?></button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                    <?php endif; ?>
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
                    <?php if($commentDB['hidden'] === 'true' && super_admin === 'true'): ?>
                    <div class="col-lg-12">
                        <div class="accordion" id="hiddenComment<?php echo $commentDB['id']; ?>">
                            <div class="card">
                                <div class="card-header bg-danger text-white" id="headingOne" data-toggle="collapse"
                                    data-target="#collapse<?php echo $commentDB['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $commentDB['id']; ?>">
                                    <?php echo locale('hiddencomment'); ?>
                                </div>
                                <div id="collapse<?php echo $commentDB['id']; ?>" class="collapse" aria-labelledby="headingOne"
                                    data-parent="#hiddenComment<?php echo $commentDB['id']; ?>">
                                    <div class="card-body border border-danger">
                                        <div class="mail-container">
                                            <div class="mail-header">
                                                <div class="mail-title">
                                                <?php echo locale('comment'); ?>
                                                </div>
                                                <?php if(super_admin === 'true'): ?>
                                                <div class="mail-actions">
                                                    <a class="btn btn-success btn-sm"
                                                        href="<?php echo $_SERVER['REQUEST_URI']; ?>&c=<?php echo $commentDB['id']; ?>&hide=false"
                                                        role="button"><?php echo locale('unhide'); ?></a>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mail-info">
                                                <div class="mail-author">
                                                    <img src="./assets/images/avatars/placeholder.png" alt="">
                                                    <div class="mail-author-info">
                                                        <span
                                                            class="mail-author-name"><?php echo $replyuDB['display_name']; ?></span>
                                                        <span
                                                            class="mail-author-address"><?php echo $replyugDB['name']; ?></span>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php elseif ($commentDB['hidden'] === 'false'): ?>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mail-container">
                                    <div class="mail-header">
                                        <div class="mail-title">
                                        <?php echo locale('comment'); ?>
                                        </div>
                                        <?php if(super_admin === 'true'): ?>
                                        <div class="mail-actions">
                                            <a class="btn btn-danger btn-sm"
                                                href="<?php echo $_SERVER['REQUEST_URI']; ?>&c=<?php echo $commentDB['id']; ?>&hide=true"
                                                role="button"><?php echo locale('hide'); ?></a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mail-info">
                                        <div class="mail-author">
                                            <img src="<?php echo DOMAIN; ?>/assets/themes/<?php echo $config['theme']; ?>/images/avatars/placeholder.png" alt="">
                                            <div class="mail-author-info">
                                                <span
                                                    class="mail-author-name"><?php echo $replyuDB['display_name']; ?></span>
                                                <span
                                                    class="mail-author-address"><?php echo $replyugDB['name']; ?></span>
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
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php } ?>
                <?php if (super_admin === 'true' || review_apps === 'true'): ?>
                <?php if($_SESSION['app_status'] === 'PENDING'): ?>
                <!-- Decline App Modal -->
                <div class="modal fade" id="declineApp" tabindex="-1" role="dialog" aria-labelledby="declineApp"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="declineApp"><?php echo locale('reasondenial'); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="material-icons">close</i>
                                </button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea class="form-control" rows="4" name="denial_reason"
                                                    id="denial_reason" placeholder="<?php echo locale('denialreason'); ?>" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo locale('cancel'); ?></button>
                                    <button type="submit" name="declineApp" class="btn btn-danger"><?php echo locale('decline'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php require 'tyler_base/page/copyright.php'; ?>
    </div>
    <?php require 'tyler_base/page/footer.php'; ?>
</body>

</html>
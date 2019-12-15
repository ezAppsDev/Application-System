<div class="lime-sidebar">
        <div class="lime-sidebar-inner slimscroll">
            <ul class="accordion-menu">
                <li class="sidebar-title">
                    <?php echo locale('navigation'); ?>
                </li>
                <li>
                    <a href="<?php echo DOMAIN; ?>/index"><i class="material-icons">home</i><?php echo locale('home'); ?></a>
                </li>
                <li>
                    <a href="<?php echo DOMAIN; ?>/apply"><i class="material-icons">assignment</i><?php echo locale('apply'); ?></a>
                </li>
                <hr>
                <li>
                    <a href="<?php echo DOMAIN; ?>/thank-you"><i class="material-icons">star</i><?php echo locale('credit'); ?></a>
                </li>
                <?php if (super_admin === 'true' || view_apps === 'true' || review_apps === 'true' || view_users === 'true' || view_usergroups === 'true' || edit_users === 'true' || edit_usergroups === 'true'): ?>
                <li class="sidebar-title">
                <?php echo locale('admin'); ?>
                </li>
                <li>
                    <a href="#"><i class="material-icons">description</i>Applications<i
                            class="material-icons has-sub-menu">keyboard_arrow_left</i></a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/app-formats"><?php echo locale('formats'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/apps"><?php echo locale('view'); ?></a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="material-icons">perm_identity</i>Users<i
                            class="material-icons has-sub-menu">keyboard_arrow_left</i></a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/users"><?php echo locale('users'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/usergroups"><?php echo locale('usergroups'); ?></a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo DOMAIN; ?>/admin/settings"><i class="material-icons">settings</i><?php echo locale('settings'); ?></a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
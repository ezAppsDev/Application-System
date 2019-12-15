    <div class="lime-sidebar">
        <div class="lime-sidebar-inner slimscroll">
            <ul class="accordion-menu">
                <li class="sidebar-title">
                    Navigation
                </li>
                <li>
                    <a href="<?php echo DOMAIN; ?>/index"><i class="material-icons">home</i>Home</a>
                </li>
                <li>
                    <a href="<?php echo DOMAIN; ?>/apply"><i class="material-icons">assignment</i>Apply</a>
                </li>
                <hr>
                <li>
                    <a href="<?php echo DOMAIN; ?>/thank-you"><i class="material-icons">star</i>Credit</a>
                </li>
                <?php if (super_admin === 'true' || view_apps === 'true' || review_apps === 'true' || view_users === 'true' || view_usergroups === 'true' || edit_users === 'true' || edit_usergroups === 'true'): ?>
                <li class="sidebar-title">
                Admin
                </li>
                <li>
                    <a href="#"><i class="material-icons">description</i>Applications<i
                            class="material-icons has-sub-menu">keyboard_arrow_left</i></a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/app-formats">Formats</a>
                        </li>
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/apps">View</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="material-icons">perm_identity</i>Users<i
                            class="material-icons has-sub-menu">keyboard_arrow_left</i></a>
                    <ul class="sub-menu">
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/users">Users</a>
                        </li>
                        <li>
                            <a href="<?php echo DOMAIN; ?>/admin/usergroups">Usergroups</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo DOMAIN; ?>/admin/settings"><i class="material-icons">settings</i>Settings</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
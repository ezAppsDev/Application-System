    <div class="lime-sidebar">
        <div class="lime-sidebar-inner slimscroll">
            <ul class="accordion-menu">
                <li class="sidebar-title">
                    Navigation
                </li>
                <li>
                    <a href="/index"><i class="material-icons">home</i>Home</a>
                </li>
                <li>
                    <a href="/apply"><i class="material-icons">assignment</i>Apply</a>
                </li>
                <hr>
                <li>
                    <a href="/thank-you"><i class="material-icons">star</i>Credit</a>
                </li>
                <?php if (super_admin === 'true'): ?>
                <li class="sidebar-title">
                Admin
                </li>
                <?php if(super_admin === 'true' and app_management === 'true'): ?>
                <li>
                    <a href="#"><i class="material-icons">description</i>Applications<i
                            class="material-icons has-sub-menu">keyboard_arrow_left</i></a>
                    <ul class="sub-menu">
                        <li>
                            <a href="/admin/formats">Formats</a>
                        </li>
                        <li>
                            <a href="/admin/view">View</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="material-icons">perm_identity</i>Users<i
                            class="material-icons has-sub-menu">keyboard_arrow_left</i></a>
                    <ul class="sub-menu">
                        <li>
                            <a href="/admin/users">Users</a>
                        </li>
                        <li>
                            <a href="/admin/usergroups">Usergroups</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/admin/settings"><i class="material-icons">settings</i>Settings</a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
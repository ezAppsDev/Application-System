    <div class="lime-header">
        <nav class="navbar navbar-expand-lg">
            <section class="material-design-hamburger navigation-toggle">
                <a href="javascript:void(0)" class="button-collapse material-design-hamburger__icon">
                    <span class="material-design-hamburger__layer"></span>
                </a>
            </section>
            <a class="navbar-brand" href="<?php echo DOMAIN; ?>"><?php echo $config['name']; ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="material-icons">keyboard_arrow_down</i>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- <form class="form-inline my-2 my-lg-0 search">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search for projects, apps, pages..."
                        aria-label="Search">
                </form> -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a class="dropdown-item" href="<?php echo DOMAIN; ?>/user?id=<?php echo $_SESSION['user_id']; ?>"><?php echo locale('account'); ?></a></li>
                            <li class="divider"></li>
                            <li><a class="dropdown-item" href="<?php echo DOMAIN; ?>/logout"><?php echo locale('logout'); ?></a></li>

                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
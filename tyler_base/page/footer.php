        <!-- Javascripts -->
        <script src="<?php echo DOMAIN; ?>/assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="<?php echo DOMAIN; ?>/assets/plugins/bootstrap/popper.min.js"></script>
        <script src="<?php echo DOMAIN; ?>/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo DOMAIN; ?>/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <?php if($page['name'] === locale('viewapps') || $page['name'] === locale('usermanage')): ?>
        <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

        <script>
        $(document).ready(function() {
                $('#appsTable').DataTable();
                $('#usersTable').DataTable();
        } );
        </script>
        <?php endif; ?>
        <script src="<?php echo DOMAIN; ?>/assets/js/lime.min.js"></script>
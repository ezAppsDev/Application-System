        <!-- Javascripts -->
        <script src="/assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="/assets/plugins/bootstrap/popper.min.js"></script>
        <script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <?php if($page['name'] === 'View Applications' || $page['name'] === 'User Management'): ?>
        <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

        <script>
        $(document).ready(function() {
                $('#appsTable').DataTable();
                $('#usersTable').DataTable();
        } );
        </script>
        <?php endif; ?>
        <script src="/assets/js/lime.min.js"></script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo locale('builtby');?> Tyler#7918">
        <meta name="keywords" content="application,applications,app system">
        <meta name="author" content="">
        <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        
        <!-- Title -->
        <title><?php echo $page['name']; ?> - <?php echo $config['name']; ?></title>

        <!-- Styles -->
        <link href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
        <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="<?php echo DOMAIN; ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/2034e08fcf.js" crossorigin="anonymous"></script>

        <?php if($page['name'] === locale('viewapps') || $page['name'] === locale('usermanage')): ?>
        <link href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <?php endif; ?>
      
        <!-- Theme Styles -->
        <link href="<?php echo DOMAIN; ?>/assets/themes/<?php echo $config['theme']; ?>/css/lime.min.css" rel="stylesheet">
        <link href="<?php echo DOMAIN; ?>/assets/themes/<?php echo $config['theme']; ?>/css/custom.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
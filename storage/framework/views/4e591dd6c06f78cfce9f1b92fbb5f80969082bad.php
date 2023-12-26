<!doctype html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/vendor/bootstrap/css/bootstrap.min.css')); ?>">
    <link href="<?php echo e(asset('concept/assets/vendor/fonts/circular-std/style.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/libs/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/vendor/fonts/fontawesome/css/fontawesome-all.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/vendor/charts/chartist-bundle/chartist.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/vendor/charts/morris-bundle/morris.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/vendor/charts/c3charts/c3.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('animate.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('concept/assets/vendor/fonts/flag-icon-css/flag-icon.min.css')); ?>">
    <title>Concept - Bootstrap 4 Admin Dashboard Template</title>
</head>

<body>
    <!-- ============================================================== -->
    <!-- main wrapper -->
    <!-- ============================================================== -->
    <div class="dashboard-main-wrapper">
        <!-- ============================================================== -->
        <!-- navbar -->
        <!-- ============================================================== -->
        <div class="dashboard-header">
            <nav class="navbar navbar-expand-lg bg-white fixed-top">
                <a class="navbar-brand" href="index.html">FocusMeet</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-right-top">
                        <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://ui-avatars.com/api/?name=<?php echo e(auth()->user()->name); ?>" alt="" class="user-avatar-md rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-white nav-user-name"><?php echo e(auth()->user()->name); ?></h5>
                                    <span class="status"></span><span class="ml-2">Available</span>
                                </div>
                                <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"><i class="fas fa-power-off mr-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
        <?php echo $__env->make('admin.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="dashboard-ecommerce">
                <div class="container-fluid  dashboard-content">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <!-- jquery 3.3.1 -->
    <script src="<?php echo e(asset('concept/assets/vendor/jquery/jquery-3.3.1.min.js')); ?>"></script>
    <!-- bootstap bundle js -->
    <script src="<?php echo e(asset('concept/assets/vendor/bootstrap/js/bootstrap.bundle.js')); ?>"></script>
    <!-- slimscroll js -->
    <script src="<?php echo e(asset('concept/assets/vendor/slimscroll/jquery.slimscroll.js')); ?>"></script>
    <!-- main js -->
    <script src="<?php echo e(asset('concept/assets/libs/js/main-js.js')); ?>"></script>
    <!-- chart chartist js -->
    <script src="<?php echo e(asset('concept/assets/vendor/charts/chartist-bundle/chartist.min.js')); ?>"></script>
    <!-- sparkline js -->
    <script src="<?php echo e(asset('concept/assets/vendor/charts/sparkline/jquery.sparkline.js')); ?>"></script>
    <!-- morris js -->
    <script src="<?php echo e(asset('concept/assets/vendor/charts/morris-bundle/raphael.min.js')); ?>"></script>
    <script src="<?php echo e(asset('concept/assets/vendor/charts/morris-bundle/morris.js')); ?>"></script>
    <!-- chart c3 js -->
    <script src="<?php echo e(asset('concept/assets/vendor/charts/c3charts/c3.min.js')); ?>"></script>
    <script src="<?php echo e(asset('concept/assets/vendor/charts/c3charts/d3-5.4.0.min.js')); ?>"></script>
    <script src="<?php echo e(asset('concept/assets/vendor/charts/c3charts/C3chartjs.js')); ?>"></script>
    <script src="<?php echo e(asset('concept/assets/libs/js/dashboard-ecomerece.js')); ?>"></script>
    <!-- bootstrap notify js -->
    <script src="<?php echo e(asset('bootstrap-notify.js')); ?>"></script>

    <?php echo $__env->yieldContent('extraScripts'); ?>
</body>
 
</html>
<?php /**PATH /var/www/html/resources/views/admin/layouts/main.blade.php ENDPATH**/ ?>
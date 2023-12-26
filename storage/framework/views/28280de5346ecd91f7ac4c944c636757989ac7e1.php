<div class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav flex-column">
                    <li class="nav-divider">
                        Menu
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link <?php echo e(Route::is('quickmeet') ? 'active' : ''); ?>" href="<?php echo e(route('index')); ?>"><i class="fa fa-fw fa-user-circle"></i>Quick Meeting</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link <?php echo e(Route::is('quickmeet') ? 'active' : ''); ?>" href="<?php echo e(route('index')); ?>"><i class="fa fa-fw fa-user-circle"></i>Scheduled Meeting</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link <?php echo e(Route::is('listMeet') ? 'active' : ''); ?>" href="<?php echo e(route('listMeet')); ?>"><i class="fa fa-fw fa-user-circle"></i>List Meeting</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('attendance_taker')); ?>"><i class="fa fa-fw fa-rocket"></i>Work Attendance</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('attendance_taker')); ?>"><i class="fa fa-fw fa-rocket"></i>Surveillance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=""><i class="fas fa-fw fa-chart-pie"></i>Your Regconition Data</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<?php /**PATH /var/www/html/resources/views/admin/partials/sidebar.blade.php ENDPATH**/ ?>
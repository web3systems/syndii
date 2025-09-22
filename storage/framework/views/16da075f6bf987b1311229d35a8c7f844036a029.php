<!--Favicon -->
<link rel="icon" href="<?php echo e(URL::asset('uploads/logo/favicon.ico')); ?>" type="image/x-icon"/>

<!-- Bootstrap 5 -->
<link href="<?php echo e(URL::asset('plugins/bootstrap-5.0.2/css/bootstrap.min.css')); ?>" rel="stylesheet">

<!-- Icons -->
<link href="<?php echo e(theme_url('css/icons.css')); ?>" rel="stylesheet" />

<!-- P-scrollbar -->
<link href="<?php echo e(URL::asset('plugins/p-scrollbar/p-scrollbar.css')); ?>" rel="stylesheet" />

<!-- Simplebar -->
<link href="<?php echo e(URL::asset('plugins/simplebar/css/simplebar.css')); ?>" rel="stylesheet">

<!-- Tippy -->
<link href="<?php echo e(URL::asset('plugins/tippy/scale-extreme.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(URL::asset('plugins/tippy/material.css')); ?>" rel="stylesheet" />

<!-- Toastr -->
<link href="<?php echo e(URL::asset('plugins/toastr/toastr.min.css')); ?>" rel="stylesheet" />

<link href="<?php echo e(URL::asset('plugins/awselect/awselect.min.css')); ?>" rel="stylesheet" />

<?php echo $__env->yieldContent('css'); ?>

<?php
    $scss_path = 'resources/views/' . get_theme() . '/scss/dashboard.scss';
?>

<!-- All Styles -->
<?php echo app('Illuminate\Foundation\Vite')($scss_path); ?>



	<?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/dashboard/header.blade.php ENDPATH**/ ?>
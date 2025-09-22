<!--Favicon -->
<link rel="icon" href="<?php echo e(URL::asset('uploads/logo/favicon.ico')); ?>" type="image/x-icon"/>

<!-- Animate -->
<link href="<?php echo e(theme_url('css/animated.css')); ?>" rel="stylesheet" />

<!-- Bootstrap 5 -->
<link href="<?php echo e(URL::asset('plugins/bootstrap-5.0.2/css/bootstrap.min.css')); ?>" rel="stylesheet">

<!-- Icons -->
<link href="<?php echo e(theme_url('css/icons.css')); ?>" rel="stylesheet" />

<!-- Toastr -->
<link href="<?php echo e(URL::asset('plugins/toastr/toastr.min.css')); ?>" rel="stylesheet" />

<link href="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.css" rel="stylesheet" />


<?php echo $__env->yieldContent('css'); ?>

<!--Custom User CSS File -->
<?php if(isset($frontend_settings)): ?>
    <?php if(!is_null($frontend_settings->custom_css_url)): ?> <link href="<?php echo e($frontend_settings->custom_css_url); ?>" rel="stylesheet"> <?php endif; ?>
<?php endif; ?>




	<?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/frontend/header.blade.php ENDPATH**/ ?>
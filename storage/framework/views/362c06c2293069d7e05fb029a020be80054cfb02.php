<?php
	$themeClass = '';
	if (!empty($_COOKIE['theme'])) {
		if ($_COOKIE['theme'] == 'dark') {
			$themeClass = 'dark-theme';
		} else if ($_COOKIE['theme'] == 'light') {
			$themeClass = 'light-theme';
		}  
	} elseif (empty($_COOKIE['theme'])) {
		$themeClass = auth()->user()->theme;
		setcookie('theme', $themeClass);
	} else {
		$themeClass = config('settings.default_theme');
		setcookie('theme', $themeClass);
	}
?>
<!DOCTYPE html>
<html lang="<?php echo e(LaravelLocalization::getCurrentLocale()); ?>"
dir="<?php echo e(LaravelLocalization::getCurrentLocaleDirection()); ?>">
	<head>
		<!-- METADATA -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta content="" name="description">
		<meta content="" name="author">
		<meta name="keywords" content=""/>
		
        <!-- CSRF TOKEN -->
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <!-- TITLE -->
        <title><?php echo e(config('app.name', 'DaVinci AI')); ?></title>
        
        <?php echo $__env->make('layouts.dashboard.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

	</head>

	<body class="app sidebar-mini <?php echo $themeClass; ?> <?php echo e(LaravelLocalization::getCurrentLocaleDirection()); ?>">

		<div id="loader-line" class="hidden"></div>

		<!-- PAGE -->
		<div class="page">
			<div class="page-main" style="poisition: relative;">

				<?php echo $__env->make('layouts.dashboard.nav-aside', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				<!-- APP CONTENT -->			
				<div class="app-content main-content" style="padding-bottom: 4rem">
					<div class="side-app">

						<?php echo $__env->make('layouts.dashboard.nav-top', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

						<?php echo $__env->yieldContent('page-header'); ?>

						<?php echo $__env->yieldContent('content'); ?>						

						
                    </div>   					
					
                </div>				
                <!-- END APP CONTENT -->
				
				<?php echo $__env->make('layouts.dashboard.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            </div>		
        </div><!-- END PAGE -->
        
	</body>
</html>


<?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/app.blade.php ENDPATH**/ ?>
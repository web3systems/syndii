<?php
	$themeClass = '';
	if (!empty($_COOKIE['theme'])) {
		if ($_COOKIE['theme'] == 'dark') {
			$themeClass = 'dark-theme';
		} else if ($_COOKIE['theme'] == 'light') {
			$themeClass = 'light-theme';
		}  
	} elseif (empty($_COOKIE['theme'])) {
		$themeClass = config('settings.default_theme');
		setcookie('theme', $themeClass);
	} 
?>
<!DOCTYPE html>
<html lang="<?php echo e(LaravelLocalization::getCurrentLocale()); ?>"
dir="<?php echo e(LaravelLocalization::getCurrentLocaleDirection()); ?>">
	<head>
		<!-- Meta data -->
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">  
        <meta name="robots" content="index, follow">	   
        <meta name="revisit-after" content="7 days">	   
        <meta name="distribution" content="web">	
		
		<?php echo $__env->yieldContent('metadata'); ?>
		
        <!-- CSRF Token -->
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

		<?php echo $__env->make('layouts.frontend.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<?php
			$scss_path = 'resources/views/' . get_theme() . '/scss/frontend.scss';
		?>

		<!-- All Styles -->
		<?php echo app('Illuminate\Foundation\Vite')($scss_path); ?>

	</head>

	<body class="app sidebar-mini white-background <?php echo $themeClass; ?>">

		<div id="loader-line" class="hidden"></div>

		<!-- Page -->
		<div class="page">
			<div class="page-main">
				
				<!-- App-Content -->			
				<div class="main-content">
					<div class="side-app">

						<?php echo $__env->yieldContent('content'); ?>

					</div>                   
				</div>
		
		</div><!-- End Page -->

		<?php echo $__env->make('layouts.frontend.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
	</body>
</html>


<?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/auth.blade.php ENDPATH**/ ?>
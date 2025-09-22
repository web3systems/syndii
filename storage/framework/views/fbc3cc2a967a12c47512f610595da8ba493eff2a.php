<?php if(config('frontend.custom_url.status') == 'on'): ?>
    <script type="text/javascript">
		window.location.href = "<?php echo e(config('frontend.custom_url.link')); ?>"
	</script>
<?php else: ?>

<!DOCTYPE html>
<html lang="<?php echo e(LaravelLocalization::getCurrentLocale()); ?>" dir="<?php echo e(LaravelLocalization::getCurrentLocaleDirection()); ?>">
	<head>
		<!-- Meta Data -->
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

		<!--Google AdSense-->
		<?php echo adsense_header(); ?>


		<!--Custom Header JS Code-->
		<?php if($frontend_settings): ?>
			<?php if(!is_null($frontend_settings->custom_header_code)): ?> 
				<?php echo $frontend_settings->custom_header_code; ?>

			<?php endif; ?>
		<?php endif; ?>
	</head>

	<body class="app sidebar-mini frontend-body <?php echo e(Request::path() != '/' ? 'blue-background' : ''); ?>">

		<?php if($extension->maintenance_feature): ?>
			
			<div class="container">
				<div class="row text-center h-100vh align-items-center">
					<div class="col-md-12">
						<img src="<?php echo e(theme_url($extension->maintenance_banner)); ?>" alt="Maintenance Image">
						<h2 class="mt-4 font-weight-bold"><?php echo e(__($extension->maintenance_header)); ?></h2>
						<h5><?php echo e(__($extension->maintenance_message)); ?> </h5>						
					</div>					
				</div>
				<footer class="text-center  align-items-center">
					<p class="text-muted"><?php echo e(__($extension->maintenance_footer)); ?> </p>
				</footer>
			</div>
		<?php else: ?>

			<?php if(config('frontend.frontend_page') == 'on'): ?>
						
				<div class="page">
					<div class="page-main">
						<section id="main">					
							<div class="relative flex items-top justify-center min-h-screen">				
								<div class="container-fluid fixed-top pl-0 pr-0" id="navbar-container">
									
									<?php echo $__env->yieldContent('menu'); ?>
				
								</div>				
							</div>  
						</section>

		
						<div class="main-content">
							<div class="side-app frontend-background">

								<?php echo $__env->yieldContent('content'); ?>

							</div>                   
						</div>
					</div>				
				</div>
			
				<!-- FOOTER SECTION
				========================================================-->
				<?php echo $__env->yieldContent('footer'); ?>
				

			<?php endif; ?>
		
		<?php endif; ?>

		<?php echo $__env->make('layouts.frontend.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<!--Custom Body JS Code-->
		<?php if($frontend_settings): ?>
			<?php if(!is_null($frontend_settings->custom_body_code)): ?> 
				<?php echo $frontend_settings->custom_body_code; ?>

			<?php endif; ?>
		<?php endif; ?>

	</body>
</html>

<?php endif; ?><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/frontend.blade.php ENDPATH**/ ?>
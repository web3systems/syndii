<?php $__env->startSection('metadata'); ?>
    <meta name="description" content="<?php echo e(__($metadata->home_description)); ?>">
    <meta name="keywords" content="<?php echo e(__($metadata->home_keywords)); ?>">
    <meta name="author" content="<?php echo e(__($metadata->home_author)); ?>">	    
    <link rel="canonical" href="<?php echo e($metadata->home_url); ?>">
    <title><?php echo e(__($metadata->home_title)); ?></title>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('plugins/slick/slick.css')); ?>" rel="stylesheet" />
	<link href="<?php echo e(URL::asset('plugins/slick/slick-theme.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(URL::asset('plugins/aos/aos.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(URL::asset('plugins/animatedheadline/jquery.animatedheadline.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('menu'); ?>
    <?php echo $__env->make('frontend.menu.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <!-- SECTION - MAIN BANNER
    ========================================================-->
    <?php echo $__env->make('frontend.banner.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <!-- SECTION - STEPS
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->how_it_works_status == 1, 'frontend.how_it_works.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
    

    <!-- SECTION - TOOLS
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->tools_status == 1, 'frontend.tools.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
    

    <!-- SECTION - INFO BANNER
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->info_status == 1, 'frontend.info.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>


    <!-- SECTION - TEMPLATES
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->templates_status == 1, 'frontend.templates.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>


    <!-- SECTION - FEATURES
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->features_status == 1, 'frontend.features.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>


    <!-- SECTION - IMAGES BANNER
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->images_status == 1, 'frontend.images.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
    

    <!-- SECTION - PRICING
    ========================================================-->
    <?php if(App\Services\HelperService::extensionSaaS()): ?>
        <?php echo $__env->renderWhen($frontend_sections->pricing_status == 1, 'frontend.pricing.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
    <?php endif; ?>


        <!-- SECTION - CLIENTS
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->clients_status == 1, 'frontend.clients.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>


    <!-- SECTION - REVIEWS
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->reviews_status == 1, 'frontend.reviews.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>


    <!-- SECTION - FAQ
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->faq_status == 1, 'frontend.faq.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>


    <!-- SECTION - BLOGS
    ========================================================-->
    <?php echo $__env->renderWhen($frontend_sections->blogs_status == 1, 'frontend.blogs.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
    <?php echo $__env->make('frontend.footer.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(URL::asset('plugins/slick/slick.min.js')); ?>"></script>  
    <script src="<?php echo e(URL::asset('plugins/aos/aos.js')); ?>"></script> 
    <script src="<?php echo e(URL::asset('plugins/animatedheadline/jquery.animatedheadline.min.js')); ?>"></script> 
    <script src="<?php echo e(theme_url('js/frontend.js')); ?>"></script> 
    <script type="text/javascript">
		$(function () {

            $('.word-container').animatedHeadline({
                animationType: "slide",
                animationDelay: 2500,
                barAnimationDelay: 3800,
                barWaiting: 800,
                lettersDelay: 50,
                typeLettersDelay: 150,
                selectionDuration: 500,
                typeAnimationDelay: 1300,
                revealDuration: 600,
                revealAnimationDelay: 1500
            });

            AOS.init();

		});    
    </script>
<?php $__env->stopSection(); ?>
        
        
       
        
       
    


<?php echo $__env->make('layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/home.blade.php ENDPATH**/ ?>
<footer id="welcome-footer" >

    <div class="container-fluid" id="curve-container">
        <div class="curve-box">
            <div class="overflow-hidden">
                <svg class="curve" preserveAspectRatio="none" width="1440" height="86" viewBox="0 0 1440 86" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 85.662C240 29.1253 480 0.857 720 0.857C960 0.857 1200 29.1253 1440 85.662V0H0V85.662Z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- FOOTER MAIN CONTENT -->
    <div id="footer" class="container text-center">

        <div class="row">
            <div class="col-sm-12 mb-6 mt-6">
                <h1><?php echo e(__('Save time. Get Started Now.')); ?></h1>
                <h3><?php echo e(__('Unleash the most advanced AI creator')); ?></h3>
                <h3><?php echo e(__('and boost your productivity')); ?></h3>
            </div>
        </div>
                
        <div class="row"> 
            <div class="col-sm-12">	
                <div>
                    <img src="<?php echo e(URL::asset($settings->logo_frontend_footer)); ?>" alt="Brand Logo">									
                </div>

                <div class="mb-7">
                    <span class="notification fs-12 mr-2"><?php echo e(__('Try for free')); ?>.</span><span class="notification fs-12"><?php echo e(__('No credit card required')); ?></span>
                </div>
                    
                                                                            
            </div>							
        </div>

        <div class="row"> 
            <div class="col-sm-12 d-flex justify-content-center">	
                <?php if($custom_pages): ?>
                    <?php $__currentLoopData = $custom_pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page->show_footer_nav): ?>
                            <div class="flex mr-6">
                                <a class="footer-link" href="<?php echo e(url('/')); ?>/page/<?php echo e($page->slug); ?>"><?php echo e(__($page->title)); ?></a>
                            </div>
                        <?php endif; ?>  
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>				
                <?php if($frontend_sections->contact_status): ?>													
                    <div class="flex">
                        <a class="footer-link" href="<?php echo e(route('contact')); ?>"><?php echo e(__('Contact Us')); ?></a>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>

    </div> <!-- END CONTAINER-->

    <!-- COPYRIGHT INFORMATION -->
    <div id="copyright" class="container pl-0 pr-0">	
        <div class="row no-gutters text-center">
            <div class="col-sm-12 d-flex justify-content-center">
                <ul id="footer-icons" class="list-inline">
                    <?php if(config('frontend.social_linkedin')): ?>
                        <a href="<?php echo e(config('frontend.social_linkedin')); ?>" target="_blank"><li class="list-inline-item"><i class="footer-icon fa-brands fa-linkedin"></i></li></a>
                    <?php endif; ?>
                    <?php if(config('frontend.social_twitter')): ?>
                        <a href="<?php echo e(config('frontend.social_twitter')); ?>" target="_blank"><li class="list-inline-item">
                            <svg class="twitter-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16">
                                <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z"/>
                            </svg></li>
                        </a>
                    <?php endif; ?>
                    <?php if(config('frontend.social_instagram')): ?>
                        <a href="<?php echo e(config('frontend.social_instagram')); ?>" target="_blank"><li class="list-inline-item"><i class="footer-icon fa-brands fa-instagram"></i></li></a>
                    <?php endif; ?>
                    <?php if(config('frontend.social_facebook')): ?>
                        <a href="<?php echo e(config('frontend.social_facebook')); ?>" target="_blank"><li class="list-inline-item"><i class="footer-icon fa-brands fa-facebook"></i></li></a>
                    <?php endif; ?>	
                    <?php if(config('frontend.social_youtube')): ?>
                        <a href="<?php echo e(config('frontend.social_youtube')); ?>" target="_blank"><li class="list-inline-item"><i class="footer-icon fa-brands fa-youtube"></i></li></a>
                    <?php endif; ?>										
                </ul>
            </div>

            <div class="col-sm-12 justify-content-center mt-5 mb-4">
                <p id="frontend-copyright">© <?php echo e(date("Y")); ?> <a href="<?php echo e(config('app.url')); ?>"><?php echo e(config('app.name')); ?></a>. <?php echo e(__('All rights reserved')); ?>.</p>
            </div>
        </div>
    

        <!-- SCROLL TO TOP -->
        <a href="#top" id="back-to-top"><i class="fa fa-angle-double-up"></i></a>

    </div>
    
</footer> <?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/frontend/footer/section.blade.php ENDPATH**/ ?>
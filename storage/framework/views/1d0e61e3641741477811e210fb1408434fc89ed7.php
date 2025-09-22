<section id="benefits-wrapper">

    <div class="container pt-9"> 
        
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 mb-5" data-aos="fade-right" data-aos-delay="100" data-aos-once="true" data-aos-duration="400">                        
                <div class="title">
                    <p class="m-2"><?php echo e(__($frontend_sections->features_subtitle)); ?></p>
                    <h3><?php echo __($frontend_sections->features_title); ?></h3>    
                    <h6 class="font-weight-normal fs-14 mb-4"><?php echo e(__($frontend_sections->features_description)); ?></h6>                    
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary-frontend-small btn-frontend-scroll-effect mb-2">
                        <div>
                            <span><?php echo e(__('Try Creating for Free')); ?></span>
                            <span><?php echo e(__('Try Creating for Free')); ?></span>
                        </div>
                    </a>
                </div>                                               
            </div>

            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-5" data-aos="zoom-in" data-aos-delay="<?php echo e((200 * $feature->id)/2); ?>" data-aos-once="true" data-aos-duration="500">
                    <div class="benefits-box-wrapper text-center">
                        <div class="benefit-box">
                            <div class="benefit-image">
                                <img src="<?php echo e(theme_url($feature->image)); ?>" alt="">
                            </div>
                            <div class="benefit-title">
                                <h6><?php echo __($feature->title); ?></h6>
                            </div>
                            <div class="benefit-description">
                                <p><?php echo __($feature->description); ?></p>
                            </div>
                        </div>
                    </div>                        
                </div> 
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                       
        </div>
    </div>

</section><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/frontend/features/section.blade.php ENDPATH**/ ?>
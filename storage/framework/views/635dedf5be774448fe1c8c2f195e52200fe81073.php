<section id="steps-wrapper">

    <div class="container pt-9 text-center">

        <!-- SECTION TITLE -->
        <div class="row mb-6">
            <div class="title">
                <p class="m-2"><?php echo e(__($frontend_sections->how_it_works_subtitle)); ?></p>
                <h3><?php echo __($frontend_sections->how_it_works_title); ?></h3>                         
            </div>
        </div> <!-- END SECTION TITLE --> 
                      
    </div> <!-- END CONTAINER -->

    <div class="container">

        <div class="row">
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 col-md-12 col-sm-12" data-aos="fade-up" data-aos-delay="<?php echo e(100 * $step->order); ?>" data-aos-once="true" data-aos-duration="400">
                    <div class="steps-box-wrapper">
                        <div class="steps-box">
                            <div class="step-number-big">
                                <p><?php echo e($step->order); ?></p>
                            </div>
                            <div class="step-number">
                                <h6><?php echo e(__('Step')); ?> <?php echo e($step->order); ?></h6>
                            </div>
                            <div class="step-title">
                                <h2><?php echo e(__($step->title)); ?></h2>
                            </div>
                            <div class="step-description">
                                <p><?php echo __($step->description); ?></p>
                            </div>
                        </div>
                    </div>                        
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

    </div>
    
</section><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/frontend/how_it_works/section.blade.php ENDPATH**/ ?>
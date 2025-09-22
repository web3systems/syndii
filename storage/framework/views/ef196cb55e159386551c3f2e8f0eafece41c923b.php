<section id="features">

    <?php echo adsense_frontend_features_728x90(); ?>

    
    <div class="container pt-7 text-center">

        <!-- SECTION TITLE -->
        <div class="row mb-6">
            <div class="title">
                <p class="m-2"><?php echo e(__($frontend_sections->tools_subtitle)); ?></p>
                <h3><?php echo __($frontend_sections->tools_title); ?></h3>                        
            </div>
        </div> <!-- END SECTION TITLE --> 
                        
    </div> <!-- END CONTAINER -->


    <div class="container">    
        
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12" data-aos="zoom-in" data-aos-delay="100" data-aos-once="true" data-aos-duration="400">                
                <div class="features-nav-menu">
                    <div class="features-nav-menu-inner">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($tool->status): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?php if($loop->first): ?> active <?php endif; ?>" id="<?php echo e($tool->tool_code); ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo e($tool->tool_code); ?>" type="button" role="tab" aria-controls="<?php echo e($tool->tool_code); ?>" aria-selected="true"><?php echo e(__($tool->tool_name)); ?></button>
                                    </li>
                                <?php endif; ?>                                            
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                    
                        </ul>
                    </div>
                </div>					
            </div>
    
            <div class="col-lg-12 col-md-12 col-sm-12 ">
                <div class="pt-6">
                    <div class="features-panel">
    
                        <div class="tab-content" id="myTabContent">
    
                            <?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <div class="tab-pane fade  <?php if($loop->first): ?> show active <?php endif; ?>" id="<?php echo e($tool->tool_code); ?>" role="tabpanel" aria-labelledby="<?php echo e($tool->tool_code); ?>">  
                                    <div class="row features-outer-wrapper">

                                        <div class="col-lg-6 col-md-6 col-sm-12 pl-6 pr-6 align-middle" data-aos="fade-right" data-aos-delay="200" data-aos-once="true" data-aos-duration="500">                                                    
                                            <div class="features-inner-wrapper text-center">                                                                   
                                            
                                                <div class="feature-title">
                                                    <h6 class="fs-12 mb-5"><i class="fa-solid mr-2 <?php echo e($tool->title_icon); ?>"></i><?php echo e(__($tool->title_meta)); ?></h6>
                                                    <h4 class="mb-5 fs-30"><?php echo __($tool->title); ?></h4>                                                            
                                                </div>	

                                                <div class="feature-description">
                                                    <p class="mb-6"><?php echo __($tool->description); ?></p>
                                                </div>                                                            
                                            </div>                                                                                                  						
                                        </div>	

                                        <div class="col-lg-6 col-md-6 col-sm-12" data-aos="fade-left" data-aos-delay="300" data-aos-once="true" data-aos-duration="600">
                                            <div class="feature-image-wrapper">
                                                <img src="<?php echo e(theme_url($tool->image)); ?>" alt="">
                                            </div>
                                            <div class="feature-footer text-center">
                                                <p class="fs-12 text-muted"><?php echo e(__($tool->image_footer)); ?></p>
                                            </div>
                                        </div>
        
                                    </div>	
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                        </div>                                    
                    </div>
                </div>
            </div>
    
        </div>            

    </div>

</section>
        <?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/frontend/tools/section.blade.php ENDPATH**/ ?>
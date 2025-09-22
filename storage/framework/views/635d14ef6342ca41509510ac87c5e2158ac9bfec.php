<section id="templates-wrapper">

    <?php echo adsense_frontend_features_728x90(); ?>

    

    <div class="container pt-9 text-center">

        <!-- SECTION TITLE -->
        <div class="row mb-6">
            <div class="title">
                <p class="m-2"><?php echo e(__($frontend_sections->features_subtitle)); ?></p>
                <h3><?php echo __($frontend_sections->features_title); ?></h3>                        
            </div>
        </div> <!-- END SECTION TITLE --> 
                      
    </div> <!-- END CONTAINER -->

    <div class="container">    
            
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12" data-aos="zoom-in" data-aos-delay="100" data-aos-once="true" data-aos-duration="400">                
                <div class="templates-nav-menu">
                    <div class="template-nav-menu-inner">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true"><?php echo e(__('All Templates')); ?></button>
                            </li>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(strtolower($category->name) != 'other'): ?>
                                    <li class="nav-item category-check" role="presentation">
                                        <button class="nav-link" id="<?php echo e($category->code); ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo e($category->code); ?>" type="button" role="tab" aria-controls="<?php echo e($category->code); ?>" aria-selected="false"><?php echo e(__($category->name)); ?></button>
                                    </li>
                                <?php endif; ?>									
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(strtolower($category->name) == 'other'): ?>
                                <li class="nav-item category-check" role="presentation">
                                    <button class="nav-link" id="<?php echo e($category->code); ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo e($category->code); ?>" type="button" role="tab" aria-controls="<?php echo e($category->code); ?>" aria-selected="false"><?php echo e(__($category->name)); ?></button>
                                </li>
                            <?php endif; ?>									
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>				
                        </ul>
                    </div>
                </div>					
            </div>
    
            <div class="col-lg-12 col-md-12 col-sm-12 ">
                <div class="pt-2">
                    <div class="favorite-templates-panel show-templates">
    
                        <div class="tab-content" id="myTabContent">
    
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="row templates-panel">
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(strtolower($category->name) != 'other'): ?>
                                            <div class="col-12 templates-panel-group <?php if($loop->first): ?> <?php else: ?>  mt-3 <?php endif; ?>">
                                                <h6 class="fs-14 font-weight-bold text-muted"><?php echo e(__($category->name)); ?></h6>
                                                <h4 class="fs-12 text-muted"><?php echo e(__($category->description)); ?></h4>
                                            </div>						
                    
                                            <?php $__currentLoopData = $other_templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($template->group == $category->code): ?>
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="template">                                                                        
                                                            <div class="card <?php if($template->package == 'professional'): ?> professional <?php elseif($template->package == 'premium'): ?> premium <?php elseif($template->favorite): ?> favorite <?php endif; ?>" id="<?php echo e($template->template_code); ?>-card" onclick="window.location.href='<?php echo e(url('app/user/templates/original-template')); ?>/<?php echo e($template->slug); ?>'">
                                                                <div class="card-body pt-5">
                                                                    <div class="template-icon mb-4">
                                                                        <?php echo $template->icon; ?>												
                                                                    </div>
                                                                    <div class="template-title">
                                                                        <h6 class="mb-2 fs-15 number-font"><?php echo e(__($template->name)); ?></h6>
                                                                    </div>
                                                                    <div class="template-info">
                                                                        <p class="fs-13 text-muted mb-2"><?php echo e(__($template->description)); ?></p>
                                                                    </div>
                                                                    <?php if($template->package == 'professional'): ?> 
                                                                        <p class="fs-8 btn btn-pro mb-0"><i class="fa-sharp fa-solid fa-crown mr-2"></i><?php echo e(__('Pro')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-pro"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'free'): ?>
                                                                        <p class="fs-8 btn btn-free mb-0"><i class="fa-sharp fa-solid fa-gift mr-2"></i><?php echo e(__('Free')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-free"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'premium'): ?>
                                                                        <p class="fs-8 btn btn-yellow mb-0"><i class="fa-sharp fa-solid fa-gem mr-2"></i><?php echo e(__('Premium')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-premium"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->new): ?>
                                                                        <span class="fs-8 btn btn-new mb-0"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></span>
                                                                    <?php endif; ?>		
                                                                </div>
                                                            </div>
                                                        </div>							
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
                                            
                                            <?php $__currentLoopData = $custom_templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($template->group == $category->code): ?>
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="template">                                                                       
                                                            <div class="card <?php if($template->package == 'professional'): ?> professional <?php elseif($template->package == 'premium'): ?> premium <?php elseif($template->favorite): ?> favorite <?php endif; ?>" id="<?php echo e($template->template_code); ?>-card" onclick="window.location.href='<?php echo e(url('app/user/templates')); ?>/<?php echo e($template->slug); ?>/<?php echo e($template->template_code); ?>'">
                                                                <div class="card-body pt-5">
                                                                    <div class="template-icon mb-4">
                                                                        <?php echo $template->icon; ?>												
                                                                    </div>
                                                                    <div class="template-title">
                                                                        <h6 class="mb-2 fs-15 number-font"><?php echo e(__($template->name)); ?></h6>
                                                                    </div>
                                                                    <div class="template-info">
                                                                        <p class="fs-13 text-muted mb-2"><?php echo e(__($template->description)); ?></p>
                                                                    </div>
                                                                    <?php if($template->package == 'professional'): ?> 
                                                                        <p class="fs-8 btn btn-pro mb-0"><i class="fa-sharp fa-solid fa-crown mr-2"></i><?php echo e(__('Pro')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-pro"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'free'): ?>
                                                                        <p class="fs-8 btn btn-free mb-0"><i class="fa-sharp fa-solid fa-gift mr-2"></i><?php echo e(__('Free')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-free"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'premium'): ?>
                                                                        <p class="fs-8 btn btn-yellow mb-0"><i class="fa-sharp fa-solid fa-gem mr-2"></i><?php echo e(__('Premium')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-premium"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->new): ?>
                                                                        <span class="fs-8 btn btn-new mb-0"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></span>
                                                                    <?php endif; ?>	
                                                                </div>
                                                            </div>
                                                        </div>							
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>	
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>		
    
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(strtolower($category->name) == 'other'): ?>
                                            <div class="col-12 templates-panel-group <?php if($loop->first): ?> <?php else: ?>  mt-3 <?php endif; ?>">
                                                <h6 class="fs-14 font-weight-bold text-muted"><?php echo e(__($category->name)); ?></h6>
                                                <h4 class="fs-12 text-muted"><?php echo e(__($category->description)); ?></h4>
                                            </div>					
                    
                                            <?php $__currentLoopData = $other_templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($template->group == $category->code): ?>
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="template">                                                                        
                                                            <div class="card <?php if($template->package == 'professional'): ?> professional <?php elseif($template->package == 'premium'): ?> premium <?php elseif($template->favorite): ?> favorite <?php endif; ?>" id="<?php echo e($template->template_code); ?>-card" onclick="window.location.href='<?php echo e(url('app/user/templates/original-template')); ?>/<?php echo e($template->slug); ?>'">
                                                                <div class="card-body pt-5">
                                                                    <div class="template-icon mb-4">
                                                                        <?php echo $template->icon; ?>												
                                                                    </div>
                                                                    <div class="template-title">
                                                                        <h6 class="mb-2 fs-15 number-font"><?php echo e(__($template->name)); ?></h6>
                                                                    </div>
                                                                    <div class="template-info">
                                                                        <p class="fs-13 text-muted mb-2"><?php echo e(__($template->description)); ?></p>
                                                                    </div>
                                                                    <?php if($template->package == 'professional'): ?> 
                                                                        <p class="fs-8 btn btn-pro mb-0"><i class="fa-sharp fa-solid fa-crown mr-2"></i><?php echo e(__('Pro')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-pro"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'free'): ?>
                                                                        <p class="fs-8 btn btn-free mb-0"><i class="fa-sharp fa-solid fa-gift mr-2"></i><?php echo e(__('Free')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-free"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'premium'): ?>
                                                                        <p class="fs-8 btn btn-yellow mb-0"><i class="fa-sharp fa-solid fa-gem mr-2"></i><?php echo e(__('Premium')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-premium"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->new): ?>
                                                                        <span class="fs-8 btn btn-new mb-0"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></span>
                                                                    <?php endif; ?>	
                                                                </div>
                                                            </div>
                                                        </div>							
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
                                            
                                            <?php $__currentLoopData = $custom_templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($template->group == $category->code): ?>
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="template">                                                                      
                                                            <div class="card <?php if($template->package == 'professional'): ?> professional <?php elseif($template->package == 'premium'): ?> premium <?php elseif($template->favorite): ?> favorite <?php endif; ?>" id="<?php echo e($template->template_code); ?>-card" onclick="window.location.href='<?php echo e(url('app/user/templates')); ?>/<?php echo e($template->slug); ?>/<?php echo e($template->template_code); ?>'">
                                                                <div class="card-body pt-5">
                                                                    <div class="template-icon mb-4">
                                                                        <?php echo $template->icon; ?>												
                                                                    </div>
                                                                    <div class="template-title">
                                                                        <h6 class="mb-2 fs-15 number-font"><?php echo e(__($template->name)); ?></h6>
                                                                    </div>
                                                                    <div class="template-info">
                                                                        <p class="fs-13 text-muted mb-2"><?php echo e(__($template->description)); ?></p>
                                                                    </div>
                                                                    <?php if($template->package == 'professional'): ?> 
                                                                        <p class="fs-8 btn btn-pro mb-0"><i class="fa-sharp fa-solid fa-crown mr-2"></i><?php echo e(__('Pro')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-pro"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'free'): ?>
                                                                        <p class="fs-8 btn btn-free mb-0"><i class="fa-sharp fa-solid fa-gift mr-2"></i><?php echo e(__('Free')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-free"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->package == 'premium'): ?>
                                                                        <p class="fs-8 btn btn-yellow mb-0"><i class="fa-sharp fa-solid fa-gem mr-2"></i><?php echo e(__('Premium')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-premium"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                    <?php elseif($template->new): ?>
                                                                        <span class="fs-8 btn btn-new mb-0"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></span>
                                                                    <?php endif; ?>	
                                                                </div>
                                                            </div>
                                                        </div>							
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>	
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
                                </div>	
                            </div>
    
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="tab-pane fade" id="<?php echo e($category->code); ?>" role="tabpanel" aria-labelledby="<?php echo e($category->code); ?>-tab">
                                    <div class="row templates-panel">
                
                                        <?php $__currentLoopData = $other_templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($template->group == $category->code): ?>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="template">                                                                    
                                                        <div class="card <?php if($template->package == 'professional'): ?> professional <?php elseif($template->package == 'premium'): ?> premium <?php elseif($template->favorite): ?> favorite <?php endif; ?>" id="<?php echo e($template->template_code); ?>-card" onclick="window.location.href='<?php echo e(url('app/user/templates/original-template')); ?>/<?php echo e($template->slug); ?>'">
                                                            <div class="card-body pt-5">
                                                                <div class="template-icon mb-4">
                                                                    <?php echo $template->icon; ?>												
                                                                </div>
                                                                <div class="template-title">
                                                                    <h6 class="mb-2 fs-15 number-font"><?php echo e(__($template->name)); ?></h6>
                                                                </div>
                                                                <div class="template-info">
                                                                    <p class="fs-13 text-muted mb-2"><?php echo e(__($template->description)); ?></p>
                                                                </div>
                                                                <?php if($template->package == 'professional'): ?> 
                                                                    <p class="fs-8 btn btn-pro mb-0"><i class="fa-sharp fa-solid fa-crown mr-2"></i><?php echo e(__('Pro')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-pro"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                <?php elseif($template->package == 'free'): ?>
                                                                    <p class="fs-8 btn btn-free mb-0"><i class="fa-sharp fa-solid fa-gift mr-2"></i><?php echo e(__('Free')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-free"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                <?php elseif($template->package == 'premium'): ?>
                                                                    <p class="fs-8 btn btn-yellow mb-0"><i class="fa-sharp fa-solid fa-gem mr-2"></i><?php echo e(__('Premium')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-premium"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                <?php elseif($template->new): ?>
                                                                    <span class="fs-8 btn btn-new mb-0"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></span>
                                                                <?php endif; ?>	
                                                            </div>
                                                        </div>
                                                    </div>							
                                                </div>	
                                            <?php endif; ?>									
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>		
    
                                        <?php $__currentLoopData = $custom_templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($template->group == $category->code): ?>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="template">                                                                   
                                                        <div class="card <?php if($template->package == 'professional'): ?> professional <?php elseif($template->package == 'premium'): ?> premium <?php elseif($template->favorite): ?> favorite <?php endif; ?>" id="<?php echo e($template->template_code); ?>-card" onclick="window.location.href='<?php echo e(url('app/user/templates')); ?>/<?php echo e($template->slug); ?>/<?php echo e($template->template_code); ?>'">
                                                            <div class="card-body pt-5">
                                                                <div class="template-icon mb-4">
                                                                    <?php echo $template->icon; ?>												
                                                                </div>
                                                                <div class="template-title">
                                                                    <h6 class="mb-2 fs-15 number-font"><?php echo e(__($template->name)); ?></h6>
                                                                </div>
                                                                <div class="template-info">
                                                                    <p class="fs-13 text-muted mb-2"><?php echo e(__($template->description)); ?></p>
                                                                </div>
                                                                <?php if($template->package == 'professional'): ?> 
                                                                    <p class="fs-8 btn btn-pro mb-0"><i class="fa-sharp fa-solid fa-crown mr-2"></i><?php echo e(__('Pro')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-pro"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                <?php elseif($template->package == 'free'): ?>
                                                                    <p class="fs-8 btn btn-free mb-0"><i class="fa-sharp fa-solid fa-gift mr-2"></i><?php echo e(__('Free')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-free"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                <?php elseif($template->package == 'premium'): ?>
                                                                    <p class="fs-8 btn btn-yellow mb-0"><i class="fa-sharp fa-solid fa-gem mr-2"></i><?php echo e(__('Premium')); ?> <?php if($template->new): ?> <p class="fs-8 btn btn-new mb-0 btn-new-premium"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></p> <?php endif; ?></p> 
                                                                <?php elseif($template->new): ?>
                                                                    <span class="fs-8 btn btn-new mb-0"><i class="fa-sharp fa-solid fa-sparkles mr-2"></i><?php echo e(__('New')); ?></span>
                                                                <?php endif; ?>	
                                                            </div>
                                                        </div>
                                                    </div>							
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
                        
    
                        </div>
                        
                        <div class="show-templates-button">
                            <a href="#">
                                <span><?php echo e(__('Show More')); ?> <i class="ml-2 fs-10 fa-solid fa-chevrons-down"></i></span>
                                <span><?php echo e(__('Show Less')); ?> <i class="ml-2 fs-10 fa-solid fa-chevrons-up"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>


    </div>

</section><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/frontend/templates/section.blade.php ENDPATH**/ ?>
<div class="row no-gutters">
    <nav class="navbar navbar-expand-lg navbar-light w-100" id="navbar-responsive">
        <a class="navbar-brand" href="<?php echo e(url('/')); ?>"><img id="brand-img"  src="<?php echo e(URL::asset($settings->logo_frontend)); ?>" alt=""></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse section-links" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link scroll active" data="#main" href="<?php echo e(url('/')); ?>"><?php echo e(__('Home')); ?> <span class="sr-only">(current)</span></a>
                </li>	
                <?php if($frontend_sections->features_status): ?>
                    <li class="nav-item">
                        <a class="nav-link scroll" data="#features" href="<?php echo e(url('/')); ?>/#features"><?php echo e(__('Features')); ?></a>
                    </li>
                <?php endif; ?>	
                <?php if(App\Services\HelperService::extensionSaaS()): ?>
                    <?php if($frontend_sections->pricing_status): ?>
                        <li class="nav-item">
                            <a class="nav-link scroll" data="#prices" href="<?php echo e(url('/')); ?>/#prices"><?php echo e(__('Pricing')); ?></a>
                        </li>
                    <?php endif; ?>	
                <?php endif; ?>						
                <?php if($frontend_sections->faq_status): ?>
                    <li class="nav-item">
                        <a class="nav-link scroll" data="#faqs" href="<?php echo e(url('/')); ?>/#faqs"><?php echo e(__('FAQs')); ?></a>
                    </li>
                <?php endif; ?>	
                <?php if($frontend_sections->blogs_status): ?>
                    <li class="nav-item">
                        <a class="nav-link scroll" data="#blogs" href="<?php echo e(url('/')); ?>/#blogs"><?php echo e(__('Blogs')); ?></a>
                    </li>
                <?php endif; ?>	
                <?php if($custom_pages): ?>
                    <?php $__currentLoopData = $custom_pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page->show_main_nav): ?>
                            <li class="nav-item">
                                <a class="nav-link scroll" href="<?php echo e(url('/')); ?>/page/<?php echo e($page->slug); ?>"><?php echo e(__($page->title)); ?></a>
                            </li>
                        <?php endif; ?>                       
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>									
            </ul>    
            <?php if(Route::has('login')): ?>
            <div id="login-buttons" class="pr-4">
                <div class="dropdown header-languages" id="frontend-local">
                    <a class="icon" data-bs-toggle="dropdown">
                        <span class="header-icon fa-solid fa-globe mr-4 fs-15"></span>
                    </a>
                    <div class="dropdown-menu animated">
                        <div class="local-menu">
                            <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(in_array($localeCode, explode(',', $settings->languages))): ?>
                                    <a href="<?php echo e(LaravelLocalization::getLocalizedURL($localeCode, null, [], true)); ?>" class="dropdown-item d-flex pl-4" hreflang="<?php echo e($localeCode); ?>">
                                        <div>
                                            <span class="font-weight-normal fs-12"><?php echo e(ucfirst($properties['native'])); ?></span> <span class="fs-10 text-muted"><?php echo e($localeCode); ?></span>
                                        </div>
                                    </a>   
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('user.dashboard')); ?>" class="action-button dashboard-button pl-5 pr-5"><?php echo e(__('Dashboard')); ?></a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="" id="login-button"><?php echo e(__('Sign In')); ?></a>

                    <?php if(config('settings.registration') == 'enabled'): ?>
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>" class="ml-2 action-button register-button pl-5 pr-5"><?php echo e(__('Sign Up')); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>                
        </div>
    </nav>
</div><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/frontend/menu/page.blade.php ENDPATH**/ ?>
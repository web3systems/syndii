<!-- SIDE MENU BAR -->
<aside class="app-sidebar"> 
    <div class="app-sidebar__logo">
        <a class="header-brand" href="<?php echo e(url('/')); ?>">
            <img src="<?php echo e(URL::asset($settings->logo_dashboard)); ?>" class="header-brand-img desktop-lgo" alt="Dashboard Logo">
            <img src="<?php echo e(URL::asset($settings->logo_dashboard_collapsed)); ?>" class="header-brand-img mobile-logo" alt="Dashboard Logo">
        </a>
        <div class="app-sidebar__toggle" data-toggle="sidebar">
            <a class="open-toggle" href="#">
                <svg class="w-4 menu-toggle-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 6l-6 6l6 6"></path>
                  </svg>
            </a>
        </div>
    </div>
    <ul class="side-menu app-sidebar3">

        <?php
            $menuController = new \App\Http\Controllers\Admin\MenuController();
            $menuUserItems = $menuController->getUserMenu();
            $menuAdminItems = $menuController->getAdminMenu();
        ?>

        <?php $__currentLoopData = $menuUserItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($item['type'] == 'label'): ?>
                <?php if($loop->first): ?>
                    <li class="side-item side-item-category mt-3 mb-3"><?php echo e(__($item['label'])); ?></li>
                <?php else: ?>
                    <li class="side-item side-item-category mt-4 mb-3"><?php echo e(__($item['label'])); ?></li>
                <?php endif; ?>
            <?php elseif($item['type'] == 'divider'): ?>
                <hr class="w-90 text-center ml-auto mr-auto mt-3">
            <?php else: ?>
                <?php if($item['has_access']): ?>
                    <li class="slide">
                        <?php if(!empty($item['children'])): ?>
                            <a class="side-menu__item" data-toggle="slide" href="<?php echo e($item['url'] ?? '#'); ?>">
                                <span class="side-menu__icon <?php echo e($item['icon']); ?>"></span>                    
                                <span class="side-menu__label"><?php echo e(__($item['label'])); ?></span>
                                <?php if(!empty($item['badge_text'])): ?>
                                    <span class="badge badge-<?php echo e($item['badge_type'] ?? 'primary'); ?>"><?php echo e($item['badge_text']); ?></span>
                                <?php endif; ?>
                                <i class="angle fa fa-angle-right"></i>
                            </a>
                            <ul class="slide-menu">
                                <?php $__currentLoopData = $item['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e($child['route'] ? route($child['route']) : $child['url']); ?>" class="slide-item"><?php if(!is_null($child['icon'])): ?> <i class="slide-child-icon <?php echo e($child['icon']); ?>"></i> <?php endif; ?><?php echo e(__($child['label'])); ?></a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <a class="side-menu__item" href="<?php echo e($item['route'] ? route($item['route']) : $item['url']); ?>">
                                <span class="side-menu__icon <?php echo e($item['icon']); ?>"></span>                        
                                <span class="side-menu__label"><?php echo e(__($item['label'])); ?></span>
                                <?php if(!empty($item['badge_text'])): ?>
                                    <span class="badge badge-<?php echo e($item['badge_type'] ?? 'primary'); ?>"><?php echo e($item['badge_text']); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>                
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'admin')): ?>
            <?php $__currentLoopData = $menuAdminItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($item['type'] == 'label'): ?>
                    <li class="side-item side-item-category mt-4 mb-3"><?php echo e(__($item['label'])); ?></li>
                <?php elseif($item['type'] == 'divider'): ?>
                    <hr class="w-90 text-center ml-auto mr-auto mt-3">
                <?php else: ?>
                    <?php if($item['has_access']): ?>
                        <li class="slide">
                            <?php if(!empty($item['children'])): ?>
                                <a class="side-menu__item" data-toggle="slide" href="<?php echo e($item['url'] ?? '#'); ?>">
                                    <span class="side-menu__icon <?php echo e($item['icon']); ?>"></span>                    
                                    <span class="side-menu__label"><?php echo e(__($item['label'])); ?></span>
                                    <?php if(!empty($item['badge_text'])): ?>
                                        <span class="badge badge-<?php echo e($item['badge_type'] ?? 'primary'); ?>"><?php echo e($item['badge_text']); ?></span>
                                    <?php endif; ?>
                                    <i class="angle fa fa-angle-right"></i>
                                </a>
                                <ul class="slide-menu">
                                    <?php $__currentLoopData = $item['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a href="<?php echo e($child['route'] ? route($child['route']) : $child['url']); ?>" class="slide-item"><?php if(!is_null($child['icon'])): ?> <i class="slide-child-icon <?php echo e($child['icon']); ?>"></i> <?php endif; ?><?php echo e(__($child['label'])); ?></a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <a class="side-menu__item" href="<?php echo e($item['route'] ? route($item['route']) : $item['url']); ?>">
                                    <span class="side-menu__icon <?php echo e($item['icon']); ?>"></span>                        
                                    <span class="side-menu__label"><?php echo e(__($item['label'])); ?></span>
                                    <?php if(!empty($item['badge_text'])): ?>
                                        <span class="badge badge-<?php echo e($item['badge_type'] ?? 'primary'); ?>"><?php echo e($item['badge_text']); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>                
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        
        <hr class="w-90 text-center ml-auto mr-auto mt-3">
        
        <div class="side-progress-position mt-4">
            <div class="side-plan-wrapper text-center pt-3 pb-3">
                <?php if(App\Services\HelperService::extensionSaaS()): ?>
                    <span class="side-item side-item-category mt-4"><?php echo e(__('Plan')); ?>: <?php if(is_null(auth()->user()->plan_id)): ?><span class="text-primary"><?php echo e(__('No Active Subscription')); ?></span> <?php else: ?> <span class="text-primary"><?php echo e(__(App\Services\HelperService::getPlanName())); ?></span>  <?php endif; ?> </span>
                <?php endif; ?>
                <div class="view-credits <?php if(App\Services\HelperService::extensionSaaS()): ?> mt-1 <?php endif; ?>"><a class=" fs-11 text-muted mb-2" href="javascript:void(0)" id="view-credits" data-bs-toggle="modal" data-bs-target="#creditsModel"><i class="fa-solid fa-coin-front text-yellow "></i> <?php echo e(__('View Credits')); ?></a></div> 
                <?php if(App\Services\HelperService::extensionSaaS()): ?>
                    <?php if(is_null(auth()->user()->plan_id)): ?>
                        <div class="text-center mt-3 mb-2"><a href="<?php echo e(route('user.plans')); ?>" class="btn btn-primary btn-primary-small pl-6 pr-6 fs-11"> <i class="fa-solid fa-bolt text-yellow mr-2"></i> <?php echo e(__('Upgrade')); ?></a></div> 
                    <?php endif; ?>              
                <?php endif; ?>              
            </div>
            <?php if(App\Services\HelperService::extensionSaaS()): ?>
                <?php if(config('payment.referral.enabled') == 'on'): ?>
                    <div class="side-plan-wrapper mt-4 text-center p-3 pl-5 pr-5">
                        <div class="mb-1"><i class="fa-solid fa-gifts fs-20 text-yellow"></i></div>
                        <span class="fs-12 mt-4" style="color: #344050"><?php echo e(__('Invite your friends and get')); ?> <?php echo e(config('payment.referral.payment.commission')); ?>% <?php if(config('payment.referral.payment.policy') == 'all'): ?> <?php echo e(__('of all their purchases')); ?> <?php else: ?> <?php echo e(__('of their first purchase')); ?><?php endif; ?></span>
                        <div class="text-center mt-3 mb-2"><a href="<?php echo e(route('user.referral')); ?>" class="btn btn-primary btn-primary-small pl-6 pr-6 fs-11" id="referral-button"> <?php echo e(__('Invite Friends')); ?></a></div>              
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </ul>
</aside>

<div class="modal fade" id="creditsModel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="text-center font-weight-bold fs-16"> <?php echo e(__('Credits on')); ?> <?php echo e(config('app.name')); ?></h6>	
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pl-5 pr-5">
                
                <h6 class="font-weight-semibold mb-2 mt-3"><?php echo e(__('Unlock your creativity with')); ?> <?php echo e(config('app.name')); ?> <?php echo e(__('credits')); ?></h6>
                <p class="text-muted"><?php echo e(__('Maximize your content creation with')); ?> <?php echo e(config('app.name')); ?>. <?php echo e(__('Each credit unlocks powerful AI tools and features designed to enhance your content creation.')); ?></p>
                
                <div class="d-flex justify-content-between mt-3">
                    <div class="font-weight-bold fs-12"><?php echo e(__('AI Model')); ?></div>
                    <div class="font-weight-bold fs-12"><?php echo e(__('Credits')); ?></div>
                </div>
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10"> <?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('Words')); ?> <?php else: ?> <?php echo e(__('Tokens')); ?> <?php endif; ?> <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-words=''></i></div>
                    <div class="text-muted fs-10"><?php echo e(\App\Services\HelperService::getTotalWords()); ?></div>
                </div>                
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10"><?php echo e(__('Media Credits')); ?> <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-images=''></i></div>
                    <div class="text-muted fs-10"><?php echo e(\App\Services\HelperService::getTotalImages()); ?></div>
                </div>
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10"><?php echo e(__('Characters')); ?> <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-characters=''></i></div>
                    <div class="text-muted fs-10"><?php echo e(App\Services\HelperService::getTotalCharacters()); ?></div>
                </div>
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10"><?php echo e(__('Minutes')); ?> <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-minutes=''></i></div>
                    <div class="text-muted fs-10"><?php echo e(App\Services\HelperService::getTotalMinutes()); ?></div>
                </div>
               
                <?php if(App\Services\HelperService::extensionSaaS()): ?>
                    <div class="text-center mt-4"><a href="<?php echo e(route('user.plans')); ?>" class="btn btn-primary pl-6 pr-6 fs-11" style="text-transform: none"> <i class="fa-solid fa-bolt text-yellow mr-2"></i> <?php echo e(__('Upgrade Now')); ?></a></div> 
                <?php endif; ?>
            </div>
        </div>
        <div id="nav-info-words" style="display: none;">
            <span class="mb-4 text-underline"><strong class="mb-4"><?php echo e(__('Valid For')); ?>:</strong></span><br>
            <span><?php echo e(__('AI Writer')); ?></span><br>
            <span><?php echo e(__('AI Article Wizard')); ?></span><br>
            <span><?php echo e(__('Smart Editor')); ?></span><br>
            <span><?php echo e(__('AI ReWriter')); ?></span><br>
            <span><?php echo e(__('AI Chat')); ?></span><br>
            <span><?php echo e(__('AI File Chat')); ?></span><br>
            <span><?php echo e(__('AI Web Chat')); ?></span><br>
            <span><?php echo e(__('AI Youtube')); ?></span><br>
            <span><?php echo e(__('AI RSS')); ?></span><br>
            <span><?php echo e(__('AI Code')); ?></span><br>
            <span><?php echo e(__('AI Vision')); ?></span><br>
        </div>
        <div id="nav-info-images" style="display: none;">
            <strong class="mb-4 underline"><?php echo e(__('Valid For')); ?>:</strong><br>
            <span><?php echo e(__('AI Avatar')); ?></span><br>
            <span><?php echo e(__('AI Images')); ?></span><br>
            <span><?php echo e(__('AI Video Image')); ?></span><br>
            <span><?php echo e(__('AI Video Text')); ?></span><br>
            <span><?php echo e(__('AI Video Video')); ?></span><br>
            <span><?php echo e(__('AI Photo Studio')); ?></span><br>
            <span><?php echo e(__('AI Product Photo')); ?></span><br>
            <span><?php echo e(__('Faceswap')); ?></span><br>
            <span><?php echo e(__('AI Music')); ?></span><br>
        </div>
        <div id="nav-info-characters" style="display: none;">
            <strong class="mb-4 underline"><?php echo e(__('Valid For')); ?>:</strong><br>
            <span><?php echo e(__('AI Text to Speech')); ?></span><br>
            <span><?php echo e(__('Voice Cloning')); ?></span><br>
            <span><?php echo e(__('Voice Isolator')); ?></span><br>
        </div>
        <div id="nav-info-minutes" style="display: none;">
            <strong class="mb-4 underline"><?php echo e(__('Valid For')); ?>:</strong><br>
            <span><?php echo e(__('AI Speech To Text')); ?></span><br>
        </div>
    </div>
</div>
<!-- END SIDE MENU BAR --><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/dashboard/nav-aside.blade.php ENDPATH**/ ?>
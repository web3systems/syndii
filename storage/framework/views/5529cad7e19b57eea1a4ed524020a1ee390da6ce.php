<!-- TOP MENU BAR -->
<div class="app-header header">
    <div class="container"> 
        <div class="d-flex">
            <a class="header-brand" href="<?php echo e(url('/')); ?>">
                <img src="<?php echo e(URL::asset($settings->logo_dashboard)); ?>" class="header-brand-img desktop-lgo" alt="Dashboard Logo">
                <img src="<?php echo e(URL::asset($settings->logo_dashboard_collapsed)); ?>" class="header-brand-img mobile-logo" alt="Dashboard Logo">
            </a>
            <div class="app-sidebar__toggle2 nav-link icon" data-toggle="sidebar">
                <a class="open-toggle" href="<?php echo e(url('#')); ?>">
                    <span class="fa fa-align-justify header-icon"></span>
                </a>
            </div>
            <div id="search-bar" class="search-container mt-auto">
                <div class="search-wrapper">
                    <i class="fa-solid fa-search" id="search-icon-top"></i>
                    <input id="main-search" type="text" class="form-control search-input" placeholder="<?php echo e(__('Search for documents, templates and chatbots...')); ?>">
                    <span class="left-pan" id="mic-search"><i class="fa fa-microphone"></i></span>                      
                </div>       
            </div>
            <!-- END SEARCH BAR -->
            <!-- MENU BAR -->
            <div class="d-flex">
                <?php if(App\Services\HelperService::extensionSaaS()): ?>
                    <div class="mt-auto mb-auto header-upgrade">
                        <?php if(is_null(auth()->user()->plan_id)): ?>
                            <div class="text-center mr-4 mt-1"><a href="<?php echo e(route('user.plans')); ?>" class="btn btn-primary btn-primary-small pl-5 pr-5 fs-11"> <i class="fa-solid fa-bolt text-yellow mr-2"></i> <?php echo e(__('Upgrade')); ?></a></div> 
                        <?php endif; ?> 
                    </div>  
                <?php endif; ?>                
                <div class="dropdown items-center flex">
                    <a href="#" class="nav-link icon btn-theme-toggle">
                        <span class="header-icon fa-solid"></span>
                    </a>
                </div>
                <div class="dropdown header-notify">
                    <a class="nav-link icon" data-bs-toggle="dropdown">                        
                        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'admin')): ?>
                            <span class="header-icon fa-regular fa-bell pr-3"></span>
                            <?php if(auth()->user()->unreadNotifications->where('type', '<>', 'App\Notifications\GeneralNotification')->count()): ?>
                                <span class="pulse "></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'user|subscriber')): ?>
                            <?php if(config('settings.user_notification') == 'enabled'): ?>
                                <span class="header-icon fa-solid fa-bell pr-3"></span>                            
                                    <?php if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count()): ?>
                                        <span class="pulse "></span>
                                    <?php endif; ?>                            
                            <?php endif; ?>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right animated">
                        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'admin')): ?>
                            <?php if(auth()->user()->unreadNotifications->where('type', '<>', 'App\Notifications\GeneralNotification')->count()): ?>
                                <div class="dropdown-header">
                                    <h6 class="mb-0 fs-12 font-weight-bold notification-dark-theme"><span id="total-notifications"></span> <span class="text-primary"><?php echo e(__('New')); ?></span> <?php echo e(__('Notification(s)')); ?></h6>
                                    <a href="#" class="mb-1 badge badge-primary ml-auto pl-3 pr-3 mark-read" id="mark-all"><?php echo e(__('Mark All Read')); ?></a>
                                </div>
                                <div class="notify-menu">
                                    <div class="notify-menu-inner">
                                        <?php $__currentLoopData = auth()->user()->unreadNotifications->where('type', '<>', 'App\Notifications\GeneralNotification'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="d-flex dropdown-item border-bottom pl-4 pr-4">
                                                <?php if($notification->data['type'] == 'new-user'): ?>                                                
                                                    <div>
                                                        <a href="<?php echo e(route('admin.notifications.systemShow', [$notification->id])); ?>" class="d-flex">
                                                            <div class="notifyimg bg-info-transparent text-info"> <i class="fa-solid fa-user-check fs-18"></i></div>
                                                            <div class="mr-6">
                                                                <div class="font-weight-bold fs-12 notification-dark-theme"><?php echo e(__('New User Registered')); ?></div>
                                                                <div class="text-muted fs-10"><?php echo e(__('Name')); ?>: <?php echo e($notification->data['name']); ?></div>
                                                                <div class="small text-muted fs-10"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                                            </div>                                            
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <a href="#" class="badge badge-primary mark-read mark-as-read" data-id="<?php echo e($notification->id); ?>"><?php echo e(__('Mark as Read')); ?></a>
                                                    </div>
                                                <?php endif; ?>  
                                                <?php if($notification->data['type'] == 'new-payment'): ?>                                                
                                                    <div>
                                                        <a href="<?php echo e(route('admin.notifications.systemShow', [$notification->id])); ?>" class="d-flex">
                                                            <div class="notifyimg bg-info-green"> <i class="fa-solid fa-sack-dollar leading-loose"></i></div>
                                                            <div class="mr-4">
                                                                <div class="font-weight-bold fs-12 notification-dark-theme"><?php echo e(__('New User Payment')); ?></div>
                                                                <div class="text-muted fs-10"><?php echo e(__('From')); ?>: <?php echo e($notification->data['name']); ?></div>
                                                                <div class="small text-muted fs-10"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                                            </div>                                            
                                                        </a>
                                                    </div>
                                                    <div class="text-right">
                                                        <a href="#" class="badge badge-primary mark-read mark-as-read ml-5" data-id="<?php echo e($notification->id); ?>"><?php echo e(__('Mark as Read')); ?></a>
                                                    </div>
                                                <?php endif; ?>  
                                                <?php if($notification->data['type'] == 'payout-request'): ?>                                                
                                                    <div>
                                                        <a href="<?php echo e(route('admin.notifications.systemShow', [$notification->id])); ?>" class="d-flex">
                                                            <div class="notifyimg bg-info-green"> <i class="fa-solid fa-face-tongue-money fs-20 leading-loose"></i></div>
                                                            <div class="mr-4">
                                                                <div class="font-weight-bold fs-12 notification-dark-theme"><?php echo e(__('New Payout Request')); ?></div>
                                                                <div class="text-muted fs-10"><?php echo e(__('From')); ?>: <?php echo e($notification->data['name']); ?></div>
                                                                <div class="small text-muted fs-10"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                                            </div>                                            
                                                        </a>
                                                    </div>
                                                    <div class="text-right">
                                                        <a href="#" class="badge badge-primary mark-read mark-as-read ml-5" data-id="<?php echo e($notification->id); ?>"><?php echo e(__('Mark as Read')); ?></a>
                                                    </div>
                                                <?php endif; ?>                                                
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
                                    </div>                              
                                </div>
                                <div class="view-all-button text-center">                            
                                    <a href="<?php echo e(route('admin.notifications.system')); ?>" class="fs-12 font-weight-bold notification-dark-theme"><?php echo e(__('View All Notifications')); ?></a>
                                </div>                            
                            <?php else: ?>
                                <div class="view-all-button text-center">
                                    <h6 class=" fs-12 font-weight-bold mb-1 notification-dark-theme"><?php echo e(__('There are no new notifications')); ?></h6>                                    
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(config('settings.user_notification') == 'enabled'): ?>
                            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'user|subscriber')): ?>
                                <?php if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count()): ?>
                                    <div class="dropdown-header">
                                        <h6 class="mb-0 fs-12 font-weight-bold notification-dark-theme"><?php echo e(auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count()); ?> <span class="text-primary">New</span> Notification(s)</h6>
                                        <a href="#" class="mb-1 badge badge-primary ml-auto pl-3 pr-3 mark-read" id="mark-all"><?php echo e(__('Mark All Read')); ?></a>
                                    </div>
                                    <div class="notify-menu">
                                        <div class="notify-menu-inner">
                                            <?php $__currentLoopData = auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="dropdown-item border-bottom pl-4 pr-4">
                                                    <div>
                                                        <a href="<?php echo e(route('user.notifications.show', [$notification->id])); ?>" class="d-flex">
                                                            <div class="notifyimg bg-info-transparent text-info"> <i class="fa fa-bell fs-18"></i></div>
                                                            <div>
                                                                <div class="font-weight-bold fs-12 mt-2 notification-dark-theme"><?php echo e(__('New')); ?> <?php echo e($notification->data['type']); ?> <?php echo e(__('Notification')); ?></div>
                                                                <div class="small text-muted fs-10"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                                            </div>                                            
                                                        </a>
                                                    </div>                                            
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                
                                        </div>
                                    </div>
                                    <div class="view-all-button text-center">                            
                                        <a href="<?php echo e(route('user.notifications')); ?>" class="fs-12 font-weight-bold notification-dark-theme"><?php echo e(__('View All Notifications')); ?></a>
                                    </div>                             
                                <?php else: ?>
                                    <div class="view-all-button text-center">
                                        <h6 class=" fs-12 font-weight-bold mb-1 notification-dark-theme"><?php echo e(__('There are no new notifications')); ?></h6>                                    
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>                        
                    </div>
                </div>                
                <div class="dropdown header-expand" >
                    <a  class="nav-link icon" id="fullscreen-button">
                        <span class="header-icon fa-solid fa-expand" id="fullscreen-icon"></span>
                    </a>
                </div>
                <div class="dropdown header-languages mr-2">
                    <a class="nav-link icon" data-bs-toggle="dropdown">
                        <span class="header-icon fa-solid fa-globe"></span>
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
                <div class="dropdown profile-dropdown">
                    <a href="#" class="nav-link" data-bs-toggle="dropdown">
                        <span class="float-right">
                            <img src="<?php if(auth()->user()->profile_photo_path): ?><?php echo e(asset(auth()->user()->profile_photo_path)); ?> <?php else: ?> <?php echo e(theme_url('img/users/avatar.jpg')); ?> <?php endif; ?>" alt="img" class="avatar avatar-md">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right animated">
                        <div class="text-center pt-2">
                            <span class="text-center user fs-12 pb-0 font-weight-bold"><?php echo e(Auth::user()->name); ?></span><br>
                            <span class="text-center fs-12 text-muted"><?php echo e(__(Auth::user()->email)); ?></span>
                            <?php if(App\Services\HelperService::extensionSaaS()): ?>
                                <br><span class="text-center fs-12 text-muted"><?php if(is_null(auth()->user()->plan_id)): ?><span class="text-primary"><?php echo e(__('No Active Subscription')); ?></span> <?php else: ?> <span class="text-primary"><?php echo e(__(App\Services\HelperService::getPlanName())); ?></span> <?php echo e(__('Plan')); ?>  <?php endif; ?> </span>
                            <?php endif; ?>
                            <div class="view-credits <?php if(App\Services\HelperService::extensionSaaS()): ?> mt-1 <?php endif; ?>"><a class=" fs-11 text-muted mb-2" href="javascript:void(0)" id="view-credits" data-bs-toggle="modal" data-bs-target="#creditsModel"><i class="fa-solid fa-coin-front text-yellow "></i> <?php echo e(__('View Credits')); ?></a></div> 
                            <?php if(App\Services\HelperService::extensionSaaS()): ?>
                                <?php if(is_null(auth()->user()->plan_id)): ?>
                                    <div class="text-center mt-3 mb-2"><a href="<?php echo e(route('user.plans')); ?>" class="btn btn-primary btn-primary-small pl-6 pr-6 fs-11"> <i class="fa-solid fa-bolt text-yellow mr-2"></i> <?php echo e(__('Upgrade')); ?></a></div> 
                                <?php endif; ?>              
                            <?php endif; ?>     
                            <div class="dropdown-divider mt-3"></div>    
                        </div>
                        <div class="profile-dropdown-items-wrapper pl-2 pr-2">
                            <?php if(App\Services\HelperService::extensionSaaS()): ?>
                                <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('user.plans')); ?>">
                                    <span class="profile-icon fa-solid fa-box-circle-check"></span>
                                    <div class="fs-12"><?php echo e(__('Subscription Plans')); ?></div>
                                </a>     
                            <?php endif; ?>   
                            <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('user.workbooks')); ?>">
                                <span class="profile-icon fa-solid fa-folder-bookmark"></span>
                                <div class="fs-12"><?php echo e(__('My Workbooks')); ?></div>
                            </a> 
                            <?php if(App\Services\HelperService::extensionSaaS()): ?>
                                <?php if(config('payment.referral.enabled') == 'on'): ?>
                                    <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('user.referral')); ?>">
                                        <span class="profile-icon fa-solid fa-badge-dollar"></span>
                                        <span class="fs-12"><?php echo e(__('Affiliate Program')); ?></span></a>
                                    </a>
                                <?php endif; ?>                        
                                <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('user.purchases')); ?>">
                                    <span class="profile-icon fa-solid fa-money-check-pen"></span>
                                    <span class="fs-12"><?php echo e(__('Orders')); ?></span></a>
                                </a>
                            <?php endif; ?>
                            <?php if(config('settings.user_support') == 'enabled'): ?>
                                <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('user.support')); ?>">
                                    <span class="profile-icon fa-solid fa-headset"></span>
                                    <div class="fs-12"><?php echo e(__('Support Request')); ?></div>
                                </a>
                            <?php endif; ?>        
                            <?php if(config('settings.user_notification') == 'enabled'): ?>
                                <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('user.notifications')); ?>">
                                    <span class="profile-icon fa-solid fa-message-exclamation"></span>
                                    <div class="fs-12"><?php echo e(__('Notifications')); ?></div>
                                    <?php if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count()): ?>
                                        <span class="badge badge-warning ml-3"><?php echo e(auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count()); ?></span>
                                    <?php endif; ?>   
                                </a>
                            <?php endif; ?> 
                            <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('user.profile')); ?>">
                                <span class="profile-icon fa-solid fa-id-badge"></span>
                                <span class="fs-12"><?php echo e(__('Profile Settings')); ?></span></a>
                            </a>
                            <a class="dropdown-item d-flex ml-auto mr-auto" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();"> 
                                <span class="profile-icon fa-solid fa-right-from-bracket"></span>          
                                <div class="fs-12"><?php echo e(__('Logout')); ?></div>                            
                            </a>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                <?php echo csrf_field(); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MENU BAR -->
        </div>
    </div>
</div>
<!-- END TOP MENU BAR -->
<?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/dashboard/nav-top.blade.php ENDPATH**/ ?>
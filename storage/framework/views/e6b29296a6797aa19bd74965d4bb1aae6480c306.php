<div class="card-footer p-0">	
    <div class="row text-center pb-4 pt-4">
        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16"><?php if(auth()->user()->tokens == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(App\Services\HelperService::userAvailableTokens()); ?> <?php endif; ?></h4>
            <h6 class="fs-12"><?php if($settings->model_credit_name == 'words'): ?> <?php echo e(__('Words Left')); ?> <?php else: ?> <?php echo e(__('Tokens Left')); ?> <?php endif; ?></h6>
        </div>

        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16"><?php if(auth()->user()->images == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(App\Services\HelperService::userAvailableImages()); ?> <?php endif; ?></h4>
            <h6 class="fs-12"><?php echo e(__('Media Credits Left')); ?></h6>
        </div>
    </div>   

    <div class="row text-center pb-4">
        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16"><?php if(auth()->user()->characters == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(App\Services\HelperService::userAvailableChars()); ?> <?php endif; ?></h4>
            <h6 class="fs-12"><?php echo e(__('Characters Left')); ?></h6>
        </div>

        <div class="col-sm">
            <h4 class="mb-3 mt-1 font-weight-800 text-primary fs-16"><?php if(auth()->user()->minutes == -1): ?> <?php echo e(__('Unlimited')); ?> <?php else: ?> <?php echo e(App\Services\HelperService::userAvailableMinutes()); ?> <?php endif; ?></h4>
            <h6 class="fs-12"><?php echo e(__('Minutes Left')); ?></h6>
        </div>
    </div>    															
</div><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/components/user-credits.blade.php ENDPATH**/ ?>
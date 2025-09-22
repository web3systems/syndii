

<?php $__env->startSection('page-header'); ?>
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><img src="<?php echo e(theme_url('img/csp/stability-sm.png')); ?>" class="fw-2 mr-2" alt=""> <?php echo e(__('Stable Diffusion Settings')); ?></h4>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="fa-solid fa-microchip-ai mr-2 fs-12"></i><?php echo e(__('Admin')); ?></a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="<?php echo e(route('admin.davinci.configs')); ?>"> <?php echo e(__('AI Settings')); ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> <?php echo e(__('API')); ?></a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>						
	<div class="row justify-content-center">
		<div class="col-lg-7 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-7 pl-7 pr-7 pb-6">									
					<form action="<?php echo e(route('admin.davinci.configs.api.stability.store')); ?>" method="post" enctype="multipart/form-data">
						<?php echo csrf_field(); ?>	
								
						<div class="card shadow-0 mb-6 pt-3 pb-3">							
							<div class="card-body">

								<div class="row">
									<div class="col-lg-12 col-md-6 col-sm-12 no-gutters">
										<div class="row">							
											<div class="col-sm-12">
												<div class="input-box">								
													<h6><?php echo e(__('Stable Diffusion API Key')); ?> <span class="text-muted">(<?php echo e(__('Main API Key')); ?>)</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">							    
														<input type="text" class="form-control <?php $__errorArgs = ['stable-diffusion-key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="stable-diffusion-key" name="stable-diffusion-key" value="<?php echo e(config('services.stable_diffusion.key')); ?>" autocomplete="off">
														<?php $__errorArgs = ['stable-diffusion-key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
															<p class="text-danger"><?php echo e($errors->first('stable-diffusion-key')); ?></p>
														<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>												
													</div> 
												</div> 
											</div>

											<div class="col-md-6 col-sm-12">
												<div class="input-box mb-0">								
													<h6><?php echo e(__('SD API Key Usage Model')); ?> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="sd-key-usage" name="sd-key-usage" class="form-select" data-placeholder="<?php echo e(__('Set API Key Usage Model')); ?>">
														<option value="main" <?php if(config('settings.sd_key_usage') == 'main'): ?> selected <?php endif; ?>><?php echo e(__('Only Main API Key')); ?></option>
														<option value="random" <?php if(config('settings.sd_key_usage') == 'random'): ?> selected <?php endif; ?>><?php echo e(__('Random API Key')); ?></option>																																																																																																									
													</select>
												</div> 
											</div>

											<div class="col-md-6 col-sm-12">
												<div class="input-box mb-0">
													<h6><?php echo e(__('Personal Stable Diffusion API Key')); ?> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="<?php echo e(__('If enabled, all users will be required to include their Personal Stable Diffusion API keys in their profile pages. You can also enable it via Subscription plans only.')); ?>"></i></h6>
													<select id="personal-sd-api" name="personal-sd-api" class="form-select">
														<option value="allow" <?php if(config('settings.personal_sd_api') == 'allow'): ?> selected <?php endif; ?>><?php echo e(__('Allow')); ?></option>
														<option value="deny" <?php if(config('settings.personal_sd_api') == 'deny'): ?> selected <?php endif; ?>><?php echo e(__('Deny')); ?></option>																																																																																																								
													</select>
												</div>
											</div>
										</div>
										<a href="<?php echo e(route('admin.davinci.configs.keys')); ?>" class="btn btn-primary mt-4 mb-2" style="padding-left: 25px; padding-right: 25px; width: 223px;"><?php echo e(__('Store additional SD API Keys')); ?></a>
									</div>							
								</div>

							</div>
						</div>	

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary pl-8 pr-8 pt-2 pb-2"><?php echo e(__('Save')); ?></button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/admin/davinci/configuration/vendors/stablediffusion/setting.blade.php ENDPATH**/ ?>
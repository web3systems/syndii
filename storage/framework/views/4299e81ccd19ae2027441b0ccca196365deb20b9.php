

<?php $__env->startSection('page-header'); ?>
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><?php echo e(__('API Credit Management')); ?></h4>
			<p class="fs-12 text-muted mb-2"><?php echo e(__('Adjust the multiplier value accordingly if you wish to increase the charges associated with API consumption')); ?></p>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="fa-solid fa-microchip-ai mr-2 fs-12"></i><?php echo e(__('Admin')); ?></a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="<?php echo e(route('admin.davinci.dashboard')); ?>"> <?php echo e(__('AI Management')); ?></a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="<?php echo e(route('admin.davinci.configs')); ?>"> <?php echo e(__('AI Settings')); ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> <?php echo e(__('API Credit Management')); ?></a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>						
	<div class="row justify-content-center">
		<div class="col-lg-8 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-6">									
					<form id="" action="<?php echo e(route('admin.davinci.configs.api.credit.store')); ?>" method="post" enctype="multipart/form-data">
						<?php echo csrf_field(); ?>

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">	
									<h6><?php echo e(__('Credit Calculation Method')); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>
									<select id="model_charge_type" name="model_charge_type" class="form-select">			
										<option value="both" <?php if( $config->model_charge_type  == 'both'): ?> selected <?php endif; ?>><?php echo e(__('Count both Input and Output Tokens in the final cost')); ?></option>
										<option value="input" <?php if( $config->model_charge_type  == 'input'): ?> selected <?php endif; ?>><?php echo e(__('Count only Input Tokens')); ?></option>
										<option value="output" <?php if( $config->model_charge_type  == 'output'): ?> selected <?php endif; ?>><?php echo e(__('Count only Output Tokens')); ?></option>
									</select>
								</div>								
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">	
									<h6><?php echo e(__('Model Credits Naming')); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>
									<select id="model_credit_name" name="model_credit_name" class="form-select">			
										<option value="words" <?php if( $config->model_credit_name  == 'words'): ?> selected <?php endif; ?>><?php echo e(__('Use Words as model credits name')); ?></option>
										<option value="tokens" <?php if( $config->model_credit_name  == 'tokens'): ?> selected <?php endif; ?>><?php echo e(__('Use Tokens as model credits name')); ?></option>										
									</select>
								</div>	
							</div>

							<?php if(App\Services\HelperService::extensionCheckSaaS()): ?>
								<div class="col-lg-6 col-md-6 col-sm-12">
									<div class="input-box">	
										<h6><?php echo e(__('Show Disabled Models in AI Chat')); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>
										<select id="model_disabled_vendors" name="model_disabled_vendors" class="form-select">			
											<option value="hide" <?php if( $config->model_disabled_vendors  == 'hide'): ?> selected <?php endif; ?>><?php echo e(__('Hide')); ?></option>
											<option value="show" <?php if( $config->model_disabled_vendors  == 'show'): ?> selected <?php endif; ?>><?php echo e(__('Show')); ?></option>										
										</select>
									</div>	
								</div>
							<?php endif; ?>
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14"><?php echo e(__('OpenAI Models')); ?></h6>

						<div class="row">	
							<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<?php if($model->vendor == 'openai'): ?>					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12"><?php echo e(__($model->vendor_model)); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted"><?php echo e(__('Model Name')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__title" value="<?php echo e($model->title); ?>" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted"><?php echo e(__('Model Description')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__description" value="<?php echo e($model->description); ?>" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Input Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__input_token" value="<?php echo e($model->input_token); ?>" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Output Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__output_token" value="<?php echo e($model->output_token); ?>" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="<?php echo $model->model; ?>__new" value="0">
															<input type="checkbox" name="<?php echo $model->model; ?>__new" class="custom-switch-input" <?php if($model->new): ?> checked <?php endif; ?>>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12"><?php echo e(__('Show as New Model')); ?></span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>							
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14"><?php echo e(__('Anthropic Claude Models')); ?></h6>

						<div class="row pl-5 pr-5">							
							<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<?php if($model->vendor == 'anthropic'): ?>					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12"><?php echo e(__($model->vendor_model)); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted"><?php echo e(__('Model Name')); ?></h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__title" value="<?php echo e($model->title); ?>" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted"><?php echo e(__('Model Description')); ?></h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__description" value="<?php echo e($model->description); ?>" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Input Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__input_token" value="<?php echo e($model->input_token); ?>" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Output Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__output_token" value="<?php echo e($model->output_token); ?>" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="<?php echo $model->model; ?>__new" value="0">
															<input type="checkbox" name="<?php echo $model->model; ?>__new" class="custom-switch-input" <?php if($model->new): ?> checked <?php endif; ?>>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12"><?php echo e(__('Show as New Model')); ?></span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14"><?php echo e(__('Google Gemini Models')); ?></h6>

						<div class="row pl-5 pr-5">							
							<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<?php if($model->vendor == 'google'): ?>					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12"><?php echo e(__($model->vendor_model)); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Name')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__title" value="<?php echo e($model->title); ?>" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Description')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__description" value="<?php echo e($model->description); ?>" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Input Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__input_token" value="<?php echo e($model->input_token); ?>" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Output Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__output_token" value="<?php echo e($model->output_token); ?>" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="<?php echo $model->model; ?>__new" value="0">
															<input type="checkbox" name="<?php echo $model->model; ?>__new" class="custom-switch-input" <?php if($model->new): ?> checked <?php endif; ?>>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12"><?php echo e(__('Show as New Model')); ?></span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14"><?php echo e(__('DeepSeek Models')); ?></h6>

						<div class="row pl-5 pr-5">							
							<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<?php if($model->vendor == 'deepseek'): ?>					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12"><?php echo e(__($model->vendor_model)); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted"><?php echo e(__('Model Name')); ?></h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__title" value="<?php echo e($model->title); ?>" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Description')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__description" value="<?php echo e($model->description); ?>" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Input Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__input_token" value="<?php echo e($model->input_token); ?>" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Output Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__output_token" value="<?php echo e($model->output_token); ?>" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="<?php echo $model->model; ?>__new" value="0">
															<input type="checkbox" name="<?php echo $model->model; ?>__new" class="custom-switch-input" <?php if($model->new): ?> checked <?php endif; ?>>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12"><?php echo e(__('Show as New Model')); ?></span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14"><?php echo e(__('xAI Models')); ?></h6>

						<div class="row pl-5 pr-5">							
							<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<?php if($model->vendor == 'xai'): ?>					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12"><?php echo e(__($model->vendor_model)); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Name')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__title" value="<?php echo e($model->title); ?>" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted"><?php echo e(__('Model Description')); ?></h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__description" value="<?php echo e($model->description); ?>" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Input Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__input_token" value="<?php echo e($model->input_token); ?>" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Output Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__output_token" value="<?php echo e($model->output_token); ?>" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="<?php echo $model->model; ?>__new" value="0">
															<input type="checkbox" name="<?php echo $model->model; ?>__new" class="custom-switch-input" <?php if($model->new): ?> checked <?php endif; ?>>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12"><?php echo e(__('Show as New Model')); ?></span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14"><?php echo e(__('Perplexity Models')); ?></h6>

						<div class="row pl-5 pr-5">							
							<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<?php if($model->vendor == 'perplexity'): ?>					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12"><?php echo e(__($model->vendor_model)); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Name')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__title" value="<?php echo e($model->title); ?>" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Description')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__description" value="<?php echo e($model->description); ?>" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Input Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__input_token" value="<?php echo e($model->input_token); ?>" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Output Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__output_token" value="<?php echo e($model->output_token); ?>" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="<?php echo $model->model; ?>__new" value="0">
															<input type="checkbox" name="<?php echo $model->model; ?>__new" class="custom-switch-input" <?php if($model->new): ?> checked <?php endif; ?>>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12"><?php echo e(__('Show as New Model')); ?></span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14"><?php echo e(__('Amazon Nova Models')); ?></h6>

						<div class="row pl-5 pr-5">							
							<?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<?php if($model->vendor == 'amazon'): ?>					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12"><?php echo e(__($model->vendor_model)); ?> <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Name')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__title" value="<?php echo e($model->title); ?>" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted"><?php echo e(__('Model Description')); ?></h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="<?php echo $model->model; ?>__description" value="<?php echo e($model->description); ?>" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Input Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__input_token" value="<?php echo e($model->input_token); ?>" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted"><?php echo e(__($model->vendor_model)); ?> (<?php echo e(__('Output Tokens')); ?>)</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="<?php echo $model->model; ?>__output_token" value="<?php echo e($model->output_token); ?>" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="<?php echo $model->model; ?>__new" value="0">
															<input type="checkbox" name="<?php echo $model->model; ?>__new" class="custom-switch-input" <?php if($model->new): ?> checked <?php endif; ?>>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12"><?php echo e(__('Show as New Model')); ?></span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
						</div>

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary pl-9 pr-9 pt-3 pb-3"><?php echo e(__('Save')); ?></button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/admin/davinci/configuration/api.blade.php ENDPATH**/ ?>
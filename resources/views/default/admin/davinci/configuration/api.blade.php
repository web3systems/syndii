@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('API Credit Management') }}</h4>
			<p class="fs-12 text-muted mb-2">{{__('Adjust the multiplier value accordingly if you wish to increase the charges associated with API consumption')}}</p>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-microchip-ai mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.davinci.dashboard') }}"> {{ __('AI Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.davinci.configs') }}"> {{ __('AI Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('API Credit Management') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-8 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-6">									
					<form id="" action="{{ route('admin.davinci.configs.api.credit.store') }}" method="post" enctype="multipart/form-data">
						@csrf

						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">	
									<h6>{{ __('Credit Calculation Method') }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>
									<select id="model_charge_type" name="model_charge_type" class="form-select">			
										<option value="both" @if ( $config->model_charge_type  == 'both') selected @endif>{{ __('Count both Input and Output Tokens in the final cost') }}</option>
										<option value="input" @if ( $config->model_charge_type  == 'input') selected @endif>{{ __('Count only Input Tokens') }}</option>
										<option value="output" @if ( $config->model_charge_type  == 'output') selected @endif>{{ __('Count only Output Tokens') }}</option>
									</select>
								</div>								
							</div>

							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="input-box">	
									<h6>{{ __('Model Credits Naming') }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>
									<select id="model_credit_name" name="model_credit_name" class="form-select">			
										<option value="words" @if ( $config->model_credit_name  == 'words') selected @endif>{{ __('Use Words as model credits name') }}</option>
										<option value="tokens" @if ( $config->model_credit_name  == 'tokens') selected @endif>{{ __('Use Tokens as model credits name') }}</option>										
									</select>
								</div>	
							</div>

							@if (App\Services\HelperService::extensionCheckSaaS())
								<div class="col-lg-6 col-md-6 col-sm-12">
									<div class="input-box">	
										<h6>{{ __('Show Disabled Models in AI Chat') }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>
										<select id="model_disabled_vendors" name="model_disabled_vendors" class="form-select">			
											<option value="hide" @if ( $config->model_disabled_vendors  == 'hide') selected @endif>{{ __('Hide') }}</option>
											<option value="show" @if ( $config->model_disabled_vendors  == 'show') selected @endif>{{ __('Show') }}</option>										
										</select>
									</div>	
								</div>
							@endif
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14">{{ __('OpenAI Models') }}</h6>

						<div class="row">	
							@foreach ($models as $model)	
								@if ($model->vendor == 'openai')					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12">{{ __($model->vendor_model) }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted">{{__('Model Name')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__title" value="{{$model->title}}" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted">{{__('Model Description')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__description" value="{{$model->description}}" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Input Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__input_token" value="{{$model->input_token}}" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Output Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__output_token" value="{{$model->output_token}}" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="{!!$model->model!!}__new" value="0">
															<input type="checkbox" name="{!!$model->model!!}__new" class="custom-switch-input" @if ($model->new) checked @endif>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12">{{__('Show as New Model')}}</span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								@endif
							@endforeach							
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14">{{ __('Anthropic Claude Models') }}</h6>

						<div class="row pl-5 pr-5">							
							@foreach ($models as $model)	
								@if ($model->vendor == 'anthropic')					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12">{{ __($model->vendor_model) }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted">{{__('Model Name')}}</h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__title" value="{{$model->title}}" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted">{{__('Model Description')}}</h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__description" value="{{$model->description}}" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Input Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__input_token" value="{{$model->input_token}}" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Output Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__output_token" value="{{$model->output_token}}" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="{!!$model->model!!}__new" value="0">
															<input type="checkbox" name="{!!$model->model!!}__new" class="custom-switch-input" @if ($model->new) checked @endif>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12">{{__('Show as New Model')}}</span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								@endif
							@endforeach	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14">{{ __('Google Gemini Models') }}</h6>

						<div class="row pl-5 pr-5">							
							@foreach ($models as $model)	
								@if ($model->vendor == 'google')					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12">{{ __($model->vendor_model) }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Name')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__title" value="{{$model->title}}" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Description')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__description" value="{{$model->description}}" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Input Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__input_token" value="{{$model->input_token}}" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Output Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__output_token" value="{{$model->output_token}}" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="{!!$model->model!!}__new" value="0">
															<input type="checkbox" name="{!!$model->model!!}__new" class="custom-switch-input" @if ($model->new) checked @endif>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12">{{__('Show as New Model')}}</span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								@endif
							@endforeach	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14">{{ __('DeepSeek Models') }}</h6>

						<div class="row pl-5 pr-5">							
							@foreach ($models as $model)	
								@if ($model->vendor == 'deepseek')					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12">{{ __($model->vendor_model) }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted">{{__('Model Name')}}</h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__title" value="{{$model->title}}" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Description')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__description" value="{{$model->description}}" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Input Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__input_token" value="{{$model->input_token}}" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Output Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__output_token" value="{{$model->output_token}}" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="{!!$model->model!!}__new" value="0">
															<input type="checkbox" name="{!!$model->model!!}__new" class="custom-switch-input" @if ($model->new) checked @endif>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12">{{__('Show as New Model')}}</span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								@endif
							@endforeach	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14">{{ __('xAI Models') }}</h6>

						<div class="row pl-5 pr-5">							
							@foreach ($models as $model)	
								@if ($model->vendor == 'xai')					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12">{{ __($model->vendor_model) }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Name')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__title" value="{{$model->title}}" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">		
													<h6 class="text-muted">{{__('Model Description')}}</h6>	
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__description" value="{{$model->description}}" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Input Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__input_token" value="{{$model->input_token}}" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Output Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__output_token" value="{{$model->output_token}}" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="{!!$model->model!!}__new" value="0">
															<input type="checkbox" name="{!!$model->model!!}__new" class="custom-switch-input" @if ($model->new) checked @endif>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12">{{__('Show as New Model')}}</span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								@endif
							@endforeach	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14">{{ __('Perplexity Models') }}</h6>

						<div class="row pl-5 pr-5">							
							@foreach ($models as $model)	
								@if ($model->vendor == 'perplexity')					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12">{{ __($model->vendor_model) }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Name')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__title" value="{{$model->title}}" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Description')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__description" value="{{$model->description}}" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Input Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__input_token" value="{{$model->input_token}}" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Output Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__output_token" value="{{$model->output_token}}" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="{!!$model->model!!}__new" value="0">
															<input type="checkbox" name="{!!$model->model!!}__new" class="custom-switch-input" @if ($model->new) checked @endif>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12">{{__('Show as New Model')}}</span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								@endif
							@endforeach	
						</div>

						<hr>

						<h6 class="font-weight-bold text-center mb-2 mt-6 fs-14">{{ __('Amazon Nova Models') }}</h6>

						<div class="row pl-5 pr-5">							
							@foreach ($models as $model)	
								@if ($model->vendor == 'amazon')					
									<div class="col-lg-12 col-md-12 col-sm-12 no-gutters mt-5">
										<div class="row">	
											<h6 class="font-weight-bold fs-12">{{ __($model->vendor_model) }} <span class="text-required"><i class="fa-solid fa-asterisk ml-1"></i></span></h6>					
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Name')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__title" value="{{$model->title}}" placeholder="Model Name">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box">	
													<h6 class="text-muted">{{__('Model Description')}}</h6>		
													<div class="form-group">							    
														<input type="text" class="form-control" name="{!!$model->model!!}__description" value="{{$model->description}}" placeholder="Model Description">
													</div> 	
												</div>									
											</div> 
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Input Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__input_token" value="{{$model->input_token}}" placeholder="Input Token Cost">
													</div> 	
												</div>									
											</div> 	
											<div class="col-md-6 col-sm-12">						
												<div class="input-box mb-2">	
													<h6 class="text-muted">{{ __($model->vendor_model) }} ({{__('Output Tokens')}})</h6>		
													<div class="form-group">							    
														<input type="number" min="0.001" step="0.001" class="form-control" name="{!!$model->model!!}__output_token" value="{{$model->output_token}}" placeholder="Output Token Cost">
													</div> 	
												</div>									
											</div> 
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<div class="form-group mt-3">
														<label class="custom-switch">
															<input type="hidden" name="{!!$model->model!!}__new" value="0">
															<input type="checkbox" name="{!!$model->model!!}__new" class="custom-switch-input" @if ($model->new) checked @endif>
															<span class="custom-switch-indicator"></span>
															<span class="custom-switch-label text-muted fs-12">{{__('Show as New Model')}}</span>
														</label>
													</div>
												</div>
											</div>		
										</div>											
									</div>
								@endif
							@endforeach	
						</div>

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary pl-9 pr-9 pt-3 pb-3">{{ __('Save') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
@endsection


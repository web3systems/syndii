@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><img src="{{theme_url('img/csp/stability-sm.png')}}" class="fw-2 mr-2" alt=""> {{ __('Stable Diffusion Settings') }}</h4>
			<ol class="breadcrumb mb-2 justify-content-center">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-microchip-ai mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.davinci.configs')}}"> {{ __('AI Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('API') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row justify-content-center">
		<div class="col-lg-7 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body pt-7 pl-7 pr-7 pb-6">									
					<form action="{{ route('admin.davinci.configs.api.stability.store') }}" method="post" enctype="multipart/form-data">
						@csrf	
								
						<div class="card shadow-0 mb-6 pt-3 pb-3">							
							<div class="card-body">

								<div class="row">
									<div class="col-lg-12 col-md-6 col-sm-12 no-gutters">
										<div class="row">							
											<div class="col-sm-12">
												<div class="input-box">								
													<h6>{{ __('Stable Diffusion API Key') }} <span class="text-muted">({{ __('Main API Key') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">							    
														<input type="text" class="form-control @error('stable-diffusion-key') is-danger @enderror" id="stable-diffusion-key" name="stable-diffusion-key" value="{{ config('services.stable_diffusion.key') }}" autocomplete="off">
														@error('stable-diffusion-key')
															<p class="text-danger">{{ $errors->first('stable-diffusion-key') }}</p>
														@enderror												
													</div> 
												</div> 
											</div>

											<div class="col-md-6 col-sm-12">
												<div class="input-box mb-0">								
													<h6>{{ __('SD API Key Usage Model') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="sd-key-usage" name="sd-key-usage" class="form-select" data-placeholder="{{ __('Set API Key Usage Model') }}">
														<option value="main" @if (config('settings.sd_key_usage') == 'main') selected @endif>{{ __('Only Main API Key') }}</option>
														<option value="random" @if (config('settings.sd_key_usage') == 'random') selected @endif>{{ __('Random API Key') }}</option>																																																																																																									
													</select>
												</div> 
											</div>

											<div class="col-md-6 col-sm-12">
												<div class="input-box mb-0">
													<h6>{{ __('Personal Stable Diffusion API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('If enabled, all users will be required to include their Personal Stable Diffusion API keys in their profile pages. You can also enable it via Subscription plans only.') }}"></i></h6>
													<select id="personal-sd-api" name="personal-sd-api" class="form-select">
														<option value="allow" @if (config('settings.personal_sd_api') == 'allow') selected @endif>{{ __('Allow') }}</option>
														<option value="deny" @if (config('settings.personal_sd_api') == 'deny') selected @endif>{{ __('Deny') }}</option>																																																																																																								
													</select>
												</div>
											</div>
										</div>
										<a href="{{ route('admin.davinci.configs.keys') }}" class="btn btn-primary mt-4 mb-2" style="padding-left: 25px; padding-right: 25px; width: 223px;">{{ __('Store additional SD API Keys') }}</a>
									</div>							
								</div>

							</div>
						</div>	

						<!-- ACTION BUTTON -->
						<div class="border-0 text-center mb-2 mt-1">
							<button type="submit" class="btn ripple btn-primary pl-8 pr-8 pt-2 pb-2">{{ __('Save') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
@endsection



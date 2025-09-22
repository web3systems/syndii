@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><img src="{{theme_url('img/csp/gcp-sm.png')}}" class="fw-2 mr-2" alt=""> {{ __('Google Settings') }}</h4>
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
					<form action="{{ route('admin.davinci.configs.api.google.store') }}" method="post" enctype="multipart/form-data">
						@csrf
						
						<div class="card shadow-0 mb-6">							
							<div class="card-body">

								<h6 class="fs-12 font-weight-bold mb-4">{{ __('Google Gemini') }}</h6>

								<div class="row">
									<div class="col-lg-12 col-sm-12 no-gutters">
										<div class="row">							
											<div class="col-sm-12">
												<div class="input-box">								
													<h6>{{ __('Gemini API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">							    
														<input type="text" class="form-control @error('gemini-api-key') is-danger @enderror" id="gemini-api-key" name="gemini-api-key" value="{{ config('gemini.api_key') }}" autocomplete="off">
														@error('gemini-api-key')
															<p class="text-danger">{{ $errors->first('gemini-api-key') }}</p>
														@enderror												
													</div> 
												</div> 
											</div>
										</div>												
									</div>							
								</div>
	
							</div>
						</div>

						<div class="card shadow-0 mb-6">							
							<div class="card-body">

								<h6 class="fs-12 font-weight-bold mb-4">{{ __('GCP Voiceover Settings') }}</h6>

								<div class="form-group mb-3">
									<label class="custom-switch">
										<input type="checkbox" name="enable-gcp" class="custom-switch-input" @if ( config('settings.enable.gcp')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Activate GCP Voices') }}</span>
									</label>
								</div>

								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('GCP Configuration File Path') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('gcp-configuration-path') is-danger @enderror" id="gcp-configuration-path" name="gcp-configuration-path" value="{{ config('services.gcp.key_path') }}" autocomplete="off">
												@error('gcp-configuration-path')
													<p class="text-danger">{{ $errors->first('gcp-configuration-path') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>	
									<div class="col-lg-6 col-md-6 col-sm-12">								
										<div class="input-box">								
											<h6>{{ __('GCP Storage Bucket Name') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('gcp-bucket') is-danger @enderror" id="gcp-bucket" name="gcp-bucket" value="{{ config('services.gcp.bucket') }}" autocomplete="off">
												@error('gcp-bucket')
													<p class="text-danger">{{ $errors->first('gcp-bucket') }}</p>
												@enderror
											</div> 
										</div> 
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



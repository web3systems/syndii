@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><img src="{{theme_url('img/csp/dropbox-ssm.png')}}" class="fw-2 mr-2" alt=""> {{ __('Dropbox Settings') }}</h4>
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
					<form action="{{ route('admin.davinci.configs.api.dropbox.store') }}" method="post" enctype="multipart/form-data">
						@csrf
						
						<div class="card shadow-0 mb-6 pt-3">							
							<div class="card-body">

								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Dropbox App Key') }}  <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('set-dropbox-app-key') is-danger @enderror" id="dropbox-app-key" name="set-dropbox-app-key" value="{{ config('services.dropbox.key') }}" autocomplete="off">
												@error('set-dropbox-app-key')
													<p class="text-danger">{{ $errors->first('set-dropbox-app-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<!-- SECRET ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Dropbox Secret Key') }}  <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6> 
											<div class="form-group">							    
												<input type="text" class="form-control @error('set-dropbox-secret-key') is-danger @enderror" id="dropbox-secret-key" name="set-dropbox-secret-key" value="{{ config('services.dropbox.secret') }}" autocomplete="off">
												@error('set-dropbox-secret-key')
													<p class="text-danger">{{ $errors->first('set-dropbox-secret-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END SECRET ACCESS KEY -->
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Dropbox Access Token') }}  <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('set-dropbox-access-token') is-danger @enderror" id="dropbox-access-token" name="set-dropbox-access-token" value="{{ config('services.dropbox.token') }}" autocomplete="off">
												@error('set-dropbox-access-token')
													<p class="text-danger">{{ $errors->first('set-dropbox-access-token') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
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



@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0"><img src="{{theme_url('img/csp/deepseek.png')}}" class="fw-2 mr-2" alt=""> {{ __('DeepSeek Settings') }}</h4>
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
					<form action="{{ route('admin.davinci.configs.api.deepseek.store') }}" method="post" enctype="multipart/form-data">
						@csrf
						
						<div class="card shadow-0 mb-6 pt-3">							
							<div class="card-body">

								<div class="row">
									<div class="col-lg-12 col-sm-12 no-gutters">
										<div class="row">							
											<div class="col-sm-12">
												<div class="input-box">								
													<h6>{{ __('DeepSeek API Key') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">							    
														<input type="text" class="form-control" name="deepseek_api" value="{{ $config->deepseek_api }}" autocomplete="off">												
													</div> 
												</div> 
											</div>
										</div>												
									</div>	
									<div class="col-lg-12 col-sm-12 no-gutters">
										<div class="row">							
											<div class="col-sm-12">
												<div class="input-box mb-2">								
													<h6>{{ __('DeepSeek Base URL') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">							    
														<input type="text" class="form-control" name="deepseek_base_url" value="{{ $config->deepseek_base_url }}" autocomplete="off">												
													</div> 
												</div> 
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



@extends('layouts.app')

@section('page-header')
	<!-- EDIT PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('GDPR Cookie Settings') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-sliders mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{url('#')}}"> {{ __('General Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('GDPR Cookie Settings') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-6 col-sm-12">
			<div class="card border-0 p-5">
				<div class="card-body">
					<form id="generate-sitemap-form" method="POST" action="{{ route('admin.settings.gdpr')}}" enctype="multipart/form-data">
						@csrf
						
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Enable GDPR Cookies') }}</h6>
									<div class="form-group mt-3">
										<label class="custom-switch">
											<input type="checkbox" name="enable_cookies" class="custom-switch-input" @if ($cookies->enable_cookies) checked @endif>
											<span class="custom-switch-indicator"></span>
										</label>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Disable Page Interaction') }}</h6>
									<div class="form-group mt-3">
										<label class="custom-switch">
											<input type="checkbox" name="disable_page_interaction" class="custom-switch-input" @if ($cookies->disable_page_interaction) checked @endif>
											<span class="custom-switch-indicator"></span>
										</label>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Hide from Bots') }}</h6>
									<div class="form-group mt-3">
										<label class="custom-switch">
											<input type="checkbox" name="hide_from_bots" class="custom-switch-input" @if ($cookies->hide_from_bots) checked @endif>
											<span class="custom-switch-indicator"></span>
										</label>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="input-box">
									<h6>{{ __('Enable Dark Mode') }}</h6>
									<div class="form-group mt-3">
										<label class="custom-switch">
											<input type="checkbox" name="enable_dark_mode" class="custom-switch-input" @if ($cookies->enable_dark_mode) checked @endif>
											<span class="custom-switch-indicator"></span>
										</label>
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="input-box">								
									<h6>{{ __('Cookie Valid Days') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
									<div class="form-group">							    
										<input type="number" min=1 class="form-control" name="days" value="{{ $cookies->days }}" autocomplete="off">
									</div> 												
								</div> 
							</div>
							<div class="col-sm-12">									
								<div class="input-box">								
									<h6>{{ __('Consent Modal Layout') }}</h6>
									<select id="support-ticket" name="consent_modal_layouts" class="form-select">	
										<option value="box" {{ ($cookies->consent_modal_layouts == 'box') ? 'selected' : '' }}>{{ __('box') }}</option>
										<option value="box inline" {{ ($cookies->consent_modal_layouts == 'box inline') ? 'selected' : '' }}>{{ __('box inline') }}</option>																		
										<option value="box wide" {{ ($cookies->consent_modal_layouts == 'box wide') ? 'selected' : '' }}>{{ __('box wide') }}</option>																		
										<option value="cloud" {{ ($cookies->consent_modal_layouts == 'cloud') ? 'selected' : '' }}>{{ __('cloud') }}</option>																		
										<option value="cloud inline" {{ ($cookies->consent_modal_layouts == 'cloud inline') ? 'selected' : '' }}>{{ __('cloud inline') }}</option>																		
										<option value="bar" {{ ($cookies->consent_modal_layouts == 'bar') ? 'selected' : '' }}>{{ __('bar') }}</option>																		
										<option value="bar inline" {{ ($cookies->consent_modal_layouts == 'bar inline') ? 'selected' : '' }}>{{ __('bar inline') }}</option>																		
									</select> 
								</div> 
							</div>
							<div class="col-sm-12">									
								<div class="input-box">								
									<h6>{{ __('Consent Modal Position') }}</h6>
									<select id="support-ticket" name="consent_modal_position" class="form-select">	
										<option value="top left" {{ ($cookies->consent_modal_position == 'top left') ? 'selected' : '' }}>{{ __('top left') }}</option>																		
										<option value="top center" {{ ($cookies->consent_modal_position == 'top center') ? 'selected' : '' }}>{{ __('top center') }}</option>																		
										<option value="top right" {{ ($cookies->consent_modal_position == 'top right') ? 'selected' : '' }}>{{ __('top right') }}</option>	
										<option value="middle left" {{ ($cookies->consent_modal_position == 'middle left') ? 'selected' : '' }}>{{ __('middle left') }}</option>																		
										<option value="middle center" {{ ($cookies->consent_modal_position == 'middle center') ? 'selected' : '' }}>{{ __('middle center') }}</option>																		
										<option value="middle right" {{ ($cookies->consent_modal_position == 'middle right') ? 'selected' : '' }}>{{ __('middle right') }}</option>
										<option value="bottom left" {{ ($cookies->consent_modal_position == 'bottom left') ? 'selected' : '' }}>{{ __('bottom left') }}</option>																		
										<option value="bottom center" {{ ($cookies->consent_modal_position == 'bottom center') ? 'selected' : '' }}>{{ __('bottom center') }}</option>																		
										<option value="bottom right" {{ ($cookies->consent_modal_position == 'bottom right') ? 'selected' : '' }}>{{ __('bottom right') }}</option>																	
									</select> 
								</div> 
							</div>
							<div class="col-sm-12">									
								<div class="input-box">								
									<h6>{{ __('Preferences Modal Layout') }}</h6>
									<select id="support-ticket" name="preferences_modal_layout" class="form-select">	
										<option value="box" {{ ($cookies->preferences_modal_layout == 'box') ? 'selected' : '' }}>{{ __('box') }}</option>																	
										<option value="bar" {{ ($cookies->preferences_modal_layout == 'bar') ? 'selected' : '' }}>{{ __('bar') }}</option>																		
										<option value="bar wide" {{ ($cookies->preferences_modal_layout == 'bar wide') ? 'selected' : '' }}>{{ __('bar wide') }}</option>																		
									</select> 
								</div> 
							</div>
							<div class="col-sm-12">									
								<div class="input-box">								
									<h6>{{ __('Preferences Modal Position') }}</h6>
									<select id="support-ticket" name="preferences_modal_position" class="form-select">	
										<option value="right" {{ ($cookies->preferences_modal_position == 'right') ? 'selected' : '' }}>{{ __('right') }}</option>																	
										<option value="left" {{ ($cookies->preferences_modal_position == 'left') ? 'selected' : '' }}>{{ __('left') }}</option>																																				
									</select> 
								</div> 
							</div>
						</div>

						<div class="card-footer text-center border-0 pb-2 pt-5">													
							<button id="generate-sitemap" type="submit" class="btn btn-primary">{{ __('Save') }}</button>						
						</div>		
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection




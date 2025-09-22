@extends('layouts.app')

@section('css')
	<!-- Telephone Input CSS -->
	<link href="{{URL::asset('plugins/telephoneinput/telephoneinput.css')}}" rel="stylesheet" >
@endsection

@section('page-header')
	<!-- EDIT PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Personal API Keys') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('user.dashboard')}}"><i class="fa-solid fa-id-badge mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{route('user.profile')}}"> {{ __('My Profile') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Personal API Keys') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<!-- EDIT USER PROFILE PAGE -->
	<div class="row">
		<div class="col-xl-3 col-lg-4 col-sm-12">
			<div class="card  " id="dashboard-background">
				<div class="widget-user-image overflow-hidden mx-auto mt-5"><img alt="User Avatar" class="rounded-circle" src="@if(auth()->user()->profile_photo_path){{ asset(auth()->user()->profile_photo_path) }} @else {{ theme_url('img/users/avatar.jpg') }} @endif"></div>
				<div class="card-body text-center">
					<div>
						<h4 class="mb-1 mt-1 font-weight-bold text-primary fs-16">{{ auth()->user()->name }}</h4>
						<h6 class="font-weight-bold fs-12">{{ auth()->user()->job_role }}</h6>
					</div>
				</div>
				
				<x-user-credits />
				
				<div class="card-footer p-0">
					<div class="row" id="profile-pages">
						<div class="col-sm-12">
							<div class="text-center pt-4">
								<a href="{{ route('user.profile') }}" class="fs-13"><i class="fa fa-user-shield mr-1"></i> {{ __('View Profile') }}</a>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="text-center pt-3">
								<a href="{{ route('user.profile.defaults') }}" class="fs-13"><i class="   fa-solid fa-sliders mr-1"></i> {{ __('Set Defaults') }}</a>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="text-center p-3 ">
								<a href="{{ route('user.security') }}" class="fs-13"><i class="fa fa-lock-hashtag mr-1"></i> {{ __('Change Password') }}</a>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="text-center pb-4">
								<a href="{{ route('user.security.2fa') }}" class="fs-13"><i class="fa fa-shield-check mr-1"></i> {{ __('2FA Authentication') }}</a>
							</div>
						</div>
						@if (auth()->user()->group == 'user')
							@if (config('settings.personal_openai_api') == 'allow' || config('settings.personal_sd_api') == 'allow' || config('settings.personal_gemini_api') == 'allow' || config('settings.personal_claude_api') == 'allow')
								<div class="col-sm-12">
									<div class="text-center pb-3">
										<a href="{{ route('user.profile.api') }}" class="fs-13 text-primary"><i class="fa-solid fa-key mr-1"></i> {{ __('Personal API Keys') }}</a>
									</div>
								</div>
							@endif
						@elseif (!is_null(auth()->user()->plan_id))
							@if ($check_api_feature->personal_openai_api || $check_api_feature->personal_sd_api || $check_api_feature->personal_gemini_api || $check_api_feature->personal_claude_api)
								<div class="col-sm-12">
									<div class="text-center pb-3">
										<a href="{{ route('user.profile.api') }}" class="fs-13 text-primary"><i class="fa-solid fa-key mr-1"></i> {{ __('Personal API Keys') }}</a>
									</div>
								</div>
							@endif
						@elseif (auth()->user()->group == 'admin')
							@if (config('settings.personal_openai_api') == 'allow' || config('settings.personal_sd_api') == 'allow' || config('settings.personal_gemini_api') == 'allow' || config('settings.personal_claude_api') == 'allow')
								<div class="col-sm-12">
									<div class="text-center pb-3">
										<a href="{{ route('user.profile.api') }}" class="fs-13 text-primary"><i class="fa-solid fa-key mr-1"></i> {{ __('Personal API Keys') }}</a>
									</div>
								</div>
							@endif
						@endif							
						<div class="col-sm-12">
							<div class="text-center pb-4">
								<a href="{{ route('user.profile.delete') }}" class="fs-13"><i class="fa fa-user-xmark mr-1"></i> {{ __('Delete Account') }}</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-9 col-lg-8 col-sm-12">
			<form method="POST" class="w-100" action="{{ route('user.profile.api.store') }}" enctype="multipart/form-data">
				@method('PUT')
				@csrf

				<div class="card  ">
					<div class="card-header">
						<h3 class="card-title"><i class="   fa-solid fa-sliders mr-2 text-primary"></i>{{ __('Personal API Keys') }}</h3>
					</div>
					<div class="card-body pb-0">	
						@if (auth()->user()->group == 'user')
							@if (config('settings.personal_openai_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your OpenAI Secret Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('openai-key') is-danger @enderror" id="openai-key" name="openai-key" value="{{ auth()->user()->personal_openai_key }}" autocomplete="off">
												@error('openai-key')
													<p class="text-danger">{{ $errors->first('openai-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if (config('settings.personal_claude_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Claude API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('claude-key') is-danger @enderror" id="claude-key" name="claude-key" value="{{ auth()->user()->personal_claude_key }}" autocomplete="off">
												@error('claude-key')
													<p class="text-danger">{{ $errors->first('claude-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if (config('settings.personal_gemini_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Gemini API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('gemini-key') is-danger @enderror" id="gemini-key" name="gemini-key" value="{{ auth()->user()->personal_gemini_key }}" autocomplete="off">
												@error('gemini-key')
													<p class="text-danger">{{ $errors->first('gemini-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if (config('settings.personal_sd_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Stable Diffusion API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('sd-key') is-danger @enderror" id="sd-key" name="sd-key" value="{{ auth()->user()->personal_sd_key }}" autocomplete="off">
												@error('sd-key')
													<p class="text-danger">{{ $errors->first('sd-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif							
										
						
						@elseif (!is_null(auth()->user()->plan_id))
							@if ($check_api_feature->personal_openai_api)
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your OpenAI Secret Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('openai-key') is-danger @enderror" id="openai-key" name="openai-key" value="{{ auth()->user()->personal_openai_key }}" autocomplete="off">
												@error('openai-key')
													<p class="text-danger">{{ $errors->first('openai-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if ($check_api_feature->personal_claude_api)
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Claude API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('claude-key') is-danger @enderror" id="claude-key" name="claude-key" value="{{ auth()->user()->personal_claude_key }}" autocomplete="off">
												@error('claude-key')
													<p class="text-danger">{{ $errors->first('claude-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if ($check_api_feature->personal_gemini_api)
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Gemini API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('gemini-key') is-danger @enderror" id="gemini-key" name="gemini-key" value="{{ auth()->user()->personal_gemini_key }}" autocomplete="off">
												@error('gemini-key')
													<p class="text-danger">{{ $errors->first('gemini-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if ($check_api_feature->personal_sd_api)
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Stable Diffusion API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('sd-key') is-danger @enderror" id="sd-key" name="sd-key" value="{{ auth()->user()->personal_sd_key }}" autocomplete="off">
												@error('sd-key')
													<p class="text-danger">{{ $errors->first('sd-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif
						
						@elseif (auth()->user()->group == 'admin')
							@if (config('settings.personal_openai_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your OpenAI Secret Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('openai-key') is-danger @enderror" id="openai-key" name="openai-key" value="{{ auth()->user()->personal_openai_key }}" autocomplete="off">
												@error('openai-key')
													<p class="text-danger">{{ $errors->first('openai-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if (config('settings.personal_claude_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Claude API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('claude-key') is-danger @enderror" id="claude-key" name="claude-key" value="{{ auth()->user()->personal_claude_key }}" autocomplete="off">
												@error('claude-key')
													<p class="text-danger">{{ $errors->first('claude-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if (config('settings.personal_gemini_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Gemini API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('gemini-key') is-danger @enderror" id="gemini-key" name="gemini-key" value="{{ auth()->user()->personal_gemini_key }}" autocomplete="off">
												@error('gemini-key')
													<p class="text-danger">{{ $errors->first('gemini-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif

							@if (config('settings.personal_sd_api') == 'allow')
								<div class="row">
									<div class="col-sm-12">								
										<!-- ACCESS KEY -->
										<div class="input-box">								
											<h6>{{ __('Enter Your Stable Diffusion API Key') }}</h6>
											<div class="form-group">							    
												<input type="text" class="form-control @error('sd-key') is-danger @enderror" id="sd-key" name="sd-key" value="{{ auth()->user()->personal_sd_key }}" autocomplete="off">
												@error('sd-key')
													<p class="text-danger">{{ $errors->first('sd-key') }}</p>
												@enderror
											</div> 
										</div> <!-- END ACCESS KEY -->
									</div>
								</div>
							@endif							
					
						@endif
						
						<div class="card-footer   text-center mb-2 pt-0">
							<button type="submit" class="btn btn-primary pl-7 pr-7 ripple">{{ __('Save') }}</button>							
						</div>					
					</div>				
				</div>
			</form>
		</div>
	</div>
	<!-- EDIT USER PROFILE PAGE --> 
@endsection

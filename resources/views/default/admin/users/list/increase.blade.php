@extends('layouts.app')

@section('page-header')
	<!-- EDIT PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Update User Credits') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-id-badge mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.user.dashboard') }}"> {{ __('User Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.user.list') }}">{{ __('User List') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Update User Credits') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<div class="row justify-content-center">
		<div class="col-lg-8 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body">
					<form method="POST" action="{{ route('admin.user.increase', [$user->id]) }}" enctype="multipart/form-data">
						@csrf
						
						<div class="row">

							<div class="col-sm-12 col-md-12 mt-2">
								<div>
									<p class="fs-12 mb-2">{{ __('Full Name') }}: <span class="font-weight-bold ml-2 text-primary">{{ $user->name }}</span></p>
									<p class="fs-12 mb-2">{{ __('Email Address') }}: <span class="font-weight-bold ml-2">{{ $user->email }}</span></p>
									<p class="fs-12 mb-2">{{ __('User Group') }}: <span class="font-weight-bold ml-2">{{ ucfirst($user->group) }}</span></p>
								</div>
								<div class="row mt-4 mb-5">
									<div class="col-sm-12 col-md-6">										
										<p class="fs-12 mb-2">@if ($settings->model_credit_name == 'words') {{ __('Available Words') }} @else {{ __('Available Tokens') }} @endif: <span class="font-weight-bold ml-2">@if ($user->tokens == -1) {{ __('Unlimited') }} @else {{ number_format($user->tokens ) }} @endif</span></p>										
										<p class="fs-12 mb-2">{{ __('Available Media Credits') }}: <span class="font-weight-bold ml-2">@if ($user->images == -1) {{ __('Unlimited') }} @else {{ number_format($user->images ) }} @endif</span></p>										
										<p class="fs-12 mb-2">{{ __('Available Characters') }}: <span class="font-weight-bold ml-2">@if ($user->characters == -1) {{ __('Unlimited') }} @else {{ number_format($user->characters) }} @endif</span></p>
										<p class="fs-12 mb-2">{{ __('Available Minutes') }}: <span class="font-weight-bold ml-2">@if ($user->minutes == -1) {{ __('Unlimited') }} @else {{ number_format($user->minutes) }} @endif</span></p>
									</div>
									<div class="col-sm-12 col-md-6">										
										<p class="fs-12 mb-2">@if ($settings->model_credit_name == 'words') {{ __('Available Prepaid Words') }} @else {{ __('Available Prepaid Tokens') }} @endif: <span class="font-weight-bold ml-2">{{ number_format($user->tokens_prepaid) }}</span></p>										
										<p class="fs-12 mb-2">{{ __('Available Prepaid Media Credits') }}: <span class="font-weight-bold ml-2">{{ number_format($user->images_prepaid) }}</span></p>										
										<p class="fs-12 mb-2">{{ __('Available Prepaid Characters') }}: <span class="font-weight-bold ml-2">{{ number_format($user->characters_prepaid) }}</span></p>
										<p class="fs-12 mb-2">{{ __('Available Prepaid Minutes') }}: <span class="font-weight-bold ml-2">{{ number_format($user->minutes_prepaid) }}</span></p>
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-text mr-2 text-info"></i>@if ($settings->model_credit_name == 'words') {{ __('User Words') }} @else {{ __('User Tokens') }} @endif</label>
										<input type="number" class="form-control" value={{ $user->tokens }} name="tokens">
										<span class="text-muted fs-10">{{ __('Set as -1 for unlimited words') }}</span>									
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-text mr-2 text-info"></i>@if ($settings->model_credit_name == 'words') {{ __('User Prepaid Words') }} @else {{ __('User Prepaid Tokens') }} @endif</label>
										<input type="number" class="form-control" value={{ $user->tokens_prepaid }} name="tokens-prepaid">								
									</div>
								</div>
							</div>
							
							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-image mr-2 text-info"></i>{{ __('User Media Credits') }}</label>
										<input type="number" class="form-control" value={{ $user->images }} name="image-credits">
										<span class="text-muted fs-10">{{ __('Set as -1 for unlimited media credits') }}</span>									
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-image mr-2 text-info"></i>{{ __('User Prepaid Media Credits') }}</label>
										<input type="number" class="form-control" value={{ $user->images_prepaid }} name="image-credits-prepaid">								
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-waveform-lines mr-2 text-info"></i>{{ __('User Character Credits') }}</label>
										<input type="number" class="form-control @error('chars') is-danger @enderror" value={{ $user->characters }} name="chars">	
										<span class="text-muted fs-10">{{ __('Set as -1 for unlimited characters') }}</span>							
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-waveform-lines mr-2 text-info"></i>{{ __('User Prepaid Character Credits') }}</label>
										<input type="number" class="form-control @error('chars_prepaid') is-danger @enderror" value={{ $user->characters_prepaid }} name="chars_prepaid">								
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-folder-music mr-2 text-info"></i>{{ __('User Minutes Credits') }}</label>
										<input type="number" class="form-control @error('minutes') is-danger @enderror" value={{ $user->minutes }} name="minutes">
										<span class="text-muted fs-10">{{ __('Set as -1 for unlimited minutes') }}</span>									
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-6">
								<div class="input-box mb-4">
									<div class="form-group">
										<label class="form-label fs-12 font-weight-bold"><i class="fa-solid fa-folder-music mr-2 text-info"></i>{{ __('User Prepaid Minutes Credits') }}</label>
										<input type="number" class="form-control @error('minutes_prepaid') is-danger @enderror" value={{ $user->minutes_prepaid }} name="minutes_prepaid">									
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer border-0 text-center pr-0">							
							<a href="{{ route('admin.user.list') }}" class="btn btn-cancel mr-2">{{ __('Return') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

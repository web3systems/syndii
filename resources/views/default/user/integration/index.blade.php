@extends('layouts.app')

@section('page-header')
<!-- PAGE HEADER -->
<div class="page-header mt-5-7 justify-content-center">
	<div class="page-leftheader text-center">
		<h4 class="page-title mb-0"><i class="text-primary mr-2 fs-16 fa-solid fa-rectangles-mixed"></i>{{ __('Integrations') }}</h4>
		<h6 class="text-muted">{{ __('Posts your contents directly to your favorite CMS') }}</h6>
		<ol class="breadcrumb mb-2 justify-content-center">
			<li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}"><i class="fa-solid fa-id-badge mr-2 fs-12"></i>{{ __('User') }}</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Integrations') }}</a></li>
		</ol>
	</div>
</div>
<!-- END PAGE HEADER -->
@endsection
@section('content')	
	<div class="row justify-content-center">
		<div class="col-lg-10 col-md-12 col-sm-12">
			<div class="card border-0 p-6 pt-7 pb-7">
				<div class="card-body pt-2">
					@foreach ($integrations as $integration)
						@if ($integration->app == 'wordpress')
							@if (App\Services\HelperService::extensionWordpressIntegration())
								<div class="col-4">
									<div class="cms-box text-center" style="height: auto; width: auto">																
										<img class="cms-image mb-4" src="{{ theme_url($integration->logo) }}" alt="">							
										<h5 class="cms-title font-weight-semibold fs-18">{{ ucfirst($integration->app) }}</h5>
										<p class="cms-description fs-14 text-muted">{{ __($integration->description) }}</p>
										<a href="{{ route('user.integration.wordpress') }}" class="cms-action ripple btn btn-primary pl-8 pr-8 fs-12">{{ __('Manage') }}</a>
									</div>
								</div>
							@endif
						@endif						
					@endforeach						
				</div>
			</div>
		</div>
	</div>
@endsection

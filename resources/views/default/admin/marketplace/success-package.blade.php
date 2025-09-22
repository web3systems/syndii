@extends('layouts.app')

@section('content')
	<div class="row mt-24">
		<div class="col-sm-12">
			<div class="card border-0 p-5 pt-4">
				<div class="card-body">
					<div class="row justify-content-center">	
						<div class="col-lg-4 col-md-5 col-sm-12">
							<h3 class="mb-3 fs-20 super-strong text-center">{{ __('Thank you for your purchase!') }}</h3>
							<p class="fs-12 text-muted text-center mb-5">{{ __('Payment for') }} {{ $theme['name'] }} {{ __('was successful') }}</p>

							<div class="card shadow-0 theme">								
								<div class="card-body text-center">	
									<div class="theme-name mt-3">
										<h6 class="mb-4 fs-12 text-muted">{{ __('For further instructions and details please contact our support team') }}</h6>
									</div>

									<div class="theme-action text-center mt-4 mb-4">
										<a href="https://berkine.ticksy.com/" target="_blank"  class="btn btn-primary ripple" style="min-width: 250px; text-transform: none; font-size: 11px; padding-top: 10px; padding-bottom: 10px;">{{ __('Contact Support') }}</a>
									</div>	
								</div>
							</div>	

						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
@endsection




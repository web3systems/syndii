@if (config('frontend.custom_url.status') == 'on')
    <script type="text/javascript">
		window.location.href = "{{ config('frontend.custom_url.link') }}"
	</script>
@else

<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
	<head>
		<!-- Meta Data -->
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">  
        <meta name="robots" content="index, follow">	   
        <meta name="revisit-after" content="7 days">	   
        <meta name="distribution" content="web">	
		
		@yield('metadata')

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

		@include('layouts.frontend.header')

		@php
			$scss_path = 'resources/views/' . get_theme() . '/scss/frontend.scss';
		@endphp

		<!-- All Styles -->
		@vite($scss_path)

		<!--Google AdSense-->
		{!! adsense_header() !!}

		<!--Custom Header JS Code-->
		@if ($frontend_settings)
			@if (!is_null($frontend_settings->custom_header_code)) 
				{!! $frontend_settings->custom_header_code !!}
			@endif
		@endif
	</head>

	<body class="app sidebar-mini frontend-body {{ Request::path() != '/' ? 'blue-background' : '' }}">

		@if ($extension->maintenance_feature)
			
			<div class="container">
				<div class="row text-center h-100vh align-items-center">
					<div class="col-md-12">
						<img src="{{ theme_url($extension->maintenance_banner) }}" alt="Maintenance Image">
						<h2 class="mt-4 font-weight-bold">{{ __($extension->maintenance_header) }}</h2>
						<h5>{{ __($extension->maintenance_message) }} </h5>						
					</div>					
				</div>
				<footer class="text-center  align-items-center">
					<p class="text-muted">{{ __($extension->maintenance_footer) }} </p>
				</footer>
			</div>
		@else

			@if (config('frontend.frontend_page') == 'on')
						
				<div class="page">
					<div class="page-main">
						<section id="main">					
							<div class="relative flex items-top justify-center min-h-screen">				
								<div class="container-fluid fixed-top pl-0 pr-0" id="navbar-container">
									
									@yield('menu')
				
								</div>				
							</div>  
						</section>

		
						<div class="main-content">
							<div class="side-app frontend-background">

								@yield('content')

							</div>                   
						</div>
					</div>				
				</div>
			
				<!-- FOOTER SECTION
				========================================================-->
				@yield('footer')
				

			@endif
		
		@endif

		@include('layouts.frontend.footer')

		<!--Custom Body JS Code-->
		@if ($frontend_settings)
			@if (!is_null($frontend_settings->custom_body_code)) 
				{!! $frontend_settings->custom_body_code !!}
			@endif
		@endif

	</body>
</html>

@endif
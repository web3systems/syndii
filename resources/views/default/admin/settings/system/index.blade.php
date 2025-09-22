@extends('layouts.app')

@section('page-header')
	<!-- EDIT PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('System Settings') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-sliders mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{url('#')}}"> {{ __('General Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('System Settings') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<div class="row justify-content-center">
		<div class="col-lg-4 col-md-6 col-sm-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{{ __('Clear Cache') }}</h3>
				</div>
				<div class="card-body">
					<form id="clear-cache-form" method="POST" action="{{ route('admin.settings.system.cache')}}" enctype="multipart/form-data">
						@csrf
						
						<div class="row">
							<div class="col-sm-12 col-md-12">
								<h6 class="fs-14 mt-2">{{ __('Clear all application cache files') }}</h6>
							</div>
						</div>
						<div class="card-footer text-center border-0 pb-2 pt-5">													
							<button id="clear-cache" type="button" class="btn btn-primary">{{ __('Clear Cache') }}</button>						
						</div>		
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-6 col-sm-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{{ __('Sitemap') }}</h3>
				</div>
				<div class="card-body">
					<form id="generate-sitemap-form" method="POST" action="{{ route('admin.settings.system.sitemap')}}" enctype="multipart/form-data">
						@csrf
						
						<div class="row">
							<div class="col-sm-12 col-md-12">
								<h6 class="fs-14 mt-2">{{ __('Generated sitemap.xml file, stored at public folder') }}</h6>
							</div>
						</div>
						<div class="card-footer text-center border-0 pb-2 pt-5">													
							<button id="generate-sitemap" type="button" class="btn btn-primary">{{ __('Generate Sitemap') }}</button>						
						</div>		
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script type="text/javascript">
		let loading = `<span class="loading">
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						<span style="background-color: #fff;"></span>
						</span>`;
		$('#clear-cache').on('click',function(e) {

			e.preventDefault();

			const form = document.getElementById("clear-cache-form");
			let data = new FormData(form);

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: $('#clear-cache-form').attr('action'),
				data: data,
				processData: false,
				contentType: false,
				beforeSend: function() {
					$('#clear-cache').prop('disabled', true);
					let btn = document.getElementById('clear-cache');					
					btn.innerHTML = loading;  
					document.querySelector('#loader-line')?.classList?.remove('hidden');      
				},
				complete: function() {
					document.querySelector('#loader-line')?.classList?.add('hidden'); 
					$('#clear-cache').prop('disabled', false);
					$('#clear-cache').html('{{ __("Clear Cache") }}');            
				},
				success: function(data) {

					if (data['status'] == 200) {
						toastr.success('{{ __('Application cache was cleared successfully') }}');
					} else {
						toastr.error('{{ __('Cache was not cleared properly') }}');
					}

				},
				error: function(data) {
					toastr.error('{{ __('Cache was not cleared properly') }}');
				}
			}).done(function(data) {})
		});


		$('#generate-sitemap').on('click',function(e) {

			e.preventDefault();

			const form = document.getElementById("generate-sitemap-form");
			let data = new FormData(form);

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: $('#generate-sitemap-form').attr('action'),
				data: data,
				processData: false,
				contentType: false,
				beforeSend: function() {
					$('#generate-sitemap').prop('disabled', true);
					let btn = document.getElementById('generate-sitemap');					
					btn.innerHTML = loading;  
					document.querySelector('#loader-line')?.classList?.remove('hidden');      
				},
				complete: function() {
					document.querySelector('#loader-line')?.classList?.add('hidden'); 
					$('#generate-sitemap').prop('disabled', false);
					$('#generate-sitemap').html('{{ __("Generate Sitemap") }}');            
				},
				success: function(data) {

					if (data['status'] == 200) {
						toastr.success('{{ __('Sitemap.xml file has been successfully generated') }}');
					} else {
						toastr.error(data['message']);
					}

				},
				error: function(data) {
					toastr.error('{{ __('There was an issue with generating sitemap.xml file') }}');
				}
			}).done(function(data) {})
		});
	</script>
@endsection


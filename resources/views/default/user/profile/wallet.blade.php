@extends('layouts.app')

@section('css')
<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- EDIT PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('My Wallet') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('user.dashboard')}}"><i class="fa-solid fa-id-badge mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{route('user.profile')}}"> {{ __('My Profile') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('My Wallet') }}</a></li>
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
						@if (App\Services\HelperService::extensionSaaS())
							@if (App\Services\HelperService::extensionWallet())
								@if (App\Services\HelperService::extensionWalletFeature())
									<div class="col-sm-12">
										<div class="text-center pt-4">
											<a href="{{ route('user.wallet') }}" class="fs-13 text-primary"><i class="fa-solid fa-wallet mr-1"></i> {{ __('My Wallet') }}</a>
										</div>
									</div>
								@endif
							@endif
						@endif
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
			<div class="row">
				<div class="col-lg-4 col-md-6 col-sm-12">
					<div class="card overflow-hidden  ">
						<div class="card-body d-flex">
							<div class="usage-info w-100">
								<p class=" mb-3 fs-12 font-weight-bold">{{ __('My Wallet Balance') }}</p>
								<h2 class="mb-2 number-font fs-20">{{ number_format(auth()->user()->wallet, 2) }} {{config('payment.default_system_currency')}}</h2>
							</div>
							<div class="usage-icon text-right">
								<i class="fa-solid fa-wallet"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-12">
					<div class="card overflow-hidden  ">
						<div class="card-body d-flex">
							<div class="usage-info w-100">
								<p class=" mb-3 fs-12 font-weight-bold">{{ __('Total Redeemed Codes') }}</p>
								<h2 class="mb-2 number-font fs-20">{{ number_format($data['total']) }}</h2>
							</div>
							<div class="usage-icon text-right">
								<i class="fa-solid fa-gift"></i>
							</div>
						</div>
					</div>
				</div>
	
				<div class="col-lg-4 col-md-6 col-sm-12">
					<div class="card overflow-hidden  ">
						<div class="card-body d-flex">
							<div class="usage-info w-100">
								<p class=" mb-3 fs-12 font-weight-bold">{{ __('Total Redeemed Amount') }}</p>
								<h2 class="mb-2 number-font fs-20">{{ number_format($data['amount'], 2) }} {{config('payment.default_system_currency')}}</h2>
							</div>
							<div class="usage-icon text-right">
								<i class="fa-solid fa-sack-dollar"></i>
							</div>
						</div>
					</div>
				</div>
					
			</div>

			<form method="POST" class="w-100" action="{{ route('user.wallet.store') }}" enctype="multipart/form-data">
				@method('PUT')
				@csrf

				<div class="card  ">
					<div class="card-header">
						<h3 class="card-title"><i class="fa-solid fa-sack-dollar mr-2 text-primary"></i>{{ __('Redeem Gift Codes') }}</h3>
					</div>
					<div class="card-body pb-0">					
						<div class="row">
							<div class="col-sm-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Redeem Gift Code') }}</label>
										<input type="text" class="form-control" name="code" placeholder="{{__('Please include your gift code to redeem here...')}}">								
									</div>
								</div>
							</div>

						</div>
						<div class="card-footer   text-center mb-2 pr-0">
							<button type="submit" class="btn btn-primary">{{ __('Redeem') }}</button>							
						</div>					
					</div>				
				</div>
			</form>


			<form id="transfer-funds-form" method="POST" class="w-100" action="" enctype="multipart/form-data">
				@csrf

				<div class="card  ">
					<div class="card-header">
						<h3 class="card-title"><i class="fa-solid fa-transfer mr-2 text-primary"></i>{{ __('Transfer Your Wallet Balance to Friends') }}</h3>
					</div>
					<div class="card-body pb-0">					
						<div class="row">
							<div class="col-sm-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Email Address') }}</label>
										<input type="text" class="form-control @error('email') is-danger @enderror" name="email" placeholder="{{__('Please enter your friends email address...')}}">
										@error('email')
											<p class="text-danger">{{ $errors->first('email') }}</p>
										@enderror								
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Transfer Amount') }}</label>
										<input type="number" class="form-control @error('amount') is-danger @enderror" min="1" name="amount" placeholder="{{__('Specify how much you want to transfer...')}}">
										@error('amount')
											<p class="text-danger">{{ $errors->first('amount') }}</p>
										@enderror								
									</div>
								</div>
							</div>

						</div>
						<div class="card-footer   text-center mb-2 pr-0">
							<button type="button" class="btn btn-primary" id="transfer-amount">{{ __('Transfer') }}</button>							
						</div>					
					</div>				
				</div>
			</form>

			
			<div class="row mt-5">
				<div class="col-lg-12 col-md-12 col-xm-12">
					<div class="card  ">
						<div class="card-header">
							<h3 class="card-title">{{ __('Redeemed Gift Cards') }}</h3>
						</div>
						<div class="card-body pt-2">
							<!-- SET DATATABLE -->
							<table id='usageTable' class='table' width='100%'>
									<thead>
										<tr>
											<th width="10%">{{ __('Gift Card') }}</th>									
											<th width="10%">{{ __('Amount') }}</th>
											<th width="10%">{{ __('Status') }}</th>
											<th width="10%">{{ __('Redeemed On') }}</th>
										</tr>
									</thead>
							</table> <!-- END SET DATATABLE -->

						</div>
					</div>
				</div>
			</div>


			<div class="row mt-5">
				<div class="col-lg-12 col-md-12 col-xm-12">
					<div class="card  ">
						<div class="card-header">
							<h3 class="card-title">{{ __('Transfers') }}</h3>
						</div>
						<div class="card-body pt-2">
							<!-- SET DATATABLE -->
							<table id='transferTable' class='table' width='100%'>
									<thead>
										<tr>
											<th width="10%">{{ __('Transfer ID') }}</th>
											<th width="10%">{{ __('Sender') }}</th>									
											<th width="10%">{{ __('Receiver') }}</th>									
											<th width="10%">{{ __('Amount') }}</th>
											<th width="10%">{{ __('Status') }}</th>
											<th width="10%">{{ __('Transfer Date') }}</th>
										</tr>
									</thead>
							</table> <!-- END SET DATATABLE -->

						</div>
					</div>
				</div>
			</div>
		</div>


	</div>
	<!-- EDIT USER PROFILE PAGE --> 
@endsection

@section('js')
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			var table2 = $('#usageTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				"order": [[ 3, "desc" ]],
				language: {
					"emptyTable": "<div><br>{{ __('No gift cards redeemed yet') }}</div>",
					"info": "{{ __('Showing page') }} _PAGE_ {{ __('of') }} _PAGES_",
					search: "<i class='fa fa-search search-icon'></i>",
					lengthMenu: '_MENU_ ',
					paginate : {
						first    : '<i class="fa fa-angle-double-left"></i>',
						last     : '<i class="fa fa-angle-double-right"></i>',
						previous : '<i class="fa fa-angle-left"></i>',
						next     : '<i class="fa fa-angle-right"></i>'
					}
				},
				pagingType : 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: "{{ route('user.wallet') }}",
				columns: [
					{
						data: 'custom-code',
						name: 'custom-code',
						orderable: false,
						searchable: true
					},					
					{
						data: 'custom-value',
						name: 'custom-value',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-status',
						name: 'custom-status',
						orderable: true,
						searchable: true
					},	
					{
						data: 'created-on',
						name: 'created-on',
						orderable: true,
						searchable: true
					},										
				]
			});


			var table3 = $('#transferTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				"order": [[ 4, "desc" ]],
				language: {
					"emptyTable": "<div><br>{{ __('No user transfers conducted yet') }}</div>",
					"info": "{{ __('Showing page') }} _PAGE_ {{ __('of') }} _PAGES_",
					search: "<i class='fa fa-search search-icon'></i>",
					lengthMenu: '_MENU_ ',
					paginate : {
						first    : '<i class="fa fa-angle-double-left"></i>',
						last     : '<i class="fa fa-angle-double-right"></i>',
						previous : '<i class="fa fa-angle-left"></i>',
						next     : '<i class="fa fa-angle-right"></i>'
					}
				},
				pagingType : 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: "{{ route('user.wallet.transfer.list') }}",
				columns: [{
						data: 'transfer_id',
						name: 'transfer_id',
						orderable: true,
						searchable: true
					},
					{
						data: 'sender',
						name: 'sender',
						orderable: false,
						searchable: true
					},	
					{
						data: 'receiver',
						name: 'receiver',
						orderable: false,
						searchable: true
					},					
					{
						data: 'custom-value',
						name: 'custom-value',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-status',
						name: 'custom-status',
						orderable: true,
						searchable: true
					},	
					{
						data: 'created-on',
						name: 'created-on',
						orderable: true,
						searchable: true
					}
				]
			});

			$('#transfer-amount').on('click', function(e) {
				e.preventDefault();
				
				Swal.fire({
					title: '{{ __("Confirm Balance Transfer") }}',
					text: '{{ __("Are you sure you want to transfer this amount to this user? This action cannot be undone.") }}',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: '{{ __("Yes, transfer it") }}',
					cancelButtonText: '{{ __("Cancel") }}',
					reverseButtons: true,
				}).then((result) => {
					if (result.isConfirmed) {
						transferFunds();
					}
				});
			});

			function transferFunds() {

				const form = document.getElementById('transfer-funds-form');
    			const formData = new FormData(form);

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: 'POST',
					url: '/app/user/profile/wallet/transfer',
					data: formData,
					processData: false,
					contentType: false,
					beforeSend: function() {
						// Show loading indicator
						document.querySelector('#loader-line')?.classList?.remove('hidden');
					},
					complete: function() {
						// Hide loading indicator
						document.querySelector('#loader-line')?.classList?.add('hidden');
					},
					success: function(response) {
						if (response.status === 200) {
							
							// Show success message
							toastr.success('{{ __("Funds have been transfered successfully") }}');

							setTimeout(function() {
								window.location.reload();
							}, 1000);
							
						} else {
							// Show error message
							toastr.error(response.message || '{{ __("Failed to transfer funds") }}');
						}
					},
					error: function(xhr) {
						// Show error message
						let errorMessage = '{{ __("An error occurred while transfering funds") }}';
						if (xhr.responseJSON && xhr.responseJSON.message) {
							errorMessage = xhr.responseJSON.message;
						}
						toastr.error(errorMessage);
					}
				});
			}

		});
	</script>
@endsection


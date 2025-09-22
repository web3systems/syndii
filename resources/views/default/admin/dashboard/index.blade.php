@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER-->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0 fs-30">{{ __('Welcome Admin') }}</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-tree-map mr-2 fs-12"></i>{{ __('Admin Dashboard') }}</a></li>
			</ol>
		</div>
	</div>
	<!--END PAGE HEADER -->
@endsection

@section('content')	
	
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 mt-3">
			<div class="card ">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h4 class="page-title fs-25 mt-0" style="line-height: 1.3rem;">{{ __("What's New Today") }}</h4>
						<span class="fs-10 text-muted"><i class="fa-solid fa-calendar mr-2"></i> {{ now()->format('d M, Y H:i A'); }}</span>
					</div>
				</div>
				<div class="card-body pb-5 pt-2">
					<div class="row m-1">
						@if (App\Services\HelperService::extensionSaaS())
							<div class="col-lg-2 col-md-3 col-sm-12">     
								<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/finance/dashboard') }}'">
									<div class="p-4">
										<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['revenue']) }}</h3>  
										<i class="fa-solid fa-sack-dollar text-muted fs-18 mb-4 font-weight-normal"></i>
										<h6 class="text-muted fs-14">{{ __('Revenue') }} <i class="ml-1 text-muted fs-13 font-weight-normal fa-solid fa-circle-info" data-tippy-content="{{ __("Today's revenue") }}"></i></h6>   
									</div>    
								</div>                                                             
							</div>
						@endif
						<div class="col-lg-2 col-md-3 col-sm-12">     
							<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/users/list') }}'">
								<div class="p-4">
									<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['new_users']) }}</h3>  
									<i class="fa-solid fa-user text-muted fs-18 mb-4 font-weight-normal"></i>
									<h6 class="text-muted fs-14">{{ __('New Users') }} <i class="ml-1 text-muted fs-13 font-weight-normal fa-solid fa-circle-info" data-tippy-content="{{ __("Today's new users") }}"></i></h6>   
								</div>    
							</div>                                                             
						</div>
						@if (App\Services\HelperService::extensionSaaS())
							<div class="col-lg-2 col-md-3 col-sm-12">     
								<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/finance/subscriptions') }}'">
									<div class="p-4">
										<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['subscribers']) }}</h3>  
										<i class="fa-solid fa-user-visor text-muted fs-18 mb-4 font-weight-normal"></i>
										<h6 class="text-muted fs-14">{{ __('Subscribers') }} <i class="ml-1 text-muted fs-13 font-weight-normal fa-solid fa-circle-info" data-tippy-content="{{ __("Today's new subscribers") }}"></i></h6>   
									</div>    
								</div>                                                             
							</div>
						@endif
						@if (App\Services\HelperService::extensionSaaS())
							<div class="col-lg-2 col-md-3 col-sm-12">     
								<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/finance/transactions') }}'">
									<div class="p-4">
										<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['transactions']) }}</h3>  
										<i class="fa-solid fa-money-bill-transfer text-muted fs-18 mb-4 font-weight-normal"></i>
										<h6 class="text-muted fs-14">{{ __('Transactions') }} <i class="ml-1 text-muted fs-13 font-weight-normal fa-solid fa-circle-info" data-tippy-content="{{ __("Today's new transactions") }}"></i></h6>   
									</div>    
								</div>                                                             
							</div>
						@endif
						<div class="col-lg-2 col-md-3 col-sm-12">     
							<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/support') }}'">
								<div class="p-4">
									<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['tickets']) }}</h3>  
									<i class="fa-solid fa-headset text-muted fs-18 mb-4 font-weight-normal"></i>
									<h6 class="text-muted fs-14">{{ __('Tickets') }} <i class="ml-1 text-muted fs-13 font-weight-normal fa-solid fa-circle-info" data-tippy-content="{{ __("Today's new support tickets") }}"></i></h6>   
								</div>    
							</div>                                                             
						</div>	
						<div class="col-lg-2 col-md-3 col-sm-12">     
							<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/users/activity') }}'">
								<div class="p-4">
									<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['online_users']) }}</h3>  
									<i class="fa-solid fa-screen-users text-muted fs-18 mb-4 font-weight-normal"></i>
									<h6 class="text-muted fs-14">{{ __('Online Users') }} <i class="ml-1 text-muted fs-13 font-weight-normal fa-solid fa-circle-info" data-tippy-content="{{ __("Currently online users") }}"></i></h6>   
								</div>    
							</div>                                                             
						</div>					
					</div>
					<div class="row m-1 mt-3">
						<div class="col-lg-6 col-md-12 col-sm-12">     
							<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/settings/upgrade') }}'" style="height: 153px">
								<div class="p-6 mt-auto mb-auto">
									<h3 class="fs-16 font-weight-semibold"><span id="current-update-status">{{config('app.name')}} {{ __('has the latest version installed') }}</span><span id="new-update-status" class="hidden">{{__('There is a new update available for')}} {{config('app.name')}}</span></h3> 						
									<h6 class="text-muted fs-14 mb-0"><i class="fa-solid fa-box-open text-muted fs-14 mr-1 font-weight-normal"></i> {{ __('Version') }} <span id="version-number">{{config('app.version')}}</span> </h6>
								</div>    
							</div>                                                             
						</div>
						<div class="col-lg-2 col-md-3 col-sm-12">     
							<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/davinci/dashboard') }}'">
								<div class="p-4">
									<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['tokens_used']) }}</h3>  
									<i class="fa-solid fa-message-lines text-muted fs-18 mb-4 font-weight-normal"></i>
									<h6 class="text-muted fs-14">{{ __('Tokens Credits Used') }}</h6>   
								</div>    
							</div>                                                             
						</div>
						<div class="col-lg-2 col-md-3 col-sm-12">     
							<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/admin/davinci/dashboard') }}'">
								<div class="p-4">
									<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['media_used']) }}</h3>  
									<i class="fa-solid fa-photo-film-music text-muted fs-18 mb-4 font-weight-normal"></i>
									<h6 class="text-muted fs-14">{{ __('Media Credits Used') }}</h6>   
								</div>    
							</div>                                                             
						</div>
						<div class="col-lg-2 col-md-3 col-sm-12">     
							<div class="card mb-2 dashboard-tool-box" onclick="window.location.href='{{ url('app/user/document') }}'">
								<div class="p-4">
									<h3 class="fs-25 mb-6 font-weight-bold">{{ number_format($today['contents']) }}</h3>  
									<i class="fa-solid fa-folder-grid text-muted fs-18 mb-4 font-weight-normal"></i>
									<h6 class="text-muted fs-14">{{ __('Content Created') }}</h6>   
								</div>    
							</div>                                                             
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>	

	@if (App\Services\HelperService::extensionSaaS())
		<h4 class="page-title fs-25">{{ __('Finance Metrics') }}</h4>

		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 mt-3">
				<div class="card  " id="admin-dashboard-panels">
					<div class="card-header pt-4 pb-4 border-0">
						<div class="mt-3">
							<h3 class="card-title mb-2"><i class="fa-solid fa-badge-dollar mr-2 text-muted"></i>{{ __('Total Earnings') }}</h3>
						</div>
					</div>
					<div class="card-body pl-0 pr-0 pb-0">
						<div class="row mb-6">
							<div class="col-lg col-md-6 col-sm-12 mt-auto mb-auto dashboard-box-border-right">                        
								<div class="title text-center dashboard-title">
									<h6 class="text-muted fs-12 mb-2 font-weight-bold">{{ __('Total Revenue') }}</h6>
									<h3 class="fs-20 mb-2 font-weight-bold text-primary">{!! config('payment.default_system_currency_symbol') !!} {{ number_format((float)$total['total_income'][0]['data'],2) }}</h3>  
									<h6 class="text-muted fs-10">{{ __('Lifetime') }} <span class="font-weight-bold">{{ __('earnings') }}</span></h6>    
								</div>                                               
							</div>
							<div class="col-lg col-md-6 col-sm-12 mt-auto mb-auto dashboard-box-border-right">                        
								<div class="title text-center dashboard-title">
									<h6 class="text-muted fs-12 mb-2 font-weight-bold">{{ __('Total Spending') }}</h6>
									<h3 class="fs-20 mb-2 font-weight-bold text-danger">${{ number_format((float)$total['total_spending'], 3) }}</h3>     
									<h6 class="text-muted fs-10">{{ __('Estimated') }} <span class="font-weight-bold">{{ __('AI service costs') }}</span></h6> 
								</div>                                               
							</div>
							<div class="col-lg col-md-6 col-sm-12 p-5  dashboard-box-border-right">
								<div class="text-center">
									<h6 class="fs-12 text-muted font-weight-bold">{{ __('Total Active Subscribers') }}</h6>
									<h6 class="mb-0 fs-20 font-weight-bold">{{ number_format($total['total_subscribers']) }}</h6>
								</div>
							</div>
							<div class="col-lg col-md-6 col-sm-12 p-5  dashboard-box-border-right">
								<div class="text-center">
									<h6 class="fs-12 text-muted font-weight-bold">{{ __('Referral Earnings') }}</h6>
									<h6 class="mb-0 fs-20 font-weight-bold">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total['referral_earnings'][0]['data'], 2) }}</h6>
								</div>
							</div>
							<div class="col-lg col-md-6 col-sm-12 p-5 ">
								<div class="text-center">
									<h6 class="fs-12 text-muted font-weight-bold">{{ __('Referral Payouts') }}</h6>
									<h6 class="mb-0 fs-20 font-weight-bold">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total['referral_payouts'][0]['data'], 2) }}</h6>
								</div>
							</div>
						</div>
						<div class="row pl-5 pr-5">						
							<div class="col-lg-2 col-md-2 col-sm-12">
								<div class="mb-6">
									<h2 class="mb-1"><span class="number-font fs-20">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total_monthly['income_current_month'][0]['data'], 2) }}</span> <span id="revenue_difference"></span><h2>
									<p class="text-muted fs-11 mb-2"> {{ __('Current Month Earnings') }}</p>
								</div>
								<div class="mb-7">
									<h2 class="mb-1"><span class="number-font fs-20">${{ number_format((float)$total_monthly['spending_current_month'], 3) }}</span> <span id="spending_difference"></span><h2>
									<p class="text-muted fs-11 mb-2"> {{ __('Current Month Spendings') }}</p>
								</div>
								<a href="{{ route('admin.finance.report.monthly') }}" class="btn btn-primary mb-5" style="text-transform: none; width: 175px">{{ __('Current Month Report') }}</a>
								<a href="{{ route('admin.finance.report.yearly') }}" class="btn btn-primary mb-4" style="text-transform: none; width: 175px; background: #1e1e2d; border-color: #1e1e2d;">{{ __('Current Year Report') }}</a>
							</div>

							<div class="col-lg-10 col-md-10 col-sm-12">
								<div>
									<span class="fs-10 text-muted" style="position: absolute; right: 1.5rem; top: -10px; background: #f5f9fc; padding: 0.5rem 1rem; border-radius: 10px;">{{ __('Current Year') }}</span>
									<canvas id="financeEarningsChart" style="height: 300px"></canvas>
								</div>
							</div>
						</div>
						<div class="row dashboard-box-border-top mt-6 no-gutters">
							<div class="col-lg-3 col-md-4 col-sm-12 dashboard-box-border-right">
								<div class="p-5">
									<h6 class="text-muted fs-12 mb-2"> {{ __('New Subscribers') }} ({{  __(date('M')) }})</h6>
									<h6 class="mb-1"><span class="number-font fs-20">{{ number_format($total_data_monthly['new_subscribers_current_month']) }}</span></h6>								
									<p class="text-muted fs-11 data-percentage-change mb-0"><span id="subscribers_change"></span> {{ __('this month vs last') }}</p>
								</div>
							</div>
							<div class="col-lg-3 col-md-4 col-sm-12 dashboard-box-border-right">
								<div class="p-5">
									<h6 class="text-muted fs-12 mb-2"> {{ __('New Transactions') }} ({{  __(date('M')) }})</h6>
									<h6 class="mb-1"><span class="number-font fs-20">{{ number_format($total_data_monthly['transactions_current_month']) }}</span></h6>								
									<p class="text-muted fs-11 data-percentage-change mb-0"><span id="transactions_change"></span> {{ __('this month vs last') }}</p>
								</div>							
							</div>
							<div class="col-lg-3 col-md-4 col-sm-12 dashboard-box-border-right">
								<div class="p-5">
									<h6 class="text-muted fs-12 mb-2"> {{ __('Gift Cards Redeemed') }} ({{  __(date('M')) }})</h6>
									<h6 class="mb-1"><span class="number-font fs-20">{{ number_format($total_data_monthly['gift_current_month']) }}</span></h6>								
									<p class="text-muted fs-11 data-percentage-change mb-0"><span id="gift_card_change"></span> {{ __('this month vs last') }}</p>
								</div>							
							</div>
							<div class="col-lg-3 col-md-4 col-sm-12">
								<div class="p-5">
									<h6 class="text-muted fs-12 mb-2"> {{ __('Applied Gift Funds') }} ({{  __(date('M')) }})</h6>
									<h6 class="mb-1"><span class="number-font fs-20">{{ number_format($total_data_monthly['gift_usage_current_month']) }}</span></h6>								
									<p class="text-muted fs-11 data-percentage-change mb-0"><span id="gift_funds_change"></span> {{ __('this month vs last') }}</p>
								</div>							
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div>	

		<div class="row mt-3">
			<div class="col-md-4 col-sm-12 mt-3">
				<div class="card   dashboard-fixed-457" id="admin-dashboard-panels">
					<div class="pl-5 pt-4 pb-4  ">
						<div class="mt-3">
							<h3 class="card-title mb-2"><i class="fa-solid fa-box-dollar mr-2 text-muted"></i>{{ __('Revenue Source') }}</h3>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div style="position: relative">
									<div class="mt-4">
										<canvas id="revenuePlan" class="h-330"></canvas>
									</div>
								</div>							
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-12 mt-3">
				<div class="card   dashboard-fixed-457" id="admin-dashboard-panels">
					<div class="pl-5 pt-4 pb-4  ">
						<div class="mt-3">
							<h3 class="card-title mb-2"><i class="fa-solid fa-microchip-ai mr-2 text-muted"></i>{{ __('AI Cost Breakdown') }} (USD)</h3>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div style="position: relative">
									<div class="mt-4">
										<canvas id="costService" class="h-330"></canvas>
									</div>
								</div>							
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-12 mt-3">
				<div class="card dashboard-fixed-457" id="admin-dashboard-panels">
					<div class="card-header pt-4 pb-4 border-0">
						<div class="mt-3">
							<h3 class="card-title mb-2"><i class="fa-solid fa-users-viewfinder mr-2 text-muted"></i>{{ __('Non-Subscribers vs Subscribers') }}</h3>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div style="position: relative">
									<div class="mt-4">
										<canvas id="userDoughnut" class="h-330"></canvas>
									</div>								
								</div>							
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif

	<div class="row mt-3">
		@if (App\Services\HelperService::extensionSaaS())
			<div class="col-lg col-md-12 col-sm-12 mt-3">
				<div class="card pb-5" id="admin-dashboard-panels">
					<div class="card-header pt-4 pb-4 border-0">
						<div class="mt-3">
							<h3 class="card-title mb-2"><i class="fa-solid fa-money-bill-transfer mr-2 text-muted"></i>{{ __('Latest Transactions') }}</h3>
							<div class="btn-group dashboard-menu-button">
								<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
								<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
									<a class="dropdown-item" href="{{ route('admin.finance.transactions') }}">{{ __('View All') }}</a>	
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12 pl-6 pr-6">
						<div class="dashboard-3-column">
							<div class="font-weight-semibold text-muted fs-12">{{ __('Plan') }}</div>
							<div class="text-right font-weight-semibold text-muted fs-12">{{ __('Price') }}</div>
							<div class="text-right font-weight-semibold text-muted fs-12">{{ __('Gateway') }}</div>
							<div class="text-right mr-4 font-weight-semibold text-muted fs-12">{{ __('Status') }}</div>
							<div class="text-right mr-5 font-weight-semibold text-muted fs-12">{{ __('Date') }}</div>
						</div>
					</div>
					<div class="card-body pt-2 height-400">

						<div class="row">
							
							@foreach ($transaction as $data)
								<div class="col-sm-12">					
									<div class="card" onclick="window.location.href='{{ url('app/admin/finance/transaction/'.$data->id.'/show') }}'">
										<div class="card-body pt-2 pb-2 pl-4 pr-4 dashboard-3-column">
											<div class="template-icon">
												<div class="fs-12">
													<p class="font-weight-semibold fs-12 mb-0">{{ $data->plan_name }}</p>
													<p class="text-muted fs-10 mb-0">{{ ucfirst($data->frequency) }} {{ __('Plan') }}</p>
												</div>								
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-12 mb-0 text-muted">{!! config('payment.default_system_currency_symbol') !!}{{ number_format($data->price) }}</p>
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-12 mb-0 text-muted">{{ $data->gateway }}</p>
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-12 mb-0 text-muted">{{ __(ucfirst($data->status)) }}</p>
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-10 mb-0 text-muted">{{ date_format($data->created_at, 'd M Y') }}<br><span>{{ date_format($data->created_at, 'H:i A') }}</span></p>
											</div>
										</div>
									</div>													
								</div>
							@endforeach

						</div>
					</div>
				</div>
			</div>
			<div class="col-lg col-md-12 col-sm-12 mt-3">
				<div class="card pb-5 dashboard-fixed-457" id="admin-dashboard-panels">
					<div class="pl-5 pt-4 pb-4  ">
						<div class="mt-3">
							<h3 class="card-title mb-2"><i class="fa-solid fa-credit-card-front mr-2 text-muted"></i>{{ __('Pending Approvals') }}</h3>
							<div class="btn-group dashboard-menu-button">
								<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
								<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
									<a class="dropdown-item" href="{{ route('admin.finance.transactions') }}">{{ __('View All') }}</a>	
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12 pl-6 pr-6">
						<div class="dashboard-5-column">
							<div class="font-weight-semibold text-muted fs-12">{{ __('Plan') }}</div>
							<div class="text-right font-weight-semibold text-muted fs-12">{{ __('User') }}</div>
							<div class="text-right font-weight-semibold text-muted fs-12">{{ __('Price') }}</div>
							<div class="text-right font-weight-semibold text-muted fs-12">{{ __('Gateway') }}</div>
							<div class="text-right font-weight-semibold text-muted fs-12">{{ __('Status') }}</div>
						</div>
					</div>
					<div class="card-body pt-2 height-400">

						<div class="row">
							
							@foreach ($approvals as $data)
								<div class="col-sm-12">					
									<div class="card" onclick="window.location.href='{{ theme_url('app/admin/finance/transaction/'.$data->id.'/show') }}'">
										<div class="card-body pt-2 pb-2 pl-4 pr-4 dashboard-5-column">
											<div>
												<div class="fs-12">
													<p class="font-weight-semibold fs-12 mb-0">{{ $data->plan_name }}</p>
													<p class="text-muted fs-10 mb-0">{{ ucfirst($data->frequency) }} {{ __('Plan') }}</p>
												</div>								
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-12 mb-0 text-muted">{{ $data->name }}</p>
												<p class="text-muted fs-10 mb-0">{{ ucfirst($data->email) }}</p>
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-12 mb-0 text-muted">{!! config('payment.default_system_currency_symbol') !!}{{ number_format($data->price) }}</p>
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-12 mb-0 text-muted">{{ $data->gateway }}</p>
											</div>
											<div class="text-right mb-auto mt-auto">
												<p class="fs-12 mb-0 text-muted">{{ __(ucfirst($data->status)) }}</p>
											</div>
										</div>
									</div>													
								</div>
							@endforeach

						</div>
					</div>
				</div>
			</div>
		@endif
	</div>

	<h4 class="page-title fs-25">{{ __('User Metrics') }}</h4>

	<div class="row mt-3">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body p-5">
					<div class="row">
						<div class="col-lg col-md-6 col-sm-12 dashboard-box-border-right">
							<div class="pl-6 pr-6">
								<i class="fa-solid fa-user-check fs-35 mt-3 float-right"></i>	
								<p class=" mb-2 fs-12 font-weight-bold text-muted mt-1">{{ __('Total Users') }}</p>
								<h2 class="mb-0"><span class="number-font-chars">{{ number_format($total['total_users']) }}</span></h2>									
							</div>
						</div>
						@if (App\Services\HelperService::extensionSaaS())
							<div class="col-lg col-md-6 col-sm-12 dashboard-box-border-right">
								<div class="pl-6 pr-6">
									<i class="fa-solid fa-user-visor text-primary fs-35 mt-3 float-right"></i>	
									<p class=" mb-2 fs-12 font-weight-bold mt-1 text-muted">{{ __('Total Subscribers') }}</p>
									<h2 class="mb-0"><span class="number-font-chars">{{ number_format($total['total_subscribers']) }}</span></h2>									
								</div>
							</div>
						@endif
						@if (App\Services\HelperService::extensionSaaS())
							<div class="col-lg col-md-6 col-sm-12 dashboard-box-border-right">
								<div class="pl-6 pr-6">
									<i class="fa-solid fa-users-medical fs-35 mt-3 float-right"></i>	
									<p class=" mb-2 fs-12 font-weight-bold mt-1 text-muted">{{ __('Total Referred') }}</p>
									<h2 class="mb-0"><span class="number-font-chars">{{ number_format($total['total_referred']) }}</span></h2>									
								</div>
							</div>
						@endif
						<div class="col-lg col-md-6 dashboard-box-border-right">
							<div class="pl-6 pr-6">
								<i class="fa-solid fa-user-headset fs-35 mt-3 float-right yellow"></i>	
								<p class=" mb-2 fs-12 font-weight-bold mt-1 text-muted">{{ __('Online Users') }}</p>
								<h2 class="mb-0"><span class="number-font-chars">{{ $users_online }}</span></h2>
							</div>
						</div>
						<div class="col-lg col-md-6">
							<div class="pl-6 pr-6">
								<i class="fa-solid fa-user-clock fs-35 mt-3 float-right"></i>	
								<p class=" mb-2 fs-12 font-weight-bold mt-1 text-muted">{{ __('Visitors Today') }}</p>
								<h2 class="mb-0"><span class="number-font-chars">{{ $users_today }}</span></h2>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-earth-americas mr-2 text-muted"></i>{{ __('Top Visitor Countries') }}</h3>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-3 col-md-4 col-sm-12 mt-3">
							<div class="card border-0 dashboard-fixed-457" id="admin-dashboard-panels" style="max-height: 457px;">
								
								<div class="card-body" style="overflow-y: scroll">
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<div>
												@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
													<div id="ga-preloader-3" style="position: absolute; left: 48%; top: 40%;"></div>
													<ul id="countryList"></ul>
												@else
													<h6 class="text-center fs-12 text-muted justify-content-center">{{ __('GA 4 is not configured yet') }}</h6>
												@endif
												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-6 col-md-4 col-sm-12 mt-auto mb-auto">	
							<div class="mt-3">
								@if (config('services.google.maps.enable') == 'on')
									<div id="countries-analytics-chart" class="h-330"></div>
								@else 
									<div class="text-center">
										<p class="fs-12 mt-6">{{ __('Google Maps is Disabled') }}</p>
									</div>
								@endif				
							</div>						
						</div>

						<div class="col-lg-3 col-md-4 col-sm-12 mt-3 no-gutters">
							<div class="col-sm-12 no-gutters">
								<div class="card text-center">
									<div class="card-header pt-3 pb-1 border-0 justify-content-center">
										<div class="mt-2 mb-0 text-center">
											<h3 class="card-title mb-0 text-center font-weight-semibold">{{ __('Average Session Duration') }}</h3>
											<span class="fs-10 text-muted">({{ __('Last 30 Days') }})</span>
										</div>
									</div>
									<div class="card-body pb-2" style="overflow-y: scroll">
										<div>
											@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
												<div class="ga-preloader" style="position: absolute; left: 48%; top: -50%;"></div>
												<h6 class="text-muted" id="google_average_session"></h6>
											@else
												<h6 class="text-center fs-12 text-muted justify-content-center">{{ __('GA 4 is not configured yet') }}</h6>
											@endif							
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 no-gutters">
								<div class="card text-center">
									<div class="card-header pt-3 pb-1 border-0 justify-content-center">
										<div class="mt-2 mb-0 text-center">
											<h3 class="card-title mb-0 text-center font-weight-semibold">{{ __('Bounce Rate') }}</h3>
											<span class="fs-10 text-muted">({{ __('Last 30 Days') }})</span>
										</div>
									</div>
									<div class="card-body pb-2" style="overflow-y: scroll">
										<div>
											@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
												<div class="ga-preloader" style="position: absolute; left: 48%; top: -50%;"></div>
												<h6 class="text-muted" id="google_bounce_rate"></h6>
											@else
												<h6 class="text-center fs-12 text-muted justify-content-center">{{ __('GA 4 is not configured yet') }}</h6>
											@endif							
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 no-gutters">
								<div class="card text-center">
									<div class="card-header pt-3 pb-1 border-0 justify-content-center">
										<div class="mt-2 mb-0 text-center">
											<h3 class="card-title mb-0 text-center font-weight-semibold">{{ __('Sessions') }}</h3>
											<span class="fs-10 text-muted">({{ __('Last 30 Days') }})</span>
										</div>
									</div>
									<div class="card-body pb-2" style="overflow-y: scroll">
										<div>
											@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
												<div class="ga-preloader" style="position: absolute; left: 48%; top: -50%;"></div>
												<h6 class="text-muted" id="google_sessions"></h6>								
											@else
												<h6 class="text-center fs-12 text-muted justify-content-center">{{ __('GA 4 is not configured yet') }}</h6>
											@endif							
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 no-gutters">
								<div class="card text-center">
									<div class="card-header pt-3 pb-1 border-0 justify-content-center">
										<div class="mt-2 mb-0 text-center">
											<h3 class="card-title mb-0 text-center font-weight-semibold">{{ __('Views per Session') }}</h3>
											<span class="fs-10 text-muted">({{ __('Last 30 Days') }})</span>
										</div>
									</div>
									<div class="card-body pb-2" style="overflow-y: scroll">
										<div>
											@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
												<div class="ga-preloader" style="position: absolute; left: 48%; top: -50%;"></div>
												<h6 class="text-muted" id="google_session_views"></h6>								
											@else
												<h6 class="text-center fs-12 text-muted justify-content-center">{{ __('GA 4 is not configured yet') }}</h6>
											@endif							
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-lg-3 col-md-4 col-sm-12 mt-3">
			<div class="card dashboard-fixed-457" id="admin-dashboard-panels">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-browser mr-2 text-muted"></i>{{ __('User Traffic') }}</h3>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="mt-4">
								@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
									<div id="ga-preloader" style="position: absolute; left: 48%; top: 40%;"></div>
									<canvas id="trafficDoughnut" class="h-330"></canvas>
								@else
									<h6 class="text-center fs-12 text-muted justify-content-center">{{ __('GA 4 is not configured yet') }}</h6>
								@endif
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-12 mt-3">
			<div class="card dashboard-fixed-457" id="admin-dashboard-panels">
				<div class="card-header d-inline pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-earth-americas mr-2 text-muted"></i>{{ __('Users and Sessions') }}</h3>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="">
								@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
									<div id="ga-preloader-2" style="position: absolute; left: 48%; top: 40%;"></div>
									<canvas id="chart-total-users-year" class="h-330"></canvas>
								@else
									<h6 class="text-center fs-12 text-muted justify-content-center">{{ __('GA 4 is not configured yet') }}</h6>
								@endif	
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-3">		
		<div class="col-lg-6 col-md-12 col-md-12 mt-3" style="max-height: 515px">
			<div class="card h-100">
				<div class="pb-4 pt-5">
					<h3 class="card-title pl-5">{{ __('Total Registered Users') }}<span class="text-muted"> ({{__('FY')}} {{  date('Y') }})</span></h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="mt-1">
								<canvas id="chart-new-users-year" class="h-400"></canvas>
							</div>
						</div>						
					</div>
				</div>
			</div>
		</div> 
		<div class="col-lg-6 col-md-12 col-sm-12 mt-3">
			<div class="card pb-5" id="admin-dashboard-panels">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-money-check-pen mr-2 text-muted"></i>{{ __('Latest Registrations') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
								<a class="dropdown-item" href="{{ route('admin.user.list') }}">{{ __('View All') }}</a>	
							</div>
						</div>
					</div>
				</div>
			
				<div class="col-sm-12 pl-6 pr-6">
					<div class="dashboard-3-column">
						<div class="font-weight-semibold text-muted fs-12">{{ __('User') }}</div>
						<div class="text-right mr-4 font-weight-semibold text-muted fs-12">{{ __('Status') }}</div>
						<div class="text-right mr-5 font-weight-semibold text-muted fs-12">{{ __('Date') }}</div>
					</div>
				</div>
							
				<div class="card-body pt-2 height-400">

					<div class="row">
						
						@foreach ($users as $data)
							<div class="col-sm-12">					
								<div class="card" onclick="window.location.href='{{ url('app/admin/users/'.$data->id.'/show') }}'">
									<div class="card-body pt-2 pb-2 pl-4 pr-4 dashboard-3-column">
										<div class="template-icon">
											@if ($data->profile_photo_path)
												<div class="d-flex">
													<div class="widget-user-image-sm overflow-hidden mr-4"><img alt="Avatar" src="{{ $data->profile_photo_path }}"></div>
													<div class="widget-user-name fs-12"><span class="font-weight-semibold">{{ $data->name }}</span><br><span class="text-muted">{{ $data->email }}</span></div>
												</div>
											@else
												<div class="d-flex">
													<div class="widget-user-image-sm overflow-hidden mr-4"><img alt="Avatar" class="rounded-circle" src="{{ theme_url('img/users/avatar.png') }}"></div>
													<div class="widget-user-name fs-12"><span class="font-weight-semibold">{{ $data->name }}</span><br><span class="text-muted">{{ $data->email }}</span></div>
												</div>
											@endif										
										</div>
										<div class="text-right mb-auto mt-auto">
											<p class="fs-12 mb-0 text-muted"><span class="cell-box user-{{ $data->status }}">{{ __(ucfirst($data->status)) }}</span></p>
										</div>
										<div class="text-right mb-auto mt-auto">
											<p class="fs-10 mb-0 text-muted">{{ date_format($data->created_at, 'd M Y') }}<br><span>{{ date_format($data->created_at, 'H:i A') }}</span></p>
										</div>
									</div>
								</div>													
							</div>
						@endforeach

					</div>
				</div>
			</div>
		</div>
	</div>

	<h4 class="page-title fs-25">{{ __('Platform Metrics') }}</h4>

	<div class="row">
		<div class="col-lg-4 col-md-12 col-sm-12 mt-3">
			<div class="card">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-700">{{ __('Input Tokens Used') }} <span class="text-muted">({{ __(date('M')) }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['input_tokens_current_month']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span id="input_tokens_change"></span> {{ __('this month') }}</span></h2>

						</div>
						<span class="fs-35 text-muted mt-m1"><i class="fa-solid fa-right-to-bracket"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-11 mr-1">{{ __('Last Month') }}</span>
							<span class="number-font fs-11"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['input_tokens_past_month']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-11 mr-1">{{ __('Total') }} ({{ date('Y') }})</span>
							<span class="number-font fs-11"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['input_tokens_generated']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-12 col-sm-12 mt-3">
			<div class="card">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-700">{{ __('Output Tokens Used') }} <span class="text-muted">({{ __(date('M')) }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['output_tokens_current_month']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span id="output_tokens_change"></span> {{ __('this month') }}</span></h2>

						</div>
						<span class="fs-35 text-muted mt-m1"><i class="fa-solid fa-right-from-bracket"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-11 mr-1">{{ __('Last Month') }}</span>
							<span class="number-font fs-11"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['output_tokens_past_month']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-11 mr-1">{{ __('Total') }} ({{ date('Y') }})</span>
							<span class="number-font fs-11"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['output_tokens_generated']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>			
		<div class="col-lg-4 col-md-12 col-sm-12 mt-3">
			<div class="card">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-700">{{ __('Images Generated') }} <span class="text-muted">({{ __(date('M')) }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['images_current_month']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span id="images_change"></span> {{ __('this month') }}</span></h2>
						</div>
						<span class="fs-35 text-muted mt-m1"><i class="fa-solid fa-image-landscape"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-11 mr-1">{{ __('Last Month') }}</span>
							<span class="number-font fs-11"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['images_past_month']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-11 mr-1">{{ __('Total') }} ({{ date('Y') }})</span>
							<span class="number-font fs-11"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['images_generated']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>				
	</div>	

	<div class="row mt-3">
		<div class="col-lg-6 col-md-12 col-sm-12 mt-3">
			<div class="card pb-5 h-100">
				<div class="card-header pt-4 pb-0 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-cloud-word mr-2 text-muted"></i>{{ __('Words Generated') }}</h3>
					</div>
				</div>
				<div class="card-body pt-2 pb-0">
					<div class="row">						
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div>								
								<div id="chartdiv" class="h-330"></div>							
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 text-center">
							<div class="text-center">
								<h6 class="fs-12 text-muted">{{ __('Current Month Words') }}</h6>
								<h6 class="mb-0 fs-14 font-weight-semibold">{{ number_format($total_data_monthly['words_current_month']) }}</h6>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 text-center">
							<div class="text-center">
								<h6 class="fs-12 text-muted">{{ __('Last Month Words') }}</h6>
								<h6 class="mb-0 fs-14 font-weight-semibold">{{ number_format($total_data_monthly['words_past_month']) }}</h6>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-md-12 mt-3">
			<div class="card h-100">
				<div class="pb-4 pt-5">
					<h3 class="card-title pl-5">{{ __('Tokens Usage') }}<span class="text-muted"> ({{__('FY')}} {{  date('Y') }})</span></h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="mt-1">
								<canvas id="chart-tokens-usage" class="h-400"></canvas>
							</div>
						</div>						
					</div>
				</div>
			</div>
		</div> 
	</div>	

	<div class="row mt-5">
		<div class="col-lg-4 col-md-12 col-sm-12 mt-5">
			<div class="card">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-700">{{ __('Support Tickets Created') }} <span class="text-muted">({{ __(date('M')) }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['support_tickets_current_month']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span id="support_change"></span> {{ __('this month') }}</span></h2>

						</div>
						<span class="fs-35 text-muted mt-m1"><i class="fa-solid fa-headset"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-11 mr-1">{{ __('Last Month') }}</span>
							<span class="number-font fs-11"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['support_tickets_past_month']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-11 mr-1">{{ __('Total') }} ({{ date('Y') }})</span>
							<span class="number-font fs-11"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['support_tickets_generated']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-12 col-sm-12 mt-5">
			<div class="card">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-700">{{ __('Contents Created') }} <span class="text-muted">({{ __(date('M')) }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['contents_current_month']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span id="contents_change"></span> {{ __('this month') }}</span></h2>

						</div>
						<span class="fs-35 text-muted mt-m1"><i class="fa-solid fa-subtitles"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-11 mr-1">{{ __('Last Month') }}</span>
							<span class="number-font fs-11"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['contents_past_month']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-11 mr-1">{{ __('Total') }} ({{ date('Y') }})</span>
							<span class="number-font fs-11"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['contents_generated']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-12 col-sm-12 mt-5">
			<div class="card">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-700">{{ __('Chat Messages Created') }} <span class="text-muted">({{ __(date('M')) }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['chats_current_month']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span id="chats_change"></span> {{ __('this month') }}</span></h2>

						</div>
						<span class="fs-35 text-muted mt-m1"><i class="fa-solid fa-message-dots"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-11 mr-1">{{ __('Last Month') }}</span>
							<span class="number-font fs-11"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['chats_past_month']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-11 mr-1">{{ __('Total') }} ({{ date('Y') }})</span>
							<span class="number-font fs-11"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['chats_generated']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-3">		
		<div class="col-lg-6 col-md-6 col-sm-12 mt-3">
			<div class="card pb-4">
				<div class="card-header pt-4 pb-0 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-headset mr-2 text-muted"></i>{{ __('Support Tickets') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
								<a class="dropdown-item" href="{{ route('admin.support') }}">{{ __('View All') }}</a>	
							</div>
						</div>
					</div>
				</div>
				<div class="card-body pt-2 height-400">
					<table class="table table-hover" id="database-backup">
						<thead>
							<tr role="row">
								<th class="fs-12 font-weight-700 border-top-0">{{ __('Ticket ID') }}</th>
								<th class="fs-12 font-weight-700 border-top-0 text-left">{{ __('Subject') }}</th>
								<th class="fs-12 font-weight-700 border-top-0 text-center">{{ __('Category') }}</th>
								<th class="fs-12 font-weight-700 border-top-0 text-center">{{ __('Status') }}</th>
								<th class="fs-12 font-weight-700 border-top-0 text-right">{{ __('Last Updated') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($tickets as $data)
							<tr class="relative" style="height: 60px">
								<td><a class="font-weight-bold text-primary" href="{{ route("admin.support.show", $data->ticket_id ) }}">{{ $data->ticket_id }}</a>
								</td>
								<td class="text-left text-muted">{{ ucfirst($data->subject) }}</td>
								<td class="text-center text-muted">{{ ucfirst($data->category) }}</td>
								<td class="text-center"><span class="cell-box support-{{ strtolower($data->status) }}">{{ __(ucfirst($data->status)) }}</span></td>
								<td class="text-right text-muted">{{ \Carbon\Carbon::parse($data->updated_at)->diffForHumans() }}</td>
								<td class="w-0 p-0" colspan="0">
									<a class="strage-things" style="position: absolute; inset: 0px; width: 100%" href="{{ route("admin.support.show", $data->ticket_id ) }}"><span class="sr-only">{{ __('View') }}</span></a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>					
				</div>
			</div>                      
		</div> 	 
		<div class="col-lg-6 col-md-6 col-sm-12 mt-3">
			<div class="card pb-4">
				<div class="card-header pt-4 pb-0 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-solid fa-message-exclamation mr-2 text-muted"></i>{{ __('Recent Activities') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
								<a class="dropdown-item" href="{{ route('admin.notifications') }}">{{ __('View All') }}</a>	
							</div>
						</div>
					</div>
				</div>
				<div class="card-body pt-2 dashboard-timeline height-400">					
					<div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
						@foreach ($notifications as $notification)
							<div class="vertical-timeline-item vertical-timeline-element">
								<div>
									<span class="vertical-timeline-element-icon">
										@if ($notification->data['type'] == 'new-payment')
											<i class="badge badge-dot badge-dot-xl badge-secondary"> </i>
										@elseif ($notification->data['type'] == 'new-user')
											<i class="badge badge-dot badge-dot-xl badge-primary"> </i>
										@elseif ($notification->data['type'] == 'payout-request')
											<i class="badge badge-dot badge-dot-xl badge-success"> </i>
										@else
											<i class="badge badge-dot badge-dot-xl badge-warning"> </i>
										@endif
										
									</span>
									<div class="vertical-timeline-element-content">
										<h4 class="fs-13"><a href="{{ route("admin.notifications.systemShow", $notification->id)  }}">
											@if ($notification->data['type'] == 'new-payment')
												<b>{{ __('Payment') }}:</b>
											@elseif ($notification->data['type'] == 'new-user')
												<b>{{ __('Registration') }}:</b>
											@elseif ($notification->data['type'] == 'payout-request')
												<b>{{ __('Payout') }}:</b>
											@endif
											</a> {{ __($notification->data['subject']) }}</h4>
										<p><span class="text-muted fs-12">{{ __('User Info') }}: {{ __($notification->data['name']) }} | {{ __($notification->data['email']) }}</span></p>
										<span class="vertical-timeline-element-date text-center">{{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y') }} <br> {{ \Carbon\Carbon::parse($notification->created_at)->format('H:i A') }}</span>
									</div>
								</div>
							</div>
						@endforeach
					</div>											  					
				</div>
			</div>                      
		</div>  
	</div>
@endsection

@section('js')
	<!-- Chart JS -->
	<script src="{{URL::asset('plugins/chart/chart.min.js')}}"></script>
	<script src="{{URL::asset('plugins/googlemaps/loader.js')}}"></script>
	<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
	<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
	<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
	<script type="text/javascript">
		let loading = `<span class="loading">
						<span style="background-color: #1e1e2d;"></span>
						<span style="background-color: #1e1e2d;"></span>
						<span style="background-color: #1e1e2d;"></span>
						</span>`;
		function getGridColor() {
			return document.body.classList.contains('dark-theme') ? 'rgba(255, 255, 255, 0.1)' : '#ebecf1';
		}

		$(function() {

			"use strict";

			

			// FINANCE REVENUE TABLE
			let chartColor = "#FFFFFF";
			var earningData = JSON.parse(`<?php echo $chart_data['monthly_earnings']; ?>`);
			var costData = JSON.parse(`<?php echo $chart_data['monthly_spendings']; ?>`);
			var earningDataset = Object.values(earningData);
			var costDataset = Object.values(costData);

			let usersOptionsConfiguration = {
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: false,
					},
					tooltip: {
						titleAlign: 'center',
						bodySpacing: 4,
						mode: "nearest",
						intersect: 0,
						position: "nearest",
						xPadding: 20,
						yPadding: 20,
						caretPadding: 20
					},
				},			
				responsive: true,
				scales: {
					y: {
						display: 1,
						grid: 0,
						ticks: {
							display: true,
							padding: 10,
							beginAtZero: true,
							stepSize: 500,
							color: '#b7bdc9',
							font: {
                        		size: 10
                    		},
						},
						grid: {
							zeroLineColor: "transparent",
							drawTicks: false,
							display: false,
							drawBorder: false,
						}
					},
					x: {
						display: 1,
						grid: 0,
						ticks: {
							display: true,
							padding: 10,
							beginAtZero: true,
							color: '#b7bdc9',
							font: {
                        		size: 10
                    		},
						},
						grid: {
							zeroLineColor: "transparent",
							drawTicks: false,
							display: false,
							drawBorder: false,
						}
					}
				},
				layout: {
					padding: {
						left: 0,
						right: 0,
						top: 0,
						bottom: 0	
					}
				},
				elements: {
					line: {
						tension : 0.4
					},
				},
			};

			let ctx3 = document.getElementById('financeEarningsChart').getContext("2d");
			let gradientStroke3 = ctx3.createLinearGradient(500, 0, 100, 0);
			gradientStroke3.addColorStop(0, '#007bff');
			gradientStroke3.addColorStop(1, chartColor);
			let gradientFill3 = ctx3.createLinearGradient(0, 250, 0, 150);
			gradientFill3.addColorStop(0, "rgba(128, 182, 244, 0)");
			gradientFill3.addColorStop(1, "rgba(0, 123, 255, 0.4)");
			let gradientFill4 = ctx3.createLinearGradient(0, 250, 0, 150);
			gradientFill4.addColorStop(0, "rgba(128, 182, 244, 0)");
			gradientFill4.addColorStop(1, "rgba(255, 191, 0, 0.4)");
			let myChart3 = new Chart(ctx3, {
				type: 'line',
				data: {
					labels: ['{{ __('Jan') }}', '{{ __('Feb') }}', '{{ __('Mar') }}', '{{ __('Apr') }}', '{{ __('May') }}', '{{ __('Jun') }}', '{{ __('Jul') }}', '{{ __('Aug') }}', '{{ __('Sep') }}', '{{ __('Oct') }}', '{{ __('Nov') }}', '{{ __('Dec') }}'],
					datasets: [{
						label: "{{ __('Earnings') }}",
						borderColor: "#007bff",
						pointBorderColor: "#FFF",
						pointBackgroundColor: "#007bff",
						pointBorderWidth: 1,
						pointHoverRadius: 4,
						pointHoverBorderWidth: 1,
						pointRadius: 2,
						fill: true,
						backgroundColor: gradientFill3,
						borderWidth: 2,
						data: earningDataset
					},
					{
						label: "{{ __('Spendings') }}",
						borderColor: "#ffab00",
						pointBorderColor: "#FFF",
						pointBackgroundColor: "#ffab00",
						pointBorderWidth: 1,
						pointHoverRadius: 4,
						pointHoverBorderWidth: 1,
						pointRadius: 2,
						fill: true,
						backgroundColor: gradientFill4,
						borderWidth: 2,
						data: costDataset
					}]
				},
				options: usersOptionsConfiguration
			});


			// REVENUE DONUGHNUT CHART
			let sourceData = JSON.parse(`<?php echo $chart_data['source_data']; ?>`);
			let sourceLabelDataset = Object.keys(sourceData);
			let sourceDataDataset = Object.values(sourceData);
			let revenueDoughnut = document.getElementById('revenuePlan');
			let delayed4;
			new Chart(revenueDoughnut, {
				type: 'doughnut',
				data: {
					labels: sourceLabelDataset,
					datasets: [{
						data: sourceDataDataset,
						backgroundColor: [
							'#67b7dc',
							'#6494dc',
							'#6771dc',
							'#8067dc',
							'#a367dc',
							'#c767dc',
							'#dc67ce',
							'#dc67ab',
							'#dc6788',
							'#dc6867',
						],
						hoverOffset: 20,
						weight: 0.001,
						borderWidth: 0
					}]
				},
				options: {
					cutout: 20,
					maintainAspectRatio: false,
					legend: {
						display: false,
						labels: {
							display: false
						}
					},
					responsive: true,
					animation: {
						onComplete: () => {
							delayed4 = true;
						},
						delay: (context) => {
							let delay = 0;
							if (context.type === 'data' && context.mode === 'default' && !delayed4) {
								delay = context.dataIndex * 50 + context.datasetIndex * 5;
							}
							return delay;
						},
					},
					plugins: {
						tooltip: {
							cornerRadius: 2,
							xPadding: 10,
							yPadding: 10,
							backgroundColor: '#000000',
							titleColor: '#FF9D00',
							yAlign: 'bottom',
							xAlign: 'center',
						},
						legend: {
							position: 'bottom',
							labels: {
								boxWidth: 10,
								font: {
									size: 10
								},
								padding: 30
							}
						}
					}
				}
			});


			// COST DONUGHNUT CHART
			let spendingData = JSON.parse(`<?php echo $chart_data['cost_data']; ?>`);
			let costLabelDataset = Object.keys(spendingData);
			let costDataDataset = Object.values(spendingData);
			let costDoughnut = document.getElementById('costService');
			let delayed2;
			new Chart(costDoughnut, {
				type: 'doughnut',
				data: {
					labels: costLabelDataset,
					datasets: [{
						data: costDataDataset,
						backgroundColor: [
							'#67b7dc',
							'#6494dc',
							'#6771dc',
							'#8067dc',
							'#a367dc',
							'#c767dc',
							'#dc67ce',
							'#dc67ab',
							'#dc6788',
							'#dc6867',
						],
						hoverOffset: 20,
						weight: 0.001,
						borderWidth: 0
					}]
				},
				options: {
					cutout: 20,
					maintainAspectRatio: false,
					legend: {
						display: false,
						labels: {
							display: false
						}
					},
					responsive: true,
					animation: {
						onComplete: () => {
							delayed2 = true;
						},
						delay: (context) => {
							let delay = 0;
							if (context.type === 'data' && context.mode === 'default' && !delayed2) {
								delay = context.dataIndex * 50 + context.datasetIndex * 5;
							}
							return delay;
						},
					},
					plugins: {
						tooltip: {
							cornerRadius: 2,
							xPadding: 10,
							yPadding: 10,
							backgroundColor: '#000000',
							titleColor: '#FF9D00',
							yAlign: 'bottom',
							xAlign: 'center',
						},
						legend: {
							position: 'bottom',
							labels: {
								boxWidth: 10,
								font: {
									size: 12
								},
								padding: 30
							}
						}
					}
				}
			});


			$(document).ready(function() {

				@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
					$.ajax({
						headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
						method: 'GET',
						url: '/app/admin/dashboard/analytics',
						beforeSend: function() {
							let btn = document.getElementById('ga-preloader');					       
							btn.innerHTML = loading;  
							let btn2 = document.getElementById('ga-preloader-2');					       
							btn2.innerHTML = loading;
							let btn3 = document.getElementById('ga-preloader-3');					       
							btn3.innerHTML = loading;  
							var elements = document.getElementsByClassName("ga-preloader");
							for (var i = 0; i < elements.length; i++) {elements[i].innerHTML = loading;}      
						},
						complete: function() {
							let btn = document.getElementById('ga-preloader');					
							btn.innerHTML = ''; 
							let btn2 = document.getElementById('ga-preloader-2');					       
							btn2.innerHTML = ''; 
							let btn3 = document.getElementById('ga-preloader-3');					       
							btn3.innerHTML = ''; 
							var elements = document.getElementsByClassName("ga-preloader");
							for (var i = 0; i < elements.length; i++) {elements[i].innerHTML = ''}              
						},
						success: function (data) {

							if (data['status'] == 200) {
								
								@if (!empty(config('services.google.analytics.property')) && !empty(config('services.google.analytics.credentials')))
									// GA USERS
									var usersData = JSON.parse(data['google_users']);
									var userSessionsData = JSON.parse(data['google_user_sessions']);
									var usersDataset = Object.values(usersData);
									var userSessionsDataset = Object.values(userSessionsData);

									let usersOptionsConfiguration = {
										maintainAspectRatio: false,
										plugins: {
											legend: {
												display: false,
											},
											tooltip: {
												titleAlign: 'center',
												bodySpacing: 4,
												mode: "nearest",
												intersect: 0,
												position: "nearest",
												xPadding: 20,
												yPadding: 20,
												caretPadding: 20
											},
										},			
										responsive: true,
										scales: {
											y: {
												display: 1,
												grid: 0,
												ticks: {
													display: true,
													padding: 10,
													beginAtZero: true,
													stepSize: 50,
													color: '#b7bdc9',
													font: {
														size: 10
													},
												},
												grid: {
													zeroLineColor: "transparent",
													drawTicks: false,
													display: false,
													drawBorder: false,
												}
											},
											x: {
												display: 1,
												grid: 0,
												ticks: {
													display: true,
													padding: 10,
													beginAtZero: true,
													color: '#b7bdc9',
													font: {
														size: 10
													},
												},
												grid: {
													zeroLineColor: "transparent",
													drawTicks: false,
													display: false,
													drawBorder: false,
												}
											}
										},
										layout: {
											padding: {
												left: 0,
												right: 0,
												top: 0,
												bottom: 0	
											}
										},
										elements: {
											line: {
												tension : 0.4
											},
										},
									};

									let ctx3 = document.getElementById('chart-total-users-year').getContext("2d");
									let gradientStroke3 = ctx3.createLinearGradient(500, 0, 100, 0);
									gradientStroke3.addColorStop(0, '#007bff');
									gradientStroke3.addColorStop(1, chartColor);
									let gradientFill3 = ctx3.createLinearGradient(0, 250, 0, 150);
									gradientFill3.addColorStop(0, "rgba(128, 182, 244, 0)");
									gradientFill3.addColorStop(1, "rgba(0, 123, 255, 0.4)");
									let gradientFill4 = ctx3.createLinearGradient(0, 250, 0, 150);
									gradientFill4.addColorStop(0, "rgba(128, 182, 244, 0)");
									gradientFill4.addColorStop(1, "rgba(255, 191, 0, 0.4)");
									let myChart3 = new Chart(ctx3, {
										type: 'line',
										data: {
											labels: usersDataset[0],
											datasets: [{
												label: "{{ __('Users') }}",
												borderColor: "#007bff",
												pointBorderColor: "#FFF",
												pointBackgroundColor: "#007bff",
												pointBorderWidth: 1,
												pointHoverRadius: 4,
												pointHoverBorderWidth: 1,
												pointRadius: 2,
												fill: true,
												backgroundColor: gradientFill3,
												borderWidth: 2,
												data: usersDataset[1]
											},
											{
												label: "{{ __('Sessions') }}",
												borderColor: "#ffab00",
												pointBorderColor: "#FFF",
												pointBackgroundColor: "#ffab00",
												pointBorderWidth: 1,
												pointHoverRadius: 4,
												pointHoverBorderWidth: 1,
												pointRadius: 2,
												fill: true,
												backgroundColor: gradientFill4,
												borderWidth: 2,
												data: userSessionsDataset[1]
											}]
										},
										options: usersOptionsConfiguration
									});
									

									// TRAFFIC SOURCE
									let trafficLabel = JSON.parse(data['traffic_label']);
									let trafficData = JSON.parse(data['traffic_data']);
									let trafficLabelDataset = Object.values(trafficLabel);
									let trafficDataDataset = Object.values(trafficData);
									let trafficDoughnut = document.getElementById('trafficDoughnut');
									new Chart(trafficDoughnut, {
										type: 'doughnut',
										data: {
											labels: trafficLabelDataset,
											datasets: [{
												data: trafficDataDataset,
												backgroundColor: [
													'#1e1e2d',
													'#007bff',
													'#a367dc',
													'#fca639',
													'#52b3ea',
													'#e34a8a',
												],
												hoverOffset: 4,
												weight: 0.001,
												borderWidth: 0
											}]
										},
										options: {
											cutout: 90,
											maintainAspectRatio: false,
											legend: {
												display: false,
												labels: {
													display: false
												}
											},
											responsive: true,
											animation: {
												onComplete: () => {
													delayed3 = true;
												},
												delay: (context) => {
													let delay = 0;
													if (context.type === 'data' && context.mode === 'default' && !delayed3) {
														delay = context.dataIndex * 50 + context.datasetIndex * 5;
													}
													return delay;
												},
											},
											plugins: {
												tooltip: {
													cornerRadius: 2,
													xPadding: 10,
													yPadding: 10,
													backgroundColor: '#000000',
													titleColor: '#FF9D00',
													yAlign: 'bottom',
													xAlign: 'center',
												},
												legend: {
													position: 'bottom',
													labels: {
														boxWidth: 10,
														font: {
															size: 10
														},
													}
												}
											}
										}
									});


									// BOX INFO
									document.getElementById('google_session_views').innerHTML = data['google_session_views'];
									document.getElementById('google_sessions').innerHTML  = data['google_sessions'];
									document.getElementById('google_bounce_rate').innerHTML  = (data['google_bounce_rate'] * 100).toFixed(2) + '%';
									document.getElementById('google_average_session').innerHTML  = data['google_average_session'];

									$('#countryList').append(data['google_countries']).show().fadeIn("slow");

								@endif
							} 	
									
						},
						error: function(data) {
							let btn = document.getElementById('ga-preloader');					
							btn.innerHTML = ''; 
							let btn2 = document.getElementById('ga-preloader-2');					       
							btn2.innerHTML = ''; 
							let btn3 = document.getElementById('ga-preloader-3');					       
							btn3.innerHTML = ''; 
							var elements = document.getElementsByClassName("ga-preloader");
							for (var i = 0; i < elements.length; i++) {elements[i].innerHTML = ''}    
						}
				
					});
				@endif
			});


			$(document).ready(function() {

				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					method: 'GET',
					url: '/app/admin/dashboard/check-update',
					success: function (data) {

						if (data['status'] == 200) {
							$('#current-update-status').addClass('hidden');
							$('#new-update-status').removeClass('hidden');
							$('#version-number').html(data['version']);
						} 	
								
					},
					error: function(data) {

					}
			
				});
			
			});

			
			// USER DONUGHNUT CHART
			let userDoughnut = document.getElementById('userDoughnut');
			let delayed3;
			new Chart(userDoughnut, {
				type: 'doughnut',
				data: {
					labels: [
						'{{ __('Non-Subscribers') }}',
						'{{ __('Subscribers') }}',
					],
					datasets: [{
						data: ['{{ $total['total_nonsubscribers'] }}', '{{ $total['total_subscribers'] }}'],
						backgroundColor: [
							'#1e1e2d',
							'#007bff',
						],
						hoverOffset: 4,
						weight: 0.001,
						borderWidth: 0
					}]
				},
				options: {
					cutout: 90,
					maintainAspectRatio: false,
					legend: {
						display: false,
						labels: {
							display: false
						}
					},
					responsive: true,
					animation: {
						onComplete: () => {
							delayed3 = true;
						},
						delay: (context) => {
							let delay = 0;
							if (context.type === 'data' && context.mode === 'default' && !delayed3) {
								delay = context.dataIndex * 50 + context.datasetIndex * 5;
							}
							return delay;
						},
					},
					plugins: {
						tooltip: {
							cornerRadius: 2,
							xPadding: 10,
							yPadding: 10,
							backgroundColor: '#000000',
							titleColor: '#FF9D00',
							yAlign: 'bottom',
							xAlign: 'center',
						},
						legend: {
							position: 'bottom',
							labels: {
								boxWidth: 10,
								font: {
									size: 12
								},
								padding: 30
							}
						}
					}
				},
				plugins: [{
					id: 'centerText',
					afterDraw: function(chart) {
						const width = chart.width;
						const height = chart.height;
						const ctx = chart.ctx;
						
						ctx.restore();
						ctx.font = '12px Poppins';
						ctx.fillStyle = '#728096'; // text-muted color
						ctx.textBaseline = 'middle';
						ctx.textAlign = 'center';
						ctx.fillText('{{ __('Total Subscribers') }}', width / 2, height / 2 - 30);
						
						ctx.font = 'bold 14px Poppins';
						ctx.fillStyle = '#1e1e2d';
						ctx.textBaseline = 'middle';
						ctx.textAlign = 'center';
						ctx.fillText('{{ number_format($total['total_subscribers']) }}', width / 2, height / 2 - 5);
						
						ctx.save();
					}
				}]
			});

			
			// WORDS GENERATED
			am5.ready(function() {

				var root = am5.Root.new("chartdiv");

				root.setThemes([
				am5themes_Animated.new(root)
				]);

				var chart = root.container.children.push(
				am5percent.PieChart.new(root, {
					startAngle: 160, endAngle: 380
				})
				);

				var series0 = chart.series.push(
				am5percent.PieSeries.new(root, {
					valueField: "documents",
					categoryField: "model",
					startAngle: 160,
					endAngle: 380,
					radius: am5.percent(70),
					innerRadius: am5.percent(65)
				})
				);

				var colorSet = am5.ColorSet.new(root, {
				colors: [series0.get("colors").getIndex(0)],
				passOptions: {
					lightness: -0.05,
					hue: 0
				}
				});

				series0.set("colors", colorSet);

				series0.ticks.template.set("forceHidden", true);
				series0.labels.template.set("forceHidden", true);

				var series1 = chart.series.push(
				am5percent.PieSeries.new(root, {
					startAngle: 160,
					endAngle: 380,
					valueField: "words",
					innerRadius: am5.percent(80),
					categoryField: "model"
				})
				);

				series1.ticks.template.set("forceHidden", true);
				series1.labels.template.set("forceHidden", true);


				var label = chart.seriesContainer.children.push(
				am5.Label.new(root, {
					textAlign: "center",
					centerY: am5.p100,
					centerX: am5.p50,
					text: "[fontSize:12px]{{ __('Total Words Generated') }}[/]\n[bold fontSize:22px]{{ number_format($total_data_yearly['words_generated']) }}[/]"
				})
				);

				var data = [
					{
						model: "GPT 3.5 Turbo",
						words: '{{ $chart_data['gpt3_words'] }}',
						documents: '{{ $chart_data['gpt3_tasks'] }}',
					},
					{
						model: "GPT 4",
						words: '{{ $chart_data['gpt4_words'] }}',
						documents: '{{ $chart_data['gpt4_tasks'] }}',
					},
					{
						model: "GPT 4o",
						words: '{{ $chart_data['gpt4o_words'] }}',
						documents: '{{ $chart_data['gpt4o_tasks'] }}',
					},
					{
						model: "GPT 4 Turbo",
						words: '{{ $chart_data['gpt4t_words'] }}',
						documents: '{{ $chart_data['gpt4t_tasks'] }}',
					},
					{
						model: "Claude 3 Opus",
						words: '{{ $chart_data['opus_words'] }}',
						documents: '{{ $chart_data['opus_tasks'] }}',
					},
					{
						model: "Claude 3 Sonnet",
						words: '{{ $chart_data['sonnet_words'] }}',
						documents: '{{ $chart_data['sonnet_tasks'] }}',
					},
					{
						model: "Claude 3 Haiku",
						words: '{{ $chart_data['haiku_words'] }}',
						documents: '{{ $chart_data['haiku_tasks'] }}',
					},
					{
						model: "Gemini Pro",
						words: '{{ $chart_data['gemini_words'] }}',
						documents: '{{ $chart_data['gemini_tasks'] }}',
					},
				];

				series0.data.setAll(data);
				series1.data.setAll(data);

			}); 


			// Percentage Difference First Row
			var subscribers_current_month = JSON.parse(`<?php echo $percentage['subscribers_current']; ?>`);	
			var subscribers_past_month = JSON.parse(`<?php echo $percentage['subscribers_past']; ?>`);

			var images_current_month = JSON.parse(`<?php echo $percentage['images_current']; ?>`);
			var images_past_month = JSON.parse(`<?php echo $percentage['images_past']; ?>`);
			var contents_current_month = JSON.parse(`<?php echo $percentage['contents_current']; ?>`);
			var contents_past_month = JSON.parse(`<?php echo $percentage['contents_past']; ?>`);
			var transactions_current_month = JSON.parse(`<?php echo $percentage['transactions_current']; ?>`);
			var transactions_past_month = JSON.parse(`<?php echo $percentage['transactions_past']; ?>`);
			var chats_current_month = JSON.parse(`<?php echo $percentage['chats_current']; ?>`);
			var chats_past_month = JSON.parse(`<?php echo $percentage['chats_past']; ?>`);

			var input_tokens_current_month = JSON.parse(`<?php echo $percentage['input_tokens_current']; ?>`);
			var input_tokens_past_month = JSON.parse(`<?php echo $percentage['input_tokens_past']; ?>`);
			var output_tokens_current_month = JSON.parse(`<?php echo $percentage['output_tokens_current']; ?>`);
			var output_tokens_past_month = JSON.parse(`<?php echo $percentage['output_tokens_past']; ?>`);

			var support_tickets_current_month = JSON.parse(`<?php echo $percentage['support_tickets_current']; ?>`);
			var support_tickets_past_month = JSON.parse(`<?php echo $percentage['support_tickets_past']; ?>`);

			var gift_cards_current_month = JSON.parse(`<?php echo $percentage['gift_current']; ?>`);
			var gift_cards_past_month = JSON.parse(`<?php echo $percentage['gift_past']; ?>`);
			var gift_funds_current_month = JSON.parse(`<?php echo $percentage['gift_funds_current']; ?>`);
			var gift_funds_past_month = JSON.parse(`<?php echo $percentage['gift_funds_past']; ?>`);

			var subscribers_current_total = parseInt(subscribers_current_month);
			var subscribers_past_total = parseInt(subscribers_past_month);

			var images_current_total = parseInt(images_current_month);
			var images_past_total = parseInt(images_past_month);
			var contents_current_total = parseInt(contents_current_month);
			var contents_past_total = parseInt(contents_past_month);
			var transactions_current_total = parseInt(transactions_current_month);
			var transactions_past_total = parseInt(transactions_past_month);

			var subscribers_change = mainPercentageDifference(subscribers_past_month, subscribers_current_month);
			var images_change = mainPercentageDifference(images_past_month, images_current_month);
			var contents_change = mainPercentageDifference(contents_past_month, contents_current_month);
			var chats_change = mainPercentageDifference(chats_past_month, chats_current_month);
			var support_tickets_change = mainPercentageDifference(support_tickets_past_month, support_tickets_current_month);
			var input_tokens_change = mainPercentageDifference(input_tokens_past_month, input_tokens_current_month);
			var output_tokens_change = mainPercentageDifference(output_tokens_past_month, output_tokens_current_month);
			var transactions_change = mainPercentageDifference(transactions_past_month, transactions_current_month);
			var gift_card_change = mainPercentageDifference(gift_cards_past_month, gift_cards_current_month);
			var gift_funds_change = mainPercentageDifference(gift_funds_past_month, gift_funds_current_month);

			document.getElementById('subscribers_change').innerHTML = subscribers_change;
			document.getElementById('images_change').innerHTML = images_change;
			document.getElementById('contents_change').innerHTML = contents_change;
			document.getElementById('chats_change').innerHTML = chats_change;
			document.getElementById('support_change').innerHTML = support_tickets_change;
			document.getElementById('input_tokens_change').innerHTML = input_tokens_change;
			document.getElementById('output_tokens_change').innerHTML = output_tokens_change;
			document.getElementById('transactions_change').innerHTML = transactions_change;
			document.getElementById('gift_card_change').innerHTML = gift_card_change;
			document.getElementById('gift_funds_change').innerHTML = gift_funds_change;

			function mainPercentageDifference(past, current) {
				if (past == 0) {
					var change = (current == 0) ? '<span class="text-muted"> 0%</span>' : '<span class="text-success"><i class="fa fa-caret-up"></i> 100%</span>';   					
					return change;
				} else if(current == 0) {
					var change = (past == 0) ? '<span class="text-muted"> 0%</span>' : '<span class="text-danger"><i class="fa fa-caret-down"></i> 100%</span>';
					return change;
				} else if(past == current) {
					var change = '<span class="text-muted"> 0%</span>';
					return change; 
				}

				var difference = current - past;
    			var difference_value, result;

				var totalDifference = Math.abs(difference);
				var change = (totalDifference/past) * 100;				

				if (difference > 0) { result = '<span class="text-success"><i class="fa fa-caret-up"></i> ' + change.toFixed(1) + '%</span>'; }
				else if(difference < 0) {result = '<span class="text-danger"><i class="fa fa-caret-down"></i> ' + change.toFixed(1) + '%</span>'; }
				else { difference_value = '<span class="text-muted"> ' + change.toFixed(1) + '%</span>'; }				

				return result;
			}


			// Percentage Difference				
			var income_current_month = JSON.parse(`<?php echo $percentage['income_current']; ?>`);			
			var income_past_month = JSON.parse(`<?php echo $percentage['income_past']; ?>`);
			var spending_current_month = JSON.parse(`<?php echo $percentage['spending_current']; ?>`);	
			var spending_past_month = JSON.parse(`<?php echo $percentage['spending_past']; ?>`);

			(income_current_month[0]['data'] == null) ? income_current_month = 0 : income_current_month = income_current_month[0]['data'];
			(income_past_month[0]['data'] == null) ? income_past_month = 0 : income_past_month = income_past_month[0]['data'];

			var income_current_total = parseInt(income_current_month);	
			var income_past_total = parseInt(income_past_month);
			var spending_current_total = parseInt(spending_current_month);
			var spending_past_total = parseInt(spending_past_month);

			var income_change = mainFinancePercentageDifference(income_past_total, income_current_total);
			var spending_change = mainFinancePercentageDifference(spending_past_month, spending_current_month);

			document.getElementById('revenue_difference').innerHTML = income_change;
			document.getElementById('spending_difference').innerHTML = spending_change;

			function mainFinancePercentageDifference(past, current) {
				if (past == 0) {
					var change = (current == 0) ? '<span class="text-muted fs-12" style="vertical-align: middle"> 0%</span>' : '<span class="text-success fs-12" style="vertical-align: middle"><i class="fa fa-caret-up"></i> 100%</span>';   					
					return change;
				} else if(current == 0) {
					var change = (past == 0) ? '<span class="text-muted fs-12" style="vertical-align: middle"> 0%</span>' : '<span class="text-danger" style="vertical-align: middle"><i class="fa fa-caret-down"></i> 100%</span>';
					return change;
				} else if(past == current) {
					var change = '<span class="text-muted fs-12" style="vertical-align: middle"> 0%</span>';
					return change; 
				}

				var difference = current - past;
    			var difference_value, result;

				var totalDifference = Math.abs(difference);
				var change = (totalDifference/past) * 100;				

				if (difference > 0) { result = '<span class="text-success fs-12" style="vertical-align: middle;"><i class="fa fa-caret-up"></i> ' + change.toFixed(1) + '%</span>'; }
				else if(difference < 0) {result = '<span class="text-danger" style="vertical-align: middle;"><i class="fa fa-caret-down"></i> ' + change.toFixed(1) + '%</span>'; }
				else { difference_value = '<span class="text-muted fs-12" style="vertical-align: middle;"> ' + change.toFixed(1) + '%</span>'; }				

				return result;
			}

		});


		let paymentData = JSON.parse(`<?php echo $chart_data['user_countries']; ?>`);
		let sessionData = [];
		for (const [key, value] of Object.entries(paymentData)) {
			sessionData.push([`${key}`, `${value}`]);
		}

		google.charts.load('current', {
			'packages':['geochart'],
			// Note: you will need to get a mapsApiKey for your project.
			// See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
			'mapsApiKey': '{{ config('services.google.maps.key') }}'
		});

		google.charts.setOnLoadCallback(drawRegionsMap);

		function drawRegionsMap() {     

			let options = {
				colors: ['#007bff'],
				backgroundColor: 'transparent', // Make background transparent
				datalessRegionColor: 'rgba(32, 32, 50, 0.2)', // Light color for regions with no data
				defaultColor: '#007bff' // Default color for regions with data
			};
			let result = [];

			result.push(['Country', 'Users']);

			sessionData.map(function(row) { result.push([row[0], parseInt(row[1])]); });

			let data = google.visualization.arrayToDataTable(result);
			let chart = new google.visualization.GeoChart(document.getElementById('countries-analytics-chart'));
			chart.draw(data, options);
		}


		let freeData = JSON.parse(`<?php echo $chart_data['total_registration_yearly']; ?>`);
		let subscribersData = JSON.parse(`<?php echo $chart_data['paid_registration_yearly']; ?>`);
		let freeDataset = Object.values(freeData);
		let subscribersDataset = Object.values(subscribersData);
		let delayed1;

		let ctx = document.getElementById('chart-new-users-year');
		new Chart(ctx, {
			type: 'bar',
			data: {
				labels: ['{{ __('Jan') }}', '{{ __('Feb') }}', '{{ __('Mar') }}', '{{ __('Apr') }}', '{{ __('May') }}', '{{ __('Jun') }}', '{{ __('Jul') }}', '{{ __('Aug') }}', '{{ __('Sep') }}', '{{ __('Oct') }}', '{{ __('Nov') }}', '{{ __('Dec') }}'],
				datasets: [{
					label: '{{ __('Non-Subscribers') }}',
					data: freeDataset.map((total, index) => total - (subscribersDataset[index] || 0)),
					backgroundColor: '#1e1e2d',
					borderWidth: 1,
					barPercentage: 0.7,
					fill: true
				}, {
					label: '{{ __('Subscribers') }}',
					data: subscribersDataset,
					backgroundColor: '#007bff',
					borderWidth: 1,
					borderRadius: {
						topLeft: 20,
						topRight: 20,
						bottomLeft: 0,
						bottomRight: 0
					},
					barPercentage: 0.7,
					fill: true
				}]
			},
			options: {
				maintainAspectRatio: false,
				legend: {
					display: false,
					labels: {
						display: false
					}
				},
				responsive: true,
				animation: {
					onComplete: () => {
						delayed1 = true;
					},
					delay: (context) => {
						let delay = 0;
						if (context.type === 'data' && context.mode === 'default' && !delayed1) {
							delay = context.dataIndex * 50 + context.datasetIndex * 5;
						}
						return delay;
					},
				},
				scales: {
					y: {
						stacked: true,
						ticks: {
							beginAtZero: true,
							font: {
								size: 10
							},
							stepSize: 40,
						},
						grid: {
							color: getGridColor(),
							borderDash: [3, 2]                            
						}
					},
					x: {
						stacked: true,
						ticks: {
							font: {
								size: 10
							}
						},
						grid: {
							color: getGridColor(),
							borderDash: [3, 2]                            
						}
					}
				},
				plugins: {
					tooltip: {
						cornerRadius: 10,
						xPadding: 10,
						yPadding: 10,
						backgroundColor: '#000000',
						titleColor: '#FF9D00',
						yAlign: 'bottom',
						xAlign: 'center',
					},
					legend: {
						position: 'bottom',
						labels: {
							boxWidth: 10,
							font: {
								size: 10
							}
						}
					}
				}
			}
		});


		let inputData = JSON.parse(`<?php echo $chart_data['monthly_input_tokens']; ?>`);
		let outputData = JSON.parse(`<?php echo $chart_data['monthly_output_tokens']; ?>`);
		let inputDataset = Object.values(inputData);
		let outputDataset = Object.values(outputData);
		let delayed2;

		let ctx7 = document.getElementById('chart-tokens-usage');
		new Chart(ctx7, {
			type: 'bar',
			data: {
				labels: ['{{ __('Jan') }}', '{{ __('Feb') }}', '{{ __('Mar') }}', '{{ __('Apr') }}', '{{ __('May') }}', '{{ __('Jun') }}', '{{ __('Jul') }}', '{{ __('Aug') }}', '{{ __('Sep') }}', '{{ __('Oct') }}', '{{ __('Nov') }}', '{{ __('Dec') }}'],
				datasets: [{
					label: '{{ __('Input Tokens') }}',
					data: inputDataset,
					backgroundColor: '#1e1e2d',
					borderWidth: 1,
					barPercentage: 0.7,
					fill: true
				}, {
					label: '{{ __('Output Tokens') }}',
					data: outputDataset,
					backgroundColor: '#007bff',
					borderWidth: 1,
					borderRadius: {
						topLeft: 20,
						topRight: 20,
						bottomLeft: 0,
						bottomRight: 0
					},
					barPercentage: 0.7,
					fill: true
				}]
			},
			options: {
				maintainAspectRatio: false,
				legend: {
					display: false,
					labels: {
						display: false
					}
				},
				responsive: true,
				animation: {
					onComplete: () => {
						delayed1 = true;
					},
					delay: (context) => {
						let delay = 0;
						if (context.type === 'data' && context.mode === 'default' && !delayed2) {
							delay = context.dataIndex * 50 + context.datasetIndex * 5;
						}
						return delay;
					},
				},
				scales: {
					y: {
						stacked: true,
						ticks: {
							beginAtZero: true,
							font: {
								size: 10
							},
							stepSize: 50000,
						},
						grid: {
							color: getGridColor(),
							borderDash: [3, 2]                            
						}
					},
					x: {
						stacked: true,
						ticks: {
							font: {
								size: 10
							}
						},
						grid: {
							color: getGridColor(),
							borderDash: [3, 2]                            
						}
					}
				},
				plugins: {
					tooltip: {
						cornerRadius: 10,
						xPadding: 10,
						yPadding: 10,
						backgroundColor: '#000000',
						titleColor: '#FF9D00',
						yAlign: 'bottom',
						xAlign: 'center',
					},
					legend: {
						position: 'bottom',
						labels: {
							boxWidth: 10,
							font: {
								size: 10
							}
						}
					}
				}
			}
		});

		
	</script>
@endsection
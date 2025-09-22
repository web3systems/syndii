@extends('layouts.app')

@section('css')
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
	<link rel="stylesheet" href="{{ URL::asset('plugins/slick/slick.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('plugins/slick/slick-theme.css') }}">
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0 fs-30">{{ __('Welcome') }} {{auth()->user()->name}}</h4>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{route('user.dashboard')}}"><i class="fa-solid fa-chart-tree-map mr-2 fs-12"></i>{{ __('User Dashboard') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<div class="row mb-6">		
		<div class="col-lg-3 col-md-12">
			<div class="card border-0" style="height: 100%;">
				<div class="card-body pt-4 pb-0 pl-6 pr-6 custom-banner-bg" @if (!App\Services\HelperService::extensionSaaS()) style="height: 165px" @endif>
					<div class="custom-banner-bg-image"></div>
					<div class="row">
						<div class="col-sm-12">
							<span class="fs-10"><i class="fa-solid fa-calendar mr-2"></i> {{ now()->format('d M, Y H:i A'); }}</span>
							@if (App\Services\HelperService::extensionSaaS())
								<p class="fs-10 custom-span mt-2 mb-3">{{ __('Current Plan') }}</p>
								@if (is_null(auth()->user()->plan_id))
									<h4 class="mb-2 mt-2 font-weight-800 fs-24">{{ __('No Active Plan') }}</h4>						
									<h6 class="fs-12" style="line-height: 1.5">{{ __('You do not have an active subscription plan. Please select a subscription plan or a prepaid plan') }}</h6>
								@else
									<h4 class="mb-2 mt-2 font-weight-800 fs-24">{{ $subscription }} {{ __('Plan') }}</h4>
									<h6 class="fs-12" style="line-height: 1.5">{{ __('You can always upgrade your subscription plan for more premium plans') }}</h6>
								@endif
							@else
								<p class="fs-10 custom-span mt-2 mb-3">{{ __('Current Available Credits') }}</p>
							@endif
							<div>
								<div style="position: relative">
									<canvas id="userDoughnut" style="width: 100%; height: 160px;"></canvas>									
								</div>	
							</div>
						</div>		
						@if (App\Services\HelperService::extensionSaaS())					
							<a href="{{ route('user.plans') }}" class="btn btn-primary yellow mt-2 custom-pricing-plan-button">{{ __('Upgrade Your Plan') }} <i class="fa-regular fa-chevron-right fs-8 ml-1"></i></a>
						@endif
					</div>					
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-12">
			@if (App\Services\HelperService::extensionSaaS())
				@if (App\Services\HelperService::extensionWallet())
					@if (App\Services\HelperService::extensionWalletFeature())			
						<div class="col-sm-12">
							<div class="card mb-3"  style="height: 100%;">
								<div class="card-body p-4">
									<div class="row" style="height: 100%">
										<div class="col-sm-12 text-center mt-auto mb-auto">
											<h6 class="fs-14 text-muted"><i class="fa-solid fa-badge-dollar mr-2"></i>{{ __('Your Wallet Balance') }}</h6>
											<h4 class="mb-3 fs-20 font-weight-800 text-muted">{{ number_format(auth()->user()->wallet) }} {!! config('payment.default_system_currency_symbol') !!}</h4>
											<a href="{{ route('user.wallet') }}" class="btn btn-primary custom-pricing-plan-button" style="text-transform: none;">{{ __('My Wallet') }} <i class="fa-regular fa-chevron-right fs-8 ml-1"></i></a>
										</div>							
									</div>		
								</div>
							</div>
						</div>
					@endif
				@endif	
				@if (config('payment.referral.enabled') == 'on')
					<div class="col-sm-12">
						<div class="card mb-3">
							<div class="card-body p-4 ">
								<div class="row" style="height: 100%">
									<div class="col-sm-12 text-center mt-auto mb-auto">
										<h6 class="fs-14 text-muted"><i class="fa-solid fa-badge-dollar mr-2"></i>{{ __('Total Referral Earnings') }}</h6>
										<h4 class="mb-3 fs-20 font-weight-800 text-muted">{{ number_format(auth()->user()->balance) }} {!! config('payment.default_system_currency_symbol') !!}</h4>
										<a href="{{ route('user.referral') }}" class="btn btn-primary custom-pricing-plan-button" style="text-transform: none;">{{ __('Invite & Earn') }} <i class="fa-regular fa-chevron-right fs-8 ml-1"></i></a>
									</div>							
								</div>		
							</div>
						</div>
					</div>	
				@endif			
			@endif
			<div class="col-sm-12">
				<div class="card mb-0">
					<div class="card-body pb-0">
						<div class="row" style="height: 100%">
							<div class="col-sm-12 text-center mt-auto mb-auto">
								<h6 class="fs-14 text-muted"><i class="fa-solid fa-clock mr-2"></i>{{ __('Total Time Saved') }}</h6>
								<h4 class="font-weight-800 fs-20 text-muted">{{ number_format($total_words) }} {{__('Hours')}}</h4>
							</div>
							<div class="col-sm-12 d-flex align-items-end justify-content-end" style="margin-bottom: -5px">
								<canvas id="hoursSavedChart" style="max-height: 50px"></canvas>
							</div>
						</div>					
					</div>
				</div>
			</div>
		</div>
				
		<div class="col-lg-6 col-md-12">
			<div class="card"  style="height: 100%;">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-grid-2-plus mr-2 text-muted"></i>{{ __('Add New') }}</h3>
					</div>
				</div>
				<div class="card-body pt-2" style="height: 100%; max-height: 300px; overflow-y: scroll;">
					<div class="row">
						@if (App\Services\HelperService::extensionRealtimeChat())
							<div class="col-md-6 col-sm-12">												
								<div class="card dashboard-tool-box" onclick="window.location.href='{{ url('app/user/chats/realtime') }}/'">
									<div class="card-body p-4 align-items-center">
										<i class="fa-solid fa-waveform-lines fs-25 text-muted"></i>
										<div class="tool-title align-items-center">
											<h6 class="fs-14 font-weight-bold mb-1">{{__('Voice Chat')}}</h6> 
											<h6 class="fs-13 text-muted mb-0">{{__('Start an instant voice conversation')}}</h6> 
										</div>			
									</div>
								</div>													
							</div>
						@endif
						<div class="col-md-6 col-sm-12">												
							<div class="card dashboard-tool-box" onclick="window.location.href='{{ url('app/user/chat/custom') }}/'">
								<div class="card-body p-4 align-items-center">
									<i class="fa-solid fa-message-captions fs-25 text-muted"></i>
									<div class="tool-title align-items-center">
										<h6 class="fs-14 font-weight-bold mb-1">{{__('Chat Assistant')}}</h6> 
										<h6 class="fs-13 text-muted mb-0">{{__('Create your own chatbots')}}</h6> 
									</div>			
								</div>
							</div>													
						</div>						
						<div class="col-md-6 col-sm-12">												
							<div class="card dashboard-tool-box" onclick="window.location.href='{{ url('app/user/wizard') }}/'">
								<div class="card-body p-4 align-items-center">
									<i class="fa-solid fa-sparkles fs-25 text-muted"></i>
									<div class="tool-title align-items-center">
										<h6 class="fs-14 font-weight-bold mb-1">{{__('Blog Post')}}</h6> 
										<h6 class="fs-13 text-muted mb-0">{{__('Write a long article with full control')}}</h6> 
									</div>			
								</div>
							</div>													
						</div>
						@if (App\Services\HelperService::extensionSocialMedia())
							<div class="col-md-6 col-sm-12">												
								<div class="card dashboard-tool-box" onclick="window.location.href='{{ url('app/user/social-media') }}/'">
									<div class="card-body p-4 align-items-center">
										<i class="fa-solid fa-share-nodes fs-25 text-muted"></i>
										<div class="tool-title align-items-center">
											<h6 class="fs-14 font-weight-bold mb-1">{{__('Social Media Post')}}</h6> 
											<h6 class="fs-13 text-muted mb-0">{{__('Create your next viral post')}}</h6> 
										</div>			
									</div>
								</div>													
							</div>
						@endif
						<div class="col-md-6 col-sm-12">												
							<div class="card dashboard-tool-box" onclick="window.location.href='{{ url('app/user/templates/custom') }}/'">
								<div class="card-body p-4 align-items-center">
									<i class="fa-solid fa-microchip-ai fs-25 text-muted"></i>
									<div class="tool-title align-items-center">
										<h6 class="fs-14 font-weight-bold mb-1">{{__('Template')}}</h6> 
										<h6 class="fs-13 text-muted mb-0">{{__('Create your own AI Writer template')}}</h6> 
									</div>			
								</div>
							</div>													
						</div>
						<div class="col-md-6 col-sm-12">												
							<div class="card dashboard-tool-box" onclick="window.location.href='{{ url('app/user/smart-editor') }}/'">
								<div class="card-body p-4 align-items-center">
									<i class="fa-solid fa-feather fs-25 text-muted"></i>
									<div class="tool-title align-items-center">
										<h6 class="fs-14 font-weight-bold mb-1">{{__('Content')}}</h6> 
										<h6 class="fs-13 text-muted mb-0">{{__('Get creative and use them all')}}</h6> 
									</div>			
								</div>
							</div>													
						</div>
					</div>					
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-7">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body p-6">
					<h4 class="mb-4 mt-2 font-weight-700 fs-20"><i class="fa-solid fa-sparkles mr-2 text-primary"></i> {{ __('Hey, What can I do for you today?') }}</h4>	
					<div id="search-bar" class="main-search-container mt-auto mb-3">
						<div class="search-wrapper">
							<i class="fa fa-search" id="search-icon-main"></i>
							<input id="main-search-banner" type="text" class="form-control search-input" placeholder="{{__('Search for documents, templates and chatbots...')}}">
							<span class="left-pan" id="mic-search"><i class="fa fa-microphone"></i></span>                      
						</div>       
					</div>
					<a class="fs-12 main-search-action-button text-muted" href="{{ route('user.smart.editor') }}">{{ __('Create a Blank Document') }} <i class="fa-solid fa-plus ml-2"></i></a>				
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body pt-5 pb-5 pl-6 pr-6">
					<div class="row text-center mb-4">
						<div class="col-lg col-md-6 col-sm-6 dashboard-border-right mt-auto mb-auto">
							<h6 class="fs-12 mt-3 font-weight-bold">@if ($configs->model_credit_name == 'words') {{ __('Words Left') }} @else {{ __('Tokens Left') }} @endif</h6>
							<h4 class="mb-0 font-weight-800 text-primary fs-20">@if(auth()->user()->tokens == -1) {{ __('Unlimited') }} @else {{ number_format(auth()->user()->tokens + auth()->user()->tokens_prepaid) }} @endif</h4>
							<div class="view-credits mb-3"><a class=" fs-11 text-muted" href="javascript:void(0)" id="view-credits" data-bs-toggle="modal" data-bs-target="#creditsModel"> {{ __('View All Credits') }}</a></div> 										
						</div>
						@role('user|subscriber|admin')
							@if (config('settings.image_feature_user') == 'allow')
								<div class="col-lg col-md-6 col-sm-6 dashboard-border-right mt-auto mb-auto">
									<h6 class="fs-12 mt-3 font-weight-bold">{{ __('Media Credits Left') }}</h6>
									<h4 class="mb-3 font-weight-800 text-primary fs-20">@if(auth()->user()->images == -1) {{ __('Unlimited') }} @else {{ number_format(auth()->user()->images + auth()->user()->images_prepaid) }} @endif</h4>										
								</div>	
							@endif
						@endrole	
						@role('user|subscriber|admin')
							@if (config('settings.voiceover_feature_user') == 'allow')				
								<div class="col-lg col-md-6 col-sm-6 dashboard-border-right mt-auto mb-auto">
									<h6 class="fs-12 mt-3 font-weight-bold">{{ __('Characters Left') }}</h6>
									<h4 class="mb-3 font-weight-800 text-primary fs-20">@if(auth()->user()->characters == -1) {{ __('Unlimited') }} @else {{ number_format(auth()->user()->characters + auth()->user()->characters_prepaid) }} @endif</h4>										
								</div>
							@endif
						@endrole
						@role('user|subscriber|admin')
							@if (config('settings.whisper_feature_user') == 'allow')
								<div class="col-lg col-md-6 col-sm-6 mt-auto mb-auto">
									<h6 class="fs-12 mt-3 font-weight-bold">{{ __('Minutes Left') }}</h6>
									<h4 class="mb-3 font-weight-800 text-primary fs-20">@if(auth()->user()->minutes == -1) {{ __('Unlimited') }} @else {{ number_format(auth()->user()->minutes + auth()->user()->minutes_prepaid) }} @endif</h4>										
								</div>
							@endif
						@endrole
					</div>

					<div class="row mb-6">
						<div class="col-md-12">
							<h6 class="fs-12 font-weight-semibold text-muted">{{ __('Your Documents') }}</h6>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $content_documents * 100  }}%; border-top-left-radius: 10px; border-bottom-left-radius: 10px" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ $content_images * 100  }}%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: {{ $content_voiceovers * 100  }}%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: {{ $content_transcripts * 100  }}%; border-top-right-radius: 10px; border-bottom-right-radius: 10px" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
							  </div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-lg col-md-4 col-sm-12">
							<div class="card overflow-hidden user-dashboard-special-box">
								<div class="card-body d-flex">
									<div class="usage-info w-100">
										<p class=" mb-3 fs-12 font-weight-bold">{{ __('Words Generated') }}</p>
										<h2 class="mb-2 fs-14 font-weight-semibold text-muted">{{ number_format($data['words']) }} <span class="text-muted fs-14">{{ __('words') }}</span></h2>
									</div>
									<div class="usage-icon-dashboard text-muted text-right">
										<i class="fa-solid fa-microchip-ai"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg col-md-4 col-sm-12">
							<div class="card overflow-hidden user-dashboard-special-box">
								<div class="card-body d-flex">
									<div class="usage-info w-100">
										<p class=" mb-3 fs-12 font-weight-bold">{{ __('Documents Saved') }}</p>
										<h2 class="mb-2 fs-14 font-weight-semibold text-muted">{{ number_format($data['documents']) }} <span class="text-muted fs-14">{{ __('documents') }}</span></h2>
									</div>
									<div class="usage-icon-dashboard text-primary text-right">
										<i class="fa-solid fa-folder-grid"></i>
									</div>
								</div>
							</div>
						</div>						
						@role('user|subscriber|admin')
                    		@if (config('settings.image_feature_user') == 'allow')
								<div class="col-lg col-md-4 col-sm-12">
									<div class="card overflow-hidden user-dashboard-special-box">
										<div class="card-body d-flex">
											<div class="usage-info w-100">
												<p class=" mb-3 fs-12 font-weight-bold">{{ __('Images Created') }}</p>
												<h2 class="mb-2 fs-14 font-weight-semibold text-muted">{{ number_format($data['images']) }} <span class="text-muted fs-14">{{ __('images') }}</span></h2>
											</div>
											<div class="usage-icon-dashboard text-success text-right">
												<i class="fa-solid fa-image-landscape"></i>
											</div>
										</div>
									</div>
								</div>
							@endif
						@endrole
						@role('user|subscriber|admin')
                    		@if (config('settings.voiceover_feature_user') == 'allow')
								<div class="col-lg col-md-4 col-sm-12">
									<div class="card overflow-hidden user-dashboard-special-box">
										<div class="card-body d-flex">
											<div class="usage-info w-100">
												<p class=" mb-3 fs-12 font-weight-bold">{{ __('Voiceover Tasks') }}</p>
												<h2 class="mb-2 fs-14 font-weight-semibold text-muted">{{ number_format($data['synthesized']) }} <span class="text-muted fs-14">{{ __('tasks') }}</span></h2>
											</div>
											<div class="usage-icon-dashboard text-yellow text-right">
												<i class="fa-solid fa-waveform-lines"></i>
											</div>
										</div>
									</div>
								</div>
							@endif
						@endrole
						@role('user|subscriber|admin')
                    		@if (config('settings.whisper_feature_user') == 'allow')
								<div class="col-lg col-md-4 col-sm-12">
									<div class="card overflow-hidden user-dashboard-special-box">
										<div class="card-body d-flex">
											<div class="usage-info w-100">
												<p class=" mb-3 fs-12 font-weight-bold">{{ __('Audio Transcribed') }}</p>
												<h2 class="mb-2 fs-14 font-weight-semibold text-muted">{{ number_format($data['transcribed']) }} <span class="text-muted fs-14">{{ __('audio files') }}</span></h2>
											</div>
											<div class="usage-icon-dashboard text-danger text-right">
												<i class="   fa-solid fa-folder-music"></i>
											</div>
										</div>
									</div>
								</div>
							@endif
						@endrole
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-lg col-md-12 col-sm-12">
			<div class="card pb-5" id="user-dashboard-panels">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-message-captions mr-2 text-muted"></i>{{ __('Favorite AI Chat Assistants') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
								<a class="dropdown-item" href="{{ route('user.chat') }}">{{ __('View All') }}</a>	
							</div>
						</div>
					</div>
				</div>
				<div class="card-body pt-2 favorite-dashboard-panel">

					@if ($chat_quantity)
						<div class="row" id="templates-panel">

							@foreach ($favorite_chats as $chat)
								<div class="col-md-6 col-sm-12" id="{{ $chat->chat_code }}">
									<div class="chat-boxes-dasboard text-center">
										<a id="{{ $chat->chat_code }}" @if($chat->favorite) data-tippy-content="{{ __('Remove from favorite') }}" @else data-tippy-content="{{ __('Select as favorite') }}" @endif onclick="favoriteChatStatus(this.id)"><i id="{{ $chat->chat_code }}-icon" class="@if($chat->favorite) fa-solid fa-stars @else fa-regular fa-star @endif star"></i></a>
										<div class="card @if($chat->category == 'professional') professional @elseif($chat->category == 'premium') premium @endif" id="{{ $chat->chat_code }}-card" onclick="window.location.href='{{ url('app/user/chats') }}/{{ $chat->chat_code }}'">
											<div class="card-body p-4 d-flex align-items-center">
												<div class="widget-user-image overflow-hidden mt-4 mb-4"><img alt="User Avatar" class="rounded-circle" src="{{ URL::asset($chat->logo) }}"></div>
												<div class="template-title align-items-center">
													<h6 class="fs-13 font-weight-bold mb-0">{{ __($chat->name) }}</h6> 
													<h6 class="fs-13 text-muted mb-0">{{ __($chat->sub_name) }}</h6> 
												</div>
												@if($chat->category == 'professional') 
													<p class="fs-8 btn package-badge btn-pro"><i class="   fa-solid fa-crown mr-2"></i>{{ __('Pro') }}</p> 
												@elseif($chat->category == 'free')
													<p class="fs-8 btn package-badge btn-free"><i class="   fa-solid fa-gift mr-2"></i>{{ __('Free') }}</p> 
												@elseif($chat->category == 'premium')
													<p class="fs-8 btn package-badge btn-premium"><i class="   fa-solid fa-gem mr-2"></i>{{ __('Premium') }}</p> 
												@endif						
											</div>
										</div>
									</div>							
								</div>
							@endforeach

							@foreach ($custom_chats as $chat)
								<div class="col-sm-12" id="{{ $chat->chat_code }}">
									<div class="chat-boxes-dasboard text-center">
										<a id="{{ $chat->chat_code }}" @if($chat->favorite) data-tippy-content="{{ __('Remove from favorite') }}" @else data-tippy-content="{{ __('Select as favorite') }}" @endif onclick="favoriteChatStatus(this.id)"><i id="{{ $chat->chat_code }}-icon" class="@if($chat->favorite) fa-solid fa-stars @else fa-regular fa-star @endif star"></i></a>
										<div class="card @if($chat->category == 'professional') professional @elseif($chat->category == 'premium') premium @endif" id="{{ $chat->chat_code }}-card" onclick="window.location.href='{{ url('app/user/chats/custom') }}/{{ $chat->chat_code }}'">
											<div class="card-body p-4 d-flex align-items-center">
												<div class="widget-user-image overflow-hidden mt-4 mb-4"><img alt="User Avatar" class="rounded-circle" src="{{ URL::asset($chat->logo) }}"></div>
												<div class="template-title align-items-center">
													<h6 class="fs-13 font-weight-bold mb-0">{{ __($chat->name) }}</h6> 
													<h6 class="fs-13 text-muted mb-0">{{ __($chat->sub_name) }}</h6> 
												</div>
												@if($chat->category == 'professional') 
													<p class="fs-8 btn package-badge btn-pro"><i class="   fa-solid fa-crown mr-2"></i>{{ __('Pro') }}</p> 
												@elseif($chat->category == 'free')
													<p class="fs-8 btn package-badge btn-free"><i class="   fa-solid fa-gift mr-2"></i>{{ __('Free') }}</p> 
												@elseif($chat->category == 'premium')
													<p class="fs-8 btn package-badge btn-premium"><i class="   fa-solid fa-gem mr-2"></i>{{ __('Premium') }}</p> 
												@endif						
											</div>
										</div>
									</div>							
								</div>
							@endforeach

						</div>
					@else
						<div class="row text-center mt-8">
							<div class="col-sm-12">
								<h6 class="text-muted">{{ __('To add AI chat assistant as your favorite ones, simply click on the start icon on desired') }} <a href="{{ route('user.chat') }}" class="text-primary internal-special-links font-weight-bold">{{ __('AI Chat Assistants') }}</a></h6>
							</div>
						</div>
					@endif
					
				</div>
			</div>
		</div>
		
		<div class="col-lg col-md-12 col-sm-12">
			<div class="card pb-5" id="user-dashboard-panels">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-microchip-ai mr-2 text-muted"></i>{{ __('Favorite AI Templates') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
								<a class="dropdown-item" href="{{ route('user.templates') }}">{{ __('View All') }}</a>	
							</div>
						</div>
					</div>
				</div>
				<div class="card-body pt-2 favorite-dashboard-panel">

					@if ($template_quantity)
						<div class="row" id="templates-panel">

							@foreach ($templates as $template)
								<div class="col-sm-12" id="{{ $template->template_code }}">
									<div class="template-dashboard">
										<a id="{{ $template->template_code }}" @if($template->favorite) data-tippy-content="{{ __('Remove from favorite') }}" @else data-tippy-content="{{ __('Select as favorite') }}" @endif onclick="favoriteStatus(this.id)"><i class="@if($template->favorite) fa-solid fa-stars @else fa-regular fa-star @endif star"></i></a>
										<div class="card @if($template->package == 'professional') professional @elseif($template->package == 'premium') premium @endif" onclick="window.location.href='{{ url('app/user/templates/original-template') }}/{{ $template->slug }}'">
											<div class="card-body d-flex">
												<div class="template-icon">
													{!! $template->icon !!}													
												</div>
												<div class="template-title ml-4">
													<div class="d-flex">
														<h6 class="fs-13 number-font mt-auto mb-auto">{{ __($template->name) }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __($template->description) }}</p>
												</div>
											</div>
										</div>
									</div>							
								</div>
							@endforeach

							@foreach ($custom_templates as $template)
								<div class="col-sm-12" id="{{ $template->template_code }}">
									<div class="template-dashboard">
										<a id="{{ $template->template_code }}" @if($template->favorite) data-tippy-content="{{ __('Remove from favorite') }}" @else data-tippy-content="{{ __('Select as favorite') }}" @endif onclick="favoriteStatusCustom(this.id)"><i class="@if($template->favorite) fa-solid fa-stars @else fa-regular fa-star @endif star"></i></a>
										<div class="card @if($template->package == 'professional') professional @elseif($template->package == 'premium') premium @endif" onclick="window.location.href='{{ url('app/user/templates') }}/{{ $template->slug }}/{{ $template->template_code }}'">
											<div class="card-body d-flex">
												<div class="template-icon">
													{!! $template->icon !!}													
												</div>
												<div class="template-title ml-4">
													<div class="d-flex">
														<h6 class="fs-13 number-font mt-auto mb-auto">{{ __($template->name) }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __($template->description) }}</p>
												</div>
											</div>
										</div>
									</div>							
								</div>
							@endforeach

						</div>
					@else
						<div class="row text-center mt-8">
							<div class="col-sm-12">
								<h6 class="text-muted">{{ __('To add templates as your favorite ones, simply click on the start icon on desired') }} <a href="{{ route('user.templates') }}" class="text-primary internal-special-links font-weight-bold">{{ __('templates') }}</a></h6>
							</div>
						</div>
					@endif
					
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-lg col-md-12 col-sm-12">
			<div class="card" id="user-dashboard-panels">
				<div class="card-header pt-4 pb-4 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-images mr-2 text-muted"></i>{{ __('Latest Images') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">                                
								<a class="dropdown-item" href="{{ route('user.images') }}">{{ __('View All') }}</a>    
							</div>
						</div>
					</div>
				</div>
				<div class="card-body pt-2 pb-6">
					<div class="dashboard-image-carousel">
						@foreach ($latest_images as $image)
						<div class="carousel-item">
							<div class="grid-image-wrapper">
								<div class="flex grid-buttons text-center">
									<a href="{{ url($image->image) }}" class="grid-image-view text-center" download><i class="   fa-solid fa-arrow-down-to-line" title="{{ __('Download Image') }}"></i></a>
									<a href="#" class="grid-image-view text-center viewImageResult" id="{{ $image->id }}"><i class="   fa-solid fa-camera-viewfinder" title="{{ __('View Image') }}"></i></a>
									<a href="#" class="grid-image-view text-center deleteResultButton" id="{{ $image->id }}"><i class="fa-solid fa-trash-xmark" title="{{ __('Delete Image') }}"></i></a>							
								</div>
								<div>
									<span class="grid-image">
										<img class="lazy" data-src="@if($image->storage == 'local') {{ URL::asset($image->image) }} @else {{ $image->image }} @endif" alt="" >
										<noscript>
											<img src="@if($image->storage == 'local') {{ URL::asset($image->image) }} @else {{ $image->image }} @endif" alt="">
										</noscript>
									</span>
								</div>
								<div class="grid-description">
									<span class="fs-9 text-primary">
										@if ($image->vendor == 'sd') {{ __('Stable Diffusion') }} 
										@elseif ($image->vendor == 'openai') {{ __('Dalle') }} 
										@elseif ($image->vendor == 'falai') {{ __('FLUX') }} 
										@elseif ($image->vendor == 'midjourney') {{ __('Midjourney') }} 
										@elseif ($image->vendor == 'clipdrop') {{ __('Clipdrop') }} 
										@endif
									</span>
									<p class="fs-10 mb-0">{{ substr($image->description, 0, 63) }}...</p>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-lg col-md-6 col-sm-12">
			<div class="card" style="height: 100%;">
				<div class="card-header pt-4 pb-0 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-folder-bookmark mr-2 text-muted"></i>{{ __('Recent Documents') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
								<a class="dropdown-item" href="{{ route('user.documents') }}">{{ __('View All') }}</a>	
							</div>
						</div>
					</div>
				</div>
				<div class="card-body pt-2 responsive-dashboard-table dashboard-panel-400">
					<table class="table table-hover" id="database-backup">
						<thead>
							<tr role="row">
								<th class="fs-12 font-weight-700 border-top-0">{{ __('Document Name') }}</th>
								<th class="fs-12 font-weight-700 border-top-0 text-right">{{ __('Workbook') }}</th>
								<th class="fs-12 font-weight-700 border-top-0 text-right">{{ __('Last Activity') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($documents as $data)
							<tr class="relative">
								<td><div class="d-flex">
										<div class="mr-2 rtl-small-icon">{!! $data->icon !!}</div>
										<div><a class="font-weight-bold document-title" href="{{ route("user.documents.show", $data->id ) }}">{{ ucfirst($data->title) }}</a><br><span class="text-muted">{{ ucfirst($data->template_name) }}</span><div>
									</div>
								</td>
								<td class="text-right text-muted">{{ ucfirst($data->workbook) }}</td>
								<td class="text-right text-muted">{{ \Carbon\Carbon::parse($data->updated_at)->diffForHumans() }}</td>
								<td class="w-0 p-0" colspan="0">
									<a class="strage-things" style="position: absolute; inset: 0px; width: 100%" href="{{ route("user.documents.show", $data->id ) }}"><span class="sr-only">{{ __('View') }}</span></a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>					
				</div>
			</div>
		</div>

		<div class="col-lg col-md-6 col-sm-12">
			<div class="card" style="height: 100%;">
				<div class="card-header pt-4 pb-0 border-0">
					<div class="mt-3">
						<h3 class="card-title mb-2"><i class="fa-solid fa-solid fa-message-exclamation mr-2 text-muted"></i>{{ __('News & Notifications') }}</h3>
						<div class="btn-group dashboard-menu-button">
							<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
							<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
								<a class="dropdown-item" href="{{ route('user.notifications') }}">{{ __('View All') }}</a>	
							</div>
						</div>
					</div>
				</div>
				<div class="card-body pt-2 dashboard-timeline dashboard-panel-400">					
					<div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
						@foreach ($notifications as $notification)
							<div class="vertical-timeline-item vertical-timeline-element">
								<div>
									<span class="vertical-timeline-element-icon">
										@if ($notification->data['type'] == 'Warning')
											<i class="badge badge-dot badge-dot-xl badge-secondary"> </i>
										@elseif ($notification->data['type'] == 'Info')
											<i class="badge badge-dot badge-dot-xl badge-primary"> </i>
										@elseif ($notification->data['type'] == 'Announcement')
											<i class="badge badge-dot badge-dot-xl badge-success"> </i>
										@else
											<i class="badge badge-dot badge-dot-xl badge-warning"> </i>
										@endif
										
									</span>
									<div class="vertical-timeline-element-content">
										<h4 class="fs-13"><a href="{{ route("user.notifications.show", $notification->id)  }}"><b>{{ __($notification->data['type']) }}:</b></a> {{ __($notification->data['subject']) }}</h4>
										<p>@if ($notification->data['action'] == 'Action Required') <span class="text-danger">{{ __('Action Required') }}</span> @else <span class="text-muted fs-12">{{ __('No Action Required') }}</span> @endif</p>
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

	@if (config('settings.user_support') == 'enabled')
		<div class="row mt-7">		
			<div class="col-md-6 col-sm-12">
				<div class="card" style="height: 100%;">    
					<div class="card-body pt-2 dashboard-panel-500">                   
						<div class="title text-center dashboard-title" style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
							<div>
								<img class="mb-5 support-dashboard-image" src="{{ theme_url('/img/files/support.png')}}" alt="">
							</div>
							<h3 class="fs-24 super-strong">{{ __('Need Help?') }}</h3>     
							<h6 class="text-muted fs-14 mb-4">{{ __('Got questions? We have you covered') }}</h6>                    
							<a href="{{ route('user.support') }}" class="btn btn-primary pl-6 pr-6 mb-2" style="text-transform: none">{{ __('Create Support Ticket') }}</a>
							<h6 class="text-muted fs-10 mb-4">{{ __('Available from') }} <span class="font-weight-bold">{{ __('9am till 5pm') }}</span></h6> 
						</div>  
					</div>
				</div>                                             
			</div>

			<div class="col-md-6 col-sm-12">
				<div class="card" style="height: 100%;">
					<div class="card-header pt-4 pb-0 border-0">
						<div class="mt-3">
							<h3 class="card-title mb-2"><i class="fa-solid fa-headset mr-2 text-muted"></i>{{ __('Support Tickets') }}</h3>
							<div class="btn-group dashboard-menu-button">
								<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" id="export" data-bs-display="static" aria-expanded="false"><i class="fa-solid fa-ellipsis  table-action-buttons table-action-buttons-big edit-action-button"></i></button>
								<div class="dropdown-menu" aria-labelledby="export" data-popper-placement="bottom-start">								
									<a class="dropdown-item" href="{{ route('user.support') }}">{{ __('View All') }}</a>	
								</div>
							</div>
						</div>
					</div>
					<div class="card-body pt-2 dashboard-panel-400">
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
									<td><a class="font-weight-bold text-primary" href="{{ route("user.support.show", $data->ticket_id ) }}">{{ $data->ticket_id }}</a>
									</td>
									<td class="text-left text-muted">{{ ucfirst($data->subject) }}</td>
									<td class="text-center text-muted">{{ ucfirst($data->category) }}</td>
									<td class="text-center"><span class="cell-box support-{{ strtolower($data->status) }}">{{ __(ucfirst($data->status)) }}</span></td>
									<td class="text-right text-muted">{{ \Carbon\Carbon::parse($data->updated_at)->diffForHumans() }}</td>
									<td class="w-0 p-0" colspan="0">
										<a class="strage-things" style="position: absolute; inset: 0px; width: 100%" href="{{ route("user.support.show", $data->ticket_id ) }}"><span class="sr-only">{{ __('View') }}</span></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>					
					</div>
				</div>                      
			</div>   
		</div>
	@endif

	<div class="image-modal">
		<div class="modal fade" id="image-view-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h6>{{ __('Image View') }}</h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body pb-6 pr-5 pl-5">
					
				</div>
			</div>
			</div>
	  	</div>
	</div>
@endsection

@section('js')
	<!-- Chart JS -->
	<script src="{{URL::asset('plugins/chart/chart.min.js')}}"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script src="{{ URL::asset('plugins/slick/slick.min.js') }}"></script>
	<script>
		$(function() {
	
			'use strict';

			// Total New User Analysis Chart
			var userMonthlyData = JSON.parse(`<?php echo $chart_data['user_monthly_usage']; ?>`);
			var userMonthlyDataset = Object.values(userMonthlyData);

			let chartColor = "#FFFFFF";
			let gradientChartOptionsConfiguration = {
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
						display: 0,
						grid: 0,
						ticks: {
							display: false,
							padding: 0,
							beginAtZero: true,
						},
						grid: {
							zeroLineColor: "transparent",
							drawTicks: false,
							display: false,
							drawBorder: false,
						}
					},
					x: {
						display: 0,
						grid: 0,
						ticks: {
							display: false,
							padding: 0,
							beginAtZero: true,
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
						right: -10,
						top: 0,
						bottom: -10
					}
				},
				elements: {
					line: {
						tension : 0.4
					},
				},
			};

			let ctx2 = document.getElementById('hoursSavedChart').getContext("2d");
			let gradientStroke = ctx2.createLinearGradient(500, 0, 100, 0);
			gradientStroke.addColorStop(0, '#18ce0f');
			gradientStroke.addColorStop(1, chartColor);
			let gradientFill = ctx2.createLinearGradient(0, 170, 0, 50);
			gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
			gradientFill.addColorStop(1, "rgba(24,206,15, 0.4)");
			let myChart = new Chart(ctx2, {
				type: 'line',
				data: {
					labels: ['{{ __('Jan') }}', '{{ __('Feb') }}', '{{ __('Mar') }}', '{{ __('Apr') }}', '{{ __('May') }}', '{{ __('Jun') }}', '{{ __('Jul') }}', '{{ __('Aug') }}', '{{ __('Sep') }}', '{{ __('Oct') }}', '{{ __('Nov') }}', '{{ __('Dec') }}'],
					datasets: [{
						label: "{{ __('Words Generated') }}",
						borderColor: "#18ce0f",
						pointBorderColor: "#FFF",
						pointBackgroundColor: "#18ce0f",
						pointBorderWidth: 1,
						pointHoverRadius: 4,
						pointHoverBorderWidth: 1,
						pointRadius: 3,
						fill: true,
						backgroundColor: gradientFill,
						borderWidth: 2,
						data: userMonthlyDataset
					}]
				},
				options: gradientChartOptionsConfiguration
			});

		});

		function favoriteStatus(id) {

			let formData = new FormData();
			formData.append("id", id);

			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'post',
				url: 'dashboard/favorite',
				data: formData,
				processData: false,
				contentType: false,
				success: function (data) {

					if (data['status'] == 'success') {
						if (data['set']) {
							Swal.fire('{{ __('Template Removed from Favorites') }}', '{{ __('Selected template has been successfully removed from favorites') }}', 'success');
							document.getElementById(id).style.display = 'none';	
						} else {
							Swal.fire('{{ __('Template Added to Favorites') }}', '{{ __('Selected template has been successfully added to favorites') }}', 'success');
						}
														
					} else {
						Swal.fire('{{ __('Favorite Setting Issue') }}', '{{ __('There as an issue with setting favorite status for this template') }}', 'warning');
					}      
				},
				error: function(data) {
					Swal.fire('Oops...','Something went wrong!', 'error')
				}
			})

			return false;
		}

		function favoriteStatusCustom(id) {

			let formData = new FormData();
			formData.append("id", id);

			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'post',
				url: 'dashboard/favoritecustom',
				data: formData,
				processData: false,
				contentType: false,
				success: function (data) {

					if (data['status'] == 'success') {
						if (data['set']) {
							Swal.fire('{{ __('Template Removed from Favorites') }}', '{{ __('Selected template has been successfully removed from favorites') }}', 'success');
							document.getElementById(id).style.display = 'none';	
						} else {
							Swal.fire('{{ __('Template Added to Favorites') }}', '{{ __('Selected template has been successfully added to favorites') }}', 'success');
						}
														
					} else {
						Swal.fire('{{ __('Favorite Setting Issue') }}', '{{ __('There as an issue with setting favorite status for this template') }}', 'warning');
					}      
				},
				error: function(data) {
					Swal.fire('Oops...','Something went wrong!', 'error')
				}
			})

			return false;
		}

		function favoriteChatStatus(id) {

			let icon, card;
			let formData = new FormData();
			formData.append("id", id);

			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'post',
				url: 'chat/favorite',
				data: formData,
				processData: false,
				contentType: false,
				success: function (data) {

					if (data['status'] == 'success') {
						if (data['set']) {
							Swal.fire('{{ __('Chat Bot Removed from Favorites') }}', '{{ __('Selected chat bot has been successfully removed from favorites') }}', 'success');
							document.getElementById(id).style.display = 'none';
							icon = document.getElementById(id + '-icon');
							icon.classList.remove("fa-solid");
							icon.classList.remove("fa-stars");
							icon.classList.add("fa-regular");
							icon.classList.add("fa-star");

							card = document.getElementById(id + '-card');
							if(card.classList.contains("professional")) {
								// do nothing
							} else {
								card.classList.remove("favorite");
								card.classList.add('border-0');
							}							
						} else {
							Swal.fire('{{ __('Chat Bot Added to Favorites') }}', '{{ __('Selected chat bot has been successfully added to favorites') }}', 'success');
							icon = document.getElementById(id + '-icon');
							icon.classList.remove("fa-regular");
							icon.classList.remove("fa-star");
							icon.classList.add("fa-solid");
							icon.classList.add("fa-stars");

							card = document.getElementById(id + '-card');
							if(card.classList.contains("professional")) {
								// do nothing
							} else {
								card.classList.add('favorite');
								card.classList.remove('border-0');
							}
						}
														
					} else {
						Swal.fire('{{ __('Favorite Setting Issue') }}', '{{ __('There as an issue with setting favorite status for this chat bot') }}', 'warning');
					}      
				},
				error: function(data) {
					Swal.fire('Oops...','Something went wrong!', 'error')
				}
			})
		}


		document.addEventListener('DOMContentLoaded', function() {
			const ctx = document.getElementById('userDoughnut').getContext('2d');
			
			// Get token value
			const remainingTokens = {{ $remaining_tokens }};
			const usedTokens = {{ $used_tokens }};
			const balanceType = '{{ $balance}}';
			
			new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: ['{{ __("Remaining") }}', '{{ __("Used") }}'],
					datasets: [{
						data: [remainingTokens, usedTokens],
						backgroundColor: ['#FFF', 'rgba(255, 255, 255, 0.15)'],
						borderWidth: 0,
						weight: 0.7
					}]
				},
				options: {
				cutout: '70%',
				circumference: 180,
				rotation: 270,
				maintainAspectRatio: false,
				responsive: true,
				plugins: {
					legend: {
						display: true,
						position: 'bottom',
						labels: {
							color: '#FFFFFF',
							font: {
								size: 10
							},
							padding: 16,
							usePointStyle: true,
            				pointStyle: 'circle'
						}
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								return remainingTokens;
							}
						}
					}
				},
				layout: {
					padding: 10
				}
				},
				plugins: [{
				id: 'centerText',
				afterDraw: function(chart) {
					const width = chart.width;
					const height = chart.height;
					const ctx = chart.ctx;
					
					ctx.restore();
					ctx.font = 'bold 14px Poppins';
					ctx.fillStyle = '#FFFFFF';
					ctx.textBaseline = 'middle';
					ctx.textAlign = 'center';
					
					const valueText  = balanceType.toString();
					const textX = width / 2;
					const textY = height - (height / 2.5);
					
					ctx.fillText(valueText, textX, textY);
        
					// "words" text
					ctx.font = '11px Arial';
					ctx.fillStyle = '#FFFFFF';
					ctx.textAlign = 'center';
					
					const wordsText = '{{ __("words") }} {{ __("left") }}';
					const wordsY = textY + 17;
					
					ctx.fillText(wordsText, textX, wordsY);
					
					
					ctx.save();
				}
				}]
			});
		});

		
		document.addEventListener('DOMContentLoaded', function() {
			const searchInput = document.getElementById('main-search-banner');
			const searchResultsContainer = document.createElement('div');
			searchResultsContainer.className = 'search-results-dropdown';
			searchResultsContainer.style.display = 'none';
			searchInput.parentNode.appendChild(searchResultsContainer);
			let searchIcon = document.getElementById('search-icon-main');
			
			let debounceTimer;
			
			searchInput.addEventListener('input', function() {
				const query = this.value.trim();
				
				clearTimeout(debounceTimer);
				
				if (query.length < 2) {
					searchResultsContainer.style.display = 'none';
					searchIcon.classList.remove('fa-spinner', 'fa-spin');
					searchIcon.classList.add('fa-search');
					return;
				}

				searchIcon.classList.remove('fa-search');
				searchIcon.classList.add('fa-spinner', 'fa-spin');
		
				debounceTimer = setTimeout(() => {
					fetch('/app/user/search', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						},
						body: JSON.stringify({ query: query })
					})
					.then(response => response.json())
					.then(data => {
						searchIcon.classList.remove('fa-spinner', 'fa-spin');
						searchIcon.classList.add('fa-search');

						displaySearchResults(data);
					})
					.catch(error => {
						searchIcon.classList.remove('fa-spinner', 'fa-spin');
						searchIcon.classList.add('fa-search');

						console.error('Error:', error);
					});
				}, 300);
			});
			
			function displaySearchResults(results) {
				searchResultsContainer.innerHTML = '';
				
				const hasResults = 
					results.chats.length > 0 || 
					results.contents.length > 0 || 
					results.templates.length > 0;
				
				// Create section with header
				const searchSection = document.createElement('div');
				searchSection.className = 'search-section';
				
				const searchHeader = document.createElement('h4');
				searchHeader.textContent = 'Search Results';
				searchHeader.className = 'search-section-header';
				searchSection.appendChild(searchHeader);
				
				if (!hasResults) {
					const noResultsMessage = document.createElement('div');
					noResultsMessage.className = 'no-results-message';
					noResultsMessage.textContent = 'No results found, please try with another word';
					
					searchSection.appendChild(noResultsMessage);
					searchResultsContainer.appendChild(searchSection);
					searchResultsContainer.style.display = 'block';
					return;
				}
				
				// Create a single list for all results
				const resultsList = document.createElement('ul');
				resultsList.className = 'search-results-list';
				
				// Add templates
				results.templates.forEach(template => {
					const item = document.createElement('li');
					item.className = 'search-result-item';
					
					const link = document.createElement('a');
					link.href = template.url;
					
					const iconContainer = document.createElement('span');
					iconContainer.className = 'icon-container';
					iconContainer.innerHTML = template.icon || '<i class="fa-solid fa-file-lines"></i>';
					
					const nameSpan = document.createElement('span');
					nameSpan.className = 'result-name';
					nameSpan.textContent = template.name;
					
					const typeSpan = document.createElement('span');
					typeSpan.className = 'result-type';
					typeSpan.textContent = 'Template';
					
					link.appendChild(iconContainer);
					link.appendChild(nameSpan);
					link.appendChild(typeSpan);
					item.appendChild(link);
					resultsList.appendChild(item);
				});
				
				// Add chats
				results.chats.forEach(chat => {
					const item = document.createElement('li');
					item.className = 'search-result-item';
					
					const link = document.createElement('a');
					link.href = chat.url;
					
					const iconContainer = document.createElement('span');
					iconContainer.className = 'icon-container';
					if (chat.logo) {
						const img = document.createElement('img');
						img.src = chat.logo;
						img.className = 'chat-logo';
						img.alt = chat.name;
						iconContainer.appendChild(img);
					} else {
						iconContainer.innerHTML = '<i class="fa-solid fa-comments"></i>';
					}
					
					const nameSpan = document.createElement('span');
					nameSpan.className = 'result-name';
					nameSpan.textContent = chat.name;
					
					const typeSpan = document.createElement('span');
					typeSpan.className = 'result-type';
					typeSpan.textContent = 'Chat Assistant';
					
					link.appendChild(iconContainer);
					link.appendChild(nameSpan);
					link.appendChild(typeSpan);
					item.appendChild(link);
					resultsList.appendChild(item);
				});
				
				// Add contents
				results.contents.forEach(content => {
					const item = document.createElement('li');
					item.className = 'search-result-item';
					
					const link = document.createElement('a');
					link.href = content.url;
					
					const iconContainer = document.createElement('span');
					iconContainer.className = 'icon-container';
					iconContainer.innerHTML = content.icon || '<i class="fa-solid fa-file-lines"></i>';
					
					const nameSpan = document.createElement('span');
					nameSpan.className = 'result-name';
					nameSpan.textContent = content.title || 'Untitled Content';
					
					const typeSpan = document.createElement('span');
					typeSpan.className = 'result-type';
					typeSpan.textContent = 'Document';
					
					link.appendChild(iconContainer);
					link.appendChild(nameSpan);
					link.appendChild(typeSpan);
					item.appendChild(link);
					resultsList.appendChild(item);
				});
				
				searchSection.appendChild(resultsList);
				searchResultsContainer.appendChild(searchSection);
				searchResultsContainer.style.display = 'block';
			}
			
			// Close dropdown when clicking outside
			document.addEventListener('click', function(event) {
				if (!searchInput.contains(event.target) && !searchResultsContainer.contains(event.target)) {
					searchResultsContainer.style.display = 'none';
				}
			});
		});


		$(document).ready(function(){
			$('.dashboard-image-carousel').slick({
				dots: false,
				infinite: true,
				speed: 300,
				slidesToShow: 5,
				slidesToScroll: 1,
				autoplay: false,
				autoplaySpeed: 3000,
				responsive: [
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: 3,
							slidesToScroll: 1
						}
					},
					{
						breakpoint: 600,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1
						}
					}
				]
			});
		});

		$(document).on('click', '.viewImageResult', function(e) {

			"use strict";

			e.preventDefault();

			var id = $(this).attr("id");

			$.ajax({
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				method: 'post',
				url: 'images/view',
				data:{
					id: id,
				},
				success:function(data) {   
				
					if (data['status'] == 'success') {
						$("#image-view-modal .modal-body").html(data['modal']);
						var myModal = new bootstrap.Modal(document.getElementById('image-view-modal'))
						myModal.show();
					} else {
						toastr.error(data['message']);
					}
				
				}
			});
		});

		$(document).on('click', '.deleteResultButton', function(e) {

			e.preventDefault();

			Swal.fire({
				title: '{{ __('Confirm Image Deletion') }}',
				text: '{{ __('It will permanently delete this image') }}',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: '{{ __('Delete') }}',
				reverseButtons: true,
			}).then((result) => {
				if (result.isConfirmed) {
					var formData = new FormData();
					formData.append("id", $(this).attr('id'));
					$.ajax({
						headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
						method: 'post',
						url: 'images/delete',
						data: formData,
						processData: false,
						contentType: false,
						success: function (data) {
							if (data['status'] == 'success') {
								toastr.success('{{ __('Selected image has been successfully deleted') }}');	
								location.replace(location.href);								
							} else {
								toastr.error('{{ __('There was an error while deleting this image') }}');
							}      
						},
						error: function(data) {
							Swal.fire('Oops...','{{ __('Something went wrong') }}!', 'error')
						}
					})
				} 
			})
		});


		$(document).ready(function() {
    // Initialize lazy loading for carousel images
    const lazyImages = document.querySelectorAll('img.lazy');
    
    // Create an intersection observer
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });
    
    // Observe each lazy image
    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
    
    // Handle lazy loading when slick changes slides
    $('.dashboard-image-carousel').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        let slides = $(slick.$slides);
        let start = Math.max(0, nextSlide - 1);
        let end = Math.min(nextSlide + slick.options.slidesToShow + 1, slides.length);
        
        for (let i = start; i < end; i++) {
            let img = slides[i].querySelector('img.lazy');
            if (img) {
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                img.classList.add('loaded');
                imageObserver.unobserve(img);
            }
        }
    });
});
	</script>
@endsection

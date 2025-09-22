@extends('layouts.app')

@section('page-header')
	<!-- EDIT PAGE HEADER -->
	<div class="page-header mt-5-7 justify-content-center">
		<div class="page-leftheader text-center">
			<h4 class="page-title mb-0">{{ __('Upgrade Software') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-sliders mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{url('#')}}"> {{ __('General Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Upgrade Software') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<div class="row justify-content-center">
		<div class="col-lg-10 col-md-12 col-sm-12">
			<div class="card">
				<div class="card-body p-5">
					<form id="upgrade-form" method="POST" action="@if($version_metadata){{ route('admin.settings.upgrade.start', ['update_id' => $latest_version['update_id'], 'version' => $latest_version['version']]) }}@else{{ route('admin.settings.upgrade.start', ['update_id' => $current_version, 'version' => $current_version]) }} @endif" enctype="multipart/form-data">
						@csrf
						
						<div class="row">
							<div class="col-sm-12 col-md-12">
								@if($version_metadata)															
									@if ($latest_version['status'])
										<div class="text-center" id="not-installed-info">
											<h1 class="fs-24"><i class="fa-solid fa-box-check fs-24 mr-2 text-cancel"></i> {{ __('New Update is Available') }}</h1>
											<h6 class="fs-13 text-muted mt-4">{{ __('Current installed version') }}: <span class="text-info font-weight-bold">{{ $current_version }}</span></h6>	
											<h6 class="fs-13 text-muted mb-4">{{ __('New available version') }}: <span class="text-info font-weight-bold"> {{ $latest_version['version'] }} </span> </h6>
											<div id="audio-format" role="radiogroup">
												<span  id="webm-format">							
													<div class="radio-control">
														<input type="checkbox" name="concent" class="input-control fs-13" id="concent">
														<label for="concent" class="label-control text-muted fs-13" id="concent-label">{{  __('I confirm that I have read the Update tab in the documentation and will follow all steps there to finish the update') }} - <a class="font-weight-bold text-primary" target="_blank" href="https://davinci.berkine.me/documentation">{{ __('Documentation Link') }}</a></label>
													</div>	
												</span>										
											</div>	
										</div>
										<div id="installed-info">
											<div class="text-center">
												<h1 class="fs-24">{{ __('Update Installation Completed') }}</h1>
												<h6 class="text-success fs-14 font-weight-bold mt-4 mb-5"><span> {{ $latest_version['version'] }} </span> {{ __('version was installed successfully') }}</h6>
												<i class="fa-solid fa-box-check fs-50 text-success"></i>
												<h6 class="text-danger fs-14 font-weight-bold mt-4">{{ __('Warning! To complete the update process follow all steps listed under') }} <a class="font-weight-bold text-primary" target="_blank" href="https://davinci.berkine.me/documentation">update instructions </a> {{ __('in the docs') }}</h6>
											</div>
										</div>														
									@else
										<div class="text-center">
											<h1 class="fs-24">{{ __('You have the Latest Version Installed') }}</h1>
											<h1 class="fs-30 super-strong">{{ $current_version }}</h1>	
											<h6 class="text-danger fs-14 font-weight-bold mt-4">{{ __('Warning! To complete the update process follow all steps listed under') }} <a class="font-weight-bold text-primary" target="_blank" href="https://davinci.berkine.me/documentation">update instructions </a> {{ __('in the docs') }}</h6>									
											
										</div>
									@endif
								@else
									<div class="text-center">
										<h1 class="fs-24">{{ __('You have the Latest Version Installed') }}</h1>
										<h1 class="fs-30 super-strong">{{ $current_version }}</h1>	
										<h6 class="text-danger fs-14 font-weight-bold mt-4">{{ __('Warning! To complete the update process follow all steps listed under') }} <a class="font-weight-bold text-primary" target="_blank" href="https://davinci.berkine.me/documentation">update instructions </a> {{ __('in the docs') }}</h6>									
									</div>
								@endif								
							</div>
						</div>
						<div class="card-footer text-center border-0 pb-2 pt-5">
							@if($version_metadata)			
								<span id="processing"><img src="{{ theme_url('img/svgs/upgrade.svg') }}" alt=""></span>												
								<button id="upgrade" type="button" class="btn btn-primary">@if ($latest_version['status']) {{ __('Download & Install Upgrade') }} @else	{{ __('Check New Version') }} @endif</button>					
							@else
								<span id="processing"><img src="{{ theme_url('img/svgs/upgrade.svg') }}" alt=""></span>												
								<button id="update" type="button" class="btn btn-primary">{{ __('Check New Version') }}</button>					
							@endif
						</div>
					</form>
				</div>
			</div>
 
			<div class="changelogs">
				<h5>{{ __('Changelogs') }}</h5>
				<hr>

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.7</span> - <span class="fs-14 font-weight-semibold">2.07.2025</span>
					</div>   
					<div class="changelog-description mt-6">     
						<ul>	 	 				     
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">SEO Tool extension (Paid)</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">New Claude Sonnet 4 | Opus 4 models added</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Google Veo 3 added (AI Text to Video)</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Kling 2.1 Standard | Pro | Master added (AI Image to Video) & (AI Text to Video)</span></span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">External Chatbot updated (v1.1)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Wallet System disable option added (v1.1)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Image to Video updated (v1.6)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Text to Video updated (v1.4)</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Chat Share history deletion improved (v1.3)</span></span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Chat history deletion improved</span></span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">External Chatbot credit consumption fixed</span></span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">External Chatbot conversation list view fixed</span></span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">External Chatbot history view minor issue fixed</span></span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.6</span> - <span class="fs-14 font-weight-semibold">08.06.2025</span>
					</div>   
					<div class="changelog-description mt-6">     
						<ul>	 	 				     
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">External Chatbots (Paid)</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Enhanced User Dashboard</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Revamped Admin Dashboard with Advanced User and Finance Analytics</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Advanced search feature</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Redesign of the Default Theme</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Export all Gift Codes to PDF | Excel feature added</span></span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">SaaS Business extension updated (v2.2)</span></span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">RTL css styles updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Email notifications for Wallet transfers added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Dark mode for Default theme improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Theme switch performance improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">New openai models view fixed for AI Chat</span></span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">New openai models fixed for custom templates</span></span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.5</span> - <span class="fs-14 font-weight-semibold">12.05.2025</span>
					</div>   
					<div class="changelog-description mt-6">     
						<ul>	 	 				     
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Speech to Text Pro extension (Paid)</span></span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Gift System has been updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Saas Business extension updated (v2.1)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Wordpress Integration extension updated (v1.2)</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.4</span> - <span class="fs-14 font-weight-semibold">10.05.2025</span>
					</div>   
					<div class="changelog-description mt-6">     
						<ul>	 	 				     
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Wallet System added (Free)</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Gift Card System added</span></span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Wallet Balance transfer option between users added</span></span></li>										
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Saas Business extension updated (v2.0)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Modern theme updated (v1.3)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sonic theme updated (v1.7)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Classic theme updated (v1.7)</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.3</span> - <span class="fs-14 font-weight-semibold">5.05.2025</span>
					</div>   
					<div class="changelog-description mt-6">     
						<ul>	 	 				     
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">New OpenAI o4 mini | o3 | GPT 4.1 | GPT 4.1 mini | GPT 4.1 nano models added</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">New OpenAI GPT 4o Search Preview | GPT 4o mini Search Preview models with Web Search capabilities added</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Saas Business extension updated (v1.9)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Chat Share extension updated (v1.2)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Faceswap extension updated (v1.2)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom template creation feature updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Writer results saving improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Writer result view issue fixed</span></li>											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Documents results view minor issue fixed</span></li>											
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.2</span> - <span class="fs-14 font-weight-semibold">28.04.2025</span>
					</div>   
					<div class="changelog-description mt-6">     
						<ul>	 	 				     
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Coinremitter cryptocurrency payment gateway added (Paid)</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">TikTok added to Social Media Suite</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Premium Support package option added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Premium Extension package option added</span></li>							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stripe Free Trial days option added</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">New Documentation v2 released</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Social Media Suite updated (1.3)</span></li>											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">API Credit Management mechanism updated</span></li>																		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User can set default Image model as well now for AI Images feature</span></li>				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Youtube feature improved</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Saas Business Extension updated (v1.8)</span></li>																																					
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">o1 mini model response issue fixed</span></li>						
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Claude models credit calculations fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Vision credit consumption improved</span></li>						
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Smart Editor credit consumption improved</span></li>						
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Chat share extension installation fixed (v1.1)</span></li>											
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.1</span> - <span class="fs-14 font-weight-semibold">14.04.2025</span>
					</div>   
					<div class="changelog-description mt-6">     
						<ul>	 	 				     	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Xero integration extension added (Free)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Amazon Bedrock extension added (Free)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Azure OpenAI extension added (Free)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">OpenRouter extension added (Free)</span></li>							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Amazon New State of the Art Models Nova Micro / Lite / Pro added</span></li>							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Google Veo2 added</span></li>							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Advanced GDPR cookie consent banner added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">New multi language invoicing mechanism added</span></li>													
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Finance settings page updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ability to download invoices both by admins and users improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sending out invoices manually to the users options added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">All transactions have invoices attached now</span></li>							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Saas Business Extension updated (v1.4)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Image to Video extension updated (v1.5)</span></li>							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI ReWriter feature improved</span></li>		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Article Wizard feature improved</span></li>		
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Hubspot configuration fixed (v1.1)</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">File chat feature minor issue fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Web chat feature minor issue fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Speech to Text credit view minor issue fixed</span></li>						
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat Image credit view minor issue fixed</span></li>						
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.0.1</span> - <span class="fs-14 font-weight-semibold">23.01.2025</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat formatting fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat Listening button fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Voiceover credit display improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">CRON video result task handling improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Code credit calculation fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom template credit calculation improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">User list view issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Writer menu visibility fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 7.0</span> - <span class="fs-14 font-weight-semibold">15.03.2025</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">External Chatbots extension added (Paid)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Realtime Voice Chat extension added (Paid)</span></li>																												
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Textract extension added (Paid)</span></li>																															
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Hubspot extension added (Free)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Mailchimp extension added (Free)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Chat Share extension added (Free)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Perplexity AI extension added (Free)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Advanced Chat Training (Text, PDFs, URLs) extension added (Free)</span></li>													
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">IBM Watson Text to Speech extension added (Free)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Clipdrop extension added (Free)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Openai o1 / o3 mini / GPT-4.5 models added</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Gemini 1.5 Pro, 1.5 Flash & 2.0 Flash models added</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">DeepSeek R1 and V3 models added</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">xAI Grok 2 and Grok 2 Vision models added</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Anthropic Claude Sonnet 3.7 model added</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Perplexity Sonar family models added</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">New credit system for all AI Models (counts both actual input + output tokens) solution added</span></li>																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan deletion protection added when users are subscribed to it</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Stable Diffusion image models updated to 3.5</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Multi image generation option for Dalle 3 improved</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Multi image generation option for Stable Diffusion models improved</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Multi image generation option for Flux models improved</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Multi image generation option for Midjourney models improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan supports all new extensions</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan options updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Prepaid plan options updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Improved Chat result formatting and error handling</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Classic Theme updated (v1.5)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sonic Theme updated (v1.5)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Image, Video and Music credits are combined into media credits from now on</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Social Media extension added to subscription plans</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Realtime data access feature improved for Custom Chat Assistants</span></li>																
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Realtime data access feature improved for Custom Templates</span></li>																
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Realtime data access feature can switch between Serper and Perplexity now</span></li>																
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Music extension updated (deletion and credit system added) (v1.2)</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Video to Video extension updated (v1.1)</span></li>		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Faceswap extension updated (v1.1)</span></li>						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Image to Video extension updated (v1.4)</span></li>						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Text to Video extension updated (v1.3)</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Photo Studio extension updated (v1.1)</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Voice Clone extension updated (v1.1)</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Voice Isolator extension updated (v1.1)</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Product Photo extension updated (v1.2)</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">WordPress integration Auto Post feature improved (v1.1)</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">WordPress integration Multi WP Account feature improved (v1.1)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">WordPress categories, tags, feature image, SEO fields added (v1.1)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">SaaS Business extension updated (v1.3)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Improved Feature control for Free Tier users</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">User list order fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Maintenance mode minor issue fixed (v1.1)</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Timezone minor issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Added missing translation words</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Menu Builder installation issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Social Media instagram auth issue fixed (v1.2)</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.9.2</span> - <span class="fs-14 font-weight-semibold">08.02.2025</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Social Media installation issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.9.1</span> - <span class="fs-14 font-weight-semibold">27.01.2025</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Blog page view issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">System page view issues fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.9</span> - <span class="fs-14 font-weight-semibold">21.01.2025</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Video to Video extension added (Paid)</span></li>														
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Music (Text to Music) extension added (Paid)</span></li>													
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Menu Builder extension added (Paid)</span></li>																											
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Faceswap extension added (Paid)</span></li>												
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Midjourney extension added (Paid)</span></li>													
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Maintenance mode extension added (Free)</span></li>												
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Sitemap builder added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Fashion API added to AI Product Photo extension (v1.1)</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Image to Video extension new Models (Kling, Luma, Haiper, Stable Diffusion) and Custom Credit system added (v1.2)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Social Media updated (v1.1 update)</span></li>								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Avatar voice search option added (v1.4 update)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Avatar generation page updated (v1.4 update)</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Text to Video extension updated (v1.2 update)</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">SaaS Business extension updated (v1.2 update)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat welcome messages support for html tags added</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom template status editing fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Claude 3.5 Haiku model display issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Smart Editor minor issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Web Chat minor issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Flux AI image sizes fixed (v1.1 update)</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.8</span> - <span class="fs-14 font-weight-semibold">28.12.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Social Media added (Paid)</span></li>													
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Voice Isolator added (Free)</span></li>			
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Text to Video v1.1 update (Custom credit per Video Model added)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Avatar v1.2 update (Credits per Video option added)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Classic Theme v1.3 update</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sonic Theme v1.3 update</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Settings page updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Marketplace page updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">CRON image storage cleaning improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">SaaS feature are compliant with Envato License Policy now (no more access with Regular License)</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Missing translation words added to en.json</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Code copy text fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Emails SVG logo display issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Free subscription plan checkout issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Modern theme update issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.7</span> - <span class="fs-14 font-weight-semibold">04.12.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Video (Text to Video) (Paid)</span></li>							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sonic Theme v1.2</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Classic Theme v1.2</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Marketplace update mechanism improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Avatar v1.1 asset upload option added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Avatar v1.1 all voices view page added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Avatar v1.1 results delete option added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan update with AI Avatar features</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Avatar photo upload fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat Image - storj, dropbox storage issues fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.6</span> - <span class="fs-14 font-weight-semibold">27.11.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Avatar (Heygen) added (Paid)</span></li>				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Modern Theme updated - v1.1</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">o1 preview and o1 mini models are included into View Credits section</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">o1 preview and o1 mini models are included into AI Vendor Service Costs</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">o1 preview and o1 mini models are included into Dynamic cost calculator</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Marketplace and Themes performance improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Flux AI v1.1 update (Flux Pro and Flux Realism issues fixed)</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom category not working for custom templates fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Dynamic cost calculator issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Bank Transfer approval issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.5</span> - <span class="fs-14 font-weight-semibold">17.11.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Claude 3.5 Haiku and 3.5 Sonnet(latest) models added</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Wordpress Integration Auto Post feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Wordpress Integration Multi Website support feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Voice Clone feature added to Marketplace</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Image to Video feature added to Marketplace</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sound Studio feature added to Marketplace</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Photo Studio feature added to Marketplace</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Wordpress Integration added to Marketplace</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan updated to support activated extension features</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Subscription plan manual adding issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Chat Image feature credit balance view issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Photo Studio feature credit balance view issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">SD on AI Image issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Extension install issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Theme install via admin panel issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Sonic and Classic v.1.1 themes fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.4</span> - <span class="fs-14 font-weight-semibold">10.11.2024</span>
					</div> 
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Marketplace feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Product Photo feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Flux AI feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Custom credit system for image models added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sonic theme updated with v1.1</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Classic theme updated with v1.1</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Modern theme updated with v1.1</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">(Admin) Custom Chat Assistant model alignment improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">(User) Custom Chat Assistant model alignment improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Default model change for AI Vision/Web/File improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Bank transfer status change email notification send to user now</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat output formatting improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Writer output formatting improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Plagiarism Check and Content Detector added to Marketplace</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Themes updating mechanism improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Article Wizard support for Storj, GCS, Dropbox for image results added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Images feature can show all image models of the activated vendors now</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Image crediting unified and can be controller per image model now</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Balance line shows latest models during model select fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Invoice empty balance fields removed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Bank transfer confirmation upload email notification sending to user fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Plagiarism Check results issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Ai Content Detector results issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Adding credits manually issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Gemini pro streaming issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Speech to Text issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">o1-preview/o1-mini text generation issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.3</span> - <span class="fs-14 font-weight-semibold">03.10.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">OpenAI o1-preview and o1-mini models added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Modern Frontend Theme added (Paid)</span></li>		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Show purchased themes during fresh install improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Credits adding via manual subscription plan assignment improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Frontend clients list updatable via admin panel now</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Frontend new features list improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom pages logo view fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Frontend registration pages logo view fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Favicon view fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Top template nav dropdown menu fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Claude Sonnet 3.5 streaming text issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Proper model name display issue fixed on chats</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Offline payment invoice logo view issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.2</span> - <span class="fs-14 font-weight-semibold">11.09.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>								
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Logo svg/webp/jpg format support added</span></li>		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Image to Video uploaded image dimensions verification improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Frontend/backend logos can be different now</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Frontend responsiveness improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Frontend plan redirection issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Frontend templates redirection issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Logo change caching delay issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Referral payout metric double counting fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 6.1</span> - <span class="fs-14 font-weight-semibold">09.09.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Themes feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Classic Dashboard Theme added (Paid)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Dark Dashboard Theme added (Paid)</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Create new Frontend Pages via admin panel feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Frontend SEO Manager feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Frontend Section Manager feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Advanced custom header and footer feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">GPT 4o mini added to custom template creation page</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Plagiarism Check improvements</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Content Detector improvements</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Homepage responsiveness and performance improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Overall Application performance improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Admin Dashboard performance improved for GA4</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Active model is used now for AI Assistant features</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Admin Panel - Frontend Settings page updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">SEO improvements for frontend and login pages improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Frontend landing page overall backend structure updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">GPT 4o mini added to custom assistant page in admin panel</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">GPT 4o mini added to AI Code feature</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">GPT 4o mini added to Set Defaults page in user profile</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Updating contact us page via admin panel</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Referral banner removed from user dashboard when it is disabled</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Active model is used now for image processing in AI Chat features</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Documents AI Assistant editing issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Documents saving issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Missing translation words added</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Admin panel latest transaction redirection issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Disabled customer support removes it from user dashboard now</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.9</span> - <span class="fs-14 font-weight-semibold">04.08.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Advanced Finance Analytics Dashboard (Extension)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Advanced SaaS Finance and Status Reporting via Email (Extension)</span></li>										 					  						 					  						 					 					  						 					  						 							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Dynamic cost and profit margin calculator for Subscription Plans (Extension)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Dynamic cost and profit margin calculator for Prepaid Plans (Extension)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">SD 3 Large, SD 3 Medium, SD 3 Large Turbo models added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Photo Studio Same Style Image feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Added GPT 4o mini to Set Defaults page</span></li>							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Update AI vendor costs via AI settings page</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">View Credits modal updated with latest models</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Removed deprecated SD sd-xl-1024-v0-9 and sd-xl-beta-v2-2-2 models</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Added GPT4o mini to Fine Tuning feature</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Added GPT4o mini to Chat Assistant feature</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.8.1</span> - <span class="fs-14 font-weight-semibold">30.07.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>											 					  						 					  						 					 					  						 					  						 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">New Registration GPT 4o mini credits assignment fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Access to AI RSS for free tier users fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Access to AI Youtube for free tier users fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.8</span> - <span class="fs-14 font-weight-semibold">27.07.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>											 					  						 					  						 					 					  						 					  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Youtube feature</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI RSS feature</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">GPT 4o mini model added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Photo Studio credit control system added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Settings page updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plans updated with new features</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Prepaid plans updated with new feature</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Email template fix for word count field on manual credit adding</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Missing translation words added</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Home pages access issues for some customers fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Language strings saving issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.7</span> - <span class="fs-14 font-weight-semibold">15.07.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>											 					  						 					  						 					 					  						 					  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Language Manager via admin panel</span></li> 				 					  						 					  						 					 					  						 					  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">RTL feature</span></li> 	 					  	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Brand voice editing updated</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Chat assistant logo remains during update</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Speech to Text mp4 and other format processing issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.6</span> - <span class="fs-14 font-weight-semibold">01.07.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>											 					  						 					  						 					 					  						 					  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion Ultra added</span></li> 					 					  						 					  						 					 					  						 					  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Anthropic Claude 3.5 Sonnet added</span></li> 						 					  						 					  						 					 					  						 					  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom Chat Assistant custom category alignment improved</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom Chat Assistant view in the groups improved</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13"></span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.5</span> - <span class="fs-14 font-weight-semibold">29.06.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>						
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Photo Studio (Extension)</span></li> 						 					  						 					  						 					 					  						 					  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User dashboard page updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plans updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">API user registration issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">API admin manually user creation issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.4.1</span> - <span class="fs-14 font-weight-semibold">23.06.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>						
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">New user dashboard view issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.4</span> - <span class="fs-14 font-weight-semibold">23.06.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>						
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">User Panel: New User Dashboard Page</span></li> 						 					  						 					  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Admin Panel: Advanced Admin Dashboard page</span></li>  						 					  						 					  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Google Analytics Dashboards added</span></li>  					 					  						 					  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Admin cannot see custom templates created by users now</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Security improvements</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Document editing saving issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Favorite chats visibility issue on the dashboard fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">RestAPI namespace fix for Authenticate Routes</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.3.1</span> - <span class="fs-14 font-weight-semibold">06.06.2024</span>
					</div>
					<div class="changelog-description mt-6"> 
						<ul>						  						 					  						 					  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User list table performance improved</span></li>						  						 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">RestAPI laravel passport installation issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom templates created by users are removed from the homepage</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Menu buttons fixed at privacy & terms pages at homepage</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Personal API visibility on My Accounts page improved</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.3</span> - <span class="fs-14 font-weight-semibold">02.06.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>						  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">RestAPI added</span></li>  					  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User profile page credits view updated</span></li>						  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat credit view improved</span></li>						  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Chat/Template credit dynamically changed based on selected models</span></li>						  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Frontend pricing plan tables updated</span></li>						  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Default application model (GPT 3.5 Turbo) is removed</span></li>						  						 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Fine Tune creation issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Bank Transfer approval issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">SD 3 Turbo minor issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">User Custom Chat Assistant files view page issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom Chat setting as favorite issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom Chat category visibility improved</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.2</span> - <span class="fs-14 font-weight-semibold">17.05.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>						  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Push to Wordpress integration feature added (Extension)</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">GPT 4o added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Personal API feature for Anthropic Claude 3 option added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Personal API feature for Google Gemini Pro option added</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscriptions plan updated with new features</span></li>						  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Finance controller updated</span></li>						  						 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Gemini chatbot streaming response issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Dark mode styles improved</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Paddle subscription payment recording issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.1</span> - <span class="fs-14 font-weight-semibold">05.05.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>	 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Openai Custom Chatbot Assistant v2 with native vector search capabilities added</span></li> 						  						 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Adding files for custom chatbot assistant v2 at run time option added</span></li> 						  						 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Admin user view details page updated</span></li> 	 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Text streaming for Chatbot Assistant updated</span></li> 	 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User default model setting updated with Gemini Pro model</span></li>  	 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Add missing words to the translation</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 5.0</span> - <span class="fs-14 font-weight-semibold">02.05.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>	 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Google Gemini Pro added</span></li> 							 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion 3.0 (SD3) / Stable Diffusion 3.0 Turbo (SD3 Turbo) / Stable Image Core (Core) models added</span></li> 							 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ai Video updated to SD v1.1 version</span></li> 	 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan options updated</span></li> 	 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Team member adding issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Rewrite credit calculation issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Vision processing issue fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom template processing issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.9.2</span> - <span class="fs-14 font-weight-semibold">29.04.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>		 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User setting default model option added</span></li> 											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Subscription plan deletion feature fixed</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Newsletter email update issue fixed</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Newsletter email deletion issue fixed</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Credit calculation issue fixed</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Offline payment checkout issue fixed</span></li>	
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.9.1</span> - <span class="fs-14 font-weight-semibold">27.04.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>		 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Matching default model with the model list in chats updated</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Matching default model with the model list in templates updated</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Webhooks updated</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Bank transfer approval issue fixed</span></li>										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Manual subscription assignment issue fixed</span></li> 																				
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">File chat view issue fixed</span></li> 																					
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Web chat view issue fixed</span></li>											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Vision view issue fixed</span></li>											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Article Wizard view issue fixed</span></li>											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Prepaid credits display issue fixed</span></li>											

						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.9</span> - <span class="fs-14 font-weight-semibold">26.04.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>		 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Claude 3 added (Anthropic)</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Create & Train Chatbots by Admin via native OpenAI Assistant feature added</span></li> 							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Custom Models for Chat Bots feature added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Custom Models for Chat Bots feature added</span></li>  							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Direct subscription upgrade feature added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Image prompts feature added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Setting individual fixed model per template option added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Setting individual fixed model per chatbot option added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Added support for new gpt-4-turbo-2024-04-09 model</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Brand Voices for AI Chat feature added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Advanced credit calculation system based on models added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Bulk credits renewal feature for subscribers feature added</span></li>  
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription during registration process mechanism improved</span></li> 	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription cancellation mechanism improved</span></li> 	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Backend Subscriptions page improved</span></li> 																			
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Email notification for manually created users added</span></li> 																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Email notification for manually adding credits added</span></li> 																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Email notification for uploaded payment confirmation option added</span></li> 																			
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Frontend AI Tools Section adding option updated</span></li> 																																																				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User can change AI models dynamically during text generation tasks</span></li> 																																																			
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User email opt in/out option added</span></li> 																																																				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Newsletter unsubscribe option added in emails </span></li> 																																																				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Filters for sending mass email newsletter are improved </span></li> 																																																				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Prepaid plans can set credits for different models </span></li> 																																																				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Prepaid plans details view page updated </span></li> 																																																				
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat layout improvements </span></li> 																																																																																																								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Vision enhancements </span></li> 																																																																																																								
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI File Chat enhancements </span></li> 																																																																																																							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Web Chat enhancements </span></li> 																																																																																																								
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Free tier second time activation issue fixed after registration</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Missing translation words are added in en.json language file</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">File and Document deletion period not saving issues fixed</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Fixed visibility of other custom templates on the top menu bar</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Clouflare R2 view results issue fixed</span></li> 
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.8</span> - <span class="fs-14 font-weight-semibold">08.04.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>		 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Subscribe upon first registration feature added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Newsletter feature added</span></li>  
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">ReTrain/Improve existing Voice Clone feature added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Email Templates features added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Admin notifications for payment transactions feature added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Bank Transfer payment confirmation upload option added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion XL 2.2.2 Beta added</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Performance and loadtime improvements</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User Custom Chat Assistants personal API key usage improved</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User Custom Templates personal API key usage improved</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Voice clone deletion option added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Updated and improved email verification system</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User notifications for payment transactions feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Payment Checkout page updated</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plans page updated</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Support ticket email notification system improved</span></li> 
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Bank transfer dalle/sd image approval issue fixed</span></li>										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Team member creation issue fixed</span></li> 																				
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Prepaid plan dalle/sd image credit adding issue fixed</span></li> 																					
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Mobile top menu issue on Android devices fixed</span></li>											

						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.7</span> - <span class="fs-14 font-weight-semibold">24.03.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>			
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Cloudflare R2 cloud storage feature added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Internet access to Templates feature added</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Video is set to use only SD image credits</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Prepaid plans support for separate Dalle and SD images added</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ai Wizard and Chat Image updated to support proper credits based on Dalle and SD engine usage</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																													
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Support for gpt-4-0125-preview model was added</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Smart editor dark mode css issue fixed</span></li>																						
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom AI Assistant - category list corrected to show Chat Categories</span></li>											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Invoice total due value fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">License deactivation issue fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Elevenlabs large text synthesize timing out issue fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom Template creation for Users/Subscribers issue fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom Chats setting as favorite issue fixed</span></li>											

						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.6</span> - <span class="fs-14 font-weight-semibold">17.03.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>			
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AWS Amazon Polly Text to Speech feature added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Include any TTS vendor combination per subscription plan</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Brand Voice feature added</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Image/Video/Voiceover result storage duration limits can be defined in the subscription plan</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Document results (generated content) storage duration limits can be defined in the subscription plan</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plans updated to support new features</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Free tier AI Voiceover vendors access is controller via admin panel now</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Set separate image credits for Dalle and SD images via subscription plan and for free tier users option added</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat listen support for personal API keys improved</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Vision support for personal API keys improved</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Plagiarism API key saving issue fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Showing disabled prompts issue fixed</span></li>												

						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.5.1</span> - <span class="fs-14 font-weight-semibold">08.03.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																								
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Vision upload button hidden when Chat Image disabled issue fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Team member feature visibility issue fix</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom templates enabling/disabling issue fix</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom chat issue fix</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Login/Registration dark mode css issues fix</span></li>												

						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.5</span> - <span class="fs-14 font-weight-semibold">06.03.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">User personal custom AI Chat Bot creation feature added (Openai Assistant)</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">User personal custom Template creation feature added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">User personal AI Chat bot training file support (c, cpp, docx, html, java, md, php, pptx, py, rb, tex, css, js, gif, tar, ts, xlsx, xml, zip, pdf, csv, txt, json) option added</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Sound Studio adding default background audio tracks by admin updated</span></li> 																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Set Dark Mode as default option added (at General Settings page)</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User dark mode state retained as persistent</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Image to Video table refresh option added</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Clone Voice limits are controllable per plan now</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">New features are added to the subscription plan</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Security improvements</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																															
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Copy & Paste buttons added to AI Assistant feature</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																				
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Free tier access to AI Plagiarism and Detector features fixed</span></li>												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Missing translation words were improved</span></li>												
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.4</span> - <span class="fs-14 font-weight-semibold">25.02.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Plagiarism Checker added (Extension)</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Content Detector added (Extension)</span></li> 
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan updated to include new features</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																			
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Voice clone visibility for other users fixed</span></li>							
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Serper API key saving issue fixed</span></li>							
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.3</span> - <span class="fs-14 font-weight-semibold">20.02.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan update to include new features</span></li>	
							<li><span class="version-fix mr-2">Update</span> <span class="text-muted fs-13">AI Image to Video cloud storage improved</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																			
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Voiceover issue fix for Elevenlabs option</span></li>							
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Image to Video button text improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Image engine name properly assigned in metadata</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.2</span> - <span class="fs-14 font-weight-semibold">18.02.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>							
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Voice Cloning feature added (Extension)</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Sound Studio feature added (Extension)</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Image to Video feature added (Extension)</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI File Chat feature added</span></li>	
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">MS Word files support for AI File Chat feature added</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Smart Editor Mobile responsiveness improved</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat PDF/CSV merged into AI File Chat feature</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat PDF/CSV/Web page refresh issue improved</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Davinci Settings page updated</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Extended License features section added in Davinci Settings</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">License page updated</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																										
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Smart Editor missing translation words added</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																										
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Security improvements</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Assistant language response issue fixed on all AI features</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.1</span> - <span class="fs-14 font-weight-semibold">07.02.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Smart Editor Mobile responsiveness improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Smart Editor Search field added for templates dropdown menu</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom Template view issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Export in MS Word issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Smart Editor Template details view issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Storj Cloud for Voiceover & STT results fixed (new installations only)</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Dropbox for Voiceover & STT results fixed (new installations only)</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 4.0</span> - <span class="fs-14 font-weight-semibold">05.02.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Smart Editor feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI ReWriter feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Assistant for Original Templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Assistant for Custom Templates added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plans updated to included Start Editor & AI ReWriter</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Orignal Templates view updated</span></li>																																																																																																																																																																																																																																																																																																																																																																																																													
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom Templates view updated</span></li>																																																																																																																																																																																																																																																																																																																																																																																																													
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat Web css issue fix</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.9</span> - <span class="fs-14 font-weight-semibold">29.01.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Fine Tune Models feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Google Cloud Storage buckets feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Storj Cloud storage feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Dropbox storage feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Social login session handling improved</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Social login password randomized</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">OpenAI model is set based on user chat model settings for AI Chat Web/PDF/CSV features</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Embedding version can be selected for AI Chat Web/PDF/CSV features</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Personal API key support added for AI Chat Web/PDF/CSV features</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																					
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Balance check improved for AI Chat Web/PDF/CSV features</span></li>																																																																																																																																																																																																																																																																																																																																																																																																																																																																																															
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.8</span> - <span class="fs-14 font-weight-semibold">26.01.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat PDF feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat CSV feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Web Chat feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom Templates support for Select Dropdown fields added</span></li>																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom Templates support for Checkbox fields added</span></li>																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom Templates support for Radio Button fields added</span></li>																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Custom Templates required field option added</span></li>																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan is updated to include new features</span></li>																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan file size limits are added for Chat PDF & CSV service</span></li>																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Removed DB backup feature (new installations only)</span></li>																																																																																																																																																																																																																																																																																																																																																																																																												
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AWS region names mismatch minor issue fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Referral admin panel enable/disable issue fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Counting character credits for AI Chat TTS feature improved</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Counting image credits for Chat Image improved</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat minor issue with enter button fixed</span></li>																																										
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.7</span> - <span class="fs-14 font-weight-semibold">15.01.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Internet access to AI Chat option added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Chat Image Feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Listen to AI Chat response feature added</span></li> 
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Create your custom referral code feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Affiliate Program page updated</span></li>																																																																																																																																																																																																																																																																																																																																																																		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Referred number of users are visible now</span></li>																																																																																																																																																																																																																																																																																																																																																																		
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Stripe webhook updated</span></li>																																																																																																																																																																																																																																																																																																																																																																	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan updated to include new features</span></li>																																																																																																																																																																																																																																																																																																																																																																	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Removed deprecated OpenAI models</span></li>																																																																																																																																																																																																																																																																																																																																																																	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">CSS styles for document results fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Renaming newly created chat conversation issue fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Images minor SD v1.6 bug issue fixed</span></li>																																										
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.6</span> - <span class="fs-14 font-weight-semibold">01.01.2024</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">ElevenLabs Text to Speech Feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Vision availability for all users groups updated</span></li>																																																																																																																																																																																																																																																																																																																									
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Article Wizard availability for all users groups updated</span></li>																																																																																																																																																													
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Yookassa monthly/yearly subscription plan renewal updated</span></li>																																																																																																																																																													
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Yookassa cancellation refund option removed</span></li>																																																																																																																																																													
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Displaying first generated AI Image without refresh is fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Reassigning a same plan via admin issue fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Setting default language for new registrations in davinci settings page fixed</span></li>																																										
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.5</span> - <span class="fs-14 font-weight-semibold">24.12.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Vision feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chats support for image processing feature added</span></li>																																																																															
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chats generated tables view updated</span></li>																																																																															
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chats mobile view updated</span></li>																																																																															
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Vision option added to subscription plans</span></li>																																																																															
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Wizard image counting and balance protection fixed</span></li>																																										
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.4.1</span> - <span class="fs-14 font-weight-semibold">20.12.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Correct language reponse in AI Article Wizard updated</span></li>																																										
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Image vendor can be selected or completely turned off for AI Article Wizard</span></li>																																										
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Advanced options for AI Wizard keeps the changes till the end</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Article Wizard issue with GPT 4 Turbo fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Document result view show images for AI Article Wizard results fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Images view issue fix (new installations only)</span></li>																																										
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.4</span> - <span class="fs-14 font-weight-semibold">18.12.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Multistep AI Article Wizard added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Requirement for symlink removed (new installations only)</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Referral supported for social media logins and registrations</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Registered via emails initially can now login with their social media accounts as well</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Removed support for deprecated SD models: stable-diffusion-v1-5, stable-diffusion-512-v2-1, stable-diffusion-768-v2-1</span></li>	
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Added support for new SD model: stable-diffusion-v1-6</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Database password # symbol support fixed (new installations only)</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Login page accessibility for logged user fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Registration page accessibility for logged user fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Images SD Negative prompt feature issue fixed</span></li>																																										
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Images negative balance for Unlimited sign issue fixed</span></li>																																										
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.3</span> - <span class="fs-14 font-weight-semibold">22.11.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion - Image to Image feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion - Image Upscaling feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion - Image Inpainting feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion - Multi Prompting feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion - Image Strength option in settings added for Image to Image feature</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">OpenAI - Image Inpainting feature added (Dalle 2 Only)</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">OpenAI - Image Variations feature added (Dalle 2 Only)</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Free plan pricing has changed from $0 value to Free sign</span></li>	
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Voiceover properly handle unlimited characters fixed</span></li>																					
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Frontend - Unlimited credit names are properly displayed</span></li>																					
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Stable Diffusion - Generation Steps limits are improved</span></li>																					
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.2</span> - <span class="fs-14 font-weight-semibold">14.11.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">OpenAI Text to Speech feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">GCP 101 (65 Neural2; 6 Studio; 30 WaveNet) New Voices added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Both Dalle and Stable Diffusion engines can be set in the subscription plans</span></li>											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Image view modal details updated</span></li>											
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.1</span> - <span class="fs-14 font-weight-semibold">09.11.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">GPT 4 Turbo model added for Templates and Chats</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">GPT 4 Turbo with Vision model added for Templates and Chats</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Dalle-3 and Dalle-3 HD models added for AI Images</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Image Engine selection to Subscription Plans added</span></li>											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Removed deprecated model - Ada, Babbage, Curie</span></li>											
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Davinci Settings page updated</span></li>											
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Finance Management - Transactions page table issue fix</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 3.0</span> - <span class="fs-14 font-weight-semibold">06.11.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Add subscription plan manually by admin added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Show hidden plans for select users feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Free trial days feature added for subscription plans</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Update user general and prepaid balances (increase/decrease)</span></li>						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User list - personal user information view page updated</span></li>						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Total active subscribers are visible for each subscription plan</span></li>						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Users can apply to free trial subscription plan only once</span></li>						
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Free trial subscription process improved, no need to select payment gateways anymore</span></li>						
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Pricing table feature repetition fix</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 2.9</span> - <span class="fs-14 font-weight-semibold">02.11.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat template groups added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat prompts feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat (shift + enter) replaced for new line</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat mobile responsiveness improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat info banners updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat generated url links are clickable now</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat code view updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Contact us reCaptcha v3 feature updated</span></li>							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Private API key enforcement added when enabled</span></li>							
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Unlimited words support for free tier user added</span></li>							
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Subscription plans unlimited validation fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Contact us disabling via admin panel fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Voiceover issue fix</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Documents - Workbooks change issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Youtube social settings missing fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat new conversation fix</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat cyrillic words display fix</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Image creation date fix</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 2.8</span> - <span class="fs-14 font-weight-semibold">16.10.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Iyzico payment gateway added for prepaid/lifetime plans</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Unlimited words/images/characters/minutes option added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Use your own personal Openai API key feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Use your own Stable Diffusion API key feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">About Us page added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Contact Us page added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Frontend - update How it Works section via admin panel</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Frontend - update AI Tools section via admin panel</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Frontend - update Features section via admin panel</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Macedonian language added to templates</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat conversation search option added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat assistant info line added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat voice input added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat code format highlighter</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat stop button added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Macedonian language added to templates</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Language selection added to top at frontend</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Language selection added to Login and Registration pages</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Update page view updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">All missing translation words added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">All disabled AI features are disabled everywhere</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Personal API features added to the subscription plans</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Stripe complete redesign with official PHP SDK</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Table sorting for dates updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Home page blog posts listed based on created date</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Twitter logos updates, youtube social media link added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat input field changed to textarea to support larger text inputs</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Frontend blogs posts order changed to descending order</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">CSS issue in the AI Images fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Stripe for India issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Cron task credit renewal issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Download button in the transcribe result page issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-5">
						<span class="version-name">{{ __('Version') }} 2.7</span> - <span class="fs-14 font-weight-semibold">04.09.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Image creation page redesigned</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Document view images page redesigned</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Negative prompt added for Stable Diffusion</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">CFG Scale (prompt strength) added for Stable Diffusion</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Diffusion Steps added for Stable Diffusion</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Independent models for AI Chat at subscription plan creation added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">All spelling errors fixed</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Count Chinese and Japanese words improved</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Performance of all select dropdowns improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Promocode for lifetime checkout page missing fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Twitter login improved</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">
 
				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 2.6</span> - <span class="fs-14 font-weight-semibold">23.08.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Frontend layout redesigned</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Image SDXL v1.0 support added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Set default template language via user profile page added</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Paddle webhook improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Yookassa issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 2.5</span> - <span class="fs-14 font-weight-semibold">24.07.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Paddle payment gateway added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Yookassa payment gateway added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Team members feature updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Templates max word length accuracy updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Paypal webhook updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Ai Voiceover css mobile alignments improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">User dashboard favorite templates & chats views fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 2.4</span> - <span class="fs-14 font-weight-semibold">16.07.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Team Member feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Flutter payment gateway added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Midtrans payment added</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Template user notifications fix</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Youtube tag generator template fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Instagram hashtag generator template fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Meta description generator template fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Dashboard setting templates as favority issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Affiliate feature not visible in the side menu when disabled improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Voiceover delete synthesize result fix</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 2.3</span> - <span class="fs-14 font-weight-semibold">02.07.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Template text streaming feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable diffusion SDXL v0.9 engine added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">OpenAI GPT 3.5 Turbo 16K model added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">New tag added for original and custom templates</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">15 New Languages: Tamil (Malaysia), Persian (Iran), English (UK), Slovak, Latvian, Albanian, Filipino, Khmer (Cambodia), Bangla, Bengali (India), Welsh, Catalan, Serbian, Maltese, Irish added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Text Extender templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Newsletter Generator templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Ad Headlines templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Brand Names templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Promise–Picture–Proof–Push (PPPP) Framework templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Before–After–Bridge (BAB) Framework templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Attention-Interest-Desire-Action (AIDA) Framework templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Brand/Product Press Release templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Company Press Release templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Clickbait Titles templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Terms and Conditions templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Privacy Policy templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Dictionary templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Amazon Product Features templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Tone Changer templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">App and SMS Notifications templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">LinkedIn Ad Descriptions templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">LinkedIn Ad Headlines templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">TitTok Video Scripts templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Twitter Tweets templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Product Characteristics templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Product Comparisons templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Selling Product Titles templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Product Benefits templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Email Subject Lines templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Company Bio templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">LinkedIn Posts templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Business Ideas templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Song Lyrics templates added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Rewrite with Keywords templates added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Voiceover characters view feature updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User credits view updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Side menu panel updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Ai Voiceover result view audio play bar fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Saving stable diffusion main api key issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Dark mode payment tables css issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 2.2</span> - <span class="fs-14 font-weight-semibold">04.06.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Multi API keys for Openai added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Multi API keys for Stable Diffusion added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Live Chat (tawk.to) feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Sensitive words filtering feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat search feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Languages list scrolling updated</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 2.1</span> - <span class="fs-14 font-weight-semibold">01.06.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Dark mode added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Template search feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Custom category description added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Templates page updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Ai Chat first response text missing fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 2.0</span> - <span class="fs-14 font-weight-semibold">28.05.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Stable Diffusion for AI Images added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Image Lightning style added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Image Artist selection added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Export user email list as csv/excel/pdf added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User dashboard updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Favorite AI Chats added to dashboard</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Promocodes updated, now supports 100% discounts</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Custom chat avatar issues fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">CRON task daily value fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">User profile subscription status text issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.9</span> - <span class="fs-14 font-weight-semibold">18.05.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Template package system updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ai Chat bot package system updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Ai Chat bot css styling issues fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Ai Speech to Text file type blocking issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.8</span> - <span class="fs-14 font-weight-semibold">17.05.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">42 AI Chat Bots added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Unlimited Custom AI Chat Bots creation feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Premium/Standard/Free tier categories for templates and chats added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ai Code feature updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ai Speech to Text feature updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">CRON task for yearly/lifetime credit processing updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Add default credits to manually created users updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Enable/Disabling various features via subscription plan updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Referral system BankTransfer payment fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Referral system payout buttons fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Speech to Text AWS and Wasabi storage issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.7</span> - <span class="fs-14 font-weight-semibold">23.04.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Lifetime plans added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Promocodes for Prepaid Plans and Lifetime Plans added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">User Account deletion feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Voiceover symlink dependency removed</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">User Subscription table is separated from Transactions table in menu</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Voiceover AWS storage issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat is visible to user and subscribers</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Speech to Text storage issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Speech to Text minutes balance counter issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">BankTransfer approval issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.6</span> - <span class="fs-14 font-weight-semibold">19.04.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Davinci Settings: STT controls added and enabling Ai Code and AI Voiceover updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Adding characters and minutes via admin panel added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Webhooks support for characters and minutes updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Chat download buttons added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Free characters and minutes are added during registration updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Default Language and Voice is added to new user upon registration updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Registration server error issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat and AI Voiceover are visible to users as well fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">AI Chat stop if user runs out of characters fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Characters and Minutes are visible in Prepaid plans fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.5</span> - <span class="fs-14 font-weight-semibold">17.04.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Chat system added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Speech to Text with OpenAI Whisper added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Voiceover powered by Azure and GCP added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Ability to merge up to 20 AI voices for synthesize task option added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Generate up to 100K synthesize task option added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Voices customization option added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">All documents section updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscriptions plan creation updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Razorpay webhook issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Paypal prepaid issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Bank Transfer referral - payment cancellation issue fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.4</span> - <span class="fs-14 font-weight-semibold">26.03.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">GPT 4 (8K/32K) model added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Support for Thai, Bulgarian, Lithuanian, Ukrainian added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Amason S3 storage for AI Images added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Wasabi Cloud storage for AI Images added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">AI Code feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Art Styles feature for AI Images added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Art  Medium feature for AI Images added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Art Mood feature for AI Images added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Custom category creation feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Custom template editor feature added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Language file en.json with missing words updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Subscription plan cancellation keeps credits until depleted or renewed</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Login/Registration page copyright info removed</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Login page default credentials removed</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ability to removing .00 in pricing added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Stripe webhook controller updatedStripe webhook controller updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Paypal webhook controller updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Coinbase webhook controller updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Razorpay webhook controller updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Paystack webhook controller updated</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Razorpay prepaid plan payment issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Google 2FA issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Referral payment system tracks payment commissions correctly now</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Copy referral link button fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Recover password page responsiveness fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">User profile shows correct subscription between monthly or yearly now</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.3</span> - <span class="fs-14 font-weight-semibold">09.03.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Unlimited custom templates creation feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">ChatGPT 3.5 Turbo model added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Academic Essay template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Welcome Email template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Cold Email template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Follow up Email template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Creative Stories template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Grammar Checker template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Summarize for 2nd Grader template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Video Scripts template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Amazon Product Description template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Templates filter feature added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Export in Text format added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Result and Template view pages updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Auto save feature for text results removed</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">AI Image results table updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">All Documents table updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Ability to removing .00 in pricing added</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">User default workbook creation issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Workbooks page responsiveness improved</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Login page default credentials removed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Login page responsiveness improved </span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Support email link fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Referral email register link in the email fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.2</span> - <span class="fs-14 font-weight-semibold">21.02.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Support for Portuguese(Brazil), Slovenian, Vietnamese, Swahili, Romanian added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Problem-Agitate-Solution template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Video Descriptions template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Video Titles template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Youtube Tags Generator template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Instagram Captions template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Instagram Hashtags Generator template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Social Media Post (Personal) template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Social Media Post (Business) template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Facebook Headlines template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Google Ads Headlines template added</span></li>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Google Ads Description template added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Templates styling updated</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Credit view within template and image generation added</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Translation file has been updated with missing words</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Stripe 3D payment issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Bank Transfer payment issue fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Profile view monthly/yearly mix fixed</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Missing icons on some hostings fixed </span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Blogs view excerpt updated, hide html tags fixed</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.1</span> - <span class="fs-14 font-weight-semibold">16.02.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Backend side menu user UX update</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Redirect users to Templates page</span></li>
							<li><span class="version-update mr-2">Update</span> <span class="text-muted fs-13">Max text result number increase to 4K in Davinci settings</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Workbooks view table fix</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Subscribe button in user dashboard route fixSubscribe button in user dashboard route fix</span></li>
							<li><span class="version-fix mr-2">Fix</span> <span class="text-muted fs-13">Default image credits assignment fix for new registered users</span></li>
						</ul>
					</div>
				</div>

				<hr class="mt-6">

				<div class="changelog">
					<div class="changelog-version mt-6">
						<span class="version-name">{{ __('Version') }} 1.0</span> - <span class="fs-14 font-weight-semibold">13.02.2023</span>
					</div>
					<div class="changelog-description mt-6">
						<ul>
							<li><span class="version-new mr-2">New</span> <span class="text-muted fs-13">Initial Release</span></li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection

@section('js')
	<script src="{{theme_url('js/upgrade.js')}}"></script>
@endsection

@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content')					
	<div class="row justify-content-center mt-24">
		<div class="col-sm-12 text-center">
			<h3 class="card-title fs-20 mb-3 super-strong"><i class="fa-solid fa-microchip-ai mr-2 text-primary"></i>{{ __('AI Settings') }}</h3>
			<h6 class="mb-6 fs-12 text-muted">{{ __('Control all AI settings from one place') }}</h6>
		</div>

		<div class="col-lg-8 col-md-10 col-sm-12 mb-5">
			<div class="templates-nav-menu">
				<div class="template-nav-menu-inner">
					<ul class="nav nav-tabs" id="myTab" role="tablist" style="padding: 3px">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">{{ __('General AI Settings') }}</button>
						</li>
						<li class="nav-item category-check" role="presentation">
							<button class="nav-link" id="api-tab" data-bs-toggle="tab" data-bs-target="#api" type="button" role="tab" aria-controls="api" aria-selected="false">{{ __('AI API Keys') }}</button>
						</li>
						<li class="nav-item category-check" role="presentation">
							<button class="nav-link" id="extended-tab" data-bs-toggle="tab" data-bs-target="#extended" type="button" role="tab" aria-controls="extended" aria-selected="false">{{ __('Extensions') }}</button>
						</li>	
						<li class="nav-item category-check" role="presentation">
							<button class="nav-link" id="trial-tab" data-bs-toggle="tab" data-bs-target="#trial" type="button" role="tab" aria-controls="trial" aria-selected="false">{{ __('Free Trial Features') }}</button>
						</li>				
					</ul>
				</div>
			</div>
		</div>

		<div class="col-lg-10 col-md-12 col-sm-12">
			<div class="card border-0">
				<div class="card-body p-5">				
					<div class="tab-content" id="myTabContent">

						<div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
							<form id="general-features-form" action="{{ route('admin.davinci.configs.store') }}" method="POST" enctype="multipart/form-data">
								@csrf

								<div class="row">							

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">	
											<h6>{{ __('Default AI Model') }} <span class="text-muted">({{ __('For Admin Group') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="default-model-admin" name="default-model-admin" class="form-select" data-placeholder="{{ __('Select Default Model') }}:">			
												<option value="gpt-3.5-turbo-0125" @if ( config('settings.default_model_admin')  == 'gpt-3.5-turbo-0125') selected @endif>{{ __('GPT 3.5 Turbo') }}</option>												
												<option value="gpt-4" @if ( config('settings.default_model_admin')  == 'gpt-4') selected @endif>{{ __('GPT 4') }}</option>
												<option value="gpt-4o" @if ( config('settings.default_model_admin')  == 'gpt-4o') selected @endif>{{ __('GPT 4o') }}</option>
												<option value="gpt-4o-mini" @if ( config('settings.default_model_admin')  == 'gpt-4o-mini') selected @endif>{{ __('GPT 4o mini') }}</option>
												<option value="gpt-4o-search-preview" @if ( config('settings.default_model_admin')  == 'gpt-4o-search-preview') selected @endif>{{ __('GPT 4o Search Preview') }}</option>
												<option value="gpt-4o-mini-search-preview" @if ( config('settings.default_model_admin')  == 'gpt-4o-mini-search-preview') selected @endif>{{ __('GPT 4o mini Search Preview') }}</option>
												<option value="gpt-4-0125-preview" @if ( config('settings.default_model_admin')  == 'gpt-4-0125-preview') selected @endif>{{ __('GPT 4 Turbo') }}</option>
												<option value="gpt-4.5-preview" @if ( config('settings.default_model_admin')  == 'gpt-4.5-preview') selected @endif>{{ __('GPT 4.5') }}</option>
												<option value="gpt-4.1" @if ( config('settings.default_model_admin')  == 'gpt-4.1') selected @endif>{{ __('GPT 4.1') }}</option>
												<option value="gpt-4.1-mini" @if ( config('settings.default_model_admin')  == 'gpt-4.1-mini') selected @endif>{{ __('GPT 4.1 mini') }}</option>
												<option value="gpt-4.1-nano" @if ( config('settings.default_model_admin')  == 'gpt-4.1-nano') selected @endif>{{ __('GPT 4.1 nano') }}</option>
												<option value="o1" @if ( config('settings.default_model_admin')  == 'o1') selected @endif>{{ __('o1') }}</option>
												<option value="o1-mini" @if ( config('settings.default_model_admin')  == 'o1-mini') selected @endif>{{ __('o1 mini') }}</option>
												<option value="o3-mini" @if ( config('settings.default_model_admin')  == 'o3-mini') selected @endif>{{ __('o3 mini') }}</option>
												<option value="o3" @if ( config('settings.default_model_admin')  == 'o3') selected @endif>{{ __('o3') }}</option>
												<option value="o4-mini" @if ( config('settings.default_model_admin')  == 'o4-mini') selected @endif>{{ __('o4 mini') }}</option>
												<option value="claude-opus-4-20250514" @if ( config('settings.default_model_admin')  == 'claude-opus-4-20250514') selected @endif>{{ __('Claude 4 Opus') }}</option>
												<option value="claude-sonnet-4-20250514" @if ( config('settings.default_model_admin')  == 'claude-sonnet-4-20250514') selected @endif>{{ __('Claude 4 Sonnet') }}</option>
												<option value="claude-3-opus-20240229" @if ( config('settings.default_model_admin')  == 'claude-3-opus-20240229') selected @endif>{{ __('Claude 3 Opus') }}</option>
												<option value="claude-3-7-sonnet-20250219" @if ( config('settings.default_model_admin')  == 'claude-3-7-sonnet-20250219') selected @endif>{{ __('Claude 3.7 Sonnet') }}</option>
												<option value="claude-3-5-sonnet-20241022" @if ( config('settings.default_model_admin')  == 'claude-3-5-sonnet-20241022') selected @endif>{{ __('Claude 3.5v2 Sonnet') }}</option>
												<option value="claude-3-5-haiku-20241022" @if ( config('settings.default_model_admin')  == 'claude-3-5-haiku-20241022') selected @endif>{{ __('Claude 3.5 Haiku') }}</option>
												<option value="gemini-1.5-pro" @if ( config('settings.default_model_admin')  == 'gemini-1.5-pro') selected @endif>{{ __('Gemini 1.5 Pro') }}</option>
												<option value="gemini-1.5-flash" @if ( config('settings.default_model_admin')  == 'gemini-1.5-flash') selected @endif>{{ __('Gemini 1.5 Flash') }}</option>
												<option value="gemini-2.0-flash" @if ( config('settings.default_model_admin')  == 'gemini-2.0-flash') selected @endif>{{ __('Gemini 2.0 Flash') }}</option>
												<option value="deepseek-chat" @if ( config('settings.default_model_admin')  == 'deepseek-chat') selected @endif>{{ __('DeepSeek V3') }}</option>
												<option value="deepseek-reasoner" @if ( config('settings.default_model_admin')  == 'deepseek-reasoner') selected @endif>{{ __('DeepSeek R1') }}</option>
												<option value="grok-2-1212" @if ( config('settings.default_model_admin')  == 'grok-2-1212') selected @endif>{{ __('Grok 2') }}</option>
												<option value="grok-2-vision-1212" @if ( config('settings.default_model_admin')  == 'grok-2-vision-1212') selected @endif>{{ __('Grok 2 Vision') }}</option>
												@if (App\Services\HelperService::extensionPerplexity())	
													<option value="sonar" @if ( config('settings.default_model_admin')  == 'sonar') selected @endif>{{ __('Perplexity Sonar') }}</option>
													<option value="sonar-pro" @if ( config('settings.default_model_admin')  == 'sonar-pro') selected @endif>{{ __('Perplexity Sonar Pro') }}</option>
													<option value="sonar-reasoning" @if ( config('settings.default_model_admin')  == 'sonar-reasoning') selected @endif>{{ __('Perplexity Sonar Reasoning') }}</option>
													<option value="sonar-reasoning-pro" @if ( config('settings.default_model_admin')  == 'sonar-reasoning-pro') selected @endif>{{ __('Perplexity Sonar Reasoning Pro') }}</option>
												@endif
												@if (App\Services\HelperService::extensionAmazonBedrock())	
													<option value="us.amazon.nova-micro-v1:0" @if ( config('settings.default_model_admin')  == 'us.amazon.nova-micro-v1:0') selected @endif>{{ __('Nova Micro') }}</option>
													<option value="us.amazon.nova-lite-v1:0" @if ( config('settings.default_model_admin')  == 'us.amazon.nova-lite-v1:0') selected @endif>{{ __('Nova Lite') }}</option>
													<option value="us.amazon.nova-pro-v1:0" @if ( config('settings.default_model_admin')  == 'us.amazon.nova-pro-v1:0') selected @endif>{{ __('Nova Pro') }}</option>
												@endif
												@foreach ($models as $model)
													<option value="{{ $model->model }}" @if ( config('settings.default_model_admin')  == $model->model) selected @endif>{{ $model->description }} ({{ __('Fine Tune Model')}})</option>
												@endforeach
											</select>
										</div>								
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">	
											<h6>{{ __('Default Embedding Model') }} <span class="text-muted">({{ __('For All Groups') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="default-embedding-model" name="default-embedding-model" class="form-select">			
												<option value="text-embedding-ada-002" @if ( config('settings.default_embedding_model')  == 'text-embedding-ada-002') selected @endif>{{ __('Embedding V2 Ada') }}</option>
												<option value="text-embedding-3-small" @if ( config('settings.default_embedding_model')  == 'text-embedding-3-small') selected @endif>{{ __('Embedding V3 Small') }}</option>
												<option value="text-embedding-3-large" @if ( config('settings.default_embedding_model')  == 'text-embedding-3-large') selected @endif>{{ __('Embedding V3 Large') }}</option>
											</select>
										</div>								
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">	
											<h6>{{ __('Default Templates Result Language') }} <span class="text-muted">({{ __('For New Registrations Only') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
												<select id="default-language" name="default-language" class="form-select" data-placeholder="{{ __('Select Default Template Language') }}:">	
													@foreach ($languages as $language)
														<option value="{{ $language->language_code }}" data-img="{{ theme_url($language->language_flag) }}" @if (config('settings.default_language') == $language->language_code) selected @endif> {{ $language->language }}</option>
													@endforeach
											</select>
										</div>								
									</div>	

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('Templates Category Access') }} <span class="text-muted">({{ __('For Admin Group') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="templates-admin" name="templates-admin" class="form-select" data-placeholder="{{ __('Set Templates Access') }}">
												<option value="all" @if (config('settings.templates_access_admin') == 'all') selected @endif>{{ __('All Templates') }}</option>
												<option value="free" @if (config('settings.templates_access_admin') == 'free') selected @endif>{{ __('Only Free Templates') }}</option>
												<option value="standard" @if (config('settings.templates_access_admin') == 'standard') selected @endif> {{ __('Up to Standard Templates') }}</option>
												<option value="professional" @if (config('settings.templates_access_admin') == 'professional') selected @endif> {{ __('Up to Professional Templates') }}</option>																																		
												<option value="premium" @if (config('settings.templates_access_admin') == 'premium') selected @endif> {{ __('Up to Premium Templates') }} ({{ __('All') }})</option>																																																																																																									
											</select>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">
											<h6>{{ __('AI Article Wizard Image Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="wizard-image-vendor" name="wizard-image-vendor" class="form-select">
												<option value='none' @if (config('settings.wizard_image_vendor') == 'none') selected @endif>{{ __('Disable Image Generation for AI Article Wizard') }}</option>
												<option value='dall-e-2' @if (config('settings.wizard_image_vendor') == 'dall-e-2') selected @endif> {{ __('Dalle 2') }}</option>																															
												<option value='dall-e-3' @if (config('settings.wizard_image_vendor') == 'dall-e-3') selected @endif> {{ __('Dalle 3') }}</option>																															
												<option value='dall-e-3-hd' @if (config('settings.wizard_image_vendor') == 'dall-e-3-hd') selected @endif> {{ __('Dalle 3 HD') }}</option>																															
												<option value='stable-diffusion-v1-6' @if (config('settings.wizard_image_vendor') == 'stable-diffusion-v1-6') selected @endif> {{ __('Stable Diffusion v1.6') }}</option>																															
												<option value='stable-diffusion-xl-1024-v1-0' @if (config('settings.wizard_image_vendor') == 'stable-diffusion-xl-1024-v1-0') selected @endif> {{ __('Stable Diffusion XL v1.0') }}</option>																															
											</select>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">	
											<h6>{{ __('Maximum Result Length') }} <span class="text-muted">({{ __('In Words') }}) ({{ __('For Admin Group') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('OpenAI has a hard limit based on Token limits for each model. Refer to OpenAI documentation to learn more. As a recommended by OpenAI, max result length is capped at 1500 words.') }}"></i></h6>
											<input type="number" class="form-control @error('max-results-admin') is-danger @enderror" id="max-results-admin" name="max-results-admin" placeholder="Ex: 10" value="{{ config('settings.max_results_limit_admin') }}" required>
											@error('max-results-admin')
												<p class="text-danger">{{ $errors->first('max-results-admin') }}</p>
											@enderror
										</div>								
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">	
											<h6>{{ __('Custom Chats for Users') }} <span class="text-muted">({{ __('For All Groups') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="custom-chats" name="custom-chats" class="form-select">			
												<option value="anyone" @if ( config('settings.custom_chats')  == 'anyone') selected @endif>{{ __('Available to Anyone') }}</option>												
												<option value="subscription" @if ( config('settings.custom_chats')  == 'subscription') selected @endif>{{ __('Available only via Subscription Plan') }}</option>												
											</select>
										</div>								
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">	
											<h6>{{ __('Custom Templates for Users') }} <span class="text-muted">({{ __('For All Groups') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="custom-templates" name="custom-templates" class="form-select">			
												<option value="anyone" @if ( config('settings.custom_templates')  == 'anyone') selected @endif>{{ __('Available to Anyone') }}</option>												
												<option value="subscription" @if ( config('settings.custom_templates')  == 'subscription') selected @endif>{{ __('Available only via Subscription Plan') }}</option>												
											</select>
										</div>								
									</div>

									<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="input-box">	
											<h6>{{ __('Real Time Data Access Engine') }} <span class="text-muted">({{ __('For All Groups') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
											<select id="realtime-engine" name="realtime-engine" class="form-select">			
												<option value="serper" @if ( $settings->realtime_data_engine == 'serper') selected @endif>{{ __('Serper') }}</option>	
												@if (App\Services\HelperService::extensionPerplexity())											
													<option value="perplexity" @if ( $settings->realtime_data_engine == 'perplexity') selected @endif>{{ __('Perplexity') }}</option>	
												@endif											
											</select>
										</div>								
									</div>
								</div>

								<div class="card shadow-0 mb-7">							
									<div class="card-body">
										<div class="row">		

											<h6 class="fs-12 font-weight-bold mb-6 mt-3"><i class="  fa-solid fa-cogs text-info fs-14 mr-2"></i>{{ __('AI Features Control') }} <span class="text-muted">({{ __('For All Groups') }})</span></h6>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Writer Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="writer-feature-user" class="custom-switch-input" @if ($settings->writer_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Article Wizard Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="wizard-feature-user" class="custom-switch-input" @if ($settings->wizard_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('Smart Editor Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="smart-editor-feature-user" class="custom-switch-input" @if ($settings->smart_editor_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI ReWriter Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="rewriter-feature-user" class="custom-switch-input" @if ($settings->rewriter_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Vision Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="vision-feature-user" class="custom-switch-input" @if ($settings->vision_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Vision for AI Chat') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="vision-for-chat-user" class="custom-switch-input" @if ( config('settings.vision_for_chat_feature_user')  == 'allow') checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI File Chat Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="chat-file-feature-user" class="custom-switch-input" @if ($settings->file_chat_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Web Chat Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="chat-web-feature-user" class="custom-switch-input" @if ($settings->web_chat_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Chat Image Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="chat-image-feature-user" class="custom-switch-input" @if ($settings->image_chat_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Code Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="code-feature-user" class="custom-switch-input" @if ($settings->code_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Team Members Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="team-members-feature" class="custom-switch-input" @if ($settings->team_member_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div> 						
											</div>	

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Youtube Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="youtube-feature" class="custom-switch-input" @if ($settings->youtube_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI RSS Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="rss-feature" class="custom-switch-input" @if ($settings->rss_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('Integration Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="integration-feature" class="custom-switch-input" @if ($settings->integration_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Brand Voice Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="brand-voice-feature" class="custom-switch-input" @if ($settings->brand_voice_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div> 						
											</div>	
										</div>
									</div>
								</div>

								<div class="card shadow-0 mb-7">							
									<div class="card-body">

										<h6 class="fs-12 font-weight-bold mb-4"><i class="  fa-solid fa-message-captions text-info fs-14 mr-2"></i>{{ __('AI Chat Settings') }} <span class="text-muted">({{ __('For All Groups') }})</span></h6>

										<div class="row">

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Chat Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="chat-feature-user" class="custom-switch-input" @if ($settings->chat_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('AI Chat Default Voice') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="chat-default-voice" name="chat-default-voice" class="form-select">			
														<option value="alloy" @if ( config('settings.chat_default_voice')  == 'alloy') selected @endif>{{ __('Alloy') }} ({{ __('Male') }})</option>
														<option value="echo" @if ( config('settings.chat_default_voice')  == 'echo') selected @endif>{{ __('Echo') }} ({{ __('Male') }})</option>
														<option value="fable" @if ( config('settings.chat_default_voice')  == 'fable') selected @endif>{{ __('Fable') }} ({{ __('Male') }})</option>
														<option value="onyx" @if ( config('settings.chat_default_voice')  == 'onyx') selected @endif>{{ __('Onyx') }} ({{ __('Male') }})</option>
														<option value="nova" @if ( config('settings.chat_default_voice')  == 'nova') selected @endif>{{ __('Nova') }} ({{ __('Female') }})</option>
														<option value="shimmer" @if ( config('settings.chat_default_voice')  == 'shimmer') selected @endif>{{ __('Shimmer') }} ({{ __('Female') }})</option>
													</select>
												</div>								
											</div>				
										</div>		
									</div>
								</div>


								<div class="card shadow-0 mb-7">							
									<div class="card-body">

										<h6 class="fs-12 font-weight-bold mb-4"><i class="  fa-solid fa-camera-viewfinder text-info fs-14 mr-2"></i>{{ __('AI Image Settings') }} <span class="text-muted">({{ __('For All Groups') }})</span></h6>

										<div class="row">

											<div class="col-lg-12 col-md-12 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Image Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="image-feature-user" class="custom-switch-input" @if ($settings->images_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Image Vendors') }} <i class="ml-2 fa fa-info info-notification" data-tippy-content="{{ __('Image models of the selected image vendors will be available to all non-subscribers. You can control image vendors availability via subscription plans as well') }}."></i></h6>
													<select class="form-select" id="image-vendors" name="image_vendors[]" data-placeholder="{{ __('Choose AI Image vendors') }}" multiple>
														<option value='openai' @foreach ($images as $key=>$value) @if($value == 'openai') selected @endif @endforeach>{{ __('OpenAI') }}</option>																															
														<option value='sd' @foreach ($images as $key=>$value) @if($value == 'sd') selected @endif @endforeach> {{ __('Stable Diffusion') }}</option>																															
														@if (App\Services\HelperService::extensionFlux())
															<option value='falai' @foreach ($images as $key=>$value) @if($value == 'falai') selected @endif @endforeach> {{ __('Flux AI') }}</option>																																																																																													
														@endif
														@if (App\Services\HelperService::extensionMidjourney())
															<option value='midjourney' @foreach ($images as $key=>$value) @if($value == 'midjourney') selected @endif @endforeach> {{ __('Midjourney') }}</option>																																																																																													
														@endif
														@if (App\Services\HelperService::extensionClipdrop())
															<option value='clipdrop' @foreach ($images as $key=>$value) @if($value == 'clipdrop') selected @endif @endforeach> {{ __('Clipdrop') }}</option>																																																																																													
														@endif
													</select>
												</div>
											</div>
				
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Default Storage for AI Images') }} <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="storage" name="default-storage" class="form-select" data-placeholder="{{ __('Set Default Storage for AI Images') }}:">			
														<option value="local" @if ( config('settings.default_storage')  == 'local') selected @endif>{{ __('Local Server') }}</option>
														<option value="aws" @if ( config('settings.default_storage')  == 'aws') selected @endif>{{ __('Amazon Web Services') }}</option>
														<option value="wasabi" @if ( config('settings.default_storage')  == 'wasabi') selected @endif>{{ __('Wasabi Cloud') }}</option>
														<option value="gcp" @if ( config('settings.default_storage')  == 'gcp') selected @endif>{{ __('Google Cloud Platform') }}</option>
														<option value="storj" @if ( config('settings.default_storage')  == 'storj') selected @endif>{{ __('Storj') }}</option>
														<option value="dropbox" @if ( config('settings.default_storage')  == 'dropbox') selected @endif>{{ __('Dropbox') }}</option>
														<option value="r2" @if ( config('settings.default_storage')  == 'r2') selected @endif>{{ __('Cloudflare R2') }}</option>
													</select>
												</div>								
											</div>	
											
											<div class="col-lg-6 col-md-6 col-sm-12 mb-2">
												<a href="{{ route('admin.davinci.configs.image.credits') }}" class="btn btn-primary ripple pl-9 pr-9 pt-3 pb-3 fs-12" style="text-transform:none;">{{ __('Set AI Image Model Credits') }}</a>
											</div>
										</div>		
									</div>
								</div>


								<div class="card shadow-0 mb-7">							
									<div class="card-body">

										<h6 class="fs-12 font-weight-bold mb-4"><i class="  fa-solid fa-waveform-lines text-info fs-14 mr-2"></i>{{ __('AI Voiceover Settings') }} <span class="text-muted">({{ __('For All Groups') }})</span></h6>

										<div class="row">

											<div class="col-lg-12 col-md-12 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Voiceover Feature') }} <span class="text-muted">({{ __('For All Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="voiceover-feature-user" class="custom-switch-input" @if ($settings->voiceover_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>	

											<div class="col-lg-6 col-md-6 col-sm-12">
												<!-- EFFECTS -->
												<div class="input-box">	
													<h6>{{ __('SSML Effects') }}</h6>
													<select id="set-ssml-effects" name="set-ssml-effects" class="form-select" data-placeholder="{{ __('Configure SSML Effects') }}">			
														<option value="enable" @if ( config('settings.voiceover_ssml_effect')  == 'enable') selected @endif>{{ __('Enable All') }}</option>
														<option value="disable" @if ( config('settings.voiceover_ssml_effect')  == 'disable') selected @endif>{{ __('Disable All') }}</option>
													</select>
												</div> <!-- END EFFECTS -->							
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<!-- STORAGE OPTION -->
												<div class="input-box">	
													<h6>{{ __('Default Storage for AI Voiceovers') }}</h6>
													<select id="set-storage-option" name="set-storage-option" class="form-select" data-placeholder="{{ __('Select Default Storage for AI Voiceover') }}">			
														<option value="local" @if ( config('settings.voiceover_default_storage')  == 'local') selected @endif>{{ __('Local Server Storage') }}</option>
														<option value="aws" @if ( config('settings.voiceover_default_storage')  == 'aws') selected @endif>Amazon Web Services</option>
														<option value="wasabi" @if ( config('settings.voiceover_default_storage')  == 'wasabi') selected @endif>Wasabi Cloud</option>
														<option value="gcp" @if ( config('settings.voiceover_default_storage')  == 'gcp') selected @endif>{{ __('Google Cloud Platform') }}</option>
														<option value="storj" @if ( config('settings.voiceover_default_storage')  == 'storj') selected @endif>{{ __('Storj') }}</option>
														<option value="dropbox" @if ( config('settings.voiceover_default_storage')  == 'dropbox') selected @endif>{{ __('Dropbox') }}</option>
														<option value="r2" @if ( config('settings.voiceover_default_storage')  == 'r2') selected @endif>{{ __('Cloudflare R2') }}</option>
													</select>
												</div> <!-- END STORAGE OPTION -->							
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<!-- LANGUAGE -->
												<div class="input-box">	
													<h6>{{ __('Default Language') }}</h6>
													<select id="languages" name="language" class="form-select" data-placeholder="{{ __('Select Default Language') }}" data-callback="language_select">			
														@foreach ($voiceover_languages as $language)
															<option value="{{ $language->language_code }}" data-img="{{ theme_url($language->language_flag) }}" @if (config('settings.voiceover_default_language') == $language->language_code) selected @endif> {{ $language->language }}</option>
														@endforeach
													</select>
												</div> <!-- END LANGUAGE -->							
											</div>
				
											<div class="col-lg-6 col-md-6 col-sm-12">
												<!-- VOICE -->
												<div class="input-box">	
													<h6>{{ __('Default Voice') }}</h6>
													<select id="voices" name="voice" class="form-select" data-placeholder="{{ __('Select Default Voice') }}" data-callback="default_voice">			
														@foreach ($voices as $voice)
															<option value="{{ $voice->voice_id }}" 	
																data-img="{{ theme_url($voice->avatar_url) }}"										
																data-id="{{ $voice->voice_id }}" 
																data-lang="{{ $voice->language_code }}" 
																data-type="{{ $voice->voice_type }}"
																data-gender="{{ $voice->gender }}"
																@if (config('settings.voiceover_default_voice') == $voice->voice_id) selected @endif
																data-class="@if (config('settings.voiceover_default_language') !== $voice->language_code) remove-voice @endif"> 
																{{ $voice->voice }}  														
															</option>
														@endforeach
													</select>
												</div> <!-- END VOICE -->							
											</div>
																			
											<div class="col-lg-6 col-md-6 col-sm-12">
												<!-- MAX CHARACTERS -->
												<div class="input-box">								
													<h6>{{ __('Maximum Total Characters Synthesize Limit') }} <i class="ml-2 fa fa-info info-notification" data-tippy-content="{{ __('Maximum supported characters per single synthesize task can be up to 100000 characters. Each voice (textarea) has a limitation of up to 5000 characters, and you can combine up to 20 voices in a single task (20 voices x 5000 textarea limit = 100000)') }}."></i></h6>
													<div class="form-group">							    
														<input type="text" class="form-control @error('set-max-chars') is-danger @enderror" id="set-max-chars" name="set-max-chars" placeholder="Ex: 3000" value="{{ config('settings.voiceover_max_chars_limit') }}">
														@error('set-max-chars')
															<p class="text-danger">{{ $errors->first('set-max-chars') }}</p>
														@enderror
													</div> 
												</div> <!-- END MAX CHARACTERS -->							
											</div>
				
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">								
													<h6>{{ __('Maximum Concurrent Voices Limit') }} <i class="ml-2 fa fa-info info-notification" data-tippy-content="{{ __('You can mix up to 20 different voices in a single synthesize task. Each voice can synthesize up to 5000 characters, total characters can not exceed the limit set by Maximum Characters Synthesize Limit field.') }}"></i></h6>
													<div class="form-group">							    
														<input type="text" class="form-control @error('set-max-voices') is-danger @enderror" id="set-max-voices" name="set-max-voices" placeholder="Ex: 5" value="{{ config('settings.voiceover_max_voice_limit') }}">
														@error('set-max-voices')
															<p class="text-danger">{{ $errors->first('set-max-voices') }}</p>
														@enderror
													</div> 
												</div>							
											</div>					
										</div>		
									</div>
								</div>


								<div class="card shadow-0 mb-7">							
									<div class="card-body">

										<h6 class="fs-12 font-weight-bold mb-4"><i class="  fa-solid fa-folder-music text-info fs-14 mr-2"></i>{{ __('AI Speech to Text Settings') }} <span class="text-muted">({{ __('For All Groups') }})</span></h6>

										<div class="row">

											<div class="col-lg-12 col-md-12 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Speech to Text Feature') }} <span class="text-muted">({{ __('For User & Subscriber Groups') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<div class="form-group">
														<label class="custom-switch">
															<input type="checkbox" name="whisper-feature-user" class="custom-switch-input" @if ($settings->transcribe_feature) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>
																			
											<div class="col-lg-6 col-md-6 col-sm-12">
												<!-- MAX CHARACTERS -->
												<div class="input-box">								
													<h6>{{ __('Maximum Allowed Audio File Size') }} <i class="ml-2 fa fa-info info-notification" data-tippy-content="{{ __('OpenAI supports audio files only up to 25MB') }}."></i></h6>
													<div class="form-group">							    
														<input type="text" class="form-control @error('set-max-audio-size') is-danger @enderror" id="set-max-audio-size" name="set-max-audio-size" placeholder="Ex: 25" value="{{ config('settings.whisper_max_audio_size') }}">
														@error('set-max-audio-size')
															<p class="text-danger">{{ $errors->first('set-max-audio-size') }}</p>
														@enderror
													</div> 
												</div> <!-- END MAX CHARACTERS -->							
											</div>
				
											
											<div class="col-lg-6 col-md-6 col-sm-12">
												<!-- STORAGE OPTION -->
												<div class="input-box">	
													<h6>{{ __('Default Storage for AI Speech to Text') }}</h6>
													<select id="set-whisper-storage-option" name="set-whisper-storage-option" class="form-select" data-placeholder="{{ __('Select Default Storage for AI Speech to Text') }}">			
														<option value="local" @if ( config('settings.whisper_default_storage')  == 'local') selected @endif>{{ __('Local Server Storage') }}</option>
														<option value="aws" @if ( config('settings.whisper_default_storage')  == 'aws') selected @endif>Amazon Web Services</option>
														<option value="wasabi" @if ( config('settings.whisper_default_storage')  == 'wasabi') selected @endif>Wasabi Cloud</option>
														<option value="gcp" @if ( config('settings.whisper_default_storage')  == 'gcp') selected @endif>{{ __('Google Cloud Platform') }}</option>
														<option value="storj" @if ( config('settings.whisper_default_storage')  == 'storj') selected @endif>{{ __('Storj') }}</option>
														<option value="dropbox" @if ( config('settings.whisper_default_storage')  == 'dropbox') selected @endif>{{ __('Dropbox') }}</option>
														<option value="r2" @if ( config('settings.whisper_default_storage')  == 'r2') selected @endif>{{ __('Cloudflare R2') }}</option>
													</select>
												</div> <!-- END STORAGE OPTION -->							
											</div>								
										</div>		
									</div>
								</div>


								<div class="card shadow-0 ">							
									<div class="card-body">

										<h6 class="fs-12 font-weight-bold mb-4"><i class="  fa-solid fa-sliders text-info fs-14 mr-2"></i>{{ __('Miscellaneous') }}</h6>

										<div class="row">
											<div class="row">
												<div class="col-lg-12 col-md-12 col-sm-12">	
													<div class="input-box">	
														<h6>{{ __('Sensitive Words Filter') }} <span class="text-muted">({{ __('Comma Separated') }})</span></h6>							
														<textarea class="form-control" name="words-filter" rows="6" id="words-filter">{{ $filters->value }}</textarea>	
													</div>											
												</div>
											</div>							
										</div>
			
									</div>
								</div>

								<!-- SAVE CHANGES ACTION BUTTON -->
								<div class="border-0 text-center mb-2 mt-1">
									<button type="button" class="btn ripple btn-primary pl-9 pr-9 pt-3 pb-3 fs-12" style="min-width: 300px;" id="general-settings">{{ __('Save') }}</button>							
								</div>				
							</form>
						</div>



						<div class="tab-pane fade" id="api" role="tabpanel" aria-labelledby="api-tab">

							<div class="row">

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/credit')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/api.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('API Credit Management') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('Full control of words/tokens consumption per AI model')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/openai')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/openai-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('OpenAI') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Writer | Article Wizard | Smart Editor | AI Images | AI Chat')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/anthropic')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/anthropic.jpeg')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Anthropic') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Writer | AI Chat')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/deepseek')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/deepseek.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('DeepSeek') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Writer | AI Chat')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/xai')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/xai.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('xAI') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Writer | AI Chat')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/google')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/gcp-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Google') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Voiceover | AI Writer | AI Chat | Cloud Storage | Speech to Text')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/stablediffusion')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/stability-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Stable Diffusion') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Images')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/azure')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/azure-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Azure') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Voiceover')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/elevenlabs')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/elevenlabs-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Elevenlabs') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Voiceover | Voice Clone')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/aws')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/aws-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('AWS') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Voiceover | Cloud Storage | Speech to Text')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/storj')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/storj-ssm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Storj') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('Cloud Storage')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/dropbox')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/dropbox-ssm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Dropbox') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('Cloud Storage')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/wasabi')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/wasabi-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Wasabi') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('Cloud Storage')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/cloudflare')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/cloudflare-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Cloudflare') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('Cloud Storage')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/serper')}}'">
										<div class="card-body p-5 d-flex">
											<div class="extension-icon">
												<img src="{{theme_url('img/csp/serper.png')}}" class="mr-4" alt="" style="width: 40px;">												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Serper') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Writer | AI Chat')}}</p>
											</div>
										</div>							
									</div>
								</div>

								<div class="col-md-6 col-sm-12">
									<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/api/youtube')}}'">
										<div class="card-body p-5 d-flex">
											<div class="">
												<i class="mr-4 fa-brands fa-youtube fs-40"></i>												
											</div>
											<div class="extension-title">
												<div class="d-flex">
													<h6 class="fs-15 font-weight-bold mb-3">{{ __('Youtube') }}</h6>
												</div>
												<p class="fs-12 mb-0 text-muted">{{ __('AI Youtube')}}</p>
											</div>
										</div>							
									</div>
								</div>
							</div>
						</div>



						<div class="tab-pane fade" id="extended" role="tabpanel" aria-labelledby="extended-tab">

							<div class="row">

								@if (App\Services\HelperService::extensionCheckSaaS())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/saas')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('extension/saas.jpg')}}" class="mr-4 mt-2" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('SaaS Business') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('SaaS Feature Configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionPlagiarism())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/plagiarism')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/plagiarism.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Plagiarism and Content Detector') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionFlux())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/flux')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/flux.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Flux AI') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Fal AI API keys')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionPebblely())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/pebblely')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/pebblely.webp')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Product Photography') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Pebblely API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionVoiceClone())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/voice-clone')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/elevenlabs-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Voice Clone') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Elevenlabs API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionSoundStudio())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/sound-studio')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class="  fa-solid fa-photo-film-music mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Sound Studio') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Sound Studio service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionPhotoStudio())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/photo-studio')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/stability-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Photo Studio') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Stability API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionVideoImage())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/video-image')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/stability-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Video') }} ({{__('Image to Video')}})</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Stability API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionWordpressIntegration())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/integration/wordpress')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/wordpress.png')}}" class="mr-4" alt="" style="width: 70px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Wordpress Integration') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Wordpress API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionAvatar())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/avatar')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/heygen.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Avatar') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Heygen API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionVideoText())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/video-text')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/flux.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Video') }} ({{__('Text to Video')}})</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Fal AI API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionVoiceIsolator())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/voice-isolator')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/elevenlabs-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Voice Isolator') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Elevenlabs API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif		
								
								@if (App\Services\HelperService::extensionSocialMedia())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/social-media')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class="  fa-solid fa-share-nodes mr-4" style="font-size: 40px!important;"></i>											
												</div> 
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Social Media') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Social media service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif	

								@if (App\Services\HelperService::extensionVideoVideo())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/video-video')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/flux.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Video') }} ({{__('Video to Video')}})</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Fal AI API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionMaintenance())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/maintenance')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class=" fa-solid fa-gear-complex-code mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Maintenance Mode') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Maintenance mode configuration settings')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionMidjourney())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/midjourney')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="https://assets.apidog.com/app/project-icon/custom/20241009/482dc896-55d5-49bc-93d8-0ac091ec6f2a.png" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Midjourney') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Midjourney API keys')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionFaceswap())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/faceswap')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="https://assets.apidog.com/app/project-icon/custom/20241009/482dc896-55d5-49bc-93d8-0ac091ec6f2a.png" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Faceswap') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Faceswap API keys and configuration settings')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif


								@if (App\Services\HelperService::extensionMusic())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/music')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/aiml.png')}}" class="mr-4" alt="" style="width: 40px;">	
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Music (Text to Music)') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('AI/ML API keys and configuration settings')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionWatson())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/watson')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/ibm.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('IBM Watson Text to Speech') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('IBM API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionClipdrop())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/clipdrop')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/clipdrop.jpg')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Clipdrop') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Clipdrop API keys and service configurations')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionHubspot())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/hubspot')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/hubspot.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Hubspot CRM') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Hubspot API keys')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionMailchimp())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/mailchimp')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/mailchimp.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Mailchimp Newsletter') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Mailchimp API keys')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionSEO())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/seo')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class=" fa-solid fa-globe-pointer mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('SEO Tool') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('SEO Tool settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionPerplexity())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/perplexity')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/perplexity.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Perplexity AI') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Perplexity API keys')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionChatShare())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/chat-share')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class=" fa-solid fa-comments mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Chat Share') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Chat Share settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionTextract())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/textract')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/aws-sm.png')}}" class="mr-4" alt="" style="width: 40px;">											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Textract') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Textract settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionRealtimeChat())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/realtime-chat')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class=" fa-solid fa-waveform-lines mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI Realtime Voice Chat') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Realtime Voice Chat settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionExternalChatbot())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/external-chatbot')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class=" fa-solid fa-user-robot mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('AI External Chatbot') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('External Chatbot settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionAzureOpenai())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/azure-openai')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class="fa-brands fa-microsoft mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Azure OpenAI') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Azure OpenAI settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionAmazonBedrock())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/bedrock')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/aws-sm.png')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Amazon Bedrock') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Amazon Bedrock Access keys and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionOnboardingPro())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/onboarding-pro')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class=" fa-solid fa-waveform-lines mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Onboarding Pro') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Onboarding settings and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionSpeechToTextPro())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/speech-text-pro')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<i class="fa-solid fa-microphone-lines mr-4" style="font-size: 40px!important;"></i>											
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Speech to Text Pro') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Elevenlabs API key and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionXero())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/xero')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/xero.webp')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Xero') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Xero API keys and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif


								@if (App\Services\HelperService::extensionOpenRouter())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/open-router')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/csp/openrouter.png')}}" class="mr-4" alt="" style="width: 40px;">										
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('OpenRouter') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('OpenRouter API key and configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

								@if (App\Services\HelperService::extensionWallet())
									<div class="col-md-6 col-sm-12">
										<div class="card shadow-0 mb-6" onclick="window.location.href='{{ url('/app/admin/davinci/configs/wallet')}}'">
											<div class="card-body p-5 d-flex">
												<div class="extension-icon">
													<img src="{{theme_url('img/payments/wallet.avif')}}" class="mr-4" alt="" style="width: 40px;">												
												</div>
												<div class="extension-title">
													<div class="d-flex">
														<h6 class="fs-15 font-weight-bold mb-3">{{ __('Wallet System') }}</h6>
													</div>
													<p class="fs-12 mb-0 text-muted">{{ __('Wallet System Configuration')}}</p>
												</div>
											</div>							
										</div>
									</div>
								@endif

							</div>
						</div>



						<div class="tab-pane fade" id="trial" role="tabpanel" aria-labelledby="trial-tab">
							<form id="trial-features-form" action="{{ route('admin.davinci.configs.store.trial') }}" method="POST" enctype="multipart/form-data">
								@csrf
								
								<div class="card shadow-0 mb-7">							
									<div class="card-body">

										<h6 class="fs-12 font-weight-bold mb-6 text-center mt-4"><i class="fa fa-gift text-warning fs-14 mr-2"></i>{{ __('Free Trial Features') }} <span class="text-muted">({{ __('Free Tier User Group Only') }})</span></h6>

										<div class="row">			

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('Templates Category Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="templates-user" name="templates-user" class="form-select">
														<option value="all" @if (config('settings.templates_access_user') == 'all') selected @endif>{{ __('All Templates') }}</option>	
														<option value="free" @if (config('settings.templates_access_user') == 'free') selected @endif>{{ __('Only Free Templates') }}</option>																																									
														<option value="standard" @if (config('settings.templates_access_user') == 'standard') selected @endif> {{ __('Up to Standard Templates') }}</option>	
														<option value="professional" @if (config('settings.templates_access_user') == 'professional') selected @endif> {{ __('Up to Professional Templates') }}</option>	
														<option value="premium" @if (config('settings.templates_access_user') == 'premium') selected @endif> {{ __('Up to Premium Templates') }} ({{ __('All') }})</option>																																																													
													</select>
												</div>
											</div>				
											
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Chat Package Type Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="chats" name="chat-user" class="form-select">
														<option value="all" @if (config('settings.chats_access_user') == 'all') selected @endif>{{ __('All Chat Types') }}</option>
														<option value="free" @if (config('settings.chats_access_user') == 'free') selected @endif>{{ __('Only Free Chat Types') }}</option>
														<option value="standard" @if (config('settings.chats_access_user') == 'standard') selected @endif> {{ __('Up to Standard Chat Types') }}</option>
														<option value="professional" @if (config('settings.chats_access_user') == 'professional') selected @endif> {{ __('Up to Professional Chat Types') }}</option>																																		
														<option value="premium" @if (config('settings.chats_access_user') == 'premium') selected @endif> {{ __('Up to Premium Chat Types') }}</option>																																																																																																									
													</select>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Default AI Model for Chat Bots') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="default-model-user-bot" name="default-model-user-bot" class="form-select">			
														<option value="gpt-3.5-turbo-0125" @if ( config('settings.default_model_user_bot')  == 'gpt-3.5-turbo-0125') selected @endif>{{ __('GPT 3.5 Turbo') }}</option>												
														<option value="gpt-4" @if ( config('settings.default_model_user_bot')  == 'gpt-4') selected @endif>{{ __('GPT 4') }}</option>
														<option value="gpt-4o" @if ( config('settings.default_model_user_bot')  == 'gpt-4o') selected @endif>{{ __('GPT 4o') }}</option>
														<option value="gpt-4o-mini" @if ( config('settings.default_model_user_bot')  == 'gpt-4o-mini') selected @endif>{{ __('GPT 4o mini') }}</option>
														<option value="gpt-4o-search-preview" @if ( config('settings.default_model_user_bot')  == 'gpt-4o-search-preview') selected @endif>{{ __('GPT 4o Search Preview') }}</option>
														<option value="gpt-4o-mini-search-preview" @if ( config('settings.default_model_user_bot')  == 'gpt-4o-mini-search-preview') selected @endif>{{ __('GPT 4o mini Search Preview') }}</option>
														<option value="gpt-4-0125-preview" @if ( config('settings.default_model_user_bot')  == 'gpt-4-0125-preview') selected @endif>{{ __('GPT 4 Turbo') }}</option>														
														<option value="gpt-4.5-preview" @if ( config('settings.default_model_user_bot')  == 'gpt-4.5-preview') selected @endif>{{ __('GPT 4.5') }}</option>														
														<option value="gpt-4.1" @if ( config('settings.default_model_user_bot')  == 'gpt-4.1') selected @endif>{{ __('GPT 4.1') }}</option>
														<option value="gpt-4.1-mini" @if ( config('settings.default_model_user_bot')  == 'gpt-4.1-mini') selected @endif>{{ __('GPT 4.1 mini') }}</option>
														<option value="gpt-4.1-nano" @if ( config('settings.default_model_user_bot')  == 'gpt-4.1-nano') selected @endif>{{ __('GPT 4.1 nano') }}</option>
														<option value="o1" @if ( config('settings.default_model_user_bot')  == 'o1') selected @endif>{{ __('o1') }}</option>
														<option value="o1-mini" @if ( config('settings.default_model_user_bot')  == 'o1-mini') selected @endif>{{ __('o1 mini') }}</option>
														<option value="o3-mini" @if ( config('settings.default_model_user_bot')  == 'o3-mini') selected @endif>{{ __('o3 mini') }}</option>
														<option value="o3" @if ( config('settings.default_model_user_bot')  == 'o3') selected @endif>{{ __('o3') }}</option>														
														<option value="o4-mini" @if ( config('settings.default_model_user_bot')  == 'o4-mini') selected @endif>{{ __('o4 mini') }}</option>														
														<option value="claude-opus-4-20250514" @if ( config('settings.default_model_user_bot')  == 'claude-opus-4-20250514') selected @endif>{{ __('Claude 4 Opus') }}</option>
														<option value="claude-sonnet-4-20250514" @if ( config('settings.default_model_user_bot')  == 'claude-sonnet-4-20250514') selected @endif>{{ __('Claude 4 Sonnet') }}</option>
														<option value="claude-3-opus-20240229" @if ( config('settings.default_model_user_bot')  == 'claude-3-opus-20240229') selected @endif>{{ __('Claude 3 Opus') }}</option>
														<option value="claude-3-7-sonnet-20250219" @if ( config('settings.default_model_user_bot')  == 'claude-3-7-sonnet-20250219') selected @endif>{{ __('Claude 3.7 Sonnet') }}</option>
														<option value="claude-3-5-sonnet-20241022" @if ( config('settings.default_model_user_bot')  == 'claude-3-5-sonnet-20241022') selected @endif>{{ __('Claude 3.5v2 Sonnet') }}</option>
														<option value="claude-3-5-haiku-20241022" @if ( config('settings.default_model_user_bot')  == 'claude-3-5-haiku-20241022') selected @endif>{{ __('Claude 3.5 Haiku') }}</option>														
														<option value="gemini-1.5-pro" @if ( config('settings.default_model_user_bot')  == 'gemini-1.5-pro') selected @endif>{{ __('Gemini 1.5 Pro') }}</option>
														<option value="gemini-1.5-flash" @if ( config('settings.default_model_user_bot')  == 'gemini-1.5-flash') selected @endif>{{ __('Gemini 1.5 Flash') }}</option>
														<option value="gemini-2.0-flash" @if ( config('settings.default_model_user_bot')  == 'gemini-2.0-flash') selected @endif>{{ __('Gemini 2.0 Flash') }}</option>
														<option value="deepseek-chat" @if ( config('settings.default_model_user_bot')  == 'deepseek-chat') selected @endif>{{ __('DeepSeek V3') }}</option>
														<option value="deepseek-reasoner" @if ( config('settings.default_model_user_bot')  == 'deepseek-reasoner') selected @endif>{{ __('DeepSeek R1') }}</option>
														<option value="grok-2-1212" @if ( config('settings.default_model_user_bot')  == 'grok-2-1212') selected @endif>{{ __('Grok 2') }}</option>
														<option value="grok-2-vision-1212" @if ( config('settings.default_model_user_bot')  == 'grok-2-vision-1212') selected @endif>{{ __('Grok 2 Vision') }}</option>
														@if (App\Services\HelperService::extensionPerplexity())	
															<option value="sonar" @if ( config('settings.default_model_user_bot')  == 'sonar') selected @endif>{{ __('Perplexity Sonar') }}</option>
															<option value="sonar-pro" @if ( config('settings.default_model_user_bot')  == 'sonar-pro') selected @endif>{{ __('Perplexity Sonar Pro') }}</option>
															<option value="sonar-reasoning" @if ( config('settings.default_model_user_bot')  == 'sonar-reasoning') selected @endif>{{ __('Perplexity Sonar Reasoning') }}</option>
															<option value="sonar-reasoning-pro" @if ( config('settings.default_model_user_bot')  == 'sonar-reasoning-pro') selected @endif>{{ __('Perplexity Sonar Reasoning Pro') }}</option>
														@endif
														@if (App\Services\HelperService::extensionAmazonBedrock())	
															<option value="us.amazon.nova-micro-v1:0" @if ( config('settings.default_model_user_bot')  == 'us.amazon.nova-micro-v1:0') selected @endif>{{ __('Nova Micro') }}</option>
															<option value="us.amazon.nova-lite-v1:0" @if ( config('settings.default_model_user_bot')  == 'us.amazon.nova-lite-v1:0') selected @endif>{{ __('Nova Lite') }}</option>
															<option value="us.amazon.nova-pro-v1:0" @if ( config('settings.default_model_user_bot')  == 'us.amazon.nova-pro-v1:0') selected @endif>{{ __('Nova Pro') }}</option>
														@endif
														@foreach ($models as $model)
															<option value="{{ $model->model }}" @if ( config('settings.default_model_user_bot')  == $model->model) selected @endif>{{ $model->description }} ({{ __('Fine Tune Model')}})</option>
														@endforeach
													</select>
												</div>								
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Default AI Model for Templates') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<select id="default-model-user-template" name="default-model-user-template" class="form-select">	
														<option value="gpt-3.5-turbo-0125" @if ( config('settings.default_model_user_template')  == 'gpt-3.5-turbo-0125') selected @endif>{{ __('GPT 3.5 Turbo') }}</option>												
														<option value="gpt-4" @if ( config('settings.default_model_user_template')  == 'gpt-4') selected @endif>{{ __('GPT 4') }}</option>
														<option value="gpt-4o" @if ( config('settings.default_model_user_template')  == 'gpt-4o') selected @endif>{{ __('GPT 4o') }}</option>
														<option value="gpt-4o-mini" @if ( config('settings.default_model_user_template')  == 'gpt-4o-mini') selected @endif>{{ __('GPT 4o mini') }}</option>
														<option value="gpt-4o-search-preview" @if ( config('settings.default_model_user_template')  == 'gpt-4o-search-preview') selected @endif>{{ __('GPT 4o Search Preview') }}</option>
														<option value="gpt-4o-mini-search-preview" @if ( config('settings.default_model_user_template')  == 'gpt-4o-mini-search-preview') selected @endif>{{ __('GPT 4o mini Search Preview') }}</option>
														<option value="gpt-4-0125-preview" @if ( config('settings.default_model_user_template')  == 'gpt-4-0125-preview') selected @endif>{{ __('GPT 4 Turbo') }}</option>																												
														<option value="gpt-4.5-preview" @if ( config('settings.default_model_user_template')  == 'gpt-4.5-preview') selected @endif>{{ __('GPT 4.5') }}</option>																												
														<option value="gpt-4.1" @if ( config('settings.default_model_user_template')  == 'gpt-4.1') selected @endif>{{ __('GPT 4.1') }}</option>
														<option value="gpt-4.1-mini" @if ( config('settings.default_model_user_template')  == 'gpt-4.1-mini') selected @endif>{{ __('GPT 4.1 mini') }}</option>
														<option value="gpt-4.1-nano" @if ( config('settings.default_model_user_template')  == 'gpt-4.1-nano') selected @endif>{{ __('GPT 4.1 nano') }}</option>
														<option value="o1" @if ( config('settings.default_model_user_template')  == 'o1') selected @endif>{{ __('o1') }}</option>
														<option value="o1-mini" @if ( config('settings.default_model_user_template')  == 'o1-mini') selected @endif>{{ __('o1 mini') }}</option>
														<option value="o3-mini" @if ( config('settings.default_model_user_template')  == 'o3') selected @endif>{{ __('o3 mini') }}</option>
														<option value="o3" @if ( config('settings.default_model_user_template')  == 'o3') selected @endif>{{ __('o3') }}</option>
														<option value="o4-mini" @if ( config('settings.default_model_user_template')  == 'o4-mini') selected @endif>{{ __('o4 mini') }}</option>
														<option value="claude-opus-4-20250514" @if ( config('settings.default_model_user_template')  == 'claude-opus-4-20250514') selected @endif>{{ __('Claude 4 Opus') }}</option>
														<option value="claude-sonnet-4-20250514" @if ( config('settings.default_model_user_template')  == 'claude-sonnet-4-20250514') selected @endif>{{ __('Claude 4 Sonnet') }}</option>
														<option value="claude-3-opus-20240229" @if ( config('settings.default_model_user_template')  == 'claude-3-opus-20240229') selected @endif>{{ __('Claude 3 Opus') }}</option>
														<option value="claude-3-7-sonnet-20250219" @if ( config('settings.default_model_user_template')  == 'claude-3-7-sonnet-20250219') selected @endif>{{ __('Claude 3.7 Sonnet') }}</option>
														<option value="claude-3-5-sonnet-20241022" @if ( config('settings.default_model_user_template')  == 'claude-3-5-sonnet-20241022') selected @endif>{{ __('Claude 3.5v2 Sonnet') }}</option>
														<option value="claude-3-5-haiku-20241022" @if ( config('settings.default_model_user_template')  == 'claude-3-5-haiku-20241022') selected @endif>{{ __('Claude 3.5 Haiku') }}</option>
														<option value="gemini-1.5-pro" @if ( config('settings.default_model_user_template')  == 'gemini-1.5-pro') selected @endif>{{ __('Gemini 1.5 Pro') }}</option>
														<option value="gemini-1.5-flash" @if ( config('settings.default_model_user_template')  == 'gemini-1.5-flash') selected @endif>{{ __('Gemini 1.5 Flash') }}</option>
														<option value="gemini-2.0-flash" @if ( config('settings.default_model_user_template')  == 'gemini-2.0-flash') selected @endif>{{ __('Gemini 2.0 Flash') }}</option>
														<option value="deepseek-chat" @if ( config('settings.default_model_user_template')  == 'deepseek-chat') selected @endif>{{ __('DeepSeek V3') }}</option>
														<option value="deepseek-reasoner" @if ( config('settings.default_model_user_template')  == 'deepseek-reasoner') selected @endif>{{ __('DeepSeek R1') }}</option>
														<option value="grok-2-1212" @if ( config('settings.default_model_user_template')  == 'grok-2-1212') selected @endif>{{ __('Grok 2') }}</option>
														<option value="grok-2-vision-1212" @if ( config('settings.default_model_user_template')  == 'grok-2-vision-1212') selected @endif>{{ __('Grok 2 Vision') }}</option>
														@if (App\Services\HelperService::extensionPerplexity())	
															<option value="sonar" @if ( config('settings.default_model_user_template')  == 'sonar') selected @endif>{{ __('Perplexity Sonar') }}</option>
															<option value="sonar-pro" @if ( config('settings.default_model_user_template')  == 'sonar-pro') selected @endif>{{ __('Perplexity Sonar Pro') }}</option>
															<option value="sonar-reasoning" @if ( config('settings.default_model_user_template')  == 'sonar-reasoning') selected @endif>{{ __('Perplexity Sonar Reasoning') }}</option>
															<option value="sonar-reasoning-pro" @if ( config('settings.default_model_user_template')  == 'sonar-reasoning-pro') selected @endif>{{ __('Perplexity Sonar Reasoning Pro') }}</option>
														@endif
														@if (App\Services\HelperService::extensionAmazonBedrock())	
															<option value="us.amazon.nova-micro-v1:0" @if ( config('settings.default_model_user_template')  == 'us.amazon.nova-micro-v1:0') selected @endif>{{ __('Nova Micro') }}</option>
															<option value="us.amazon.nova-lite-v1:0" @if ( config('settings.default_model_user_template')  == 'us.amazon.nova-lite-v1:0') selected @endif>{{ __('Nova Lite') }}</option>
															<option value="us.amazon.nova-pro-v1:0" @if ( config('settings.default_model_user_template')  == 'us.amazon.nova-pro-v1:0') selected @endif>{{ __('Nova Pro') }}</option>
														@endif
														@foreach ($models as $model)
															<option value="{{ $model->model }}" @if ( config('settings.default_model_user_template')  == $model->model) selected @endif>{{ $model->description }} ({{ __('Fine Tune Model')}})</option>
														@endforeach
													</select>
												</div>								
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('Available AI Models') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Only listed models will be available for non-subscribers. Make sure your default models above are actually included in this list.') }}."></i></h6>
													<select class="form-select" id="models-list" name="models_list[]" multiple>
														<option value='gpt-3.5-turbo-0125' @foreach ($all_models as $key=>$value) @if($value == 'gpt-3.5-turbo-0125') selected @endif @endforeach>{{ __('GPT 3.5 Turbo') }}</option>																															
														<option value='gpt-4' @foreach ($all_models as $key=>$value) @if($value == 'gpt-4') selected @endif @endforeach>{{ __('GPT 4') }}</option>																																																																																																																																																																																																																		
														<option value='gpt-4o' @foreach ($all_models as $key=>$value) @if($value == 'gpt-4o') selected @endif @endforeach>{{ __('GPT 4o') }}</option>																																																																																																																																																																																																																		
														<option value="gpt-4o-mini" @foreach ($all_models as $key=>$value) @if($value == 'gpt-4o-mini') selected @endif @endforeach>{{ __('GPT 4o mini') }}</option>
														<option value="gpt-4o-search-preview" @foreach ($all_models as $key=>$value) @if($value == 'gpt-4o-search-preview') selected @endif @endforeach>{{ __('GPT 4o Search Preview') }}</option>
														<option value="gpt-4o-mini-search-preview" @foreach ($all_models as $key=>$value) @if($value == 'gpt-4o-mini-search-preview') selected @endif @endforeach>{{ __('GPT 4o mini Search Preview') }}</option>
														<option value='gpt-4-0125-preview' @foreach ($all_models as $key=>$value) @if($value == 'gpt-4-0125-preview') selected @endif @endforeach>{{ __('GPT 4 Turbo') }}</option>																		
														<option value='gpt-4.5-preview' @foreach ($all_models as $key=>$value) @if($value == 'gpt-4.5-preview') selected @endif @endforeach>{{ __('GPT 4.5') }}</option>																		
														<option value="gpt-4.1"  @foreach ($all_models as $key=>$value) @if($value == 'gpt-4.1') selected @endif @endforeach>{{ __('GPT 4.1') }}</option>
														<option value="gpt-4.1-mini"  @foreach ($all_models as $key=>$value) @if($value == 'gpt-4.1-mini') selected @endif @endforeach>{{ __('GPT 4.1 mini') }}</option>
														<option value="gpt-4.1-nano"  @foreach ($all_models as $key=>$value) @if($value == 'gpt-4.1-nano') selected @endif @endforeach>{{ __('GPT 4.1 nano') }}</option>
														<option value="o1"  @foreach ($all_models as $key=>$value) @if($value == 'o1') selected @endif @endforeach>{{ __('o1') }}</option>
														<option value="o1-mini"  @foreach ($all_models as $key=>$value) @if($value == 'o1-mini') selected @endif @endforeach>{{ __('o1 mini') }}</option>
														<option value="o3-mini"  @foreach ($all_models as $key=>$value) @if($value == 'o3-mini') selected @endif @endforeach>{{ __('o3 mini') }}</option>
														<option value="o3"  @foreach ($all_models as $key=>$value) @if($value == 'o3') selected @endif @endforeach>{{ __('o3') }}</option>
														<option value="o4-mini"  @foreach ($all_models as $key=>$value) @if($value == 'o4-mini') selected @endif @endforeach>{{ __('o4 mini') }}</option>
														<option value="claude-opus-4-20250514" @foreach ($all_models as $key=>$value) @if($value == 'claude-opus-4-20250514') selected @endif @endforeach>{{ __('Claude 4 Opus') }}</option>
														<option value="claude-sonnet-4-20250514" @foreach ($all_models as $key=>$value) @if($value == 'claude-sonnet-4-20250514') selected @endif @endforeach>{{ __('Claude 4 Sonnet') }}</option>
														<option value="claude-3-opus-20240229" @foreach ($all_models as $key=>$value) @if($value == 'claude-3-opus-20240229') selected @endif @endforeach>{{ __('Claude 3 Opus') }}</option>
														<option value="claude-3-7-sonnet-20250219" @foreach ($all_models as $key=>$value) @if($value == 'claude-3-7-sonnet-20250219') selected @endif @endforeach>{{ __('Claude 3.7 Sonnet') }}</option>
														<option value="claude-3-5-sonnet-20241022" @foreach ($all_models as $key=>$value) @if($value == 'claude-3-5-sonnet-20241022') selected @endif @endforeach>{{ __('Claude 3.5v2 Sonnet') }}</option>
														<option value="claude-3-5-haiku-20241022" @foreach ($all_models as $key=>$value) @if($value == 'claude-3-5-haiku-20241022') selected @endif @endforeach>{{ __('Claude 3.5 Haiku') }}</option>
														<option value="gemini-1.5-pro" @foreach ($all_models as $key=>$value) @if($value == 'gemini-1.5-pro') selected @endif @endforeach>{{ __('Gemini 1.5 Pro') }}</option>
														<option value="gemini-1.5-flash" @foreach ($all_models as $key=>$value) @if($value == 'gemini-1.5-flash') selected @endif @endforeach>{{ __('Gemini 1.5 Flash') }}</option>
														<option value="gemini-2.0-flash" @foreach ($all_models as $key=>$value) @if($value == 'gemini-2.0-flash') selected @endif @endforeach>{{ __('Gemini 2.0 Flash') }}</option>
														<option value="deepseek-chat" @foreach ($all_models as $key=>$value) @if($value == 'deepseek-chat') selected @endif @endforeach>{{ __('DeeSeek V3') }}</option>
														<option value="deepseek-reasoner" @foreach ($all_models as $key=>$value) @if($value == 'deepseek-reasoner') selected @endif @endforeach>{{ __('DeeSeek R1') }}</option>
														<option value="grok-2-1212" @foreach ($all_models as $key=>$value) @if($value == 'grok-2-1212') selected @endif @endforeach>{{ __('Grok 2') }}</option>
														<option value="grok-2-vision-1212" @foreach ($all_models as $key=>$value) @if($value == 'grok-2-vision-1212') selected @endif @endforeach>{{ __('Grok 2 Vision') }}</option>
														@if (App\Services\HelperService::extensionPerplexity())	
															<option value="sonar" @foreach ($all_models as $key=>$value) @if($value == 'sonar') selected @endif @endforeach>{{ __('Perplexity Sonar') }}</option>
															<option value="sonar-pro" @foreach ($all_models as $key=>$value) @if($value == 'sonar-pro') selected @endif @endforeach>{{ __('Perplexity Sonar Pro') }}</option>
															<option value="sonar-reasoning" @foreach ($all_models as $key=>$value) @if($value == 'sonar-reasoning') selected @endif @endforeach>{{ __('Perplexity Sonar Reasoning') }}</option>
															<option value="sonar-reasoning-pro" @foreach ($all_models as $key=>$value) @if($value == 'sonar-reasoning-pro') selected @endif @endforeach>{{ __('Perplexity Sonar Reasoning Pro') }}</option>
														@endif
														@if (App\Services\HelperService::extensionAmazonBedrock())	
															<option value="us.amazon.nova-micro-v1:0" @foreach ($all_models as $key=>$value) @if($value == 'us.amazon.nova-micro-v1:0') selected @endif @endforeach>{{ __('Nova Micro') }}</option>
															<option value="us.amazon.nova-lite-v1:0" @foreach ($all_models as $key=>$value) @if($value == 'us.amazon.nova-lite-v1:0') selected @endif @endforeach>{{ __('Nova Lite') }}</option>
															<option value="us.amazon.nova-pro-v1:0" @foreach ($all_models as $key=>$value) @if($value == 'us.amazon.nova-pro-v1:0') selected @endif @endforeach>{{ __('Nova Pro') }}</option>
														@endif
														
														@foreach ($models as $model)
															<option value="{{ $model->model }}" @foreach ($all_models as $key=>$value) @if($value == $model->model) selected @endif @endforeach>{{ $model->description }} ({{ __('Fine Tune Model')}})</option>
														@endforeach
													</select>
												</div>
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">
													<h6>{{ __('AI Voiceover Vendors Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Only listed TTS voices of the listed vendors will be available for the subscriber. Make sure to include respective vendor API keys in the Davinci settings page.') }}."></i></h6>
													<select class="form-select" id="voiceover-vendors" name="voiceover_vendors[]" multiple>
														<option value='aws' @foreach ($vendors as $key=>$value) @if($value == 'aws') selected @endif @endforeach>{{ __('AWS') }}</option>																															
														<option value='azure' @foreach ($vendors as $key=>$value) @if($value == 'azure') selected @endif @endforeach>{{ __('Azure') }}</option>																																																														
														<option value='gcp' @foreach ($vendors as $key=>$value) @if($value == 'gcp') selected @endif @endforeach>{{ __('GCP') }}</option>																																																														
														<option value='openai' @foreach ($vendors as $key=>$value) @if($value == 'openai') selected @endif @endforeach>{{ __('OpenAI') }}</option>																																																														
														<option value='elevenlabs' @foreach ($vendors as $key=>$value) @if($value == 'elevenlabs') selected @endif @endforeach>{{ __('ElevenLabs') }}</option>																																																																																																																											
														@if (App\Services\HelperService::extensionWatson())
															<option value='ibm' @foreach ($vendors as $key=>$value) @if($value == 'ibm') selected @endif @endforeach>{{ __('IBM') }}</option>																																																																																				
														@endif
													</select>
												</div>
											</div>
										</div>

										<div class="card shadow-0 mb-5 mt-5">							
											<div class="card-body">
												<div class="row">

													<h6 class="fs-12 font-weight-bold mb-6 mt-3"><i class="  fa-solid fa-cogs text-info fs-14 mr-2"></i>{{ __('AI Features Control') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span></h6>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Writer Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="writer-user-access" class="custom-switch-input" @if ($settings->writer_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Article Wizard Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="wizard-user-access" class="custom-switch-input" @if ($settings->wizard_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Chat Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="chat-user-access" class="custom-switch-input" @if ($settings->chat_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Images Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="images-user-access" class="custom-switch-input" @if ($settings->images_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('Smart Editor Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="smart-editor-user-access" class="custom-switch-input" @if ($settings->smart_editor_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI ReWriter Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="rewriter-user-access" class="custom-switch-input" @if ($settings->rewriter_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Vision Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="vision-user-access" class="custom-switch-input" @if ($settings->vision_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Voiceover Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="voiceover-user-access" class="custom-switch-input" @if ($settings->voiceover_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Speech to Text Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="transcribe-user-access" class="custom-switch-input" @if ($settings->transcribe_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI File Chat Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="chat-file-user-access" class="custom-switch-input" @if ($settings->file_chat_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Web Chat Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="chat-web-user-access" class="custom-switch-input" @if ($settings->web_chat_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>
						
													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('AI Chat Image Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="chat-image-user-access" class="custom-switch-input" @if ($settings->image_chat_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">	
															<h6>{{ __('Brand Voice Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="brand-voice-user-access" class="custom-switch-input" @if ($settings->brand_voice_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div> 						
													</div>	

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">
															<h6>{{ __('Realtime Time Data Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="internet-user-access" class="custom-switch-input" @if ( config('settings.internet_user_access')  == 'allow') checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">	
															<h6>{{ __('AI Youtube Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="youtube-user-access" class="custom-switch-input" @if ($settings->youtube_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div> 						
													</div>	

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">	
															<h6>{{ __('AI RSS Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="rss-user-access" class="custom-switch-input" @if ($settings->rss_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div> 						
													</div>	

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">	
															<h6>{{ __('AI Code Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="code-user-access" class="custom-switch-input" @if ($settings->code_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div> 						
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">	
															<h6>{{ __('Integration Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="integration-user-access" class="custom-switch-input" @if ($settings->integration_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div> 						
													</div>

													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="input-box">	
															<h6>{{ __('Team Member Feature Access') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span></h6>
															<div class="form-group">
																<label class="custom-switch">
																	<input type="checkbox" name="team-member-user-access" class="custom-switch-input" @if ($settings->team_member_feature_free_tier) checked @endif>
																	<span class="custom-switch-indicator"></span>
																</label>
															</div>
														</div> 						
													</div>
												</div>
											</div>
										</div>

										<div class="row">

											<h6 class="fs-12 font-weight-bold mb-6 mt-4 text-center"><i class="fa fa-gift text-warning fs-14 mr-2"></i>{{ __('Welcome Credits & Limits for Non-Subscribers') }}</h6>

											<div class="col-lg-6 col-md-6 col-sm-12">							
												<div class="input-box">								
													<h6>@if ($settings->model_credit_name == 'words') {{ __('Number of Words as a Gift upon Registration') }} @else {{ __('Number of Tokens as a Gift upon Registration') }} @endif<span class="text-muted">({{ __('One Time') }})<span class="text-required"><i class="fa-solid fa-asterisk"></i></span> </span></h6>
													<div class="form-group">							    
														<input type="number" class="form-control" id="token_credits" name="token_credits" value="{{ $settings->token_credits }}">
														<span class="text-muted fs-10">{{ __('Valid for all models') }}. @if ($settings->model_credit_name == 'words') {{ __('Set as -1 for unlimited words') }} @else {{ __('Set as -1 for unlimited tokens') }} @endif.</span>
													</div> 
												</div> 						
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">							
												<div class="input-box">								
													<h6>{{ __('Number of Media Credits as a Gift upon Registration') }} <span class="text-muted">({{ __('One Time') }})<span class="text-required"><i class="fa-solid fa-asterisk"></i></span> </span></h6>
													<div class="form-group">							    
														<input type="number" class="form-control" id="image_credits" name="image_credits" value="{{ $settings->image_credits }}">
														<span class="text-muted fs-10">{{ __('Valid for all media tasks') }}. {{ __('Set as -1 for unlimited media tasks') }}.</span>
													</div> 
													@error('image_credits')
														<p class="text-danger">{{ $errors->first('image_credits') }}</p>
													@enderror
												</div> 						
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">								
													<h6>{{ __('Number of Characters for AI Voiceover as a Gift upon Registration') }} <span class="text-muted">({{ __('One Time') }})<span class="text-required"><i class="fa-solid fa-asterisk"></i></span> </span></h6>
													<div class="form-group">							    
														<input type="number" class="form-control @error('set-free-chars') is-danger @enderror" id="set-free-chars" name="set-free-chars" placeholder="Ex: 1000" value="{{ config('settings.voiceover_welcome_chars') }}" required>
														<span class="text-muted fs-10">{{ __('Set as -1 for unlimited characters') }}.</span>
														@error('set-free-chars')
															<p class="text-danger">{{ $errors->first('set-free-chars') }}</p>
														@enderror
													</div> 
												</div>							
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">								
													<h6>{{ __('Number of Minutes for AI Speech to Text as a Gift upon Registration') }} <span class="text-muted">({{ __('One Time') }})<span class="text-required"><i class="fa-solid fa-asterisk"></i></span> </span></h6>
													<div class="form-group">							    
														<input type="number" class="form-control @error('set-free-minutes') is-danger @enderror" id="set-free-minutes" name="set-free-minutes" placeholder="Ex: 1000" value="{{ config('settings.whisper_welcome_minutes') }}" required>
														<span class="text-muted fs-10">{{ __('Set as -1 for unlimited minutes') }}.</span>
														@error('set-free-minutes')
															<p class="text-danger">{{ $errors->first('set-free-minutes') }}</p>
														@enderror
													</div> 
												</div>							
											</div>	

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Maximum Result Length') }} <span class="text-muted">({{ __('In Words') }}) ({{ __('For Non-Subscribers') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('OpenAI has a hard limit based on Token limits for each model. Refer to OpenAI documentation to learn more. As a recommended by OpenAI, max result length is capped at 1500 words.') }}"></i></h6>
													<input type="number" class="form-control @error('max-results-user') is-danger @enderror" id="max-results-user" name="max-results-user" placeholder="Ex: 10" value="{{ config('settings.max_results_limit_user') }}" required>
													@error('max-results-user')
														<p class="text-danger">{{ $errors->first('max-results-user') }}</p>
													@enderror
												</div>								
											</div>
											
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Maximum Allowed PDF File Size') }} <span class="text-muted">({{ __('In MB') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the maximum PDF file size limit for free tier user for AI File Chat feature') }}"></i></h6>
													<input type="number" class="form-control @error('max-pdf-size') is-danger @enderror" id="max-pdf-size" name="max-pdf-size" placeholder="Ex: 10" min="0.1" step="0.1" value="{{ config('settings.chat_pdf_file_size_user') }}" required>
													@error('max-pdf-size')
														<p class="text-danger">{{ $errors->first('max-pdf-size') }}</p>
													@enderror
												</div>								
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Maximum Allowed CSV File Size') }} <span class="text-muted">({{ __('In MB') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the maximum CSV file size limit for free tier user for AI File Chat feature') }}"></i></h6>
													<input type="number" class="form-control @error('max-csv-size') is-danger @enderror" id="max-csv-size" name="max-csv-size" placeholder="Ex: 10" min="0.1" step="0.1" value="{{ config('settings.chat_csv_file_size_user') }}" required>
													@error('max-csv-size')
														<p class="text-danger">{{ $errors->first('max-csv-size') }}</p>
													@enderror
												</div>								
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Maximum Allowed Word File Size') }} <span class="text-muted">({{ __('In MB') }})</span><span class="text-required"><i class="fa-solid fa-asterisk"></i></span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('Set the maximum Word file size limit for free tier user for AI File Chat feature') }}"></i></h6>
													<input type="number" class="form-control @error('max-word-size') is-danger @enderror" id="max-word-size" name="max-word-size" placeholder="Ex: 10" min="0.1" step="0.1" value="{{ config('settings.chat_word_file_size_user') }}" required>
													@error('max-word-size')
														<p class="text-danger">{{ $errors->first('max-word-size') }}</p>
													@enderror
												</div>								
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="input-box">	
													<h6>{{ __('Team Members Quantity') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span> <span class="text-required"><i class="fa-solid fa-asterisk"></i></span></h6>
													<input type="number" class="form-control @error('team-members-quantity') is-danger @enderror" id="team-members-quantity" name="team-members-quantity" placeholder="Ex: 5" value="{{ config('settings.team_members_quantity_user') }}">
												</div> 						
											</div>

											<div class="col-lg-6 col-md-6 col-sm-12">							
												<div class="input-box">								
													<h6>{{ __('Image/Video/Voiceover Results Storage Period') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('After set days file results will be deleted via CRON task') }}."></i></h6>
													<div class="form-group">							    
														<input type="number" class="form-control" id="file-result-duration" name="file-result-duration" value="{{ config('settings.file_result_duration_user') }}">
														<span class="text-muted fs-10">{{ __('In Days') }}. {{ __('Set as -1 for unlimited storage duration') }}.</span>
													</div> 
													@error('file-result-duration')
														<p class="text-danger">{{ $errors->first('file-result-duration') }}</p>
													@enderror
												</div> 						
											</div>
	
											<div class="col-lg-6 col-md-6 col-sm-12">							
												<div class="input-box">								
													<h6>{{ __('Generated Text Content Results Storage Period') }} <span class="text-muted">({{ __('For Non-Subscribers') }})</span><i class="ml-3 text-dark fs-13 fa-solid fa-circle-info" data-tippy-content="{{ __('After set days results will be deleted from database via CRON task') }}."></i></h6>
													<div class="form-group">							    
														<input type="number" class="form-control" id="document-result-duration" name="document-result-duration" value="{{ config('settings.document_result_duration_user') }}">
														<span class="text-muted fs-10">{{ __('In Days') }}. {{ __('Set as -1 for unlimited storage duration') }}.</span>
													</div> 
													@error('document-result-duration')
														<p class="text-danger">{{ $errors->first('document-result-duration') }}</p>
													@enderror
												</div> 						
											</div>												
										</div>											
											
									</div>
								</div>

								<!-- SAVE CHANGES ACTION BUTTON -->
								<div class="border-0 text-center mb-2 mt-1">
									<button type="button" class="btn ripple btn-primary" style="min-width: 200px;" id="trial-settings">{{ __('Save') }}</button>							
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script> 
	<script src="{{theme_url('js/admin-config.js')}}"></script>
	<script type="text/javascript">
		let list = "{{ config('settings.voiceover_free_tier_vendors') }}"
		list = list.split(', ')
		let models = "{{ config('settings.free_tier_models') }}"
		models = models.split(', ')
		let images = "{{ $settings->image_vendors }}"
		images = images.split(', ')

		$(function(){
			$("#voiceover-vendors").select2({
				theme: "bootstrap-5",
				containerCssClass: "select2--small",
				dropdownCssClass: "select2--small",
			}).val(list).trigger('change.select2');

			$("#models-list").select2({
				theme: "bootstrap-5",
				containerCssClass: "select2--small",
				dropdownCssClass: "select2--small",
			}).val(models).trigger('change.select2');

			$("#image-vendors").select2({
				theme: "bootstrap-5",
				containerCssClass: "select2--small",
				dropdownCssClass: "select2--small",
			}).val(images).trigger('change.select2');
		});

		$('#general-settings').on('click',function(e) {

			const form = document.getElementById("general-features-form");
			let data = new FormData(form);

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: $('#general-features-form').attr('action'),
				data: data,
				processData: false,
				contentType: false,
				success: function(data) {

					if (data['status'] == 200) {
						toastr.success('{{ __('Settings were successfully updated') }}');
					}

				},
				error: function(data) {
					toastr.error('{{ __('There was an issue with saving the settings') }}');
				}
			}).done(function(data) {})
		});


		$('#trial-settings').on('click',function(e) {

			const form = document.getElementById("trial-features-form");
			let data = new FormData(form);

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: $('#trial-features-form').attr('action'),
				data: data,
				processData: false,
				contentType: false,
				success: function(data) {

					if (data['status'] == 200) {
						toastr.success('{{ __('Settings were successfully updated') }}');
					}

				},
				error: function(data) {
					toastr.error('{{ __('There was an issue with saving the settings') }}');
				}
			}).done(function(data) {})
		});
	</script>
@endsection
@extends('layouts.auth')

@section('metadata')
    <meta name="description" content="{{ __($metadata->register_description) }}">
    <meta name="keywords" content="{{ __($metadata->register_keywords) }}">
    <meta name="author" content="{{ __($metadata->register_author) }}">	    
    <link rel="canonical" href="{{ $metadata->register_url }}">
    <title>{{ __($metadata->register_title) }}</title>
@endsection

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/awselect/awselect.min.css')}}" rel="stylesheet" />
@endsection

@section('content')
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
                <p class="text-muted">{{ __($extension->maintenance_message) }} </p>
            </footer>
        </div>
    @else
        @if (config('settings.registration') == 'enabled')
            <div class="container-fluid h-100vh ">
                <div class="row login-background justify-content-center">
                    <div class="col-md-6 col-sm-12" id="login-responsive"> 
                        <div class="row justify-content-center">
                            <div class="col-lg-7 mx-auto">
                                <div class="card-body pt-8">
                                    
                                    <form method="POST" action="{{ route('register') }}" onsubmit="process()">
                                        @csrf                                
                                        
                                        <h3 class="text-center login-title mb-8">{{__('Sign Up to')}} <span class="text-info"><a href="{{ url('/') }}">{{ config('app.name') }}</a></span></h3>

                                        @if (config('settings.oauth_login') == 'enabled')
                                            <div class="divider">
                                                <div class="divider-text text-muted">
                                                    <small>{{__('Continue With Your Social Media Account')}}</small>
                                                </div>
                                            </div>

                                            <div class="social-logins-box text-center">
                                                @if(config('services.facebook.enable') == 'on')<a href="{{ url('/auth/redirect/facebook') }}" class="social-login-button" id="login-facebook"><i class="fa-brands fa-facebook mr-2 fs-16"></i>{{ __('Sign In with Facebook') }}</a>@endif
                                                @if(config('services.twitter.enable') == 'on')<a href="{{ url('/auth/redirect/twitter') }}" class="social-login-button" id="login-twitter"><i class="fa-brands fa-twitter mr-2 fs-16"></i>{{ __('Sign In with Twitter') }}</a>@endif	
                                                @if(config('services.google.enable') == 'on')<a href="{{ url('/auth/redirect/google') }}" class="social-login-button" id="login-google"><i class="fa-brands fa-google mr-2 fs-16"></i>{{ __('Sign In with Google') }}</a>@endif	
                                                @if(config('services.linkedin.enable') == 'on')<a href="{{ url('/auth/redirect/linkedin') }}" class="social-login-button" id="login-linkedin"><i class="fa-brands fa-linkedin mr-2 fs-16"></i>{{ __('Sign In with Linkedin') }}</a>@endif	
                                            </div>

                                            <div class="divider">
                                                <div class="divider-text text-muted">
                                                    <small>{{ __('or register with email') }}</small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="input-box mb-4">                             
                                            <label for="name" class="fs-12 font-weight-bold text-md-right">{{ __('Full Name') }}</label>
                                            <input id="name" type="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="off" autofocus placeholder="{{ __('First and Last Names') }}">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror                            
                                        </div>

                                        <div class="input-box mb-4">                             
                                            <label for="email" class="fs-12 font-weight-bold text-md-right">{{ __('Email Address') }}</label>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="off"  placeholder="{{ __('Email Address') }}" required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror                            
                                        </div>

                                        <div class="input-box mb-4">                             
                                            <label for="country" class="fs-12 font-weight-bold text-md-right">{{ __('Country') }}</label>
                                            <select id="user-country" name="country" data-placeholder="{{ __('Select Your Country') }}" required>	
                                                @foreach(config('countries') as $value)
                                                    <option value="{{ $value }}" @if(config('settings.default_country') == $value) selected @endif>{{ $value }}</option>
                                                @endforeach										
                                            </select>
                                            @error('country')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror                            
                                        </div>

                                        <div class="input-box">                            
                                            <label for="password-input" class="fs-12 font-weight-bold text-md-right">{{ __('Password') }}</label>
                                            <input id="password-input" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="off" placeholder="{{ __('Password') }}">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror                            
                                        </div>

                                        <div class="input-box">
                                            <label for="password-confirm" class="fs-12 font-weight-bold text-md-right">{{ __('Confirm Password') }}</label>                       
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="off" placeholder="{{ __('Confirm Password') }}">                        
                                        </div>

                                        <div class="form-group mb-2">  
                                            <div class="d-flex">                        
                                                <label class="custom-switch">
                                                    <input type="checkbox" class="custom-switch-input" name="agreement" id="agreement" {{ old('remember') ? 'checked' : '' }} required>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description fs-10 text-muted">{{__('By continuing, I agree with your Terms and Conditions and Privacy Policies')}}</span>
                                                </label>   
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">  
                                            <div class="d-flex">                        
                                                <label class="custom-switch">
                                                    <input type="checkbox" class="custom-switch-input" name="newsletter" id="newsletter" {{ old('remember') ? 'checked' : '' }} checked>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description fs-10 text-muted">{{__('I agree to receive newsletters via email')}}</span>
                                                </label>   
                                            </div>
                                        </div>

                                        <input type="hidden" name="recaptcha" id="recaptcha">

                                        <div class="text-center">
                                            <div class="form-group mb-0">                        
                                                <button type="submit" class="btn btn-primary font-weight-bold login-main-button" id="register-button">{{ __('Sign Up') }}</button>              
                                            </div>                        
                                        
                                            <p class="fs-10 text-muted pt-3 mb-0">{{ __('Already have an account?') }}</p>
                                            <div class="text-center">
                                                <a href="{{ route('login') }}"  class="fs-12 font-weight-bold special-action-sign">{{ __('Sign In') }}</a>      
                                            </div>                                                                                   
                                        </div>
                                    </form>
                                </div> 
                            </div>      
                        </div>
                    </div>
                        
                    <div class="col-md-6 col-sm-12 text-center background-special align-middle p-0" id="login-background">
                        <div class="login-bg">
                            <img src="{{ theme_url('img/frontend/backgrounds/login.webp') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        @else
            <h5 class="text-center pt-9">{{__('New user registration is disabled currently')}}</h5>
        @endif
    @endif
@endsection

@section('js')
	<!-- Awselect JS -->
	<script src="{{URL::asset('plugins/awselect/awselect.min.js')}}"></script>
	<script src="{{theme_url('js/awselect.js')}}"></script>
    @if (config('services.google.recaptcha.enable') == 'on')
         <!-- Google reCaptcha JS -->
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.google.recaptcha.site_key') }}"></script>
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('services.google.recaptcha.site_key') }}', {action: 'contact'}).then(function(token) {
                    if (token) {
                    document.getElementById('recaptcha').value = token;
                    }
                });
            });
        </script>
    @endif

    <script type="text/javascript">
        let loading = `<span class="loading">
					<span style="background-color: #fff;"></span>
					<span style="background-color: #fff;"></span>
					<span style="background-color: #fff;"></span>
					</span>`;

        function process() {
            $('#register-button').prop('disabled', true);
            let btn = document.getElementById('register-button');					
            btn.innerHTML = loading;  
            document.querySelector('#loader-line')?.classList?.remove('opacity-on'); 
            return; 
        }
    </script>
   
@endsection

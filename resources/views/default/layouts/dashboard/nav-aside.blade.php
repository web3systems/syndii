<!-- SIDE MENU BAR -->
<aside class="app-sidebar"> 
    <div class="app-sidebar__logo">
        <a class="header-brand" href="{{url('/')}}">
            <img src="{{ URL::asset($settings->logo_dashboard)}}" class="header-brand-img desktop-lgo" alt="Dashboard Logo">
            <img src="{{ URL::asset($settings->logo_dashboard_collapsed)}}" class="header-brand-img mobile-logo" alt="Dashboard Logo">
        </a>
        <div class="app-sidebar__toggle" data-toggle="sidebar">
            <a class="open-toggle" href="#">
                <svg class="w-4 menu-toggle-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 6l-6 6l6 6"></path>
                  </svg>
            </a>
        </div>
    </div>
    <ul class="side-menu app-sidebar3">

        @php
            $menuController = new \App\Http\Controllers\Admin\MenuController();
            $menuUserItems = $menuController->getUserMenu();
            $menuAdminItems = $menuController->getAdminMenu();
        @endphp

        @foreach($menuUserItems as $item)
            @if ($item['type'] == 'label')
                @if ($loop->first)
                    <li class="side-item side-item-category mt-3 mb-3">{{ __($item['label']) }}</li>
                @else
                    <li class="side-item side-item-category mt-4 mb-3">{{ __($item['label']) }}</li>
                @endif
            @elseif ($item['type'] == 'divider')
                <hr class="w-90 text-center ml-auto mr-auto mt-3">
            @else
                @if ($item['has_access'])
                    <li class="slide">
                        @if(!empty($item['children']))
                            <a class="side-menu__item" data-toggle="slide" href="{{ $item['url'] ?? '#' }}">
                                <span class="side-menu__icon {{ $item['icon'] }}"></span>                    
                                <span class="side-menu__label">{{ __($item['label']) }}</span>
                                @if(!empty($item['badge_text']))
                                    <span class="badge badge-{{ $item['badge_type'] ?? 'primary' }}">{{ $item['badge_text'] }}</span>
                                @endif
                                <i class="angle fa fa-angle-right"></i>
                            </a>
                            <ul class="slide-menu">
                                @foreach($item['children'] as $child)
                                    <li>
                                        <a href="{{ $child['route'] ? route($child['route']) : $child['url'] }}" class="slide-item">@if (!is_null($child['icon'])) <i class="slide-child-icon {{$child['icon']}}"></i> @endif{{ __($child['label']) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a class="side-menu__item" href="{{ $item['route'] ? route($item['route']) : $item['url'] }}">
                                <span class="side-menu__icon {{ $item['icon'] }}"></span>                        
                                <span class="side-menu__label">{{ __($item['label']) }}</span>
                                @if(!empty($item['badge_text']))
                                    <span class="badge badge-{{ $item['badge_type'] ?? 'primary' }}">{{ $item['badge_text'] }}</span>
                                @endif
                            </a>
                        @endif
                    </li>
                @endif                
            @endif
        @endforeach

        @role('admin')
            @foreach($menuAdminItems as $item)
                @if ($item['type'] == 'label')
                    <li class="side-item side-item-category mt-4 mb-3">{{ __($item['label']) }}</li>
                @elseif ($item['type'] == 'divider')
                    <hr class="w-90 text-center ml-auto mr-auto mt-3">
                @else
                    @if ($item['has_access'])
                        <li class="slide">
                            @if(!empty($item['children']))
                                <a class="side-menu__item" data-toggle="slide" href="{{ $item['url'] ?? '#' }}">
                                    <span class="side-menu__icon {{ $item['icon'] }}"></span>                    
                                    <span class="side-menu__label">{{ __($item['label']) }}</span>
                                    @if(!empty($item['badge_text']))
                                        <span class="badge badge-{{ $item['badge_type'] ?? 'primary' }}">{{ $item['badge_text'] }}</span>
                                    @endif
                                    <i class="angle fa fa-angle-right"></i>
                                </a>
                                <ul class="slide-menu">
                                    @foreach($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['route'] ? route($child['route']) : $child['url'] }}" class="slide-item">@if (!is_null($child['icon'])) <i class="slide-child-icon {{$child['icon']}}"></i> @endif{{ __($child['label']) }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <a class="side-menu__item" href="{{ $item['route'] ? route($item['route']) : $item['url'] }}">
                                    <span class="side-menu__icon {{ $item['icon'] }}"></span>                        
                                    <span class="side-menu__label">{{ __($item['label']) }}</span>
                                    @if(!empty($item['badge_text']))
                                        <span class="badge badge-{{ $item['badge_type'] ?? 'primary' }}">{{ $item['badge_text'] }}</span>
                                    @endif
                                </a>
                            @endif
                        </li>
                    @endif                
                @endif
            @endforeach
        @endrole
        
        <hr class="w-90 text-center ml-auto mr-auto mt-3">
        
        <div class="side-progress-position mt-4">
            <div class="side-plan-wrapper text-center pt-3 pb-3">
                @if (App\Services\HelperService::extensionSaaS())
                    <span class="side-item side-item-category mt-4">{{ __('Plan') }}: @if (is_null(auth()->user()->plan_id))<span class="text-primary">{{ __('No Active Subscription') }}</span> @else <span class="text-primary">{{ __(App\Services\HelperService::getPlanName())}}</span>  @endif </span>
                @endif
                <div class="view-credits @if (App\Services\HelperService::extensionSaaS()) mt-1 @endif"><a class=" fs-11 text-muted mb-2" href="javascript:void(0)" id="view-credits" data-bs-toggle="modal" data-bs-target="#creditsModel"><i class="fa-solid fa-coin-front text-yellow "></i> {{ __('View Credits') }}</a></div> 
                @if (App\Services\HelperService::extensionSaaS())
                    @if (is_null(auth()->user()->plan_id))
                        <div class="text-center mt-3 mb-2"><a href="{{ route('user.plans') }}" class="btn btn-primary btn-primary-small pl-6 pr-6 fs-11"> <i class="fa-solid fa-bolt text-yellow mr-2"></i> {{ __('Upgrade') }}</a></div> 
                    @endif              
                @endif              
            </div>
            @if (App\Services\HelperService::extensionSaaS())
                @if (config('payment.referral.enabled') == 'on')
                    <div class="side-plan-wrapper mt-4 text-center p-3 pl-5 pr-5">
                        <div class="mb-1"><i class="fa-solid fa-gifts fs-20 text-yellow"></i></div>
                        <span class="fs-12 mt-4" style="color: #344050">{{ __('Invite your friends and get') }} {{ config('payment.referral.payment.commission') }}% @if (config('payment.referral.payment.policy') == 'all') {{ __('of all their purchases') }} @else {{ __('of their first purchase') }}@endif</span>
                        <div class="text-center mt-3 mb-2"><a href="{{ route('user.referral') }}" class="btn btn-primary btn-primary-small pl-6 pr-6 fs-11" id="referral-button"> {{ __('Invite Friends') }}</a></div>              
                    </div>
                @endif
            @endif
        </div>
    </ul>
</aside>

<div class="modal fade" id="creditsModel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="text-center font-weight-bold fs-16"> {{ __('Credits on') }} {{ config('app.name') }}</h6>	
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pl-5 pr-5">
                
                <h6 class="font-weight-semibold mb-2 mt-3">{{ __('Unlock your creativity with') }} {{ config('app.name') }} {{ __('credits') }}</h6>
                <p class="text-muted">{{ __('Maximize your content creation with') }} {{ config('app.name') }}. {{ __('Each credit unlocks powerful AI tools and features designed to enhance your content creation.') }}</p>
                
                <div class="d-flex justify-content-between mt-3">
                    <div class="font-weight-bold fs-12">{{ __('AI Model') }}</div>
                    <div class="font-weight-bold fs-12">{{ __('Credits') }}</div>
                </div>
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10"> @if ($settings->model_credit_name == 'words') {{ __('Words') }} @else {{ __('Tokens') }} @endif <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-words=''></i></div>
                    <div class="text-muted fs-10">{{ \App\Services\HelperService::getTotalWords()}}</div>
                </div>                
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10">{{ __('Media Credits') }} <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-images=''></i></div>
                    <div class="text-muted fs-10">{{ \App\Services\HelperService::getTotalImages()}}</div>
                </div>
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10">{{ __('Characters') }} <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-characters=''></i></div>
                    <div class="text-muted fs-10">{{ App\Services\HelperService::getTotalCharacters()}}</div>
                </div>
                <hr class="mt-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div class="text-muted fs-10">{{ __('Minutes') }} <i class="ml-2 text-dark fs-13 fa-solid fa-circle-info" data-tippy-minutes=''></i></div>
                    <div class="text-muted fs-10">{{ App\Services\HelperService::getTotalMinutes()}}</div>
                </div>
               
                @if (App\Services\HelperService::extensionSaaS())
                    <div class="text-center mt-4"><a href="{{ route('user.plans') }}" class="btn btn-primary pl-6 pr-6 fs-11" style="text-transform: none"> <i class="fa-solid fa-bolt text-yellow mr-2"></i> {{ __('Upgrade Now') }}</a></div> 
                @endif
            </div>
        </div>
        <div id="nav-info-words" style="display: none;">
            <span class="mb-4 text-underline"><strong class="mb-4">{{__('Valid For')}}:</strong></span><br>
            <span>{{__('AI Writer')}}</span><br>
            <span>{{__('AI Article Wizard')}}</span><br>
            <span>{{__('Smart Editor')}}</span><br>
            <span>{{__('AI ReWriter')}}</span><br>
            <span>{{__('AI Chat')}}</span><br>
            <span>{{__('AI File Chat')}}</span><br>
            <span>{{__('AI Web Chat')}}</span><br>
            <span>{{__('AI Youtube')}}</span><br>
            <span>{{__('AI RSS')}}</span><br>
            <span>{{__('AI Code')}}</span><br>
            <span>{{__('AI Vision')}}</span><br>
        </div>
        <div id="nav-info-images" style="display: none;">
            <strong class="mb-4 underline">{{__('Valid For')}}:</strong><br>
            <span>{{__('AI Avatar')}}</span><br>
            <span>{{__('AI Images')}}</span><br>
            <span>{{__('AI Video Image')}}</span><br>
            <span>{{__('AI Video Text')}}</span><br>
            <span>{{__('AI Video Video')}}</span><br>
            <span>{{__('AI Photo Studio')}}</span><br>
            <span>{{__('AI Product Photo')}}</span><br>
            <span>{{__('Faceswap')}}</span><br>
            <span>{{__('AI Music')}}</span><br>
        </div>
        <div id="nav-info-characters" style="display: none;">
            <strong class="mb-4 underline">{{__('Valid For')}}:</strong><br>
            <span>{{__('AI Text to Speech')}}</span><br>
            <span>{{__('Voice Cloning')}}</span><br>
            <span>{{__('Voice Isolator')}}</span><br>
        </div>
        <div id="nav-info-minutes" style="display: none;">
            <strong class="mb-4 underline">{{__('Valid For')}}:</strong><br>
            <span>{{__('AI Speech To Text')}}</span><br>
        </div>
    </div>
</div>
<!-- END SIDE MENU BAR -->
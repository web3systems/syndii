<!-- JQuery-->
<script src="{{URL::asset('plugins/jquery/jquery-3.6.0.min.js')}}"></script>

<!-- Bootstrap 5-->
<script src="{{URL::asset('plugins/bootstrap-5.0.2/js/bootstrap.bundle.min.js')}}"></script>

<!-- Toastr JS -->
<script src="{{URL::asset('plugins/toastr/toastr.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.umd.js"></script> 

@yield('js')

<!-- Google Analytics -->
@if (config('services.google.analytics.enable') == 'on')
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics.id') }}"></script>
    <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{ config('services.google.analytics.id') }}');
    </script>
@endif

<!-- Live Chat -->
@if (config('settings.live_chat') == 'on')
    <script type="text/javascript">
        let link = "{{ config('settings.live_chat_link') }}";
        let embed_link = link.replace('https://tawk.to/chat/', 'https://embed.tawk.to/');

        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src=embed_link;
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
@endif

<!-- Custom User JS File -->
@if (isset($frontend_settings))
    @if (!is_null($frontend_settings->custom_js_url)) 
        <script src="{{ $frontend_settings->custom_js_url }}"></script>
    @endif
@endif

<script type="text/javascript">

    @if ($cookie_settings->enable_cookies ?? true)
        @if ($cookie_settings->enable_dark_mode ?? false)
            document.documentElement.classList.add('cc--darkmode');
        @endif
        
        CookieConsent.run({

            // root: 'body',
            autoShow: true,
            disablePageInteraction: {{$cookie_settings->disable_page_interaction ?? false }},
            hideFromBots: {{$cookie_settings->hide_from_bots ?? true }},
            mode: 'opt-in',
            // revision: 0,

            cookie: {
                name: 'cc_cookie',
                // domain: location.hostname,
                // path: '/',
                // sameSite: "Lax",
                expiresAfterDays: {{$cookie_settings->days ?? 7 }},
            },

            // https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions
            guiOptions: {
                consentModal: {
                    layout: '{{$cookie_settings->consent_modal_layouts ?? "box wide"}}',
                    position: '{{$cookie_settings->consent_modal_position ?? "bottom center"}}',
                    equalWeightButtons: true,
                    flipButtons: false
                },
                preferencesModal: {
                    layout: '{{$cookie_settings->preferences_modal_layout ?? "box"}}',
                    position: '{{$cookie_settings->preferences_modal_position ?? "right"}}',
                    equalWeightButtons: true,
                    flipButtons: false
                }
            },
            categories: {
                necessary: {
                    enabled: true,  // this category is enabled by default
                    readOnly: true  // this category cannot be disabled
                },
                functionality: {},
                analytics: {
                    autoClear: {
                        cookies: [
                            {
                                name: /^_ga/,   // regex: match all cookies starting with '_ga'
                            },
                            {
                                name: '_gid',   // string: exact cookie name
                            }
                        ]
                    },

                    // https://cookieconsent.orestbida.com/reference/configuration-reference.html#category-services
                    services: {
                        ga: {
                            label: 'Google Analytics',
                            onAccept: () => {},
                            onReject: () => {}
                        },
                    }
                },
                ads: {}
            },

            language: {
                default: 'en',
                rtl: 'ar',  // enable RTL for Arabic
                autoDetect: 'document',

                translations: {
                    en: '{{URL::asset("plugins/cookies/translations/en.json")}}',
                    fr: '{{URL::asset("plugins/cookies/translations/fr.json")}}',
                    de: '{{URL::asset("plugins/cookies/translations/de.json")}}',
                    it: '{{URL::asset("plugins/cookies/translations/it.json")}}',
                    pt: '{{URL::asset("plugins/cookies/translations/pt.json")}}',
                    ru: '{{URL::asset("plugins/cookies/translations/ru.json")}}',
                    es: '{{URL::asset("plugins/cookies/translations/es.json")}}',
                    ar: '{{URL::asset("plugins/cookies/translations/ar.json")}}'
                }
            },

        });
    @endif
    
</script>
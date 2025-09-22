<!-- JQuery-->
<script src="<?php echo e(URL::asset('plugins/jquery/jquery-3.6.0.min.js')); ?>"></script>

<!-- Bootstrap 5-->
<script src="<?php echo e(URL::asset('plugins/bootstrap-5.0.2/js/bootstrap.bundle.min.js')); ?>"></script>

<!-- Toastr JS -->
<script src="<?php echo e(URL::asset('plugins/toastr/toastr.min.js')); ?>"></script>

<script src="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.umd.js"></script> 

<?php echo $__env->yieldContent('js'); ?>

<!-- Google Analytics -->
<?php if(config('services.google.analytics.enable') == 'on'): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(config('services.google.analytics.id')); ?>"></script>
    <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo e(config('services.google.analytics.id')); ?>');
    </script>
<?php endif; ?>

<!-- Live Chat -->
<?php if(config('settings.live_chat') == 'on'): ?>
    <script type="text/javascript">
        let link = "<?php echo e(config('settings.live_chat_link')); ?>";
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
<?php endif; ?>

<!-- Custom User JS File -->
<?php if(isset($frontend_settings)): ?>
    <?php if(!is_null($frontend_settings->custom_js_url)): ?> 
        <script src="<?php echo e($frontend_settings->custom_js_url); ?>"></script>
    <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">

    <?php if($cookie_settings->enable_cookies ?? true): ?>
        <?php if($cookie_settings->enable_dark_mode ?? false): ?>
            document.documentElement.classList.add('cc--darkmode');
        <?php endif; ?>
        
        CookieConsent.run({

            // root: 'body',
            autoShow: true,
            disablePageInteraction: <?php echo e($cookie_settings->disable_page_interaction ?? false); ?>,
            hideFromBots: <?php echo e($cookie_settings->hide_from_bots ?? true); ?>,
            mode: 'opt-in',
            // revision: 0,

            cookie: {
                name: 'cc_cookie',
                // domain: location.hostname,
                // path: '/',
                // sameSite: "Lax",
                expiresAfterDays: <?php echo e($cookie_settings->days ?? 7); ?>,
            },

            // https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions
            guiOptions: {
                consentModal: {
                    layout: '<?php echo e($cookie_settings->consent_modal_layouts ?? "box wide"); ?>',
                    position: '<?php echo e($cookie_settings->consent_modal_position ?? "bottom center"); ?>',
                    equalWeightButtons: true,
                    flipButtons: false
                },
                preferencesModal: {
                    layout: '<?php echo e($cookie_settings->preferences_modal_layout ?? "box"); ?>',
                    position: '<?php echo e($cookie_settings->preferences_modal_position ?? "right"); ?>',
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
                    en: '<?php echo e(URL::asset("plugins/cookies/translations/en.json")); ?>',
                    fr: '<?php echo e(URL::asset("plugins/cookies/translations/fr.json")); ?>',
                    de: '<?php echo e(URL::asset("plugins/cookies/translations/de.json")); ?>',
                    it: '<?php echo e(URL::asset("plugins/cookies/translations/it.json")); ?>',
                    pt: '<?php echo e(URL::asset("plugins/cookies/translations/pt.json")); ?>',
                    ru: '<?php echo e(URL::asset("plugins/cookies/translations/ru.json")); ?>',
                    es: '<?php echo e(URL::asset("plugins/cookies/translations/es.json")); ?>',
                    ar: '<?php echo e(URL::asset("plugins/cookies/translations/ar.json")); ?>'
                }
            },

        });
    <?php endif; ?>
    
</script><?php /**PATH /home/customer/www/syndii.net/public_html/resources/views/default/layouts/frontend/footer.blade.php ENDPATH**/ ?>
/**
 * All config. options available here:
 * https://cookieconsent.orestbida.com/reference/configuration-reference.html
 */
CookieConsent.run({

    // root: 'body',
    autoShow: true,
    // disablePageInteraction: true,
    // hideFromBots: true,
    mode: 'opt-in',
    // revision: 0,
    
    cookie: {
        name: 'cc_cookie',
        // domain: location.hostname,
        // path: '/',
        // sameSite: "Lax",
        // expiresAfterDays: 182,
    },
    
    // https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions
    guiOptions: {
        consentModal: {
            layout: 'cloud inline',
            position: 'bottom center',
            equalWeightButtons: true,
            flipButtons: false
        },
        preferencesModal: {
            layout: 'box',
            equalWeightButtons: true,
            flipButtons: false
        }
    },
    
    
    categories: {
        necessary: {
            enabled: true,  // this category is enabled by default
            readOnly: true  // this category cannot be disabled
        },
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
                }
            }
        },
        ads: {}
    },
    
    language: {
        default: 'en',
        rtl: 'ar',  // enable RTL for Arabic
        autoDetect: 'document',
    
        translations: {
            en: '/plugins/cookies/translations/en.json',
            fr: '/plugins/cookies/translations/fr.json',
            de: '/plugins/cookies/translations/de.json',
            it: '/plugins/cookies/translations/it.json',
            pt: '/plugins/cookies/translations/pt.json',
            ru: '/plugins/cookies/translations/ru.json',
            es: '/plugins/cookies/translations/es.json',
            ar: '/plugins/cookies/translations/ar.json'
        }
    },
    
});
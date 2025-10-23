<?php

return [

    /**
     * The locales you wish to support.
     */
    'supported_locales' => [], // ['de', 'en', .. ]

    'hide_default_locale' => true,

    'persist_locale' => [
        'session' => true,
        'cookie' => true,
    ],

    'detectors' => [
        \NielsNumbers\LocaleRouting\Detectors\UserDetector::class,
        \NielsNumbers\LocaleRouting\Detectors\BrowserDetector::class,
    ],
];

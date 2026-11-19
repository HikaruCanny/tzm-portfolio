<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public identity (Phase 0)
    |--------------------------------------------------------------------------
    |
    | Override in .env when your real links are ready. Seeders and default
    | profile fallbacks read these values.
    |
    */

    'name' => env('PORTFOLIO_NAME', 'Thant Zin Moe'),

    'title' => env('PORTFOLIO_TITLE', 'Full-Stack Developer (Laravel + Vue.js)'),

    'contact_email' => env('PORTFOLIO_CONTACT_EMAIL', 'thantzinmoedev07@gmail.com'),

    'phone' => env('PORTFOLIO_PHONE', '+959 447 334 223'),

    'github_url' => env('PORTFOLIO_GITHUB_URL', 'https://github.com/HikaruCanny'),

    'linkedin_url' => env('PORTFOLIO_LINKEDIN_URL', 'https://www.linkedin.com/in/thantzinmoe30'),

    'location' => env('PORTFOLIO_LOCATION', 'Myanmar · Open to global remote roles'),

    /*
    |--------------------------------------------------------------------------
    | Design tokens (keep in sync with tailwind.config.js + app.css @theme)
    |--------------------------------------------------------------------------
    */

    'design' => [
        'accent' => 'primary navy (#18283a)',
        'fonts' => ['Inter', 'Figtree'],
        'mono_font' => 'JetBrains Mono',
    ],

];

<?php

return [

    'site_name' => env('SEO_SITE_NAME', config('portfolio.name')),

    'default_description' => env(
        'SEO_DESCRIPTION',
        'Full-stack Laravel and Vue developer building maintainable APIs, Inertia apps, and recruiter-friendly portfolios.'
    ),

    'twitter_site' => env('SEO_TWITTER_HANDLE'),

    'default_og_image' => env('SEO_OG_IMAGE'),

];

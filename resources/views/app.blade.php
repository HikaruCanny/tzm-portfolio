<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script>
            (() => {
                const theme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (theme === 'dark' || (!theme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        @if (! empty($seo))
            <title>{{ $seo['title'] }}</title>
            <meta name="description" content="{{ $seo['description'] }}">
            <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">

            <meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}">
            <meta property="og:site_name" content="{{ $seo['og_site_name'] ?? config('seo.site_name') }}">
            <meta property="og:url" content="{{ $seo['og_url'] ?? url()->current() }}">
            <meta property="og:title" content="{{ $seo['og_title'] ?? $seo['title'] }}">
            <meta property="og:description" content="{{ $seo['og_description'] ?? $seo['description'] }}">
            @if (! empty($seo['og_image']))
                <meta property="og:image" content="{{ $seo['og_image'] }}">
            @endif

            <meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary_large_image' }}">
            @if (! empty($seo['twitter_site']))
                <meta name="twitter:site" content="{{ $seo['twitter_site'] }}">
            @endif
            <meta name="twitter:title" content="{{ $seo['og_title'] ?? $seo['title'] }}">
            <meta name="twitter:description" content="{{ $seo['og_description'] ?? $seo['description'] }}">
            @if (! empty($seo['og_image']))
                <meta name="twitter:image" content="{{ $seo['og_image'] }}">
            @endif
        @else
            <title inertia>{{ config('app.name', 'Laravel') }}</title>
        @endif

        @if (! empty($json_ld))
            <script type="application/ld+json">@json($json_ld)</script>
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="min-h-full font-sans antialiased text-slate-800 dark:text-slate-100">
        @inertia
    </body>
</html>
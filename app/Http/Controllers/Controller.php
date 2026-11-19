<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

abstract class Controller
{
    /**
     * @param  array<string, mixed>  $props
     * @param  array<string, mixed>  $seo
     */
    protected function inertiaWithSeo(string $page, array $props, array $seo): Response
    {
        return Inertia::render($page, $props)->withViewData([
            'seo' => $seo,
            'json_ld' => $seo['json_ld'] ?? null,
        ]);
    }
}

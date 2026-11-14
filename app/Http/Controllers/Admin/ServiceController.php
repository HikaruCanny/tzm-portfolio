<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::orderBy('sort_order')->get();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => ['nullable', 'string', Rule::in(Service::ICONS)],
            'sort_order' => 'integer|min:0',
        ]);

        Service::create($validated);

        return back()->with('success', 'Service added.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => ['nullable', 'string', Rule::in(Service::ICONS)],
            'sort_order' => 'integer|min:0',
        ]);

        $service->update($validated);

        return back()->with('success', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return back()->with('success', 'Service removed.');
    }
}

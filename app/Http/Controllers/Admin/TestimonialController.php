<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => Testimonial::query()->orderBy('sort_order')->orderByDesc('id')->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Testimonial::query()->create($this->validated($request));

        return back()->with('success', 'Testimonial added.');
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->validated($request));

        return back()->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return back()->with('success', 'Testimonial removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'author_name' => 'required|string|max:255',
            'author_role' => 'nullable|string|max:255',
            'quote' => 'required|string|max:1000',
            'sort_order' => 'integer|min:0',
            'is_published' => 'boolean',
        ]);
    }
}

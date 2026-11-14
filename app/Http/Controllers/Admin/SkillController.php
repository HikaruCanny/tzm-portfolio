<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SkillController extends Controller
{
    public function index(): Response
    {
        $categories = SkillCategory::with('skills')->orderBy('sort_order')->get();

        return Inertia::render('Admin/Skills/Index', [
            'categories' => $categories,
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'integer|min:0',
        ]);

        SkillCategory::create($validated);

        return back()->with('success', 'Skill category created.');
    }

    public function destroyCategory(SkillCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Skill category removed.');
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skill_category_id' => 'required|exists:skill_categories,id',
            'name' => 'required|string|max:100',
            'level' => 'nullable|integer|min:1|max:100',
            'sort_order' => 'integer|min:0',
        ]);

        Skill::create($validated);

        return back()->with('success', 'Skill added.');
    }

    public function destroySkill(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return back()->with('success', 'Skill removed.');
    }
}

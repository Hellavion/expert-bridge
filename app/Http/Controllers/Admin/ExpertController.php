<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpertController extends Controller
{
    public function index(): Response
    {
        $experts = Expert::withCount(['curators', 'products', 'promocodes'])
            ->latest()
            ->get();

        return Inertia::render('admin/Experts/Index', [
            'experts' => $experts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Experts/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'telegram_login' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'expert_bonus' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Expert::create($validated);

        return redirect()->route('admin.experts.index')
            ->with('success', 'Эксперт успешно создан');
    }

    public function edit(Expert $expert): Response
    {
        return Inertia::render('admin/Experts/Edit', [
            'expert' => $expert,
        ]);
    }

    public function update(Request $request, Expert $expert)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'telegram_login' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'expert_bonus' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $expert->update($validated);

        return redirect()->route('admin.experts.index')
            ->with('success', 'Эксперт успешно обновлен');
    }

    public function destroy(Expert $expert)
    {
        $expert->delete();

        return redirect()->route('admin.experts.index')
            ->with('success', 'Эксперт успешно удален');
    }
}

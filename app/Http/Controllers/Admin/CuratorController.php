<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curator;
use App\Models\Expert;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CuratorController extends Controller
{
    public function index(): Response
    {
        $curators = Curator::with('expert')
            ->withCount('promocodes')
            ->latest()
            ->get();

        return Inertia::render('admin/Curators/Index', [
            'curators' => $curators,
        ]);
    }

    public function create(): Response
    {
        $experts = Expert::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('admin/Curators/Create', [
            'experts' => $experts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'telegram_login' => 'nullable|string|max:255',
            'curator_bonus' => 'nullable|string',
            'expert_id' => 'required|exists:experts,id',
            'comment' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Curator::create($validated);

        return redirect()->route('admin.curators.index')
            ->with('success', 'Куратор успешно создан');
    }

    public function edit(Curator $curator): Response
    {
        $experts = Expert::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('admin/Curators/Edit', [
            'curator' => $curator->load('expert'),
            'experts' => $experts,
        ]);
    }

    public function update(Request $request, Curator $curator)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'telegram_login' => 'nullable|string|max:255',
            'curator_bonus' => 'nullable|string',
            'expert_id' => 'required|exists:experts,id',
            'comment' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $curator->update($validated);

        return redirect()->route('admin.curators.index')
            ->with('success', 'Куратор успешно обновлен');
    }

    public function destroy(Curator $curator)
    {
        $curator->delete();

        return redirect()->route('admin.curators.index')
            ->with('success', 'Куратор успешно удален');
    }
}

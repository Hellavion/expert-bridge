<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curator;
use App\Models\Expert;
use App\Models\Promocode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PromocodeController extends Controller
{
    public function index(): Response
    {
        $promocodes = Promocode::with(['expert', 'curator'])
            ->latest()
            ->get();

        return Inertia::render('admin/Promocodes/Index', [
            'promocodes' => $promocodes,
        ]);
    }

    public function create(): Response
    {
        $experts = Expert::where('is_active', true)->get(['id', 'name']);
        $curators = Curator::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('admin/Promocodes/Create', [
            'experts' => $experts,
            'curators' => $curators,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:50',
            'expert_id' => 'required|exists:experts,id',
            'curator_id' => 'nullable|exists:curators,id',
            'is_active' => 'boolean',
        ]);

        // Генерируем уникальный код: PREFIX-12345
        do {
            $randomPart = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $code = strtoupper($validated['prefix']) . '-' . $randomPart;
        } while (Promocode::where('code', $code)->exists());

        Promocode::create([
            'code' => $code,
            'expert_id' => $validated['expert_id'],
            'curator_id' => $validated['curator_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'usage_count' => 0,
        ]);

        return redirect()->route('admin.promocodes.index')
            ->with('success', "Промокод {$code} успешно создан");
    }

    public function edit(Promocode $promocode): Response
    {
        $experts = Expert::where('is_active', true)->get(['id', 'name']);
        $curators = Curator::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('admin/Promocodes/Edit', [
            'promocode' => $promocode->load(['expert', 'curator']),
            'experts' => $experts,
            'curators' => $curators,
        ]);
    }

    public function update(Request $request, Promocode $promocode)
    {
        $validated = $request->validate([
            'expert_id' => 'required|exists:experts,id',
            'curator_id' => 'nullable|exists:curators,id',
            'is_active' => 'boolean',
        ]);

        $promocode->update($validated);

        return redirect()->route('admin.promocodes.index')
            ->with('success', 'Промокод успешно обновлен');
    }

    public function destroy(Promocode $promocode)
    {
        $promocode->delete();

        return redirect()->route('admin.promocodes.index')
            ->with('success', 'Промокод успешно удален');
    }
}

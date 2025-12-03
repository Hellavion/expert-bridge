<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::with('expert')
            ->latest()
            ->get();

        return Inertia::render('admin/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        $experts = Expert::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('admin/Products/Create', [
            'experts' => $experts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'expert_id' => 'required|exists:experts,id',
        ]);

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продукт успешно создан');
    }

    public function edit(Product $product): Response
    {
        $experts = Expert::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('admin/Products/Edit', [
            'product' => $product->load('expert'),
            'experts' => $experts,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'expert_id' => 'required|exists:experts,id',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продукт успешно обновлен');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Продукт успешно удален');
    }
}

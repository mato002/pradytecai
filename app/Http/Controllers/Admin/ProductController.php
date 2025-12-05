<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(): View
    {
        // For now, return a simple view - can be extended when Product model is created
        return view('admin.products.index');
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // TODO: Create Product model and save
        // For now, just redirect with success message
        // Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully. (Note: Product model needs to be created to persist data)');
    }

    public function edit($id): View
    {
        // TODO: Load product when model exists
        // $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('id'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // TODO: Update product when model exists
        // $product = Product::findOrFail($id);
        // $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully. (Note: Product model needs to be created to persist data)');
    }

    public function destroy($id): RedirectResponse
    {
        // TODO: Delete product when model exists
        // $product = Product::findOrFail($id);
        // $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully. (Note: Product model needs to be created to persist data)');
    }
}



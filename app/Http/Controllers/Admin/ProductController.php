<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::ordered();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Handle export
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        $products = $query->get();

        // Distinct types for filter dropdown
        $types = Product::select('type')
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        return view('admin.products.index', compact('products', 'types'));
    }

    /**
     * Export products to CSV
     */
    private function exportCsv($products)
    {
        $filename = 'products_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Description', 'Type', 'URL', 'Active', 'Order', 
                'Features', 'Benefits', 'Button Text', 'Icon', 'Created At'
            ]);

            // Add data rows
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->description ?? '',
                    $product->type ?? '',
                    $product->url ?? '',
                    $product->is_active ? 'Yes' : 'No',
                    $product->order ?? '',
                    is_array($product->features) ? implode('; ', $product->features) : '',
                    is_array($product->benefits) ? implode('; ', $product->benefits) : '',
                    $product->button_text ?? '',
                    $product->icon ?? '',
                    $product->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'order' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['nullable', 'string'],
            'statistics' => ['nullable', 'array'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? Product::max('order') + 1;

        // Convert features text to array
        if ($request->has('features_text') && $request->features_text) {
            $data['features'] = array_filter(array_map('trim', explode("\n", $request->features_text)));
        }

        // Convert benefits text to array
        if ($request->has('benefits_text') && $request->benefits_text) {
            $data['benefits'] = array_filter(array_map('trim', explode("\n", $request->benefits_text)));
        }

        // Convert statistics JSON
        if ($request->has('statistics_json') && $request->statistics_json) {
            $statistics = json_decode($request->statistics_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['statistics'] = $statistics;
            }
        }

        $product = Product::create($data);
        
        ActivityLogService::created($product, "Created product: {$product->name}");

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['nullable', 'string'],
            'statistics' => ['nullable', 'array'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // Convert features text to array
        if ($request->has('features_text') && $request->features_text) {
            $data['features'] = array_filter(array_map('trim', explode("\n", $request->features_text)));
        }

        // Convert benefits text to array
        if ($request->has('benefits_text') && $request->benefits_text) {
            $data['benefits'] = array_filter(array_map('trim', explode("\n", $request->benefits_text)));
        }

        // Convert statistics JSON
        if ($request->has('statistics_json') && $request->statistics_json) {
            $statistics = json_decode($request->statistics_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['statistics'] = $statistics;
            }
        }

        $oldValues = $product->getAttributes();
        $product->update($data);
        
        ActivityLogService::updated($product, $oldValues, "Updated product: {$product->name}");

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $productName = $product->name;
        ActivityLogService::deleted($product, "Deleted product: {$productName}");
        
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'enabled' : 'disabled';
        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product {$status} successfully.");
    }
}



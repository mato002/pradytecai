<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->visibleTo($request->user())->ordered();

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        $products = $query->get();

        $types = Product::query()
            ->visibleTo($request->user())
            ->select('type')
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        return view('admin.products.index', compact('products', 'types'));
    }

    private function exportCsv($products)
    {
        $filename = 'products_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Name', 'Slug', 'Short', 'Market', 'Description', 'Type', 'URL', 'Active', 'Order',
                'Features', 'Benefits', 'Button Text', 'Icon', 'Last Marketed', 'Created At',
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->slug ?? '',
                    $product->short ?? '',
                    $product->market ?? '',
                    $product->description ?? '',
                    $product->type ?? '',
                    $product->url ?? '',
                    $product->is_active ? 'Yes' : 'No',
                    $product->order ?? '',
                    is_array($product->features) ? implode('; ', $product->features) : '',
                    is_array($product->benefits) ? implode('; ', $product->benefits) : '',
                    $product->button_text ?? '',
                    $product->icon ?? '',
                    $product->last_marketed_at?->format('Y-m-d H:i:s'),
                    $product->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? ((int) Product::max('order') + 1);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $this->applyListFields($request, $data);

        $product = Product::create($data);

        ActivityLogService::created($product, "Created product: {$product->name}");

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $this->validated($request, $product);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $this->applyListFields($request, $data);

        $oldValues = $product->getAttributes();
        $product->update($data);

        ActivityLogService::updated($product, $oldValues, "Updated product: {$product->name}");

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $productName = $product->name;
        ActivityLogService::deleted($product, "Deleted product: {$productName}");

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        $status = $product->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product {$status} successfully.");
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $slugRule = 'nullable|string|max:255|unique:products,slug';
        if ($product) {
            $slugRule .= ','.$product->id;
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
            'short' => ['nullable', 'string', 'max:500'],
            'market' => ['nullable', 'string', 'max:255'],
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
    }

    private function applyListFields(Request $request, array &$data): void
    {
        if ($request->filled('features_text')) {
            $data['features'] = array_values(array_filter(array_map('trim', explode("\n", $request->features_text))));
        }

        if ($request->filled('benefits_text')) {
            $data['benefits'] = array_values(array_filter(array_map('trim', explode("\n", $request->benefits_text))));
        }

        if ($request->filled('statistics_json')) {
            $statistics = json_decode($request->statistics_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['statistics'] = $statistics;
            }
        }
    }
}

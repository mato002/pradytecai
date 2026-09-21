<form method="GET" action="{{ url()->current() }}" class="inline-flex items-center gap-2">
    @foreach(request()->except('product_filter') as $key => $value)
        @if(is_array($value))
            @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <label class="sr-only" for="product_filter">Product filter</label>
    <select id="product_filter" name="product_filter" onchange="this.form.submit()"
            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800">
        <option value="all" {{ empty($activeProductFilter) ? 'selected' : '' }}>All Products</option>
        @foreach(($filterProducts ?? []) as $product)
            <option value="{{ $product->id }}" {{ (int) ($activeProductFilter ?? 0) === (int) $product->id ? 'selected' : '' }}>
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</form>

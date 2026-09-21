@php
    $c = $campaign ?? null;
    $selected = $selectedProductIds ?? ($c?->products?->pluck('id')->all() ?? []);
@endphp
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-1">Name *</label>
    <input name="name" value="{{ old('name', $c->name ?? '') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-1">Objective</label>
    <input name="objective" value="{{ old('objective', $c->objective ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Owner</label>
        <select name="owner_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">—</option>
            @foreach($owners as $owner)
                <option value="{{ $owner->id }}" @selected(old('owner_id', $c->owner_id ?? '') == $owner->id)>{{ $owner->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Status *</label>
        <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $c->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Starts at</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($c->starts_at ?? null)?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Ends at</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($c->ends_at ?? null)?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
    </div>
</div>
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-1">Budget</label>
    <input type="number" step="0.01" name="budget_amount" value="{{ old('budget_amount', $c->budget_amount ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-1">UTM campaign</label>
    <input name="utm_campaign" value="{{ old('utm_campaign', $c->utm_campaign ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-1">Audience notes</label>
    <textarea name="audience_notes" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('audience_notes', $c->audience_notes ?? '') }}</textarea>
</div>
<div>
    <label class="block text-sm font-semibold text-slate-700 mb-2">Products</label>
    <div class="grid sm:grid-cols-2 gap-2">
        @foreach($products as $product)
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" @checked(in_array($product->id, old('product_ids', $selected)))>
                {{ $product->name }}
            </label>
        @endforeach
    </div>
</div>

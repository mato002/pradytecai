@php $a = $account ?? null; @endphp
<div>
    <label class="block text-sm font-semibold mb-1">Name *</label>
    <input name="name" required value="{{ old('name', $a->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold mb-1">Platform *</label>
        <select name="platform" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            @foreach($platforms as $platform)
                <option value="{{ $platform }}" @selected(old('platform', $a->platform ?? '') === $platform)>{{ ucfirst($platform) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Status *</label>
        <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            @foreach(['active','inactive','error'] as $s)
                <option value="{{ $s }}" @selected(old('status', $a->status ?? 'active') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold mb-1">Product</label>
        <select name="product_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">—</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $a->product_id ?? '') == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Integration</label>
        <select name="integration_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">—</option>
            @foreach($integrations as $integration)
                <option value="{{ $integration->id }}" @selected(old('integration_id', $a->integration_id ?? '') == $integration->id)>{{ $integration->provider }} ({{ $integration->external_account_name }})</option>
            @endforeach
        </select>
    </div>
</div>
<input name="external_id" value="{{ old('external_id', $a->external_id ?? '') }}" placeholder="External ID" class="w-full rounded-lg border border-slate-300 px-3 py-2">
<select name="token_status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
    @foreach(['unknown','valid','expired','revoked'] as $s)
        <option value="{{ $s }}" @selected(old('token_status', $a->token_status ?? 'unknown') === $s)>{{ ucfirst($s) }}</option>
    @endforeach
</select>
<div class="flex flex-wrap gap-4 text-sm">
    <label><input type="checkbox" name="can_publish" value="1" @checked(old('can_publish', $a->can_publish ?? true))> Can publish</label>
    <label><input type="checkbox" name="can_analytics" value="1" @checked(old('can_analytics', $a->can_analytics ?? true))> Can analytics</label>
    <label><input type="checkbox" name="can_inbox" value="1" @checked(old('can_inbox', $a->can_inbox ?? false))> Can inbox</label>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $a->is_active ?? true))> Active</label>
</div>

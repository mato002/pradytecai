@php
    $itemModel = $item ?? null;
    $selected = $selectedAccountIds ?? [];
@endphp
<div>
    <label class="block text-sm font-semibold mb-1">Title *</label>
    <input name="title" required value="{{ old('title', $itemModel->title ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold mb-1">Product</label>
        <select name="product_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">—</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $itemModel->product_id ?? '') == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Campaign</label>
        <select name="campaign_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">—</option>
            @foreach($campaigns as $campaign)
                <option value="{{ $campaign->id }}" @selected(old('campaign_id', $itemModel->campaign_id ?? '') == $campaign->id)>{{ $campaign->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<textarea name="caption" rows="3" placeholder="Caption" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('caption', $itemModel->caption ?? '') }}</textarea>
<textarea name="body" rows="5" placeholder="Body" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('body', $itemModel->body ?? '') }}</textarea>
<div class="grid md:grid-cols-2 gap-4">
    <input name="cta_label" value="{{ old('cta_label', $itemModel->cta_label ?? '') }}" placeholder="CTA label" class="rounded-lg border border-slate-300 px-3 py-2">
    <input name="destination_url" value="{{ old('destination_url', $itemModel->destination_url ?? '') }}" placeholder="Destination URL" class="rounded-lg border border-slate-300 px-3 py-2">
</div>
<div>
    <label class="block text-sm font-semibold mb-2">Social destinations</label>
    <div class="grid sm:grid-cols-2 gap-2">
        @foreach($socialAccounts as $account)
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="social_account_ids[]" value="{{ $account->id }}" @checked(in_array($account->id, old('social_account_ids', $selected)))>
                {{ $account->name }} ({{ $account->platform }})
            </label>
        @endforeach
    </div>
</div>

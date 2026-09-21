@extends(admin_layout())

@section('title', 'Create Campaign - Pradytecai')
@section('page_title', 'Create Campaign')
@section('page_eyebrow', 'Marketing')

@section('content')
<form method="POST" action="{{ isset($campaign) ? route('admin.campaigns.update', $campaign) : route('admin.campaigns.store') }}" class="glass-card space-y-4 max-w-3xl">
    @csrf
    <label class="block"><span class="text-sm font-semibold">Name</span>
        <input name="name" value="{{ old('name', $campaign->name ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required>
    </label>
    <label class="block"><span class="text-sm font-semibold">Objective</span>
        <input name="objective" value="{{ old('objective', $campaign->objective ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
    </label>
    <label class="block"><span class="text-sm font-semibold">Status</span>
        <select name="status" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            @foreach(['draft','active','paused','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(old('status', $campaign->status ?? 'draft')===$s)>{{ $s }}</option>
            @endforeach
        </select>
    </label>
    <label class="block"><span class="text-sm font-semibold">Owner</span>
        <select name="owner_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="">—</option>
            @foreach($owners as $u)
                <option value="{{ $u->id }}" @selected(old('owner_id', $campaign->owner_id ?? '')==$u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
    </label>
    <div class="grid sm:grid-cols-2 gap-4">
        <label class="block"><span class="text-sm font-semibold">Starts</span>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($campaign) && $campaign->starts_at ? $campaign->starts_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </label>
        <label class="block"><span class="text-sm font-semibold">Ends</span>
            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($campaign) && $campaign->ends_at ? $campaign->ends_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </label>
    </div>
    <label class="block"><span class="text-sm font-semibold">UTM campaign</span>
        <input name="utm_campaign" value="{{ old('utm_campaign', $campaign->utm_campaign ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
    </label>
    <label class="block"><span class="text-sm font-semibold">Budget</span>
        <input type="number" step="0.01" name="budget_amount" value="{{ old('budget_amount', $campaign->budget_amount ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
    </label>
    <label class="block"><span class="text-sm font-semibold">Audience notes</span>
        <textarea name="audience_notes" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" rows="3">{{ old('audience_notes', $campaign->audience_notes ?? '') }}</textarea>
    </label>
    <div>
        <span class="text-sm font-semibold">Products</span>
        <div class="mt-2 space-y-1">
            @php $selected = old('product_ids', isset($campaign) ? $campaign->products->pluck('id')->all() : []); @endphp
            @foreach($products as $p)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="product_ids[]" value="{{ $p->id }}" @checked(in_array($p->id, $selected))>
                    {{ $p->name }}
                </label>
            @endforeach
        </div>
    </div>
    <div class="flex gap-3">
        <button class="btn-primary">Save campaign</button>
        <a href="{{ route('admin.campaigns.index') }}" class="btn-ghost" data-admin-modal-close data-turbo-frame="admin_main">Cancel</a>
    </div>
</form>
@endsection

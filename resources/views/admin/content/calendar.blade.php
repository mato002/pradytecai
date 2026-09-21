@extends('layouts.admin')
@section('title', 'Content Calendar')
@section('page_title', 'Content Calendar')
@section('header_actions')
@include('admin.partials.product-switcher')
@endsection
@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    @foreach(['month','week','list'] as $mode)
        <a href="{{ route('admin.content.calendar', array_merge(request()->query(), ['view' => $mode])) }}"
           class="nav-pill {{ $viewMode === $mode ? 'nav-pill--active' : '' }}">{{ ucfirst($mode) }}</a>
    @endforeach
</div>
<div class="mb-4 glass-card">
<form method="GET" class="grid md:grid-cols-5 gap-3">
<input type="hidden" name="view" value="{{ $viewMode }}">
<select name="campaign_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="">All campaigns</option>
@foreach($filters['campaigns'] as $campaign)
<option value="{{ $campaign->id }}" @selected(request('campaign_id')==$campaign->id)>{{ $campaign->name }}</option>
@endforeach
</select>
<select name="channel" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="">All channels</option>
@foreach($filters['channels'] as $channel)
<option value="{{ $channel }}" @selected(request('channel')===$channel)>{{ $channel }}</option>
@endforeach
</select>
<select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="all">All statuses</option>
@foreach(['draft','scheduled','published','approved'] as $s)
<option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
@endforeach
</select>
<select name="owner_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="">All owners</option>
@foreach($filters['owners'] as $owner)
<option value="{{ $owner->id }}" @selected(request('owner_id')==$owner->id)>{{ $owner->name }}</option>
@endforeach
</select>
<button class="btn-primary">Apply</button>
</form>
</div>

@if($viewMode === 'list')
<div class="glass-card space-y-3">
@forelse($items as $item)
<div class="flex justify-between border-b border-slate-100 pb-3">
<div>
<p class="font-semibold text-slate-900">{{ $item->title }}</p>
<p class="text-sm text-slate-600">{{ $item->status }} · {{ $item->product?->name }}</p>
</div>
<p class="text-sm text-slate-600">{{ optional($item->scheduled_at ?? $item->published_at)->format('M j, Y H:i') }}</p>
</div>
@empty
<p class="text-slate-600">No items in range.</p>
@endforelse
</div>
@else
@php
    $byDay = $items->groupBy(fn ($i) => optional($i->scheduled_at ?? $i->published_at)->format('Y-m-d'));
    $cursor = $rangeStart->copy();
@endphp
<div class="glass-card">
<p class="mb-4 text-sm text-slate-600">{{ $rangeStart->toFormattedDateString() }} – {{ $rangeEnd->toFormattedDateString() }}</p>
<div class="grid grid-cols-7 gap-2 text-xs">
@while($cursor <= $rangeEnd)
@php $key = $cursor->format('Y-m-d'); $dayItems = $byDay->get($key, collect()); @endphp
<div class="min-h-[90px] rounded-lg border border-slate-200 p-2 {{ $cursor->isSameMonth($anchor) || $viewMode==='week' ? 'bg-white' : 'bg-slate-50' }}">
<p class="font-semibold text-slate-700">{{ $cursor->day }}</p>
@foreach($dayItems->take(3) as $item)
<p class="mt-1 truncate text-indigo-700">{{ $item->title }}</p>
@endforeach
@if($dayItems->count() > 3)<p class="text-slate-500">+{{ $dayItems->count()-3 }}</p>@endif
</div>
@php $cursor->addDay(); @endphp
@endwhile
</div>
</div>
@endif
@endsection

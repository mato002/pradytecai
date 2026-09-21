@extends('layouts.admin')
@section('title', 'Product Analytics')
@section('page_title', 'Product Analytics')
@section('content')
@include('admin.analytics._nav')
@if(!$hasData)
<div class="glass-card text-center py-12 text-slate-600">No products in scope.</div>
@else
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm"><thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Content</th><th class="px-4 py-3">Leads</th><th class="px-4 py-3">Demos</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($products as $product)
<tr>
<td class="px-4 py-3 font-semibold">{{ $product->name }}</td>
<td class="px-4 py-3">{{ $product->content_items_count }}</td>
<td class="px-4 py-3">{{ $product->contact_messages_count }}</td>
<td class="px-4 py-3">{{ $product->demo_requests_count }}</td>
</tr>
@endforeach
</tbody></table>
</div>
@endif
@endsection

@extends('layouts.admin')
@section('title', 'Edit Campaign')
@section('page_title', 'Edit Campaign')
@section('content')
<div class="glass-card max-w-3xl">
<form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}" class="space-y-4">@csrf
    @include('admin.campaigns._form')
    <div class="flex gap-3 pt-2"><button class="btn-primary" type="submit">Save</button><a href="{{ route('admin.campaigns.show', $campaign) }}" class="btn-ghost">Cancel</a></div>
</form>
</div>
@endsection

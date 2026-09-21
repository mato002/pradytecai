@extends('layouts.admin')
@section('title', 'Edit Social Account')
@section('page_title', 'Edit Social Account')
@section('content')
<div class="glass-card max-w-3xl">
<form method="POST" action="{{ route('admin.social-accounts.update', $account) }}" class="space-y-4">@csrf
@include('admin.social-accounts._form')
<div class="flex gap-3"><button class="btn-primary">Save</button><a href="{{ route('admin.social-accounts.show', $account) }}" class="btn-ghost">Cancel</a></div>
</form></div>
@endsection

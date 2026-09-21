@extends('layouts.admin')
@section('title', 'New Social Account')
@section('page_title', 'New Social Account')
@section('content')
<div class="glass-card max-w-3xl">
<form method="POST" action="{{ route('admin.social-accounts.store') }}" class="space-y-4">@csrf
@include('admin.social-accounts._form')
<div class="flex gap-3"><button class="btn-primary">Create</button><a href="{{ route('admin.social-accounts.index') }}" class="btn-ghost">Cancel</a></div>
</form></div>
@endsection

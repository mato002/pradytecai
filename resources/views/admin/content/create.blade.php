@extends('layouts.admin')
@section('title', 'New Content')
@section('page_title', 'New Content')
@section('content')
<div class="glass-card max-w-3xl">
<form method="POST" action="{{ route('admin.content.store') }}" class="space-y-4">@csrf
@include('admin.content._form')
<div class="flex gap-3"><button class="btn-primary">Create</button><a href="{{ route('admin.content.library') }}" class="btn-ghost">Cancel</a></div>
</form></div>
@endsection

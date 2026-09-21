@extends('layouts.admin')
@section('title', 'Edit Role')
@section('page_title', 'Edit Role: '.$role->name)
@section('content')
@if($role->name === 'super_admin')
<div class="glass-card text-slate-700">Super Admin permissions cannot be changed.</div>
@else
<form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">@csrf
@foreach($permissions as $group => $perms)
<div class="glass-card">
<h3 class="font-bold text-slate-900 mb-3 capitalize">{{ $group }}</h3>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
@foreach($perms as $permission)
<label class="flex items-center gap-2 text-sm text-slate-700">
<input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked($role->permissions->contains('name', $permission->name))>
{{ $permission->name }}
</label>
@endforeach
</div>
</div>
@endforeach
<button class="btn-primary">Save permissions</button>
<a href="{{ route('admin.roles.index') }}" class="btn-ghost">Cancel</a>
</form>
@endif
@endsection

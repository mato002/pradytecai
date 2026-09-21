@extends('layouts.admin')
@section('title', 'Roles')
@section('page_title', 'Roles & Permissions')
@section('content')


<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Role</th><th class="px-4 py-3">Permissions</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($roles as $role)
<tr>
<td class="px-4 py-3 font-semibold">{{ $roleLabels[$role->name] ?? $role->name }}</td>
<td class="px-4 py-3 text-slate-600">{{ $role->permissions->count() }} permissions</td>
<td class="px-4 py-3 text-right">
@can('roles.manage')
<a href="{{ route('admin.roles.edit', $role) }}" class="btn-ghost">Edit</a>
@endcan
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection

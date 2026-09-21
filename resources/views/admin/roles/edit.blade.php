@extends(admin_layout())
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
@if(is_admin_modal_request())
<button type="button" class="btn-ghost" data-admin-modal-close>Cancel</button>
@else
<a href="{{ route('admin.roles.index') }}" class="btn-ghost" data-turbo-frame="admin_main">Cancel</a>
@endif
</form>
@endif
@endsection

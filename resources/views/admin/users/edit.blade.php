@extends(admin_layout())

@section('title', 'Admin - Edit User')
@section('page_title', 'Edit User')

@section('content')
<div class="max-w-4xl">


@if($errors->any())
<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
<ul class="list-disc pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif
<div class="glass-card">
<form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">@csrf
<div>
<label class="block text-sm font-semibold mb-1">Name *</label>
<input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div>
<label class="block text-sm font-semibold mb-1">Email *</label>
<input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
<div>
<label class="block text-sm font-semibold mb-1">Role *</label>
<select name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
@foreach($roles as $role)
<option value="{{ $role['slug'] }}" @selected(old('role', $user->roles->first()?->name ?? $user->role) === $role['slug'])>{{ $role['label'] }}</option>
@endforeach
</select>
</div>
<div class="grid md:grid-cols-2 gap-4">
<div>
<label class="block text-sm font-semibold mb-1">New password</label>
<input type="password" name="password" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Leave blank to keep">
</div>
<div>
<label class="block text-sm font-semibold mb-1">Confirm password</label>
<input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 px-3 py-2">
</div>
</div>
<div>
<p class="text-sm font-semibold mb-2">Product scopes</p>
<div class="grid sm:grid-cols-2 gap-2">
@foreach($products as $product)
<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="product_scopes[]" value="{{ $product->id }}" @checked(in_array($product->id, old('product_scopes', $selectedProductScopes ?? [])))> {{ $product->name }}</label>
@endforeach
</div>
</div>
<div>
<p class="text-sm font-semibold mb-2">Social account scopes</p>
<div class="grid sm:grid-cols-2 gap-2">
@forelse($socialAccounts as $account)
<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="social_account_scopes[]" value="{{ $account->id }}" @checked(in_array($account->id, old('social_account_scopes', $selectedAccountScopes ?? [])))> {{ $account->name }} ({{ $account->platform }})</label>
@empty
<p class="text-sm text-slate-500">No social accounts yet.</p>
@endforelse
</div>
</div>
<div class="flex gap-3">
<button type="submit" class="btn-primary">Update user</button>
@if(is_admin_modal_request())
<button type="button" class="btn-ghost" data-admin-modal-close>Cancel</button>
@else
<a href="{{ route('admin.users.index') }}" class="btn-ghost" data-turbo-frame="admin_main">Cancel</a>
@endif
</div>
</form>
</div>
</div>
@endsection

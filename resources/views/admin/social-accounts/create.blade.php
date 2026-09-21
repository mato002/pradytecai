@extends(admin_layout())
@section('title', 'New Social Account')
@section('page_title', 'New Social Account')
@section('content')
<div class="glass-card max-w-3xl">
<form method="POST" action="{{ route('admin.social-accounts.store') }}" class="space-y-4">@csrf
@include('admin.social-accounts._form')
<div class="flex gap-3"><button class="btn-primary">Create</button>@if(is_admin_modal_request())
<button type="button" class="btn-ghost" data-admin-modal-close>Cancel</button>
@else
<a href="{{ route('admin.social-accounts.index') }}" class="btn-ghost" data-turbo-frame="admin_main">Cancel</a>
@endif</div>
</form></div>
@endsection

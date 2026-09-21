@extends(admin_layout())
@section('title', 'New Content')
@section('page_title', 'New Content')
@section('content')
<div class="glass-card max-w-3xl">
<form method="POST" action="{{ route('admin.content.store') }}" class="space-y-4">@csrf
@include('admin.content._form')
<div class="flex gap-3"><button class="btn-primary">Create</button>@if(is_admin_modal_request())
<button type="button" class="btn-ghost" data-admin-modal-close>Cancel</button>
@else
<a href="{{ route('admin.content.library') }}" class="btn-ghost" data-turbo-frame="admin_main">Cancel</a>
@endif</div>
</form></div>
@endsection

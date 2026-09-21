<div class="mb-4 flex flex-wrap gap-2">
@foreach(['overview'=>'Overview','social'=>'Social','website'=>'Website','campaigns'=>'Campaigns','products'=>'Products','content'=>'Content'] as $route=>$label)
<a href="{{ route('admin.analytics.'.$route) }}" class="nav-pill {{ request()->routeIs('admin.analytics.'.$route) ? 'nav-pill--active' : '' }}">{{ $label }}</a>
@endforeach
</div>

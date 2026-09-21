@php
    $link = function (string $route, string $label, string $pattern = null) {
        $pattern = $pattern ?? $route;
        $active = request()->routeIs($pattern) ? 'nav-pill--active' : '';
        return '<a href="'.e(route($route)).'" data-turbo-frame="admin_main" class="nav-pill '.$active.'"><span class="sidebar-text block text-base font-semibold text-slate-900">'.e($label).'</span></a>';
    };
@endphp

<nav class="mt-8 flex-1 space-y-6 text-sm overflow-y-auto pr-1">
    @can('dashboard.view')
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Command</p>
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.dashboard') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Overview</span></a>
            @can('pulse.view')
            <a href="{{ route('admin.pulse.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.pulse.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Marketing Pulse</span></a>
            @endcan
            @can('tasks.view')
            <a href="{{ route('admin.tasks.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.tasks.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Tasks</span></a>
            @endcan
        </div>
    </div>
    @endcan

    @canany(['campaigns.view','content.view','social_accounts.view','analytics.view'])
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Marketing</p>
        <div class="space-y-1">
            @can('campaigns.view')
            <a href="{{ route('admin.campaigns.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.campaigns.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Campaigns</span></a>
            @endcan
            @can('content.view')
            <a href="{{ route('admin.content.calendar') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.content.calendar') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Calendar</span></a>
            <a href="{{ route('admin.content.library') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.content.library') || request()->routeIs('admin.content.create') || request()->routeIs('admin.content.edit') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Content Library</span></a>
            <a href="{{ route('admin.content.approvals') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.content.approvals') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Approvals</span></a>
            @endcan
            @can('social_accounts.view')
            <a href="{{ route('admin.social-accounts.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.social-accounts.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Social Accounts</span></a>
            @endcan
            @can('analytics.view')
            <a href="{{ route('admin.analytics.overview') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.analytics.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Analytics</span></a>
            @endcan
        </div>
    </div>
    @endcanany

    @canany(['leads.view','demo_requests.view','subscribers.view'])
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Audience</p>
        <div class="space-y-1">
            @can('leads.view')
            <a href="{{ route('admin.enquiries.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.enquiries.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Leads</span></a>
            @endcan
            @can('demo_requests.view')
            <a href="{{ route('admin.demos.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.demos.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Demo Requests</span></a>
            @endcan
            @can('subscribers.view')
            <a href="{{ route('admin.subscribers.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.subscribers.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Subscribers</span></a>
            @endcan
        </div>
    </div>
    @endcanany

    @canany(['products.view','blog.view'])
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Website</p>
        <div class="space-y-1">
            @can('products.view')
            <a href="{{ route('admin.products.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.products.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Products</span></a>
            @endcan
            @can('blog.view')
            <a href="{{ route('admin.blog.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.blog.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Blog</span></a>
            @endcan
        </div>
    </div>
    @endcanany

    @canany(['careers.view','careers.manage'])
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">People</p>
        <div class="space-y-1">
            <a href="{{ route('admin.positions.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.positions.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Careers</span></a>
            <a href="{{ route('admin.applications.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.applications.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Applications</span></a>
        </div>
    </div>
    @endcanany

    @canany(['users.view','roles.view','integrations.view','settings.view','audit.view'])
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">Administration</p>
        <div class="space-y-1">
            @can('users.view')
            <a href="{{ route('admin.users.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.users.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Users</span></a>
            @endcan
            @can('roles.view')
            <a href="{{ route('admin.roles.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.roles.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Roles & Permissions</span></a>
            @endcan
            @can('integrations.view')
            <a href="{{ route('admin.integrations.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.integrations.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Integrations</span></a>
            @endcan
            @can('settings.view')
            <a href="{{ route('admin.settings.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.settings.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Settings</span></a>
            @endcan
            @can('audit.view')
            <a href="{{ route('admin.activity-logs.index') }}" data-turbo-frame="admin_main" class="nav-pill {{ request()->routeIs('admin.activity-logs.*') ? 'nav-pill--active' : '' }}"><span class="sidebar-text font-semibold">Activity Logs</span></a>
            @endcan
        </div>
    </div>
    @endcanany
</nav>

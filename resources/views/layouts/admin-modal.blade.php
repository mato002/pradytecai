<turbo-frame id="admin_modal">
    <div class="admin-modal-panel">
        <div class="admin-modal-header">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">
                    @yield('page_eyebrow', 'Admin')
                </p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">
                    @hasSection('modal_title')
                        @yield('modal_title')
                    @else
                        @yield('page_title', 'Form')
                    @endif
                </h2>
            </div>
            <button type="button"
                    class="admin-modal-close"
                    data-admin-modal-close
                    aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="admin-modal-body">
            @include('admin.partials.flash-data')
            @yield('content')
        </div>
    </div>
</turbo-frame>

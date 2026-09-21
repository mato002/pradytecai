@if(session('success') || session('error') || session('warning') || session('info'))
    <div hidden
         data-admin-flash
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}"
         data-warning="{{ session('warning') }}"
         data-info="{{ session('info') }}"></div>
@endif

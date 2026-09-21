<?php

if (! function_exists('admin_layout')) {
    /**
     * Resolve the admin Blade layout for full pages vs Turbo modal frames.
     */
    function admin_layout(?string $frame = null): string
    {
        $frame ??= request()->header('Turbo-Frame');

        return $frame === 'admin_modal'
            ? 'layouts.admin-modal'
            : 'layouts.admin';
    }
}

if (! function_exists('is_admin_modal_request')) {
    function is_admin_modal_request(): bool
    {
        return request()->header('Turbo-Frame') === 'admin_modal';
    }
}

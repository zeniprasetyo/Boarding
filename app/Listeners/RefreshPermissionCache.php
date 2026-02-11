<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Spatie\Permission\PermissionRegistrar;

class RefreshPermissionCache
{
    /**
     * Handle the event.
     */
    public function handle(Authenticated $event): void
    {
        // Hapus cache permission setiap kali user login
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

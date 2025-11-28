<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateApprovalStatusOnVerification
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;
        
        // Check if the approval mode is set to email_verification
        // We use the helper setting() if available, or check config/DB
        // Assuming setting() helper is available globally as seen in RegisterController
        
        try {
            if (function_exists('setting') && setting('user_approval_mode') === 'email_verification') {
                $user->approval_status = 'approved';
                $user->save();
            }
        } catch (\Throwable $e) {
            // Fail silently if tenant settings cannot be accessed
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Jobs\SendWelcomeAfterVerificationJob;
use Illuminate\Auth\Events\Verified;

class SendWelcomeEmailAfterVerification
{
    public function handle(Verified $event): void
    {
        $user = $event->user;

        if (!$user || !$user->email) {
            return;
        }

        SendWelcomeAfterVerificationJob::dispatch($user->id);
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\WelcomeVerifiedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeAfterVerificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $userId
    ) {
        $this->onQueue('users.notifications.welcome');
    }

    public function handle(
    ): void {
        $user = User::query()->find($this->userId);

        if (!$user || !$user->email) {
            return;
        }

        if (Mail::to($user->email)->send(new WelcomeVerifiedMail($user)) === null) {
            Log::error("Failed to send welcome email");
            return;
        }
        Log::info("Email was sent");
    }
}

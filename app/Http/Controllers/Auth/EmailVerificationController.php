<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendRegistrationVerificationJob;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice(): Factory|View
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()
            ->route('profile.form')
            ->with('success', 'Email успешно подтвержден.');
    }

    public function send(Request $request): RedirectResponse
    {
        SendRegistrationVerificationJob::dispatch($request->user()->id);

        return back()->with('success', 'Письмо для подтверждения отправлено повторно.');
    }
}

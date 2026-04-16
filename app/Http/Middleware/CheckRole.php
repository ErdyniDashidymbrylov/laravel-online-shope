<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * @param array<int, string> $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($roles === []) {
            return $next($request);
        }

        if (!$user->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                abort(Response::HTTP_FORBIDDEN);
            }

            return redirect()
                ->route('home')
                ->with('error', 'Доступ в админку разрешён только администраторам');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class AuthType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$types)
    {
        $user = $request->user();
        if (!$user) {
            return redirect('login');
        }

        if (!in_array($user->type, $types)) {
            abort(403, 'You are not Admin!');
        }

        return $next($request);
    }
}

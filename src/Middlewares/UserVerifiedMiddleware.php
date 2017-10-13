<?php

namespace Rasulian\UserVerification\Middlewares;

use Closure;
use Rasulian\UserVerification\Exceptions\UserNotVerifiedException;

class UserVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->verfied)
            throw new UserNotVerifiedException;

        return $next($request);
    }
}

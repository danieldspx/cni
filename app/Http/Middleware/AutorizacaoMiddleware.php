<?php

namespace cni\Http\Middleware;

use Closure;
use Auth;

class AutorizacaoMiddleware
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
        if(!($request->is('login')) && Auth::guest() && !($request->is('register'))) { // && !($request->is('register'))
            return redirect()->route('login');
        }
        return $next($request);
    }
}

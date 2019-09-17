<?php namespace Jalmatari\Http\Middleware;

use Closure;

class PublicAuth
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
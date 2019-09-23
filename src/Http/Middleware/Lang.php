<?php

namespace Jalmatari\Http\Middleware;

use Closure;

class Lang
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
        if ($request->has('lang')) {
            $request->session()->put( 'lang' , $request->get('lang'));
            //session()->save();
        }
        app()->setLocale($request->session()->get('lang', config('locale')));
        return $next($request);
    }
}

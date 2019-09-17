<?php namespace Jalmatari\Http\Middleware;

use Illuminate\Contracts\Auth\Factory as Auth;
use Closure;
use Redirect;

class UserAuth
{


    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //dd('hi');
        if ($this->auth->guest()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
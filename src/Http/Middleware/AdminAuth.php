<?php namespace Jalmatari\Http\Middleware;

use Closure;
use Jalmatari\Funs\Funs;


class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if (is_null(user()) || user()->job_title < 2)
            return $request->path() == 'admin' ? redirect('/'): abort(404);
        if (!Funs::isAuth())
            return redirect()->route('admin.unauthorized');

        if (auth()->guest()) {
            if ($request->ajax())
                return response('Unauthorized.', 401);
            else
                return redirect()->route('login');
        }

        Funs::OnLoadSite();

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;

class setLang
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
        if(isset($request->lang)) {
            \App::setLocale($request->lang);
            return $next($request);
        }

        $request->route()->setParameter('lang', app()->getLocale());
        return $next($request);
    }
}

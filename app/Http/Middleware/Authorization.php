<?php

namespace App\Http\Middleware;

use Closure;

class Authorization
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
        $requestToken = $request->header('Authorization');
        $configToken = config('app.api_key');

        if($requestToken != $configToken)
            return response()->json(['message' => "Invalid authorization token."], 401);

        return $next($request);
    }
}

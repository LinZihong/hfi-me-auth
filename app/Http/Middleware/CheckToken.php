<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;

class CheckToken
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
        if ($request->token === null) {
            return formatJson(__('api_auth.missing_token'), 400);
        }
        $token = Token::tokenValue($request->token)->first();
        if ($token === null) {
            return formatJson(__('api_auth.token_not_exist'), 404);
        }
        return $next($request);
    }
}

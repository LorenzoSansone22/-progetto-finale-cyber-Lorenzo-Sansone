<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TemporaryIpBlock
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (Cache::has("blocked_ip:{$ip}")) {
            return response('IP temporarily blocked. Try again later.', 429);
        }

        return $next($request);
    }
}
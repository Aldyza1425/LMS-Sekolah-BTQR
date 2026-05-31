<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPengajar
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user instanceof \App\Models\Pengajar) {
            return $next($request);
        }
        return response()->json(['message' => 'Unauthorized. Pengajar role required.'], 403);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSiswa
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user instanceof \App\Models\Siswa) {
            return $next($request);
        }
        return response()->json(['message' => 'Unauthorized. Siswa role required.'], 403);
    }
}

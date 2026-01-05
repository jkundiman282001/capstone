<?php

namespace App\Http\Middleware;

use App\Models\BasicInfo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApplicationStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! BasicInfo::where('user_id', $user->id)->exists()) {
            return redirect()->route('student.dashboard');
        }

        return $next($request);
    }
}

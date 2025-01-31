<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfHasCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if the user has a code
        if (!empty($user->code)) {
            // Validate the code in the supports table
            $support = Support::where('unique_code', $user->code)
                ->whereHas('distribution', function ($query) use ($user) {
                    $query->where('barangay_id', $user->barangay_id);
                })
                ->first();

            // If the code exists, redirect to member dashboard
            if ($support) {
                return redirect()->route('member.dashboard');
            }

            // If the code is invalid, remove it
            $user->update(['code' => null]);
        }

        // Allow access to support login page
        return $next($request);
      
    }
}

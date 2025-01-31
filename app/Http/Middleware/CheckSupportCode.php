<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSupportCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Ensure the user has a barangay
        if (!$user->barangay_id) {
            return redirect()->route('support-login')->with('error', 'Access denied. No assigned barangay.');
        }

        // Check if the user's code exists in the supports table and belongs to the same barangay
        $support = Support::where('unique_code', $user->code)
            ->whereHas('distribution', function ($query) use ($user) {
                $query->where('barangay_id', $user->barangay_id);
            })
            ->first();

        // If the code is valid, allow access
        if ($support) {
            return $next($request);
        }

        // If the code is invalid, remove it and redirect to support login
        $user->update(['code' => null]);

        return redirect()->route('support-login')->with('error', 'Invalid code. Please enter a valid code.');
    }
}

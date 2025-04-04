<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMemberPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();


        $support = Support::where('unique_code', $user->code)
            ->whereHas('distribution', function ($query) use ($user) {
                $query->where('barangay_id', $user->barangay_id);
            })
            ->where('enable_beneficiary_management', true)
            ->first();

        if (!$support) {
            abort(403, 'Unauthorized action.');
        }


        return $next($request);
    }
}

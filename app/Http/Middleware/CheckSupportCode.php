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


        if (!$user->barangay_id) {
            return redirect()->route('support-login')->with('error', 'Access denied. No assigned barangay.');
        }

       
        $support = Support::where('unique_code', $user->code)
            ->whereHas('distribution', function ($query) use ($user) {
                $query->where('barangay_id', $user->barangay_id);
            })
            ->where('enable_beneficiary_management', true)
            ->first();

   
        if ($support) {
            return $next($request);
        }

    
        $user->update(['code' => null]);
        return redirect()->route('support-login')->with('error', 'Access denied. Beneficiary management is not enabled.');
    
    }
}

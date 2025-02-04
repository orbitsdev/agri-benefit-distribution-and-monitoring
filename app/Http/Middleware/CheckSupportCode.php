<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Support;
use Illuminate\Contracts\Session\Session;
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


        if (!$user->code) {
            return redirect()->route('support-login')->with('error', 'Please enter a valid support code.');
        }
       

        $support = Support::where('unique_code', $user->code)
            ->whereHas('distribution', function ($query) use ($user) {
                $query->where('barangay_id', $user->barangay_id);
            })
            ->first();


        if (!$support) {
            $user->update(['code' => null]);
            return redirect()->route('support-login')->with('error', 'Invalid support code.');
        }

        return $next($request);

    }
}

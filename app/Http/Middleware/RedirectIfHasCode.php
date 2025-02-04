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

        if (!empty($user->code)) {
            $support = Support::where('unique_code', $user->code)
                ->whereHas('distribution', function ($query) use ($user) {
                    $query->where('barangay_id', $user->barangay_id);
                })
                ->first();

            if ($support) {
                // Redirect based on permissions
                if ($support->enable_beneficiary_management) {
                    return redirect()->route('member.dashboard');
                }

                if ($support->enable_item_scanning) {
                    return redirect()->route('qr-scan');
                }
            }

            // If code is invalid, remove it
            $user->update(['code' => null]);
        }

        return $next($request);

    }
}

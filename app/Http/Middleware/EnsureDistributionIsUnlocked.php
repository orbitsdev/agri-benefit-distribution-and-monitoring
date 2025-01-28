<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Distribution;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDistributionIsUnlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $distributionId = $request->route('record');

        // Find the distribution
        $distribution = Distribution::find($distributionId);

        // Check if the distribution is locked
        if ($distribution && $distribution->is_locked) {
            abort(Response::HTTP_FORBIDDEN, 'You are not authorized to access this page.');
        }
        return $next($request);
    }
}

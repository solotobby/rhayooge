<?php

namespace App\Http\Middleware;

use App\Models\BusinessExecutive;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        $ref = $request->query('ref') ?? $request->query('be');

        if ($ref) {
            $code = strtolower(trim((string) $ref));
            $executive = BusinessExecutive::query()
                ->whereRaw('LOWER(code) = ?', [$code])
                ->where('status', 'active')
                ->first();

            if ($executive) {
                session([
                    'be_ref' => $executive->code,
                    'be_id' => $executive->id,
                ]);

                cookie()->queue('be_ref', $executive->code, 60 * 24 * 30);
            }
        }

        return $next($request);
    }
}

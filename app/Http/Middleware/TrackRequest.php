<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Statistic;
use Illuminate\Support\Facades\Session;

class TrackRequest
{

    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response): void
    {

        Statistic::create([
            'ip' => \Request::ip(),
            'session_id' => Session::getId() ?? null,
            'user_id' => Auth::id() ?? null,
            'status_code' => $response->getStatusCode(),
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'server' => $request->server() ?? null,
            'input' => $request->input() ?? null,
            'created_at' => Carbon::now(),
        ]);
    }
}

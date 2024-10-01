<?php

namespace App\Http\Middleware;

use App\Models\HttpRequest;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TerminatingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $data = [
            'session_id'  => session()->getId(),
            'user_id'     => $request->user()->id ?? null,
            'ip'          => $request->ip(),
            'ajax'        => $request->ajax(),
            'url'         => $request->fullUrl(),
            'payload'     => $request->toArray(),
            'status_code' => $response->getStatusCode()
        ];

        \App\Models\HttpRequest::create($data);
    }
}
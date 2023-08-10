<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        return auth()->user()->admin ? $next($request): $this->unAuthorizedResponse();
    }
}

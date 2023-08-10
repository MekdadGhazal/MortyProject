<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerCheck
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return auth()->user()->admin->owner ? $next($request): $this->unAuthorizedResponse();
    }
}

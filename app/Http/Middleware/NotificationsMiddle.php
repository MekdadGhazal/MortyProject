<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\ApiTrait\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationsMiddle
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()->id != $_GET['id']){
            return $this -> unAuthorizedResponse();
        }
        return $next($request);
    }
}

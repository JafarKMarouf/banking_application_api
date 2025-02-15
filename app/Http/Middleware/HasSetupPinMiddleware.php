<?php

namespace App\Http\Middleware;

use App\Exceptions\NotSetupPin;
use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasSetupPinMiddleware
{
    /**
     * Handle an incoming request.
     * @param Closure(Request): (Response) $next
     * @throws NotSetupPin
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $userService = new UserService();
        if (!$userService->hasSetPin($user)) {
            throw new NotSetupPin();
        }
        return $next($request);
    }
}

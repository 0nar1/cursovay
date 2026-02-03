<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAuthWithNotice
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()
                ->route('login')
                ->with('status', 'Пожалуйста, войдите в аккаунт. Без авторизации доступ к курсам, расписанию и обратной связи закрыт.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Installer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->alreadyInstalled()) {
           return redirect('/');
        }

        return $next($request);
    }

    /**
     * If application is already installed.
     *
     * @return bool
     */
    private function alreadyInstalled()
    {
        return file_exists(storage_path('installed'));
    }
}

<?php
namespace App\Http\Middleware;

class RedirectPageSpeed
{
    public function handle($request, $next)
    {
        if ($request->has('PageSpeed')) {
            return redirect()->to($request->url(), 301);
        }
        return $next($request);
    }
}
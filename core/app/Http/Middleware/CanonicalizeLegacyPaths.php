<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalizeLegacyPaths
{
    /**
     * Normalize common legacy or mistyped public URLs before controllers run.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $path = trim($request->decodedPath(), '/');

        if ($path === '') {
            return $next($request);
        }

        $canonicalPath = preg_replace('/[`\'"\s]+$/u', '', $path) ?? $path;

        $canonicalPath = [
            'login' => 'user/login',
            'register' => 'user/register',
            'password/reset' => 'user/password/reset',
        ][$canonicalPath] ?? $canonicalPath;

        if ($canonicalPath === $path) {
            return $next($request);
        }

        $target = '/' . ltrim($canonicalPath, '/');
        $queryString = $request->getQueryString();

        if ($queryString) {
            $target .= '?' . $queryString;
        }

        return redirect($target, 301);
    }
}

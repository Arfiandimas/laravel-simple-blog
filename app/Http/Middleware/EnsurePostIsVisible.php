<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePostIsVisible
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        $post = $request->route('post');

        $isInternal = $routeName === 'posts.internal';

        if (!$isInternal && ($post->is_draft || $post->publish_date > now()->format('Y-m-d'))) {
            abort(403);
        }

        return $next($request);
    }
}

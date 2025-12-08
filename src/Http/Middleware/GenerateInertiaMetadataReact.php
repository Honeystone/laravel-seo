<?php

namespace Honeystone\Seo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

use function seo;

final class GenerateInertiaMetadataReact
{
    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::share('seoPayload', static fn (): array => seo()->toArray());

        return $next($request);
    }
}


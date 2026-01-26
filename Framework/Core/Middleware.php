<?php
namespace Framework\Core;

interface Middleware {
    /**
     * @param Request $request
     * @param callable $next El siguiente middleware o el controlador final
     * @return Response
     */
    public function handle(Request $request, callable $next);
}
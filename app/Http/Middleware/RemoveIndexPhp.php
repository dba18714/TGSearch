<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RemoveIndexPhp
{
    public function handle(Request $request, Closure $next)
    {
        $requestUri = $request->getRequestUri();
        if (str_contains($requestUri, '/index.php')) {
            $url = str_replace('/index.php', '', $requestUri);
            
            // 构建完整的 URL
            $fullUrl = $request->getSchemeAndHttpHost() . $url;
            
            // 使用完整 URL 进行重定向
            return redirect()->away($fullUrl, 301);
        }

        return $next($request);
    }
}
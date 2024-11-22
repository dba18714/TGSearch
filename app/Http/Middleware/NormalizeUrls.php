<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeUrls
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->getPathInfo();
        
        // 如果路径长度大于 1 且以斜杠结尾
        if (strlen($path) > 1 && substr($path, -1) === '/') {
            // 移除结尾的斜杠
            $newPath = rtrim($path, '/');
            
            // 构建新的 URL
            $url = $request->getSchemeAndHttpHost() . $newPath;
            if ($request->getQueryString()) {
                $url .= '?' . $request->getQueryString();
            }
            
            // 301 永久重定向到无斜杠版本
            return redirect()->to($url, 301);
        }

        return $next($request);
    }
}
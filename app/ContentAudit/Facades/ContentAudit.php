<?php

namespace App\ContentAudit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\ContentAudit\Contracts\ContentAuditInterface driver(string $driver = null)
 * @method static array checkContent(string $content)
 * @method static bool isSafe(string $content)
 * @method static array getDetailedAnalysis(string $content)
 */
class ContentAudit extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'content-audit';
    }
}
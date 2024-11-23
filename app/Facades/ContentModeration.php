<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Contracts\ContentModerationService driver(string $driver = null)
 * @method static array checkContent(string $content)
 * @method static bool isSafe(string $content)
 * @method static array getDetailedAnalysis(string $content)
 */
class ContentModeration extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'moderation';
    }
}
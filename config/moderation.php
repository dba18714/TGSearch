<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content Moderation Service
    |--------------------------------------------------------------------------
    |
    | This option controls which service will be used for content moderation.
    | Supported: "openai", "tencent"
    |
    */
    'default' => env('CONTENT_MODERATION_SERVICE', 'tencent'),
];
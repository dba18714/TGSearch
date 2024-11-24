<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content Audit Service
    |--------------------------------------------------------------------------
    |
    | This option controls which service will be used for content auditing.
    | Supported: "openai", "tencent"
    |
    */
    'default' => env('CONTENT_MODERATION_SERVICE', 'tencent'),
];
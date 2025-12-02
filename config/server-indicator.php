<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | The version string to display in the footer. You can use any string
    | or reference another config value.
    |
    */
    'version' => env('APP_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Cookie Name
    |--------------------------------------------------------------------------
    |
    | The name of the cookie that contains the server identifier.
    | Common in load-balanced environments.
    |
    */
    'cookie_name' => env('SERVER_INDICATOR_COOKIE', 'SRVNAME'),

    /*
    |--------------------------------------------------------------------------
    | Server Name Prefix to Remove
    |--------------------------------------------------------------------------
    |
    | A regex pattern to clean up the server name for display.
    | Set to null to display the raw server name.
    |
    */
    'strip_prefix' => '/^load-balanced-/',

    /*
    |--------------------------------------------------------------------------
    | Copyright Text
    |--------------------------------------------------------------------------
    |
    | The copyright text to display. Use :year as placeholder for current year.
    |
    */
    'copyright' => env('SERVER_INDICATOR_COPYRIGHT', '© :year D3S'),

    /*
    |--------------------------------------------------------------------------
    | Enable Logging
    |--------------------------------------------------------------------------
    |
    | Whether to log server information on each request.
    |
    */
    'logging' => [
        'enabled' => env('SERVER_INDICATOR_LOG', false),
        'channel' => env('SERVER_INDICATOR_LOG_CHANNEL', 'stack'),
        'include_user' => true,
        'include_tenant' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Options
    |--------------------------------------------------------------------------
    |
    | Customize what information is shown in the footer.
    |
    */
    'display' => [
        'show_version' => true,
        'show_server' => true,
        'show_copyright' => true,
    ],
];

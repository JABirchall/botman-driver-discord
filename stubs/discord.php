<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Discord token
    |--------------------------------------------------------------------------
    |
    | Your Discord bot token.
    |
    */
    'token' => env('DISCORD_TOKEN'),
    'options' => [
        'disableClones' => true,
        'disableEveryone' => true,
        'fetchAllMembers' => false,
        'messageCache' => true,
        'messageCacheLifetime' => 600,
        'messageSweepInterval' => 600,
        'presenceCache' => false,
        'userSweepInterval' => 600,
        'ws.disabledEvents' => ['TYPING_START'], // Do not remove TYPING_START
    ],
];

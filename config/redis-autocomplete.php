<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | Your application's config/database.php configuration file allows you
    | to define multiple Redis connections / servers.
    | Here you may specify the connection you want to use.
    |
    | To specify an instance of the default Redis connection,
    | you may set the value to null.
    */
    'connection' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */
    'prefix' => 'redis-autocomplete',

    /*
    |--------------------------------------------------------------------------
    | Storage time
    |--------------------------------------------------------------------------
    |
    | Specifies that phrases will expire in a few seconds.
    | If set to null, phrases will be stored indefinitely.
    |
    */
    'ttl' => 60 * 60 * 24
];

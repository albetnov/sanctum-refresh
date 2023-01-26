<?php

// config for Albet/SanctumRefresh
return [
    /**
     * Set the fallback expiration time of both tokens
     * Time in minutes.
     */
    'expiration' => [
        // set the fallback of access token expiration
        'access_token' => 2, // 2 minutes,
        // set the fallback of refresh token expiration
        'refresh_token' => 30, // 30 minutes
    ],
    /**
     * Configuration of Sanctum Refresh behaviour
     */
    'sanctum_refresh' => [
        /**
         * Custom the api response message
         * Map<string, string>
         */
        'message' => [
            // Authenticated successful message to be used by /login route
            'authed' => 'Authentication success!',
            // Invalid or expired refresh token message
            'invalidMsg' => 'Refresh token is expired or invalid.',
        ],
        /**
         * Custom the routes behaviour
         * Map<string, string>
         */
        'routes' => [
            // Only show refresh route (hide the login route)
            'refreshOnly' => false,

            /**
             * Custom the routes urls
             * Map<string, string>
             */
            'urls' => [
                'login' => '/login',
                'refresh' => '/refresh',
            ],

            /**
             * Custom the routes middlewares
             * Map<string, ?array>
             */
            'middlewares' => [
                'login' => null,
                'refresh' => ['checkRefreshToken'],
            ],
        ],
    ],
];

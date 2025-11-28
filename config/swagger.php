<?php

return [
    'api' => [
        'title' => 'Laravel 12 API Documentation',
        'description' => 'API documentation for Laravel 12 API',
        'version' => '1.0.0',
        'host' => env('APP_URL', 'http://localhost:8000'),
        'base_path' => '/api',
        'schemes' => ['http', 'https'],
        'consumes' => ['application/json'],
        'produces' => ['application/json'],
    ],

    'routes' => [
        /*
         * Route for accessing api documentation interface, e.g. `/api/documentation`
         */
        'api' => 'api/documentation',

        /*
         * Route for accessing parsed swagger json, e.g. `/api/documentation.json`
         */
        'docs' => 'api/docs',

        /*
         * Route for Oauth2 callback, e.g. `/api/oauth2-callback`
         */
        'oauth2_callback' => 'api/oauth2-callback',

        /*
         * Middleware allows to filter documented routes
         */
        'middleware' => [
            'api' => [],
            'asset' => [],
            'docs' => [],
            'oauth2_callback' => [],
        ],
    ],

    'paths' => [
        /*
         * Absolute path to location where parsed swagger annotations will be stored
         */
        'docs' => storage_path('api-docs'),

        /*
         * Absolute path to location where to export views
         */
        'views' => base_path('resources/views/vendor/swagger'),

        /*
         * Edit to set the api's base path
         */
        'base' => '/api',

        /*
         * Edit to set full path to json file
         */
        'swagger_ui_assets_path' => 'vendor/swagger-ui/dist',

        /*
         * Absolute path to directories that you would like to exclude from documentation
         */
        'excludes' => [],

        /*
         * Edit to set the swagger output folder
         */
        'swagger' => 'swagger',
    ],

    'edit_swagger' => false,

    /*
     * API documentation will be generated automatically: `php artisan l5-swagger:generate`
     *
     * By default `APP_DEBUG` value will be used. Change `true/false` to force generation on production.
     */
    'generate_always' => env('SWAGGER_GENERATE_ALWAYS', false),

    /*
     * By default all view files are cached. Change `true` to not cache documentation
     */
    'operations' => [
        'tagIds' => true,
    ],

    'constants' => [
        'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://my-default-host.com'),
    ],
];

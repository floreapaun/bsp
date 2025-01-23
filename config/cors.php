<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

        'paths' => ['*'], // Specify your API routes
    
        'allowed_methods' => ['*'], // Allow all methods
    
        'allowed_origins' => [], // Allow all origins (consider restricting in production)
    
        'allowed_origins_patterns' => [],
    
        'allowed_headers' => ['*'], // Allow all headers
    
        'exposed_headers' => [],
    
        'max_age' => 0,
    
        'supports_credentials' => true, // Set to true if you want to allow cookies

    
];

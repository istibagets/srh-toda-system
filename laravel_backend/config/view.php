<?php

return [

    'paths' => [
        resource_path('views'),
    ],

    // Compiled Blade templates (also used by mail notifications). The directory is
    // created at boot if missing, see AppServiceProvider.
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),

];

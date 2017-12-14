<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
     */

    'path'                => [

        'migration'       => base_path('database/migrations/'),

        'model'           => base_path('Models/'),

        'datatables'      => base_path('DataTables/'),

        'repository'      => base_path('Repositories/'),

        'routes'          => base_path('routes/web.php'),

        'api_routes'      => base_path('routes/api.php'),

        //Exata Sistemas:Arquivo do Laravel Modules que registra as rotas
        'registerRoutes'  => base_path('start.php'),

        'request'         => base_path('Http/Requests/'),

        'api_request'     => base_path('Http/Requests/API/'),

        'controller'      => base_path('Http/Controllers/'),

        'api_controller'  => base_path('Http/Controllers/API/'),

        'test_trait'      => base_path('tests/traits/'),

        'repository_test' => base_path('tests/'),

        'api_test'        => base_path('tests/'),

        'views'           => base_path('resources/views/'),
        
        'localization'    => base_path('resources/lang/'),

        'schema_files'    => base_path('resources/model_schemas/'),

        'templates_dir'   => base_path('resources/infyom/infyom-generator-templates/'),

        'modelJs'         => base_path('resources/assets/js/models/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
     */

    'namespace'           => [

        'model'          => 'App\Models',

        'datatables'     => 'App\DataTables',

        'repository'     => 'App\Repositories',

        'controller'     => 'App\Http\Controllers',

        'api_controller' => 'App\Http\Controllers\API',

        'request'        => 'App\Http\Requests',

        'api_request'    => 'App\Http\Requests\API',
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
     */

    'templates'           => 'adminlte-templates',

    /*
    |--------------------------------------------------------------------------
    | Model extend class
    |--------------------------------------------------------------------------
    |
     */

    'model_extend_class'  => 'Eloquent',

    /*
    |--------------------------------------------------------------------------
    | API routes prefix & version
    |--------------------------------------------------------------------------
    |
     */

    'api_prefix'          => 'api',

    'api_version'         => 'v1',

    /*
    |--------------------------------------------------------------------------
    | Field Exata Sistemas
    |--------------------------------------------------------------------------
    | Criado para adicionar uma classe padrão para os input
     */

    'field_class'         => 'col-md-12', //col-xs col-sm-6 col-md-4 col-lg-3

    /*
    |--------------------------------------------------------------------------
    | Pluralize Exata Sistemas
    |--------------------------------------------------------------------------
    | Desabilita a pluralização para as views e rotas
     */

    'pluralize'         => false,

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
     */

    'options'             => [

        'softDelete'                => false,

        'tables_searchable_default' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Prefixes
    |--------------------------------------------------------------------------
    |
     */

    'prefixes'            => [

        'route'  => '', // using admin will create route('admin.?.index') type routes

        'path'   => '',

        'view'   => '', // using backend will create return view('backend.?.index') type the backend views directory

        'public' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
     */

    'localization'        => true,

    'languages'           => [
        'pt',
        'en',
        'es',
    ],

    'ignore_fields'       => [
        'created_at',
        'updated_at',
        'deleted_at',
        'id',
        'sql_rowid',
    ],

    /*
    |--------------------------------------------------------------------------
    | Add-Ons
    |--------------------------------------------------------------------------
    |
     */

    'add_on'              => [

        'swagger'    => false,

        'tests'      => true,

        'datatables' => true,

        //exata: ativa/desativa o add-on modules.
        'modules'    => true,

        'menu'       => [

            'enabled'   => false,

            'menu_file' => 'layouts/menu.blade.php',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Timestamp Fields
    |--------------------------------------------------------------------------
    |
     */

    'timestamps'          => [

        'enabled'    => false,

        'created_at' => 'created_at',

        'updated_at' => 'updated_at',

        'deleted_at' => 'deleted_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Save model files to `App/Models` when use `--prefix`. see #208
    |--------------------------------------------------------------------------
    |
     */
    'ignore_model_prefix' => false,

];

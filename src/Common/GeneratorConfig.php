<?php

namespace InfyOm\Generator\Common;

use Illuminate\Support\Str;

/**
 * Exata Sistemas
 * Classe responsável em configurar e pegar todos os parâmetros que serão utilizados
 * na geração dos arquivos
 */
class GeneratorConfig
{
    /* Namespace variables */
    public $nsApp;
    public $nsRepository;
    public $nsModel;
    public $nsDataTables;
    public $nsModelExtend;

    public $nsApiController;
    public $nsApiRequest;

    public $nsRequest;
    public $nsRequestBase;
    public $nsController;
    public $nsBaseController;

    /* Path variables */
    public $pathRepository;
    public $pathModel;
    public $pathDataTables;

    public $pathApiController;
    public $pathApiRequest;
    public $pathApiRoutes;
    public $pathApiTests;
    public $pathApiTestTraits;

    public $pathController;
    public $pathRequest;
    public $pathRoutes;
    public $pathViews;
    public $modelJsPath;

    /* Model Names */
    public $mName;
    public $mPlural;
    public $mCamel;
    public $mCamelPlural;
    public $mSnake;
    public $mSnakePlural;
    public $mDashed;
    public $mDashedPlural;
    public $mSlash;
    public $mSlashPlural;
    public $mHuman;
    public $mHumanPlural;

    public $forceMigrate;

    /* Generator Options */
    public $options;

    /* Prefixes */
    public $prefixes;

    /* Command Options */
    public static $availableOptions = [
        'fieldsFile',
        'jsonFromGUI',
        'tableName',
        'fromTable',
        'save',
        'primary',
        'prefix',
        'paginate',
        'skip',
        'datatables',
        'views',
        'relations',
        'module', //exata: nome do módulo.
    ];

    public $tableName;

    //exata: nome do módulo.
    public $module;

    /** @var string */
    protected $primaryName;

    /* Generator AddOns */
    public $addOns;

    public function init(CommandData &$commandData, $options = null)
    {
        if (!empty($options)) {
            self::$availableOptions = $options;
        }

        $this->mName = $commandData->modelName;

        $this->prepareAddOns();
        $this->prepareOptions($commandData);
        $this->prepareModelNames();
        $this->preparePrefixes();
        $this->prepareTableName();
        $this->loadPaths();
        $this->prepareTableName();
        $this->preparePrimaryName();
        $this->loadNamespaces($commandData);
        $commandData = $this->loadDynamicVariables($commandData);
    }

    public function loadNamespaces(CommandData &$commandData)
    {
        $prefix = $this->prefixes['ns'];

        if (!empty($prefix)) {
            $prefix = '\\' . $prefix;
        }

        $this->nsApp = $commandData->commandObj->getLaravel()->getNamespace();
        $this->nsApp = substr($this->nsApp, 0, strlen($this->nsApp) - 1);

        if (config('infyom.laravel_generator.ignore_model_prefix', false)) {
            $this->nsModel = config('infyom.laravel_generator.namespace.model', 'App\Models');
        }
        $this->nsModelExtend = config(
            'infyom.laravel_generator.model_extend_class',
            'Illuminate\Database\Eloquent\Model'
        );

        if ($this->options['module'] && $this->addOns['modules'] == true) {
            $this->loadModuleNamespaces();
            return;
        }

        $this->nsModel         = config('infyom.laravel_generator.namespace.model', 'App\Models') . $prefix;
        $this->nsDataTables    = config('infyom.laravel_generator.namespace.datatables', 'App\DataTables') . $prefix;
        $this->nsRepository    = config('infyom.laravel_generator.namespace.repository', 'App\Repositories') . $prefix;
        $this->nsController    = config('infyom.laravel_generator.namespace.controller', 'App\Http\Controllers') . $prefix;
        $this->nsApiController = config(
            'infyom.laravel_generator.namespace.api_controller',
            'App\Http\Controllers\API'
        ) . $prefix;
        $this->nsBaseController = config('infyom.laravel_generator.namespace.controller', 'App\Http\Controllers');
        $this->nsRequest        = config('infyom.laravel_generator.namespace.request', 'App\Http\Requests') . $prefix;
        $this->nsApiRequest     = config('infyom.laravel_generator.namespace.api_request', 'App\Http\Requests\API') . $prefix;
        $this->nsRequestBase    = config('infyom.laravel_generator.namespace.request', 'App\Http\Requests');
    }

    //exata: carrega o prefixo de namespaces pros modulos.
    public function loadModuleNamespaces()
    {
        $moduleNs = config('modules.namespace') . '\\' . $this->options['module'] . '\\';

        $this->nsModel          = $moduleNs . config('infyom.laravel_generator.namespace.model');
        $this->nsDataTables     = $moduleNs . config('infyom.laravel_generator.namespace.datatables');
        $this->nsRepository     = $moduleNs . config('infyom.laravel_generator.namespace.repository');
        $this->nsController     = $moduleNs . config('infyom.laravel_generator.namespace.controller');
        $this->nsApiController  = $moduleNs . config('infyom.laravel_generator.namespace.api_controller');
        $this->nsBaseController = config('infyom.laravel_generator.namespace.controller', 'App\Http\Controllers');
        $this->nsRequest        = $moduleNs . config('infyom.laravel_generator.namespace.request');
        $this->nsApiRequest     = $moduleNs . config('infyom.laravel_generator.namespace.api_request');
        $this->nsRequestBase    = $moduleNs . config('infyom.laravel_generator.namespace.request');
    }

    public function loadPaths()
    {
        $prefix = $this->prefixes['path'];

        if (!empty($prefix)) {
            $prefix .= '/';
        }

        $viewPrefix = $this->prefixes['view'];

        if (!empty($viewPrefix)) {
            $viewPrefix .= '/';
        }

        $this->pathRepository = config(
            'infyom.laravel_generator.path.repository',
            app_path('Repositories/')
        ) . $prefix;

        $this->pathModel = config('infyom.laravel_generator.path.model', app_path('Models/')) . $prefix;

        if (config('infyom.laravel_generator.ignore_model_prefix', false)) {
            $this->pathModel = config('infyom.laravel_generator.path.model', app_path('Models/'));
        }

        $this->pathDataTables = config('infyom.laravel_generator.path.datatables', app_path('DataTables/')) . $prefix;

        $this->pathApiController = config(
            'infyom.laravel_generator.path.api_controller',
            app_path('Http/Controllers/API/')
        ) . $prefix;

        $this->pathApiRequest = config(
            'infyom.laravel_generator.path.api_request',
            app_path('Http/Requests/API/')
        ) . $prefix;

        $this->pathApiRoutes = config('infyom.laravel_generator.path.api_routes', app_path('Http/api_routes.php'));

        $this->pathApiTests = config('infyom.laravel_generator.path.api_test', base_path('tests/'));

        $this->pathApiTestTraits = config('infyom.laravel_generator.path.test_trait', base_path('tests/traits/'));

        $this->pathController = config(
            'infyom.laravel_generator.path.controller',
            app_path('Http/Controllers/')
        ) . $prefix;

        $this->pathRequest = config('infyom.laravel_generator.path.request', app_path('Http/Requests/')) . $prefix;

        $this->pathRoutes = config('infyom.laravel_generator.path.routes', app_path('Http/routes.php'));

        $this->pathViews = config(
            'infyom.laravel_generator.path.views',
            base_path('resources/views/')
        ) . $viewPrefix . $this->mSnakePlural . '/';

        $this->modelJsPath = config(
            'infyom.laravel_generator.path.modelsJs',
            base_path('resources/assets/js/models/')
        );

        if ($this->options['module'] && $this->addOns['modules'] == true) {
            $this->loadModulePaths();
        }
    }

    //exata: carrega o prefixo de paths pros modulos.
    public function loadModulePaths()
    {
        $modulePath = config('modules.paths.modules') . '/' . $this->options['module'];

        $this->pathRepository    = str_replace(base_path(), $modulePath, $this->pathRepository);
        $this->pathModel         = str_replace(base_path(), $modulePath, $this->pathModel);
        $this->pathDataTables    = str_replace(base_path(), $modulePath, $this->pathDataTables);
        $this->pathApiController = str_replace(base_path(), $modulePath, $this->pathApiController);
        $this->pathApiRequest    = str_replace(base_path(), $modulePath, $this->pathApiRequest);
        $this->pathApiRoutes     = str_replace(base_path(), $modulePath, $this->pathApiRoutes);
        $this->pathApiTests      = str_replace(base_path(), $modulePath, $this->pathApiTests);
        $this->pathApiTestTraits = str_replace(base_path(), $modulePath, $this->pathApiTestTraits);
        $this->pathController    = str_replace(base_path(), $modulePath, $this->pathController);
        $this->pathRequest       = str_replace(base_path(), $modulePath, $this->pathRequest);
        $this->pathRoutes        = str_replace(base_path(), $modulePath, $this->pathRoutes);
        $this->pathViews         = str_replace(base_path(), $modulePath, $this->pathViews);
        $this->modelJsPath       = str_replace(base_path(), $modulePath, $this->modelJsPath);
    }

    public function loadDynamicVariables(CommandData &$commandData)
    {
        $commandData->addDynamicVariable('$NAMESPACE_APP$', $this->nsApp);
        $commandData->addDynamicVariable('$NAMESPACE_REPOSITORY$', $this->nsRepository);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL$', $this->nsModel);
        $commandData->addDynamicVariable('$NAMESPACE_DATATABLES$', $this->nsDataTables);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL_EXTEND$', $this->nsModelExtend);

        $commandData->addDynamicVariable('$NAMESPACE_API_CONTROLLER$', $this->nsApiController);
        $commandData->addDynamicVariable('$NAMESPACE_API_REQUEST$', $this->nsApiRequest);

        $commandData->addDynamicVariable('$NAMESPACE_BASE_CONTROLLER$', $this->nsBaseController);
        $commandData->addDynamicVariable('$NAMESPACE_CONTROLLER$', $this->nsController);
        $commandData->addDynamicVariable('$NAMESPACE_REQUEST$', $this->nsRequest);
        $commandData->addDynamicVariable('$NAMESPACE_REQUEST_BASE$', $this->nsRequestBase);

        $commandData->addDynamicVariable('$TABLE_NAME$', $this->tableName);
        $commandData->addDynamicVariable('$PRIMARY_KEY_NAME$', $this->primaryName);

        $commandData->addDynamicVariable('$MODEL_NAME$', $this->mName);
        $commandData->addDynamicVariable('$MODEL_NAME_CAMEL$', $this->mCamel);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL$', $this->mPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_CAMEL$', $this->mCamelPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_SNAKE$', $this->mSnake);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_SNAKE$', $this->mSnakePlural);
        $commandData->addDynamicVariable('$MODEL_NAME_DASHED$', $this->mDashed);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_DASHED$', $this->mDashedPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_SLASH$', $this->mSlash);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_SLASH$', $this->mSlashPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_HUMAN$', $this->mHuman);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_HUMAN$', $this->mHumanPlural);

        if (!empty($this->prefixes['route'])) {
            $commandData->addDynamicVariable('$ROUTE_NAMED_PREFIX$', $this->prefixes['route'] . '.');
            $commandData->addDynamicVariable('$ROUTE_PREFIX$', str_replace('.', '/', $this->prefixes['route']) . '/');
        } else {
            $commandData->addDynamicVariable('$ROUTE_PREFIX$', '');
            $commandData->addDynamicVariable('$ROUTE_NAMED_PREFIX$', '');
        }

        if (!empty($this->prefixes['ns'])) {
            $commandData->addDynamicVariable('$PATH_PREFIX$', $this->prefixes['ns'] . '\\');
        } else {
            $commandData->addDynamicVariable('$PATH_PREFIX$', '');
        }

        if (!empty($this->prefixes['view'])) {
            $commandData->addDynamicVariable('$VIEW_PREFIX$', str_replace('/', '.', $this->prefixes['view']) . '.');
        } else {
            $commandData->addDynamicVariable('$VIEW_PREFIX$', '');
        }

        if (!empty($this->prefixes['public'])) {
            $commandData->addDynamicVariable('$PUBLIC_PREFIX$', $this->prefixes['public']);
        } else {
            $commandData->addDynamicVariable('$PUBLIC_PREFIX$', '');
        }

        $commandData->addDynamicVariable(
            '$API_PREFIX$',
            config('infyom.laravel_generator.api_prefix', 'api')
        );

        $commandData->addDynamicVariable(
            '$API_VERSION$',
            config('infyom.laravel_generator.api_version', 'v1')
        );

        /**
         * Exata Sistemas
         * Adiciona parâmetro para definir classe padrão para os fields
         */
        $commandData->addDynamicVariable(
            '$FIELD_CLASS$',
            config('infyom.laravel_generator.field_class', 'col-md-12')
        );

        /**
         * Exata Sistemas
         * Adiciona parâmetro para definir classe padrão para os fields
         */
        $commandData->addDynamicVariable(
            '$FIELD_CLASS$',
            config('infyom.laravel_generator.field_class', 'col-md-12')
        );

        return $commandData;
        return $commandData;
    }

    public function prepareTableName()
    {
        if ($this->getOption('tableName')) {
            $this->tableName = $this->getOption('tableName');
        } else {
            //Exata Sistemas: Quando não é setado o nome da tabela é setada aqui
            //o padrão usando o nome do modelo no plural
            $this->tableName = $this->mSnakePlural;
        }
    }

    public function preparePrimaryName()
    {
        if ($this->getOption('primary')) {
            $this->primaryName = $this->getOption('primary');
        } else {
            $this->primaryName = 'id';
        }
    }

    public function prepareModelNames()
    {
        $this->mPlural       = Str::plural($this->mName);
        $this->mCamel        = Str::camel($this->mName);
        $this->mCamelPlural  = Str::camel($this->mPlural);
        $this->mSnake        = Str::snake($this->mName);
        $this->mSnakePlural  = Str::snake($this->mPlural);
        $this->mDashed       = str_replace('_', '-', Str::snake($this->mSnake));
        $this->mDashedPlural = str_replace('_', '-', Str::snake($this->mSnakePlural));
        $this->mSlash        = str_replace('_', '/', Str::snake($this->mSnake));
        $this->mSlashPlural  = str_replace('_', '/', Str::snake($this->mSnakePlural));
        $this->mHuman        = title_case(str_replace('_', ' ', Str::snake($this->mSnake)));
        $this->mHumanPlural  = title_case(str_replace('_', ' ', Str::snake($this->mSnakePlural)));
    }

    /*
     * Valida e prepara as opções enviadas na linha de comando.
     */
    public function prepareOptions(CommandData &$commandData)
    {
        foreach (self::$availableOptions as $option) {
            $this->options[$option] = $commandData->commandObj->option($option);
        }

        if (isset($options['fromTable']) and $this->options['fromTable']) {
            if (!$this->options['tableName']) {
                $commandData->commandError('tableName required with fromTable option.');
                exit;
            }
        }

        $this->options['softDelete'] = config('infyom.laravel_generator.options.softDelete', false);
        if (!empty($this->options['skip'])) {
            $this->options['skip'] = array_map('trim', explode(',', $this->options['skip']));
        }

        if (!empty($this->options['datatables'])) {
            if (strtolower($this->options['datatables']) == 'true') {
                $this->addOns['datatables'] = true;
            } else {
                $this->addOns['datatables'] = false;
            }
        }

        //exata: valida se add do modulos esta ativo.
        if (!empty($this->options['module'])) {
            if ($this->addOns['modules'] == false) {
                $commandData->commandError('Modules add-on not active.');
                exit;
            } else {
                // Exata: Adicionado o modulo no array para poder substituir o valor no
                // arquivo Stub
                $commandData->addDynamicVariable(
                    '$MODULE_NAME$',
                    $this->options['module']
                );
            }
        }
    }

    public function preparePrefixes()
    {
        $this->prefixes['route']  = explode('/', config('infyom.laravel_generator.prefixes.route', ''));
        $this->prefixes['path']   = explode('/', config('infyom.laravel_generator.prefixes.path', ''));
        $this->prefixes['view']   = explode('.', config('infyom.laravel_generator.prefixes.view', ''));
        $this->prefixes['public'] = explode('/', config('infyom.laravel_generator.prefixes.public', ''));

        if ($this->getOption('prefix')) {
            $multiplePrefixes = explode(',', $this->getOption('prefix'));

            $this->prefixes['route']  = array_merge($this->prefixes['route'], $multiplePrefixes);
            $this->prefixes['path']   = array_merge($this->prefixes['path'], $multiplePrefixes);
            $this->prefixes['view']   = array_merge($this->prefixes['view'], $multiplePrefixes);
            $this->prefixes['public'] = array_merge($this->prefixes['public'], $multiplePrefixes);
        }

        $this->prefixes['route']  = array_diff($this->prefixes['route'], ['']);
        $this->prefixes['path']   = array_diff($this->prefixes['path'], ['']);
        $this->prefixes['view']   = array_diff($this->prefixes['view'], ['']);
        $this->prefixes['public'] = array_diff($this->prefixes['public'], ['']);

        $routePrefix = '';

        foreach ($this->prefixes['route'] as $singlePrefix) {
            $routePrefix .= Str::camel($singlePrefix) . '.';
        }

        if (!empty($routePrefix)) {
            $routePrefix = substr($routePrefix, 0, strlen($routePrefix) - 1);
        }

        $this->prefixes['route'] = $routePrefix;

        $nsPrefix = '';

        foreach ($this->prefixes['path'] as $singlePrefix) {
            $nsPrefix .= Str::title($singlePrefix) . '\\';
        }

        if (!empty($nsPrefix)) {
            $nsPrefix = substr($nsPrefix, 0, strlen($nsPrefix) - 1);
        }

        $this->prefixes['ns'] = $nsPrefix;

        $pathPrefix = '';

        foreach ($this->prefixes['path'] as $singlePrefix) {
            $pathPrefix .= Str::title($singlePrefix) . '/';
        }

        if (!empty($pathPrefix)) {
            $pathPrefix = substr($pathPrefix, 0, strlen($pathPrefix) - 1);
        }

        $this->prefixes['path'] = $pathPrefix;

        $viewPrefix = '';

        foreach ($this->prefixes['view'] as $singlePrefix) {
            $viewPrefix .= Str::camel($singlePrefix) . '/';
        }

        if (!empty($viewPrefix)) {
            $viewPrefix = substr($viewPrefix, 0, strlen($viewPrefix) - 1);
        }

        $this->prefixes['view'] = $viewPrefix;

        $publicPrefix = '';

        foreach ($this->prefixes['public'] as $singlePrefix) {
            $publicPrefix .= Str::camel($singlePrefix) . '/';
        }

        if (!empty($publicPrefix)) {
            $publicPrefix = substr($publicPrefix, 0, strlen($publicPrefix) - 1);
        }

        $this->prefixes['public'] = $publicPrefix;
    }

    public function overrideOptionsFromJsonFile($jsonData)
    {
        $options = self::$availableOptions;

        foreach ($options as $option) {
            if (isset($jsonData['options'][$option])) {
                $this->setOption($option, $jsonData['options'][$option]);
            }
        }

        //exata: adicionado addon modules
        $addOns = ['swagger', 'tests', 'datatables', 'modules'];

        foreach ($addOns as $addOn) {
            if (isset($jsonData['addOns'][$addOn])) {
                $this->addOns[$addOn] = $jsonData['addOns'][$addOn];
            }
        }
    }

    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        return false;
    }

    public function getAddOn($addOn)
    {
        if (isset($this->addOns[$addOn])) {
            return $this->addOns[$addOn];
        }

        return false;
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    public function prepareAddOns()
    {
        $this->addOns['swagger']    = config('infyom.laravel_generator.add_on.swagger', false);
        $this->addOns['tests']      = config('infyom.laravel_generator.add_on.tests', false);
        $this->addOns['datatables'] = config('infyom.laravel_generator.add_on.datatables', false);

        //exata: verifica se o add modules esta ativo.
        $this->addOns['modules'] = config('infyom.laravel_generator.add_on.modules', false);

        $this->addOns['menu.enabled']   = config('infyom.laravel_generator.add_on.menu.enabled', false);
        $this->addOns['menu.menu_file'] = config('infyom.laravel_generator.add_on.menu.menu_file', 'layouts.menu');
    }
}

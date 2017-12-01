<?php

namespace InfyOm\Generator\Generators\API;

use Illuminate\Support\Str;
use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Generators\BaseGenerator;

class APIRoutesGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $routeContents;

    /** @var string */
    private $routesTemplate;

    /** @var string Exata:Caminho do arquivo start que fica na raiz dos modulos*/
    private $pathRegister;

    /** @var string Exata:Conteúdo do arquivo start que fica na raiz dos modulos*/
    private $registerRouteContents;

    /** @var string Exata:Template do arquivo start que fica na raiz dos modulos*/
    private $registerRoutesTemplate;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathApiRoutes;

        //Exata:Captura o conteudo do arquivo , mas se não existe cria um novo.
        $this->routeContents = get_content_create($this->path, "<?php\n");
        
        /**
         * Exata:Captura o conteúdo do arquivo start que fica na raiz dos módulos e compara
         * com o template e se for diferente ele substitui.
         * Foi feita a alteração desse jeito para não precisar criar um fork no Laravel Modules.
         */
        $this->pathRegister = $commandData->config->pathRegisterRoutes;
        $this->registerRoutesTemplate = get_template('routes.start', 'laravel-generator');
        $this->registerRouteContents = get_content_create($this->pathRegister);
        if ($this->registerRouteContents != $this->registerRoutesTemplate) {
            file_put_contents($this->pathRegister, $this->registerRoutesTemplate);
        }

        if (!empty($this->commandData->config->prefixes['route'])) {
            $routesTemplate = get_template('api.routes.prefix_routes', 'laravel-generator');
        } else {
            $routesTemplate = get_template('api.routes.routes', 'laravel-generator');
        }

        $this->routesTemplate = fill_template($this->commandData->dynamicVars, $routesTemplate);
    }

    public function generate()
    {
        $this->routeContents .= "\n\n".$this->routesTemplate;

        file_put_contents($this->path, $this->routeContents);

        $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' api routes added.');
    }

    public function rollback()
    {
        if (Str::contains($this->routeContents, $this->routesTemplate)) {
            $this->routeContents = str_replace($this->routesTemplate, '', $this->routeContents);
            file_put_contents($this->path, $this->routeContents);
            $this->commandData->commandComment('api routes deleted');
        }
    }
}

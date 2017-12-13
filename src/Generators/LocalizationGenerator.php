<?php

namespace InfyOm\Generator\Generators;

use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Common\GeneratorFieldRelation;
use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Utils\TableFieldsGenerator;

class LocalizationGenerator extends BaseGenerator
{
    /**
     * Fields not included in the generator by default.
     *
     * @var array
     */
    private $excluded;

    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;
    private $fileName;
    private $table;

    /**
     * ModelGenerator constructor.
     *
     * @param \InfyOm\Generator\Common\CommandData $commandData
     */
    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->excluded    = config('infyom.laravel_generator.ignore_fields');
        $this->path        = $commandData->config->pathLocalization;
        $this->fileName    = $this->commandData->modelName . '.php';
        $this->table       = $this->commandData->dynamicVars['$TABLE_NAME$'];
    }

    public function generate()
    {

        $templateData = get_template('localization.localization', 'laravel-generator');

        $templateData = $this->fillTemplate($templateData);

        $langFolders = config('infyom.laravel_generator.languages');

        foreach ($langFolders as $k => $lang) {
            FileUtil::createFile($this->path.$lang.'/', $this->fileName, $templateData);
            $this->commandData->commandComment("\nLanguage file created: ");
            $this->commandData->commandInfo($this->path.$lang.'/'.$this->fileName);
        }
    }

    private function fillTemplate($templateData)
    {

        //$templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $langKeys = implode("\n", $this->generateLocalizationKeys());

        $templateData = str_replace('$LANG_KEYS$', $langKeys, $templateData);
        $templateData = str_replace('$TABLE_NAME$', $this->commandData->dynamicVars['$TABLE_NAME$'], $templateData);

        return $templateData;
    }

    public function generateLocalizationKeys()
    {
        $langKeys = [];
        foreach ($this->commandData->fields as $field) {
            if(!in_array($field->name, $this->excluded)) {
                $langKeys[] = "    ".$field->localizationText;
            }
        }
        return $langKeys;
    }

    public function fillDatabase() {
        //
    }

    public function rollback()
    {
        foreach ($langFolders as $k => $lang) {
            if ($this->rollbackFile($this->path.$lang, $this->fileName)) {
            $this->commandData->commandComment('Localization file deleted: ' . $this->fileName);
            }
        }
    }
}

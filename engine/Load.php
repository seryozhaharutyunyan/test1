<?php

namespace Engine;

use Engine\DI\DI;

/**
 *
 */
class Load
{
    const MASK_MODEL_ENTITY = '\%s\Model\%s\%s';
    const MASK_MODEL_REPOSITORY = '\%s\Model\%s\%sRepository';

    const FILE_MASK_LANGUAGE = 'Language/%s/%s.ini';

    public DI $di;

    /**
     * @param DI $di
     */
    public function __construct(DI $di)
    {
        $this->di = $di;

        return $this;
    }

    /**
     * @param string $modelName
     * @param string|bool $modelDir
     * @param string|bool $env
     * @return bool
     */
    public function model(string $modelName, string|bool $modelDir = false, string|bool $env = false): bool
    {
        $modelName = ucfirst($modelName);
        $modelDir = $modelDir ? $modelDir : $modelName;
        $env = $env ? $env : ENV;

        $namespaceModel = sprintf(
            self::MASK_MODEL_REPOSITORY,
            $env, $modelDir, $modelName
        );

        $isClassModel = class_exists($namespaceModel);

        if ($isClassModel) {

            $modelRegistry = $this->di->get('model') ?: new \stdClass();
            $modelRegistry->{lcfirst($modelName)} = new $namespaceModel($this->di);

            $this->di->set('model', $modelRegistry);
        }

        return $isClassModel;
    }

    /**
     * @param string $path Format: [a-z0-9/_]
     * @return array
     */
    public function language(string $path): array
    {
        $file = sprintf(
            self::FILE_MASK_LANGUAGE,
            'english', $path
        );

        $content = parse_ini_file($file, true);

        $languageName = $this->toCamelCase($path);

        $language = $this->di->get('language') ?: new \stdClass();
        $language->{$languageName} = $content;

        $this->di->set('language', $language);

        return $content;
    }

    /**
     * @param string $str
     * @return string
     */
    private function toCamelCase(string $str): string
    {
        $replace = preg_replace('/[^a-zA-Z0-9]/', ' ', $str);
        $convert = mb_convert_case($replace, MB_CASE_TITLE);
        return lcfirst(str_replace(' ', '', $convert));
    }
}
<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;

/**
 * @NewsComponent
 *
 * Роутинг для списка новостей и детального просмотра
 */
class NewsComponent extends \CBitrixComponent
{
    /** @var array - шаблоны путей по умолчанию */
    protected array $defaultUrlTemplates404 = [];
    /** @var array - переменные шаблонов путей */
    protected array $componentVariables = [];
    /** @var string - страница шаблонов */
    protected string $page = '';

    /**
     * NewsComponent constructor
     *
     * @param null $component
     * @throws Main\LoaderException
     */
    public function __construct($component = null)
    {
        parent::__construct($component);

        if (!Main\Loader::includeModule('iblock')) {
            throw new Main\LoaderException('Не подключен модуль IBlock');
        }

        $this->defaultUrlTemplates404 = array(
            'list' => 'list.php',
            'detail' => 'detail/#ELEMENT_ID#/'
        );
        $this->componentVariables = array('ELEMENT_ID');
    }

    /**
     * Роутер для отображения компонентов
     *
     * @return void
     */
    protected function route(): void
    {
        $urlTemplates = [];

        if ($this->arParams['SEF_MODE'] === 'Y') {

            $variables = [];

            $urlTemplates = \CComponentEngine::MakeComponentUrlTemplates(
                $this->defaultUrlTemplates404,
                $this->arParams['SEF_URL_TEMPLATES']
            );

            $variableAliases = \CComponentEngine::MakeComponentVariableAliases(
                $this->defaultUrlTemplates404,
                $this->arParams['VARIABLE_ALIASES']
            );

            $engine = new CComponentEngine($this);

            $engine->addGreedyPart("#SECTION_CODE_PATH#");
            $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));

            $this->page = $engine->guessComponentPath(
                $this->arParams['SEF_FOLDER'],
                $urlTemplates,
                $variables
            );

            if (strlen($this->page) <= 0) {
                $this->page = 'list';
            }

            \CComponentEngine::InitComponentVariables(
                $this->page,
                $this->componentVariables, $variableAliases,
                $variables
            );
        }
        else {
            $this->page = 'list';
        }

        $this->arResult = array(
            'FOLDER' => $this->arParams['SEF_FOLDER'],
            'URL_TEMPLATES' => $urlTemplates,
            'VARIABLES' => $variables ?? [],
            'ALIASES' => $variableAliases ?? []
        );
    }

    /**
     * выполняет логику работы компонента
     */
    public function executeComponent()
    {
        try {
            $this->route();
            $this->includeComponentTemplate($this->page);
        }
        catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }
}
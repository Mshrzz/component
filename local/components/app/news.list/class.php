<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;
use \Bitrix\Main\Data\Cache;

/**
 * @NewsList
 *
 * Компонент списка новостей
 */
class NewsList extends \CBitrixComponent
{
    /** @var string - Путь до кеша */
    protected const CACHE_PATH = 'appnewslist';
    /** @var string - Ключ кеша */
    protected const CACHE_KEY = 'appnewslistkey';

    /** @var int - количество элементов, отображаемых за раз */
    protected int $navCount = 3;
    /** @var int - текущая страница */
    protected int $currentPage = 1;
    /** @var array - фильтр для элементов */
    protected array $arFilter = [];

    /**
     * NewsList constructor
     *
     * @param mixed $component
     * @throws Main\LoaderException
     */
    public function __construct($component = null)
    {
        parent::__construct($component);

        if (!Main\Loader::includeModule('iblock')) {
            throw new Main\LoaderException('Не подключен модуль IBlock');
        }

        if ((int) $this->arParams['NAV_COUNT']) {
            $this->navCount = $this->arParams['NAV_COUNT'];
        }

        $this->arResult['NAV_COUNT'] = $this->navCount;

        if (is_numeric($this->request['PAGE'])) {
            $this->currentPage = (int) trim(strip_tags($this->request['PAGE'])) + 1;
        }

        $this->arFilter = [
            'ACTIVE' => 'Y'
        ];
    }

    /**
     * Возвращает объект навигации
     *
     * @return Main\UI\PageNavigation
     * @throws Exception
     */
    protected function getNav(): \Bitrix\Main\UI\PageNavigation
    {
        try {
            $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
            $nav->allowAllRecords(true)->setPageSize($this->navCount)->initFromUri();
            $nav->setCurrentPage($this->currentPage);
            $listsCount = $this->getCount();
            $nav->setRecordCount($listsCount);

            return $nav;
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Возвращает параметры навигации
     *
     * @return array
     */
    protected function getNavParams(): array
    {
        try {
            $oNav = $this->getNav();
            $offset = $oNav->getOffset();
            $limit = $oNav->getLimit();
        }
        catch (Exception $e) {
            $offset = [];
            $limit = [];
        }

        return [
            'OFFSET' => $offset,
            'LIMIT' => $limit
        ];
    }

    /**
     * Возвращает количество новостей
     *
     * @return int
     * @throws Exception
     */
    protected function getCount(): int
    {
        try {
            return \Bitrix\Iblock\Elements\ElementNewsTable::getList([
                'filter' => $this->arFilter
            ])->getSelectedRowsCount();
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Устанавливает в @var array $arResult список новостей
     *
     * @return void
     */
    protected function setData(): void
    {
        try {
            if ($this->arParams['SHOW_NAV'] === 'Y') {
                $navParams = $this->getNavParams();
            }

            $arNews = \Bitrix\Iblock\Elements\ElementNewsTable::getList([
                'order' => [
                    $this->arParams['SORT_FIELD1'] => $this->arParams['SORT_DIRECTION1'],
                    $this->arParams['SORT_FIELD2'] => $this->arParams['SORT_DIRECTION2']
                ],
                'select' => [
                    'ID',
                    'NAME',
                    'CODE',
                    'PREVIEW_PICTURE',
                    'PREVIEW_TEXT',
                    'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL'
                ],
                'filter' => $this->arFilter,
                'offset' => $navParams['OFFSET'] ?? '',
                'limit' => $navParams['LIMIT'] ?? ''
            ])->fetchAll();

            $this->arResult['ITEMS'] = $arNews ?? [];
            $this->arResult['ITEMS_COUNT'] = $this->getCount();

        } catch (Exception $e) {
            $this->arResult['ITEMS'] = [];
            $this->arResult['ITEMS_COUNT'] = 0;
        }
    }

    /**
     * Вызывает компонент
     *
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        $oCache = Cache::createInstance();
        $cacheTtl = $this->arParams['CACHE_TYPE'] !== 'N' ? $this->arParams['CACHE_TIME'] : 0;

        /**
         * @note
         * В данном случае целесообразнее было сделать
         * кеширование выборки getList'а на получение новостей
         * Подключил стороннюю библиотеку для работы с blade
         * уже после того, как написал всю логику компонента
         * и как выяснилось в процессе данная зависимость
         * немного меняет процесс кеширования html-страниц
         */
        try
        {
            if ($oCache->initCache($cacheTtl, self::CACHE_KEY, self::CACHE_PATH))
            {
                $vars = $oCache->getVars();
                $this->arResult = $vars['data'];
                $this->includeComponentTemplate();
            }
            elseif ($oCache->startDataCache())
            {
                $this->setData();
                $this->includeComponentTemplate();

                $data = $this->arResult;
                $html = $this->getTemplateCachedData();

                $vars = [
                    'data' => $data,
                    'html' => $html
                ];

                $oCache->endDataCache($vars);
            }
        }
        catch (Exception $e)
        {
            $oCache->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}
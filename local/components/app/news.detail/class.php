<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;
use \Bitrix\Main\Data\Cache;

/**
 * @NewsDetail
 *
 * Компонент детального просмотра новостей
 */
class NewsDetail extends \CBitrixComponent
{
    /** @var string - Путь до кеша */
    protected const CACHE_PATH = 'appnewsdetail';
    /** @var string - Ключ кеша */
    protected const CACHE_KEY = 'appnewsdetailkey';
    /** @var array - фильтр */
    protected array $arFilter = [];

    /**
     * NewsDetail constructor
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
    }

    /**
     * Устанавливает в @var array $arResult данные новости
     *
     * @return void
     */
    protected function setData(): void
    {
        try {
            $arNews = \Bitrix\Iblock\Elements\ElementNewsTable::getList([
                'filter' => [
                    'ACTIVE' => 'Y',
                    '=ID' => $this->arParams['ID']
                ],
                'select' => [
                    'NAME',
                    'DETAIL_TEXT',
                    'DETAIL_PICTURE'
                ]
            ])->fetch();

            $this->arResult['ITEM'] = $arNews ?? [];

        } catch (Exception $e) {
            $this->arResult['ITEM'] = [];
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
<?php
/** @var array $arResult */

foreach ($arResult['ITEMS'] as $key => $arItem) {
    if ((int) $arItem['PREVIEW_PICTURE']) {
        $arResult['ITEMS'][$key]['PICTURE_SRC'] = \CFile::GetPath($arItem['PREVIEW_PICTURE']);
    }
    $arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = CIBlock::ReplaceDetailUrl($arItem['DETAIL_PAGE_URL'], $arItem, false, 'E');
}
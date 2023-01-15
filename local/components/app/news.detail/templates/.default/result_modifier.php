<?php
/** @var array $arResult */

if ($arResult['ITEM']['DETAIL_PICTURE']) {
    $arResult['ITEM']['DETAIL_PICTURE_SRC'] = CFile::GetPath($arResult['ITEM']['DETAIL_PICTURE']);
}
<?php

/** @global Cmain $APPLICATION */
/** @var array $arResult */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$APPLICATION->IncludeComponent('app:news.detail', '', [
    'ID' => $arResult['VARIABLES']['ELEMENT_ID'],
    'CACHE_TIME' => '0'
]);
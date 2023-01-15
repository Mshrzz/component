<?php

/** @global Cmain $APPLICATION */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$APPLICATION->IncludeComponent('app:news.list', '', [
    'SHOW_NAV' => 'Y',
    'SORT_FIELD1' => 'NAME',
    'SORT_FIELD2' => 'DATE_CREATE',
    'SORT_DIRECTION1' => 'ASC',
    'SORT_DIRECTION2' => 'DESC',
    'CACHE_TIME' => '0'
]);
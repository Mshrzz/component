<?php
/** @var Cmain $APPLICATION */

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

if ($_REQUEST['DETAIL'] === 'Y') {
    $APPLICATION->IncludeComponent('app:news.detail', '', [
        'ID' => 1,
        'CACHE_TIME' => '0'
    ]);
} else {
    $APPLICATION->IncludeComponent('app:news.list', '', [
        'SHOW_NAV' => 'Y',
        'SORT_FIELD1' => 'NAME',
        'SORT_FIELD2' => 'DATE_CREATE',
        'SORT_DIRECTION1' => 'ASC',
        'SORT_DIRECTION2' => 'DESC',
        'CACHE_TIME' => '0'
    ]);
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
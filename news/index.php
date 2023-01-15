<?php
/** @global Cmain $APPLICATION */

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";

$APPLICATION->SetTitle("Новости");

$APPLICATION->IncludeComponent('app:news', '', [
    'SEF_MODE' => 'Y',
    'SEF_URL_TEMPLATES' => [
        "news" => "list",
        "detail" => "detail/#ELEMENT_ID#/"
    ],
    'SEF_FOLDER' => '/news/'
]);

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
<?php
/** @var CMain $APPLICATION */
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle("Тестовое задание №1");
$APPLICATION->IncludeComponent('app:form', '', [], false);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
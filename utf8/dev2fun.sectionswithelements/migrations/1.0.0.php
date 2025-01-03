<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$moduleID = 'dev2fun.sectionswithelements';

CModule::IncludeModule("main");
CModule::IncludeModule($moduleID);

IncludeModuleLangFile(__FILE__);

if(!CopyDirFiles(__DIR__."/install/components/dev2fun", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/dev2fun/", true, true)){
    throw new Exception("Dont copy files:". __DIR__."/install/components/dev2fun");
}

echo '1.0.0 - DONE'.PHP_EOL;
<?php
IncludeModuleLangFile(__FILE__);

/**
 *
 * @author dev2fun (darkfriend)
 * @copyright darkfriend
 * @version 0.1.1
 *
 */
if (class_exists("dev2fun_sectionswithelements")) return;

class dev2fun_sectionswithelements extends CModule
{
    public $MODULE_ID = "dev2fun.sectionswithelements";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = '0.1.3';
            $this->MODULE_VERSION_DATE = '2014-12-09 15:00:00';
        }
        $this->MODULE_NAME = GetMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "dev2fun";
        $this->PARTNER_URI = "http://dev2fun.com/";
    }

    public function DoInstall()
    {
        global $APPLICATION;
        if (!check_bitrix_sessid()) return;

        $APPLICATION->IncludeAdminFile(GetMessage("STEP1"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/dev2fun.sectionswithelements/install/step1.php");
    }


    public function DoUninstall()
    {
        global $APPLICATION;
        if (!check_bitrix_sessid()) return;

        $APPLICATION->IncludeAdminFile(GetMessage("UNSTEP1"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/dev2fun.sectionswithelements/install/unstep1.php");
    }
}

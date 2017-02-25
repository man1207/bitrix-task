<?
IncludeModuleLangFile(__FILE__); //Подключаем языковые файлы


if(class_exists("ukrequest")) return;
Class ukrequest extends CModule
{
    var $MODULE_ID = "ukrequest";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function ukrequest()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = GetMessage('MODULE_TITLE');
        $this->MODULE_DESCRIPTION = GetMessage('MODULE_DESCRIPTION');
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage('INSTALL_TITLE'), $DOCUMENT_ROOT."/bitrix/modules/ukrequest/install/step.php");
        return true;
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage('UNINSTALL_TITLE') , $DOCUMENT_ROOT."/bitrix/modules/ukrequest/install/unstep.php");
        return true;
    }
}
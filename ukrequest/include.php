<?
CModule::IncludeModule("ukrequest");
global $DBType;

$arClasses=array(
    'cUkRequest'=>'classes/general/cUkRequest.php'
);

CModule::AddAutoloadClasses("ukrequest", $arClasses);
?>

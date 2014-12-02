<?
/**
* 
* @author dev2fun (darkfriend)
* @copyright darkfriend
* @version 0.1.1
* 
*/
if(!check_bitrix_sessid()) return;

CModule::IncludeModule("main");
CModule::AddAutoloadClasses(
	'',
	array(
		'dev2fun_sectionswithelements' => '/bitrix/modules/dev2fun.sectionswithelements/install/index.php',
		"CCheckPermForComponents" => '/bitrix/modules/dev2fun.sectionswithelements/classes/general/main.php',
	)
);
$CDev2fun_sectionswithelements = new dev2fun_sectionswithelements();

//check permissions /bitrix/components/
$permBX = CCheckPermForComponents::checkPermissions($_SERVER["DOCUMENT_ROOT"]."/bitrix/components");
if(!($permBX===true)){
	CAdminMessage::ShowMessage(GetMessage($permBX, array('#PATH#'=>'/bitrix/components/')));
	return false;
}

//удаление всех файлов компонента
if(!DeleteDirFilesEx("/bitrix/components/dev2fun/section.element.group")){
	CAdminMessage::ShowMessage(GetMessage("ERRORS_DELETE_DIR_ED"));
	return false;
}

UnRegisterModule($CDev2fun_sectionswithelements->MODULE_ID);

echo CAdminMessage::ShowNote(GetMessage("UNINSTALL_SUCCESS"));
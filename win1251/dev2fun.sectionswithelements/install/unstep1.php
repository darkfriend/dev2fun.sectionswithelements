<?php
/**
 *
 * @author dev2fun (darkfriend)
 * @copyright darkfriend
 * @version 1.0.0
 *
 */
if (!check_bitrix_sessid()) return;

CModule::IncludeModule("main");
CModule::AddAutoloadClasses(
    '',
    [
        'dev2fun_sectionswithelements' => '/bitrix/modules/dev2fun.sectionswithelements/install/index.php',
        "CCheckPermForComponents" => '/bitrix/modules/dev2fun.sectionswithelements/classes/general/main.php',
    ]
);
$CDev2fun_sectionswithelements = new dev2fun_sectionswithelements();

//check permissions /bitrix/components/
$permBX = CCheckPermForComponents::checkPermissions($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components");
if (!($permBX === true)) {
    CAdminMessage::ShowMessage(GetMessage("D2F_{$permBX}", ['#PATH#' => '/bitrix/components/']));
    return false;
}

//�������� ���� ������ ����������
if (!DeleteDirFilesEx("/bitrix/components/dev2fun/section.element.group")) {
    CAdminMessage::ShowMessage(GetMessage("D2F_ERRORS_DELETE_DIR_ED"));
    return false;
}

UnRegisterModule($CDev2fun_sectionswithelements->MODULE_ID);

echo CAdminMessage::ShowNote(GetMessage("D2F_UNINSTALL_SUCCESS"));
<?php
/**
 *
 * @author dev2fun (darkfriend)
 * @copyright darkfriend
 * @version 0.2.1
 *
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arCurrentValues */

if (!CModule::IncludeModule("iblock"))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(["-" => " "]);

$arIBlocks = [];
$db_iblock = CIBlock::GetList(["SORT" => "ASC"], ["SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")]);
while ($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arSorts = ["ASC" => GetMessage("T_IBLOCK_DESC_ASC"), "DESC" => GetMessage("T_IBLOCK_DESC_DESC")];
$arSortFields = [
    "ID" => GetMessage("T_IBLOCK_DESC_FID"),
    "NAME" => GetMessage("T_IBLOCK_DESC_FNAME"),
    "ACTIVE_FROM" => GetMessage("T_IBLOCK_DESC_FACT"),
    "SORT" => GetMessage("T_IBLOCK_DESC_FSORT"),
    "TIMESTAMP_X" => GetMessage("T_IBLOCK_DESC_FTSAMP"),
];

$arProperty_LNS = [];
$arProperty_LNS['ALL'] = GetMessage("T_PROP_DESC_ALL");
$rsProp = CIBlockProperty::GetList(["sort" => "asc", "name" => "asc"], ["ACTIVE" => "Y", "IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])]);
while ($arr = $rsProp->Fetch()) {
    $arProperty[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    if (in_array($arr["PROPERTY_TYPE"], ["L", "N", "S"])) {
        $arProperty_LNS[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    }
}

$arProperty_SECTION = [];
$rsProp = CIBlockSection::GetList(
    ["sort" => "asc", "name" => "asc"],
    [
        "ACTIVE" => "Y",
        "IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"]),
    ],
    false,
    [
        'ID', 'UF_*',
    ]
);
if ($arr = $rsProp->Fetch()) {
    unset($arr['ID']);
    foreach ($arr as $prKey => $prVal) {
        $arProperty_SECTION[$prKey] = $prKey;
    }
}

$arComponentParameters = [
    "GROUPS" => [
        'SECTION' => [
            'NAME' => GetMessage("T_IBLOCK_DESC_SETTINGS_SECTIONS"),
            'SORT' => '110',
        ],
        'ELEMENTS' => [
            'NAME' => GetMessage("T_IBLOCK_DESC_SETTINGS_ELEMENTS"),
            'SORT' => '120',
        ],
        'TEMP' => [
            'NAME' => 'Template settings',
            'SORT' => '130',
        ],
    ],
    "PARAMETERS" => [
        "AJAX_MODE" => [],
        "IBLOCK_TYPE" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ],
        "TEMP_OUTPUT_SECTIONS" => [
            "PARENT" => "TEMP",
            "NAME" => 'Файл шаблона вывода разделов',
            "TYPE" => "STRING",
            "DEFAULT" => 'subSection.php',
        ],
        "TEMP_OUTPUT_ELEMETS" => [
            "PARENT" => "TEMP",
            "NAME" => 'Файл шаблона вывода элементов',
            "TYPE" => "STRING",
            "DEFAULT" => 'element.php',
        ],
        //section.start
        "SECTION_DEPTH" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_DESC_DEPTH"),
            "TYPE" => "STRING",
        ],
        "SECTION_COUNT" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_DESC_COUNT_SECTIONS"),
            "TYPE" => "STRING",
            "DEFAULT" => "30",
        ],
        "SECTION_SORT_BY1" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SECTIONS_IBORD1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ID",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SECTION_SORT_ORDER1" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SECTIONS_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SECTION_SORT_BY2" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SECTIONS_IBORD2"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SECTION_SORT_ORDER2" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SECTIONS_IBBY2"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SECTION_FILTER_NAME" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_FILTER"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ],
        "SECTION_PROPERTY_CODE" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_SECTION,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SECTION_DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
            "SECTION",
            "SECTION_URL",
            GetMessage("T_IBLOCK_DESC_SECTION_PAGE_URL"),
            "",
            "SECTION"
        ),
        "SECTION_CHECK_EMPTY" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SECTION_EMPTY_ELEMENTS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "SECTION_CNT_ELEMENTS" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SECTION_CNT_ELEMENTS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "SECTION_CHILD" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SHOW_SECTION_CHILD"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "DISPLAY_SECTION_PICTURE" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_DISPLAY_SECTION_PICTURE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "PARENT_SECTION" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("IBLOCK_SECTION_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => '',
            "MULTIPLE" => "Y",
        ],
        "PARENT_SECTION_CODE" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("IBLOCK_SECTION_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => '',
            "MULTIPLE" => "Y",
        ],
        "SECTION_PREVIEW_TRUNCATE_LEN" => [
            "PARENT" => "SECTION",
            "NAME" => GetMessage("T_IBLOCK_SECTION_PREVIEW_TRUNCATE_LEN"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ],
        //section.end
        "NEWS_SHOW_SECTION" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_NEWS_SHOW_SECTION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "NEWS_COUNT" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
            "TYPE" => "STRING",
            "DEFAULT" => "40",
        ],
        "SORT_BY1" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ID",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SORT_ORDER1" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SORT_BY2" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SORT_ORDER2" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "FILTER_NAME" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_FILTER"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ],
        "FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "ELEMENTS"),
        "PROPERTY_CODE" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNS,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "CHECK_DATES" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("T_IBLOCK_DESC_CHECK_DATES"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "INCLUDE_SUBSECTIONS" => [
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("CP_BNL_INCLUDE_SUBSECTIONS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
            "DETAIL",
            "DETAIL_URL",
            GetMessage("T_IBLOCK_DESC_DETAIL_PAGE_URL"),
            "",
            "URL_TEMPLATES"
        ),
        "PREVIEW_TRUNCATE_LEN" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_PREVIEW_TRUNCATE_LEN"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ],
        "ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "ADDITIONAL_SETTINGS"),
        "SET_TITLE" => [],
        "SET_BROWSER_TITLE" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BNL_SET_BROWSER_TITLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "SET_META_KEYWORDS" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BNL_SET_META_KEYWORDS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "SET_META_DESCRIPTION" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BNL_SET_META_DESCRIPTION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "SET_STATUS_404" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("CP_BNL_SET_STATUS_404"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "INCLUDE_IBLOCK_INTO_CHAIN" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_INCLUDE_IBLOCK_INTO_CHAIN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "ADD_SECTIONS_CHAIN" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_ADD_SECTIONS_CHAIN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "HIDE_LINK_WHEN_NO_DETAIL" => [
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("T_IBLOCK_DESC_HIDE_LINK_WHEN_NO_DETAIL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "CACHE_TIME" => ["DEFAULT" => 36000000],
        "CACHE_FILTER" => [
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("IBLOCK_CACHE_FILTER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "CACHE_GROUPS" => [
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
    ],
];
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), true, true);
?>
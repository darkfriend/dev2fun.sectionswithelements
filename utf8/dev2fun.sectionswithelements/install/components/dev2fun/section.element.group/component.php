<?
/**
* 
* @author dev2fun (darkfriend)
* @copyright darkfriend
* @version 0.2.1.2
* 
*/
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/** @global CIntranetToolbar $INTRANET_TOOLBAR */
global $INTRANET_TOOLBAR;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "news";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";

if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $key=>$val)
	if($val==="")
		unset($arParams["PROPERTY_CODE"][$key]);

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

if($arParams["SECTION_COUNT"]<=0)
	$arParams["SECTION_COUNT"] = 20;

//$arParams["SECTION_COUNT"] = 2;
$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
$arParams["PREVIEW_TRUNCATE_LEN"] = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
$arParams["SECTION_PREVIEW_TRUNCATE_LEN"] = intval($arParams["SECTION_PREVIEW_TRUNCATE_LEN"]);
$arParams["HIDE_LINK_WHEN_NO_DETAIL"] = $arParams["HIDE_LINK_WHEN_NO_DETAIL"]=="Y";

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";
$arParams["ELEMENTS_DISPLAY"] = $arParams["ELEMENTS_DISPLAY"]!="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else
{
	$arNavParams = array(
		"nTopCount" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);
	$arNavigation = false;
}

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

/**
* 
* begin section $arSectionParams
* 
*/
$arFilterSection = array(
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
);

if($arParams['SECTION_DEPTH']){
	$arFilterSection['DEPTH_LEVEL'] = $arParams['SECTION_DEPTH'];
}

if(!is_array($arParams["SECTION_PROPERTY_CODE"])){
	$arParams["SECTION_PROPERTY_CODE"] = array();
	foreach($arParams["SECTION_PROPERTY_CODE"] as $key=>$val){
		if($val===""){
			unset($arParams["SECTION_PROPERTY_CODE"][$key]);
		}
	}
}
$arParams["SECTION_CHECK_EMPTY"] = $arParams["SECTION_CHECK_EMPTY"]!="N";
// $arParams["SECTION_CHECK_EMPTY"] = $arParams["SECTION_CHECK_EMPTY"]!="N";

if(!empty($GLOBALS['arrSectionFilter'])){
	$arParams["SECTION_FILTER_NAME"] = 'arrSectionFilter';
}

if(strlen($arParams["SECTION_FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["SECTION_FILTER_NAME"]))
{
	$arrSectionFilter = array();
}
else
{
	$arrSectionFilter = $GLOBALS[$arParams["SECTION_FILTER_NAME"]];
	if(!is_array($arrSectionFilter))
		$arrSectionFilter = array();
}
$arParams["SECTION_CHECK_EMPTY"] = $arParams["SECTION_CHECK_EMPTY"]!="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arSectionNavParams = array(
		"nPageSize" => $arParams["SECTION_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arSectionNavigation = CDBResult::GetNavParams($arSectionNavParams);
	if($arSectionNavigation["SPAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else
{
	/*$arSectionNavParams = array(
		"nTopCount" => $arParams["SECTION_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);*/
	$arSectionNavParams = $arSectionNavigation = false;
}

/**
* 
* end sectioon
* 
*/

if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arNavigation, $arrFilter, $arSectionNavParams, $arrSectionFilter)))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if(is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		));
	}
	else
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}
	if($arResult = $rsIBlock->GetNext())
	{
		$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
		
		//SELECT FOR ELEMENTS
		$arSelect = array_merge($arParams["FIELD_CODE"], array(
			"ID",
			"IBLOCK_ID",
			"IBLOCK_SECTION_ID",
			"NAME",
			"ACTIVE_FROM",
			"DETAIL_PAGE_URL",
			"DETAIL_TEXT",
			"DETAIL_TEXT_TYPE",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
			"PREVIEW_PICTURE",
		));
		
		//PROPERTIES FOR ELEMENTS
		$bGetProperty = count($arParams["PROPERTY_CODE"])>0;
		if($bGetProperty)
			$arSelect[]="PROPERTY_*";
		//WHERE FOR ELEMENTS
		$arFilter = array(
			"IBLOCK_ID" => $arResult["ID"],
			"IBLOCK_LID" => SITE_ID,
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);
		
		if($arParams["CHECK_DATES"])
			$arFilter["ACTIVE_DATE"] = "Y";

		//SELECT FOR SECTIONS
		$arSelectSection = array(
			"ID",
			"CODE",
			"IBLOCK_ID",
			"IBLOCK_SECTION_ID",
			"TIMESTAMP_X",
			"SORT",
			"NAME",
			"ACTIVE",
			"GLOBAL_ACTIVE",
			"PICTURE",
			"DESCRIPTION",
			"DESCRIPTION_TYPE",
			"LEFT_MARGIN",
			"RIGHT_MARGIN",
			"SEARCHABLE_CONTENT",
			"SECTION_PAGE_URL",
			"MODIFIED_BY",
			"DATE_CREATE",
			"CREATED_BY",
			"DETAIL_PICTURE",
			"DEPTH_LEVEL",
		);

		//CHECK AND ADD IN SELECT FOR SECTION
		if($arParams['SECTION_PROPERTY_CODE']){
			foreach ($arParams['SECTION_PROPERTY_CODE'] as $nameCode) {
				if($nameCode){
					array_push($arSelectSection,$nameCode);
				}
			}
		}
		
		$bIncCnt = false;
		if($arParams["SECTION_CHECK_EMPTY"]){
			$bIncCnt = true;
			if($arParams['INCLUDE_SUBSECTIONS']){
				$arFilterSection['ELEMENT_SUBSECTIONS'] = 'Y'; 
			} else {
				$arFilterSection['ELEMENT_SUBSECTIONS'] = 'N'; 
			}
			$arFilterSection['CNT_ACTIVE'] = 'Y';
		}
		//ORDER BY FOR SECTIONS
		$arSort = array(
			$arParams["SECTION_SORT_BY1"]=>$arParams["SECTION_SORT_ORDER1"],
			$arParams["SECTION_SORT_BY2"]=>$arParams["SECTION_SORT_ORDER2"],
		);
		
		//ADD SORT MAIN FOR SECTIONS
		if(!array_key_exists("ID", $arSort))
			$arSort["ID"] = "DESC";

		if(!empty($arParams["PARENT_SECTION"])){
			$arrSectionFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
		}
		
		$arResult["RUBITEMS"] = array();
		$rsSection = CIBlockSection::GetList(
			$arSort,
			array_merge($arFilterSection, $arrSectionFilter),
			$bIncCnt,
			$arSelectSection,
			$arSectionNavParams
		);
		
		$rsSection->SetUrlTemplates("", $arParams["SECTION_DETAIL_URL"], $arParams["IBLOCK_URL"]);
		$intSection = 0;
		while($arSection = $rsSection->GetNext())
		{
			if($arParams["SECTION_CHECK_EMPTY"] && !$arSection["ELEMENT_CNT"]){
				continue;
			}
			$intSection++;	
			if($intSection>$arParams["SECTION_COUNT"]){
				break;
			}
			
			$obParser = new CTextParser;
			
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arSection["IBLOCK_ID"], $arSection["ID"]);
			$arSection["IPROPERTY_VALUES"] = $ipropValues->getValues();
			
			//GET SECTION PROP
			if($arParams['SECTION_PROPERTY_CODE']){
				foreach ($arParams['SECTION_PROPERTY_CODE'] as $uPropName) {
					if($uPropName && $arSection[$uPropName]){
						$rsEnum = CUserFieldEnum::GetList(array(), array("ID" =>$arSection[$uPropName]));
						if($uPropValue = $rsEnum->GetNext()){
							$arSection[$uPropName] = $uPropValue;
							$arSection['~'.$uPropName] = $uPropValue['ID'];
						}
						unset($uPropValue);
					}
				}
			}
			
			if(isset($arSection["PICTURE"]) && $arParams["DISPLAY_SECTION_PICTURE"]!="N"){
				$arSection["PICTURE"] = (0 < $arSection["PICTURE"] ? CFile::GetFileArray($arSection["PICTURE"]) : false);
				if ($arSection["PICTURE"]){
					$arSection["PICTURE"]["ALT"] = $arSection["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
					if ($arSection["PICTURE"]["ALT"] == "")
						$arSection["PICTURE"]["ALT"] = $arSection["NAME"];
					$arSection["PICTURE"]["TITLE"] = $arSection["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
					if ($arSection["PICTURE"]["TITLE"] == "")
						$arSection["PICTURE"]["TITLE"] = $arSection["NAME"];
				}
			}
			
			if($arParams["SECTION_PREVIEW_TRUNCATE_LEN"] > 0){
				$arSection["DESCRIPTION"] = $obParser->html_cut($arSection["DESCRIPTION"], $arParams["SECTION_PREVIEW_TRUNCATE_LEN"]);
			}
			
			$arSectionButtons = CIBlock::GetPanelButtons(
				$arSection["IBLOCK_ID"],
				0,
				$arSection["ID"],
//				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
				array("SESSID"=>false, "CATALOG"=>true)
			);
//			print_r($arSectionButtons);
			
			$arSection["EDIT_LINK"] = $arSectionButtons["edit"]["edit_section"]["ACTION_URL"];
			$arSection["DELETE_LINK"] = $arSectionButtons["edit"]["delete_section"]["ACTION_URL"];
			
			if($arParams['SECTION_CHILD']=="Y"){
				$arSubSection = array();
				$arFilterChild = array(
					'ACTIVE' => 'Y',
					'IBLOCK_ID' => $arSection['IBLOCK_ID'],
					'IBLOCK_TYPE' => $arSection['IBLOCK_TYPE'],
					'>LEFT_MARGIN' => $arSection['LEFT_MARGIN'],
					'<RIGHT_MARGIN' => $arSection['RIGHT_MARGIN'],
					'>DEPTH_LEVEL' => $arSection['DEPTH_LEVEL']
				);
   				$rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilterChild,false,$arSelectSection);
   				while($arChildSection = $rsSect->GetNext()){ //IBLOCK_SECTION_ID
					if($arSubSection[$arChildSection['IBLOCK_SECTION_ID']]){
						$arSubSection[$arChildSection['IBLOCK_SECTION_ID']]['SECTION_CHILD'][] = $arChildSection;
					} else {
						$arSubSection[$arChildSection['ID']]=$arChildSection;
					}
				}
				/*if($arSubSection){
					
					$arSubSection = $this->conditionResultArray($arSubSection,'IBLOCK_SECTION_ID');
				}*/
				$arSection['SECTION_CHILD'] = $arSubSection;
			}

			$arResult["RUBITEMS"][$arSection['ID']] = $arSection;
			
			
			$arResult["NAV_STRING"] = $rsSection->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
			$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
			$arResult["NAV_RESULT"] = $rsSection;
			
			if(!$arParams["ELEMENTS_DISPLAY"]){
				continue;
			}
			
			if($arParams["INCLUDE_SUBSECTIONS"]){
				$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
			}
			
			//ORDER BY FOR ELEMENTS
			$arSort = array(
				$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
				$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
			);
			if(!array_key_exists("ID", $arSort))
				$arSort["ID"] = "DESC";

			
			$arResult["ELEMENTS"] = array();
			$arFilter['SECTION_ID'] = intval($arSection["ID"]);
			
//			$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
			$rsElement = CIBlockElement::GetList(
				$arSort,
				array_merge($arFilter, $arrFilter),
				false,
				$arNavParams,
				$arSelect
			);
			$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);
			while($obElement = $rsElement->GetNextElement())
			{
				$arItem = $obElement->GetFields();
				
				$arButtons = CIBlock::GetPanelButtons(
					$arItem["IBLOCK_ID"],
					$arItem["ID"],
					0,
					array("SECTION_BUTTONS"=>false, "SESSID"=>false)
				);
				
				$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
				$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

				if($arParams["PREVIEW_TRUNCATE_LEN"] > 0)
					$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);

				if(strlen($arItem["ACTIVE_FROM"])>0)
					$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
				else
					$arItem["DISPLAY_ACTIVE_FROM"] = "";

				$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
				$arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();

				if(isset($arItem["PREVIEW_PICTURE"]))
				{
					$arItem["PREVIEW_PICTURE"] = (0 < $arItem["PREVIEW_PICTURE"] ? CFile::GetFileArray($arItem["PREVIEW_PICTURE"]) : false);
					if ($arItem["PREVIEW_PICTURE"])
					{
						$arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
						if ($arItem["PREVIEW_PICTURE"]["ALT"] == "")
							$arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["NAME"];
						$arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
						if ($arItem["PREVIEW_PICTURE"]["TITLE"] == "")
							$arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["NAME"];
					}
				}
				if(isset($arItem["DETAIL_PICTURE"]))
				{
					$arItem["DETAIL_PICTURE"] = (0 < $arItem["DETAIL_PICTURE"] ? CFile::GetFileArray($arItem["DETAIL_PICTURE"]) : false);
					if ($arItem["DETAIL_PICTURE"])
					{
						$arItem["DETAIL_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
						if ($arItem["DETAIL_PICTURE"]["ALT"] == "")
							$arItem["DETAIL_PICTURE"]["ALT"] = $arItem["NAME"];
						$arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
						if ($arItem["DETAIL_PICTURE"]["TITLE"] == "")
							$arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["NAME"];
					}
				}

				$arItem["FIELDS"] = array();
				foreach($arParams["FIELD_CODE"] as $code)
					if(array_key_exists($code, $arItem))
						$arItem["FIELDS"][$code] = $arItem[$code];

				if($bGetProperty)
					$arItem["PROPERTIES"] = $obElement->GetProperties();
				$arItem["DISPLAY_PROPERTIES"]=array();
				
				if($arParams["PROPERTY_CODE"]=='ALL'){
					foreach($arItem["PROPERTIES"] as $pid=>$prop){
						$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "news_out");
					}
				} else {
					foreach($arParams["PROPERTY_CODE"] as $pid){
						$prop = &$arItem["PROPERTIES"][$pid];
						if(
							(is_array($prop["VALUE"]) && count($prop["VALUE"])>0)
							|| (!is_array($prop["VALUE"]) && strlen($prop["VALUE"])>0)
						)
						{
							$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "news_out");
						}
					}
				}
				
				$arResult["RUBITEMS"][$arSection['ID']]['ITEMS'][] = $arItem;
				$arResult["ELEMENTS"][] = $arItem["ID"];
			}
			/*$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
			$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
			$arResult["NAV_RESULT"] = $rsElement;*/
		
		}
		
		if($arParams['SECTION_CHECK_EMPTY']){
			$arResult["RUBITEMS"] = $this->checkSectionOnEmpty($arResult["RUBITEMS"]);
		}
		
		$this->SetResultCacheKeys(array(
			"ID",
			"IBLOCK_TYPE_ID",
			"LIST_PAGE_URL",
			"NAV_CACHED_DATA",
			"NAME",
			"SECTION",
			"ELEMENTS",
			"IPROPERTY_VALUES",
		));
		
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		ShowError(GetMessage("T_NEWS_NEWS_NA"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}

if(isset($arResult["ID"]))
{
	$arTitleOptions = null;
	/*if($USER->IsAuthorized())
	{
		if(
			$APPLICATION->GetShowIncludeAreas()
			|| (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $arParams["INTRANET_TOOLBAR"]!=="N")
			|| $arParams["SET_TITLE"]
		)
		{
			if(CModule::IncludeModule("iblock"))
			{
				$arButtons = CIBlock::GetPanelButtons(
					$arResult["ID"],
					0,
					$arParams["PARENT_SECTION"],
					array("SECTION_BUTTONS"=>false)
				);

				if($APPLICATION->GetShowIncludeAreas())
					$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

				if(
					is_array($arButtons["intranet"])
					&& is_object($INTRANET_TOOLBAR)
					&& $arParams["INTRANET_TOOLBAR"]!=="N"
				)
				{
					$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');
					foreach($arButtons["intranet"] as $arButton)
						$INTRANET_TOOLBAR->AddButton($arButton);
				}

				if($arParams["SET_TITLE"])
				{
					$arTitleOptions = array(
						'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_iblock"]["ACTION"],
						'PUBLIC_EDIT_LINK' => "",
						'COMPONENT_NAME' => $this->GetName(),
					);
				}
			}
		}
	}*/

	$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

	if($arParams["SET_TITLE"])
	{
		if ($arResult["IPROPERTY_VALUES"] && $arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
			$APPLICATION->SetTitle($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arTitleOptions);
		elseif(isset($arResult["NAME"]))
			$APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);
	}

	if ($arResult["IPROPERTY_VALUES"])
	{
		if ($arParams["SET_BROWSER_TITLE"] === 'Y' && $arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"] != "")
			$APPLICATION->SetPageProperty("title", $arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"], $arTitleOptions);

		if ($arParams["SET_META_KEYWORDS"] === 'Y' && $arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"] != "")
			$APPLICATION->SetPageProperty("keywords", $arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"], $arTitleOptions);

		if ($arParams["SET_META_DESCRIPTION"] === 'Y' && $arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"] != "")
			$APPLICATION->SetPageProperty("description", $arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"], $arTitleOptions);
	}

	/*if($arParams["INCLUDE_IBLOCK_INTO_CHAIN"] && isset($arResult["NAME"]))
	{
		if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
			$APPLICATION->AddChainItem(
				$arResult["NAME"]
				,strlen($arParams["IBLOCK_URL"]) > 0? $arParams["IBLOCK_URL"]: $arResult["LIST_PAGE_URL"]
			);
		else
			$APPLICATION->AddChainItem($arResult["NAME"]);
	}*/

	/*if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
	{
		foreach($arResult["SECTION"]["PATH"] as $arPath)
		{
			if ($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
				$APPLICATION->AddChainItem($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arPath["~SECTION_PAGE_URL"]);
			else
				$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
		}
	}*/

	return $arResult["ELEMENTS"];
}
?>
<?php

/**
 * @author dev2fun (darkfriend)
 * @copyright darkfriend
 * @version 0.2.16
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

class Dev2funListElements extends CBitrixComponent
{
    private $type;

    /**
     * @param array $arrSections
     * @return array is not section empty
     */
    final public function checkSectionOnEmpty($arrSections)
    {
        if (empty($arrSections)) {
            return false;
        }
        foreach ($arrSections as $keySection => $arrSection) {
            if ($arrSection['ELEMENT_CNT'] <= 0) {
                unset($arrSections[$keySection]);
            }
        }
        return $arrSections;
    }

    /**
     * Метод возвращает массив с целочисленными id
     * @param array $arRequestParams - запрос<br>
     *      arSort - массив сортировки
     *      arFilter - массив фильтров
     *      arNavParams - массив навигации
     *      arSelect - массив возвращаемых данных
     * @param array $arParams - параметры компонента
     *
     * @return array|false
     */
    public function getElementsSection($arRequestParams, $arParams)
    {
        $result = [];
        if ($arParams['NEWS_SHOW_SECTION'] == 'N') {
            return $result;
        }
        if (!$arRequestParams['sort']) {
            $arRequestParams['sort'] = [];
        }
        // $arResult["ELEMENTS"] = array();
        // $arFilter['SECTION_ID'] = intval($arSection["ID"]);

        //			$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        $obParser = new CTextParser;
        $rsElement = CIBlockElement::GetList(
            $arRequestParams['arSort'],
            $arRequestParams['arFilter'],
            false,
            $arRequestParams['arNavParams'],
            $arRequestParams['arSelect']
        );
        $rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);
        while ($obElement = $rsElement->GetNextElement()) {
            $arItem = $obElement->GetFields();

            $arButtons = CIBlock::GetPanelButtons(
                $arItem["IBLOCK_ID"],
                $arItem["ID"],
                0,
                ["SECTION_BUTTONS" => false, "SESSID" => false]
            );

            $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
            $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

            if ($arParams["PREVIEW_TRUNCATE_LEN"] > 0) {
                $arItem["PREVIEW_TEXT"] = $obParser->html_cut(
                    $arItem["PREVIEW_TEXT"],
                    $arParams["PREVIEW_TRUNCATE_LEN"]
                );
            }

            if (strlen($arItem["ACTIVE_FROM"]) > 0) {
                $arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat(
                    $arParams["ACTIVE_DATE_FORMAT"],
                    MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat())
                );
            } else {
                $arItem["DISPLAY_ACTIVE_FROM"] = "";
            }

            $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
            $arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();

            if (isset($arItem["PREVIEW_PICTURE"])) {
                $arItem["PREVIEW_PICTURE"] = (0 < $arItem["PREVIEW_PICTURE"] ? CFile::GetFileArray(
                    $arItem["PREVIEW_PICTURE"]
                ) : false);
                if ($arItem["PREVIEW_PICTURE"]) {
                    $arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
                    if ($arItem["PREVIEW_PICTURE"]["ALT"] == "") {
                        $arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["NAME"];
                    }
                    $arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
                    if ($arItem["PREVIEW_PICTURE"]["TITLE"] == "") {
                        $arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["NAME"];
                    }
                }
            }
            if (isset($arItem["DETAIL_PICTURE"])) {
                $arItem["DETAIL_PICTURE"] = 0 < $arItem["DETAIL_PICTURE"]
                    ? CFile::GetFileArray($arItem["DETAIL_PICTURE"])
                    : false;
                if (!empty($arItem["DETAIL_PICTURE"])) {
                    $arItem["DETAIL_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
                    if (empty($arItem["DETAIL_PICTURE"]["ALT"])) {
                        $arItem["DETAIL_PICTURE"]["ALT"] = $arItem["NAME"];
                    }
                    $arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
                    if (empty($arItem["DETAIL_PICTURE"]["TITLE"])) {
                        $arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["NAME"];
                    }
                }
            }

            $arItem["FIELDS"] = [];
            foreach ($arParams["FIELD_CODE"] as $code) {
                if (array_key_exists($code, $arItem)) {
                    $arItem["FIELDS"][$code] = $arItem[$code];
                }
            }

            $bGetProperty = count($arParams["PROPERTY_CODE"]) > 0;
            if ($bGetProperty) {
                $arItem["PROPERTIES"] = $obElement->GetProperties();
            }
            $arItem["DISPLAY_PROPERTIES"] = [];

            if ($arParams["PROPERTY_CODE"] === 'ALL') {
                foreach ($arItem["PROPERTIES"] as $pid => $prop) {
                    $arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue(
                        $arItem,
                        $prop,
                        "news_out"
                    );
                }
            } else {
                foreach ($arParams["PROPERTY_CODE"] as $pid) {
                    $prop = &$arItem["PROPERTIES"][$pid];
                    if (
                        (is_array($prop["VALUE"]) && count($prop["VALUE"]) > 0)
                        || (!is_array($prop["VALUE"]) && strlen($prop["VALUE"]) > 0)
                    ) {
                        $arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue(
                            $arItem,
                            $prop,
                            "news_out"
                        );
                    }
                }
            }
            $result[] = $arItem;
        }

        return $result;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Метод возвращает массив с целочисленными id
     * @param array $arraySections
     *
     * @return array|false
     */
    public final function GetArraySectionsID($arraySections)
    {
        if (empty($arraySections)) {
            return false;
        }
        for ($i = 0, $cnt = count($arraySections); $i < $cnt; $i++) {
            if (is_numeric($arraySections[$i])) {
                $arraySections[$i] = intval($arraySections[$i]);
            } else {
                CIBlockFindTools::GetSectionID(
                    false,
                    $arraySections[$i],
                    [
                        "GLOBAL_ACTIVE" => "Y",
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    ]
                );
            }
        }
        return $arraySections;
    }

    public final function GetArraySectionInfo()
    {
    }

    /**
     *
     * @param array $item массив раздела
     * @param string|integer $tools DEPTH_LEVEL(int, уровень вложенности)|prev(string, ближайший родитель)|main_prev(string, главный родитель)
     *
     * @return array
     */
    public final function getParentSectionArray($item, $tools = 'prev', $arRequestParams = [], $arParams = [])
    {
        if (!$item['IBLOCK_SECTION_ID']) {
            return false;
        }
        $res = CIBlockSection::GetByID($item['IBLOCK_SECTION_ID']);
        if ($ar_res = $res->GetNext()) {
            if ($arRequestParams) {
                $ar_res['ITEMS'] = $this->getElementsSection(
                    [
                        'arSort' => $arRequestParams['arSort'],
                        'arFilter' => $arRequestParams['arFilter'],
                        'arNavParams' => $arRequestParams['arNavParams'],
                        'arSelect' => $arRequestParams['arSelect'],
                    ],
                    $arParams
                );
            }
            switch ($tools) {
                case 'prev' :
                    break;
                case 'main_prev' :
                    $ar_res = $this->getParentSectionArray($ar_res['IBLOCK_SECTION_ID'], $tools);
                    break;
                default :
                    if ($ar_res['DEPTH_LEVEL'] !== $tools) {
                        $ar_res = $this->getParentSectionArray($ar_res['IBLOCK_SECTION_ID'], $tools);
                    }
            }
        }
        return $ar_res;
    }

    /**
     * Пересобирает массив, группируя по необхомым ключам
     *
     * @param array $arrItems
     * @param string $pathGroupKeys путь до группирующего значения
     *
     * @return array сгруппированный
     */
    public final function conditionResultArray($arrItems, $pathGroupKeys = 'SECTION_RESULT/ID')
    {
        $result = [];
        $groupPath = explode('/', $pathGroupKeys);

        foreach ($arrItems as $key => $item) {
            $thisKey = $key == 0 ? 0 : count($result) - 1;

            $ifs = $this->recResultArray($item, $groupPath, $result[$thisKey][0]);

            if ($ifs) {
                $result[$thisKey] = $item;
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Проверяет текущий элемент на наличие группирующего ключа и сравнивает значения с тем что уже объединенно
     *
     * @param array $item
     * @param array $groupPath
     * @param array|null $arrRes
     * @param integer|null $step
     *
     * @return true|false
     */
    private function recResultArray($item, $groupPath, $arrRes, $step)
    {
        if (!$arrRes) {
            return false;
        }
        $result = false;

        if (is_array($item)) {
            foreach ($item as $key => $val) {
                if (!$step && $step !== 0) {
                    $step = 0;
                }

                if ($this->recReturnKeyGroup($key, $groupPath, $step)) {
                    if (is_array($val)) {
                        $ifs2 = $this->recResultArray($val, $groupPath, $arrRes, $step + 1);
                        if ($ifs2) {
                            $result = true;
                        }
                    } else {
                        if ($this->recReturnValueArResult($arrRes, $groupPath) == $val) {
                            $result = true;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Проверяет наличие ключа в путях из ключей
     *
     * @param string $key
     * @param array|string $groupPath
     * @param integer $step
     *
     * @return string|false
     */
    private function recReturnKeyGroup($key, $groupPath, $step = 0)
    {
        if (is_array($groupPath)) {
            $groupPath = $this->recReturnKeyGroup($key, $groupPath[$step]);
        }

        if ($key == $groupPath) {
            return $key;
        }

        return false;
    }

    /**
     * Возвращает значение результирующего массива, учитывая путь из ключей
     *
     * @param array $arrRes
     * @param array $groupPath
     * @param integer $key
     *
     * @return string значение в результирующем массиве
     */
    private function recReturnValueArResult($arrRes, $groupPath, $key = 0)
    {
        if (is_array($groupPath)) {
            if (is_array($arrRes[$groupPath[$key]])) {
                $res = $this->recReturnValueArResult($arrRes[$groupPath[$key]], $groupPath, $key + 1);
            } else {
                $res = $arrRes[$groupPath[$key]];
            }
        } else {
            $res = $arrRes[$groupPath[$key]];
        }
        return $res;
    }

    /**
     * Отрисовка
     * @param array $arAllResult
     * @param array $arParams
     * @param string|null $templateFile
     * @return false|void
     */
    public function render($arAllResult, $arParams, $templateFile = null)
    {
        if (!$templateFile) {
            ShowError("Template file is not found");
            return false;
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . dirname($templateFile);

        include($path . '/header.php');

        $this->recRender([
            'arResult' => $arAllResult["RUBITEMS"],
            'arParams' => $arParams,
            'path' => $path,
            // 'tempPath' => $tempPath,
        ]);
        include($path . '/footer.php');
    }

    /**
     * Вывод данных
     * @param array $params массив параметров
     */
    public function recRender($params)
    {
        $params['arParams'] = array_merge(
            [
                'TEMP_OUTPUT_SECTIONS' => 'subSection.php',
                'TEMP_OUTPUT_ELEMETS' => 'element.php',
                'tempSection' => 'section.php',
                'tempElements' => 'element.php',
                'tempSubSection' => 'subSection.php',
            ],
            $params['arParams']
        );
        $params['path'] = $params['path'] . '/';
        $GLOBALS['arParams'] = $params['arParams'];
        $this->ob(
            $params['path'] . $params['arParams']['TEMP_OUTPUT_SECTIONS'],
            $params['arResult'],
            'arSectionsChild'
        );
    }

    /**
     * Выводит все данные
     * @param string $tempPath пусть до файла
     * @param array $arResult массив выводимых данных
     * @param string $keyName глобальная переменная
     */
    public function ob($tempPath, $arResult, $keyName = 'arResult')
    {
        if (!file_exists($tempPath)) {
            global $APPLICATION;
            $APPLICATION->ThrowException("File " . basename($tempPath) . " is not found");
            ShowError("File " . basename($tempPath) . " is not found");
            return false;
        }
        $GLOBALS[$keyName] = $arResult;
        include($tempPath);
    }
}
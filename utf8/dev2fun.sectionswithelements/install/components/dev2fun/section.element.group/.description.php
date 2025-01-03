<?php
/**
 *
 * @author dev2fun (darkfriend)
 * @copyright darkfriend
 * @version 1.0.0
 *
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    "NAME" => GetMessage("D2F_T_IBLOCK_DESC_LIST"),
    "DESCRIPTION" => GetMessage("D2F_T_IBLOCK_DESC_LIST_DESC"),
    "ICON" => "/images/rub_el_list.gif",
    "SORT" => 20,
    //	"SCREENSHOT" => array(
    //		"/images/post-77-1108567822.jpg",
    //		"/images/post-1169930140.jpg",
    //	),
    "CACHE_PATH" => "Y",
    "PATH" => [
        "ID" => "dev2fun",
        "CHILD" => [
            "ID" => "elements",
            "NAME" => GetMessage("D2F_T_IBLOCK_DESC_CONTENT"),
            "SORT" => 10,
            "CHILD" => [
                "ID" => "section.element.group",
            ],
        ],
    ],
];

?>
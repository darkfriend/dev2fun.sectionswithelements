<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 *
 * @author dev2fun (darkfriend)
 * @copyright darkfriend
 * @version 0.1.1
 *
 */
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="news-list">
    <?php if ($arParams["DISPLAY_TOP_PAGER"]): ?>
        <?= $arResult["NAV_STRING"] ?><br/>
    <?php endif; ?>
    <?php foreach ($arResult["RUBITEMS"] as $arItems): ?>
        <?php
        $this->AddEditAction($arItems['ID'], $arItems['EDIT_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "SECTION_EDIT"));
        $this->AddDeleteAction($arItems['ID'], $arItems['DELETE_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "SECTION_DELETE"), ["CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')]);
        ?>
        <div id="<?= $this->GetEditAreaId($arItems['ID']); ?>">
            <?php if (!empty($arItems['PICTURE']) && $arParams["DISPLAY_SECTION_PICTURE"] != "N") { ?>
                <img
                    border="0"
                    src="<?= $arItems["PICTURE"]["SRC"] ?>"
                    width="<?= $arItems["PICTURE"]["WIDTH"] ?>"
                    height="<?= $arItems["PICTURE"]["HEIGHT"] ?>"
                    alt="<?= $arItems["PICTURE"]["ALT"] ?>"
                    title="<?= $arItems["PICTURE"]["TITLE"] ?>"
                    style="float:left"
                />
            <?php } ?>
            <a href="<?= $arItems["SECTION_PAGE_URL"] ?>">
                <h2><?= $arItems["NAME"] ?></h2>
            </a>
            <?php foreach ($arItems["ITEMS"] as $arItem): ?>
                <?php
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), ["CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]);
                ?>
            <p class="news-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <?php if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])): ?>
                    <?php if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><img
                                class="preview_picture"
                                border="0"
                                src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                width="<?= $arItem["PREVIEW_PICTURE"]["WIDTH"] ?>"
                                height="<?= $arItem["PREVIEW_PICTURE"]["HEIGHT"] ?>"
                                alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
                                title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>"
                                style="float:left"
                            /></a>
                    <?php else: ?>
                        <img
                            class="preview_picture"
                            border="0"
                            src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                            width="<?= $arItem["PREVIEW_PICTURE"]["WIDTH"] ?>"
                            height="<?= $arItem["PREVIEW_PICTURE"]["HEIGHT"] ?>"
                            alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
                            title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>"
                            style="float:left"
                        />
                    <?php endif; ?>
                <?php endif ?>
                <?php if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]): ?>
                    <span class="news-date-time"><?php echo $arItem["DISPLAY_ACTIVE_FROM"] ?></span>
                <?php endif ?>
                <?php if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                    <?php if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                        <a href="<?php echo $arItem["DETAIL_PAGE_URL"] ?>"><b><?php echo $arItem["NAME"] ?></b></a><br/>
                    <?php else: ?>
                        <b><?php echo $arItem["NAME"] ?></b><br/>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
                    <?php echo $arItem["PREVIEW_TEXT"]; ?>
                <?php endif; ?>
                <?php if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])): ?>
                    <div style="clear:both"></div>
                <?php endif ?>
                <?php foreach ($arItem["FIELDS"] as $code => $value): ?>
                    <small>
                        <?= GetMessage("IBLOCK_FIELD_" . $code) ?>:&nbsp;<?= $value; ?>
                    </small><br/>
                <?php endforeach; ?>
                <?php foreach ($arItem["DISPLAY_PROPERTIES"] as $pid => $arProperty): ?>
                    <small>
                        <?= $arProperty["NAME"] ?>:&nbsp;
                        <?php if (is_array($arProperty["DISPLAY_VALUE"])): ?>
                            <?= implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]); ?>
                        <?php else: ?>
                            <?= $arProperty["DISPLAY_VALUE"]; ?>
                        <?php endif ?>
                    </small><br/>
                <?php endforeach; ?>
                </p>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <?php if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
        <br/><?= $arResult["NAV_STRING"] ?>
    <?php endif; ?>
</div>

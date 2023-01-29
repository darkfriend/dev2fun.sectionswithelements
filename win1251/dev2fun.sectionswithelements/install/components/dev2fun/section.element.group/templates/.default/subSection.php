<?php
global $arSectionsChild, $arParams;
?>
<ul class="m-l-30">
    <?php foreach ($arSectionsChild as $arSectionChild) { ?>
        <?php
        $this->AddEditAction($arSectionChild['ID'], $arSectionChild['EDIT_LINK'], CIBlock::GetArrayByID($arSectionChild["IBLOCK_ID"], "SECTION_EDIT"));
        $this->AddDeleteAction($arSectionChild['ID'], $arSectionChild['DELETE_LINK'], CIBlock::GetArrayByID($arSectionChild["IBLOCK_ID"], "SECTION_DELETE"), ["CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')]);
        ?>
        <li id="<?= $this->GetEditAreaId($arSectionChild['ID']); ?>">
            <?php if (!empty($arItems['PICTURE']) && $arParams["DISPLAY_SECTION_PICTURE"] != "N") { ?>
                <img
                    border="0"
                    src="<?= $arSectionChild["PICTURE"]["SRC"] ?>"
                    width="<?= $arSectionChild["PICTURE"]["WIDTH"] ?>"
                    height="<?= $arSectionChild["PICTURE"]["HEIGHT"] ?>"
                    alt="<?= $arSectionChild["PICTURE"]["ALT"] ?>"
                    title="<?= $arSectionChild["PICTURE"]["TITLE"] ?>"
                    style="float:left"
                />
            <?php } ?>
            <a href="<?= $arSectionChild["SECTION_PAGE_URL"] ?>">
                <h2><?= $arSectionChild["NAME"] ?></h2>(<?= $arSectionChild['ELEMENT_CNT'] ?>)
            </a>
            <?php if ($arSectionChild["ITEMS"]) {
                foreach ($arSectionChild["ITEMS"] as $kSubItems => $vSubItems) {
                    $GLOBALS['resItems'] = $vSubItems;
                    echo '<ul class="m-l-30">';
                    include(__DIR__ . '/' . $arParams['TEMP_OUTPUT_ELEMETS']);
                    echo '</ul>';
                    unset($GLOBALS['resItems']);
                }
            } ?>
            <?php if ($arSectionChild["SECTION_CHILD"]) {
                $GLOBALS['arSectionsChild'] = $arSectionChild["SECTION_CHILD"];
                include(__DIR__ . '/' . $arParams['TEMP_OUTPUT_SECTIONS']);
            } ?>
        </li>
    <?php } ?>
</ul>
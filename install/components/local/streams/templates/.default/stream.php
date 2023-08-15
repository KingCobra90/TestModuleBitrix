<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"kingcobra_streams:streams.single",
	"",
	[
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"STREAMS_COUNT" => $arParams["STREAMS_COUNT"],
		"STREAM_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["stream"],
		"ELEMENT_ID" => intval($arResult["VARIABLES"]["ELEMENT_ID"]),
	],
	$component
);?>
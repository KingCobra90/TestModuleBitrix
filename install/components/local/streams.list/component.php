<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!Loader::includeModule("iblock"))
{
	ShowError(GetMessage("KINGCOBRA_STREAMS_IBLOCK_MODULE_NONE"));
	return;
}
$arNavigation = CDBResult::GetNavParams($arNavParams);
if ($this->startResultCache(false, array($arNavigation))) {
	$arSelectElems = array (
		"ID",
		"IBLOCK_ID",
		"NAME",
		"ACTIVE_FROM",
		"PROPERTY_STREAM_SERVICE",
		"PROPERTY_STREAM_ID",
		"PROPERTY_STREAM_IS_ONLINE",
	);
	$arFilterElems = array (
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
	);
	$arSortElems = array ();

	$arResult["STREAMS"] = array();
	$rsElements = CIBlockElement::GetList($arSortElems, $arFilterElems, false, array(
		"nPageSize" => $arParams["STREAMS_COUNT"],
		"bShowAll" => true
	), $arSelectElems);
	$arResult["NAV_STRING"] = $rsElements->GetPageNavString(GetMessage("PAGE_TITLE"));
	while($arElement = $rsElements->GetNext())
	{
		$arResult["STREAMS"][] = $arElement;
	}
	$this->includeComponentTemplate();
} else {
	$this->abortResultCache();
}
?>
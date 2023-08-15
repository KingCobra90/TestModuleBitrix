<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!Loader::includeModule("iblock"))
{
	ShowError(GetMessage("KINGCOBRA_STREAMS_IBLOCK_MODULE_NONE"));
	return;
}
if ($this->startResultCache()) {
	$arSelectElems = array (
		"ID",
		"IBLOCK_ID",
		"NAME",
		"ACTIVE_FROM",
		"PROPERTY_STREAM_SERVICE",
		"PROPERTY_STREAM_ID",
	);
	$arFilterElems = array (
		"ID" => $arParams["ELEMENT_ID"],
	);
	$arSortElems = array ();

	$rsElement = CIBlockElement::GetList($arSortElems, $arFilterElems, false, array(), $arSelectElems);
	if($obElement = $rsElement->GetNextElement()){
		$arResult = $obElement->GetFields();
	}
	$this->includeComponentTemplate();
} else {
	$this->abortResultCache();
}
?>
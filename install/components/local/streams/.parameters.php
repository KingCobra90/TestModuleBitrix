<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if( !Loader::includeModule("iblock") ) {
	throw new \Exception('Не загружены модули необходимые для работы компонента');
}

// типы инфоблоков
$arIBlockType = CIBlockParameters::GetIBlockTypes();
// инфоблоки выбранного типа
$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arComponentParameters = [
	"GROUPS" => [
		"SETTINGS" => [
			"NAME" => Loc::getMessage('KINGCOBRA_STREAMS_PROP_SETTINGS'),
			"SORT" => 550,
		],
	],
	"PARAMETERS" => [
		"IBLOCK_TYPE" => [
			"PARENT" => "SETTINGS",
			"NAME" => Loc::getMessage('KINGCOBRA_STREAMS_IBLOCK_TYPE'),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"DEFAULT" => "kingcobra_streams",
			"REFRESH" => "Y"
		],
		"IBLOCK_ID" => [
			"PARENT" => "SETTINGS",
			"NAME" => Loc::getMessage('KINGCOBRA_STREAMS_IBLOCK_ID'),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y"
		],
		"STREAMS_COUNT" => [
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage('KINGCOBRA_STREAMS_PER_PAGE'),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		],
		"VARIABLE_ALIASES" => Array(
			"ELEMENT_ID" => Array("NAME" => GetMessage("KINGCOBRA_STREAMS_ELEMENT_ID_DESC")),
		),
		"SEF_MODE" => Array(
			"streams_list" => array(
				"NAME" => Loc::getMessage("KINGCOBRA_STREAMS_LIST_PAGE"),
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"stream" => array(
				"NAME" => Loc::getMessage("KINGCOBRA_STREAMS_STREAM_PAGE"),
				"DEFAULT" => "streams/#ELEMENT_ID#/",
				"VARIABLES" => array("ELEMENT_ID"),
			),
		),
		'CACHE_TIME' => ['DEFAULT' => 3600],
	]
];
<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if( !Loader::includeModule("iblock") ) {
	throw new \Exception('Не загружены модули необходимые для работы компонента');
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arComponentParameters = [
	// группы в левой части окна
	"GROUPS" => [
		"SETTINGS" => [
			"NAME" => Loc::getMessage('KINGCOBRA_STREAMS_PROP_SETTINGS'),
			"SORT" => 550,
		],
	],
	// поля для ввода параметров в правой части
	"PARAMETERS" => [
		// Произвольный параметр типа СПИСОК
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
		"ELEMENT_ID" => array(
			"PARENT" => "SETTINGS",
			"NAME" => GetMessage("KINGCOBRA_STREAMS_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		// Настройки кэширования
		'CACHE_TIME' => ['DEFAULT' => 3600],
	]
];
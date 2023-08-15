<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$context = \Bitrix\Main\Application::getInstance()->getContext();
$server = $context->getServer();
$playersSrc = [
	"GoodGame" => "//goodgame.ru/player?".$arResult["PROPERTY_STREAM_ID_VALUE"],
	"Twitch" => "//player.twitch.tv/?parent=".$server->getServerName()."&channel=".$arResult["PROPERTY_STREAM_ID_VALUE"]
];
?>
<iframe src="<?=$playersSrc[$arResult["PROPERTY_STREAM_SERVICE_VALUE"]]?>"
		frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" scrolling="no"
		width="100%" height="100%"
></iframe>

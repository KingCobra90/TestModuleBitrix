<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if (count($arResult["STREAMS"]) > 0) { ?>
	<?foreach($arResult["STREAMS"] as $stream){
		$url = $arResult["FOLDER"] . str_replace("#ELEMENT_ID#", $stream["ID"], $arParams["STREAM_URL"]);
	?>

		<a href="<?=$url?>"><?=$stream["NAME"]?></a> <?=$stream["PROPERTY_STREAM_IS_ONLINE_VALUE"] ? "Online" :"Offline";?><br>
	<?}?>
	---
	<?php echo $arResult["NAV_STRING"];?>
<?php } ?>
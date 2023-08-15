<?
use Bitrix\Main\Localization\Loc;
use	Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

$aTabs = array(
	array(
		"DIV" 	  => "edit",
		"TAB" 	  => Loc::getMessage("KINGCOBRA_STREAMS_OPTIONS_TAB_NAME"),
		"TITLE"   => Loc::getMessage("KINGCOBRA_STREAMS_OPTIONS_TAB_NAME"),
		"OPTIONS" => array(
			array(
				"twitch_code",
				Loc::getMessage("KINGCOBRA_STREAMS_OPTIONS_TAB_TWITCH_CODE"),
				"",
				array("text", 50)
			),
			array(
				"goodgame_code",
				Loc::getMessage("KINGCOBRA_STREAMS_OPTIONS_TAB_GOODGAME_CODE"),
				"",
				array("text", 50)
			),
			array(
				"iblock_id",
				Loc::getMessage("KINGCOBRA_STREAMS_OPTIONS_TAB_IBLOCK_ID"),
				"",
				array("text", 50)
			),
		)
	)
);

if($request->isPost() && check_bitrix_sessid()){

	foreach($aTabs as $aTab){

		foreach($aTab["OPTIONS"] as $arOption){

			if(!is_array($arOption)){
				continue;
			}

			if($arOption["note"]){
				continue;
			}

			if($request["apply"]){
				$optionValue = $request->getPost($arOption[0]);
				Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
			}
		}
	}

	LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}

$tabControl = new CAdminTabControl(
	"tabControl",
	$aTabs
);

$tabControl->Begin();
?>

<form action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&lang=<?=LANGUAGE_ID?>" method="post">

	<?
	foreach($aTabs as $aTab){

		if($aTab["OPTIONS"]){

			$tabControl->BeginNextTab();

			__AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
		}
	}

	$tabControl->Buttons();
	?>

	<input type="submit" name="apply" value="<? echo(Loc::GetMessage("KINGCOBRA_STREAMS_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />

	<?
	echo(bitrix_sessid_post());
	?>

</form>

<?
$tabControl->End();
?>
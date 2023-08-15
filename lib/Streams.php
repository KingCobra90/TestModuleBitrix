<?
use Bitrix\Main\Config\Option;
use Bitrix\Iblock;
use Bitrix\Main\EventLog;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class CStreams{
	public static function updateOnlineStreams(){
		//TODO брать из настроек модуля
		$client_id = "";
		$client_secret = "";
		if(!Loader::includeModule("iblock") || $client_id != "" || $client_secret != ""){
			return false;
		}
		$iblock_id = Option::get("kingcobra_streams", "iblock_id");
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
			"IBLOCK_ID" => $iblock_id,
			"ACTIVE" => "Y",
		);
		$arSortElems = array ();

		$arStreams = array();
		$rsElements = CIBlockElement::GetList($arSortElems, $arFilterElems, false, false, $arSelectElems);
		while($arElement = $rsElements->GetNext())
		{
			$arStreams[$arElement["PROPERTY_STREAM_SERVICE_VALUE"]][$arElement["PROPERTY_STREAM_ID_VALUE"]] = array(
				"element_id" => $arElement["ID"],
				"is_online" => "",
				"updated" => false,
			);
		}

		$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$iblock_id, "CODE"=>"STREAM_IS_ONLINE"));
		if($enum_fields = $property_enums->GetNext())
		{
			$online_property_value = $enum_fields["ID"];
		}

		if(!empty($arStreams["Twitch"]))
		{
			$http = new HttpClient();
			$res = Json::decode($http->post("https://id.twitch.tv/oauth2/token", array(
				"client_id" => $client_id,
				"client_secret" => $client_secret,
				"grant_type" => "client_credentials",
			)));
			$access_token = $res["access_token"];
			$http = new HttpClient();
			$http->setHeader("Authorization", "Bearer " . $access_token);
			$http->setHeader("Client-Id", $client_id);
			$req_str = [];
			foreach ($arStreams["Twitch"] as $user_login => $stream)
			{
				$req_str[] = "user_login=" . $user_login;
			}
			$req_str = join("&", $req_str);
			$res = Json::decode($http->get("https://api.twitch.tv/helix/streams?" . $req_str));
			//TODO: есть ограничение на 100 стримов в запросе, если больше то нужно сделать в цикле с разбивкой
			foreach ($res["data"] as $data)
			{
				if ($arStreams["Twitch"][$data["user_login"]])
				{
					$arStreams["Twitch"][$data["user_login"]]["updated"] = true;
					CIBlockElement::SetPropertyValuesEx($arStreams["Twitch"][$data["user_login"]]["element_id"], $iblock_id,
						array("STREAM_IS_ONLINE" => $online_property_value)
					);
				}
			}
			foreach($arStreams["Twitch"] as $user_login => $stream){
				if(!$stream["updated"])
				{
					CIBlockElement::SetPropertyValuesEx($stream["element_id"], $iblock_id,
						array("STREAM_IS_ONLINE" => "")
					);
				}
			}
		}
		return "CStreams::updateOnlineStreams();";
	}
}
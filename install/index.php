<?
if (class_exists("kingcobra_streams")) return;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;
use \Bitrix\Main\IO\Directory;
Loc::loadMessages(__FILE__);
Class kingcobra_streams extends CModule
{
	var $IBLOCKTYPE_ID = "kingcobra_streams";
	var $MODULE_ID = "kingcobra_streams";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	public function __construct()
	{
		$arModuleVersion = array();

		include(__DIR__.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		$this->MODULE_NAME = GetMessage("KINGCOBRA_STREAMS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("KINGCOBRA_STREAMS_MODULE_DESCRIPTION");
	}

	function InstallDB()
	{
		return true;
	}

	function UnInstallDB()
	{
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles(
			__DIR__."/components/local",
			$_SERVER["DOCUMENT_ROOT"]."/local/components/".$this->MODULE_ID."/",
			true,
			true
		);
		return true;
	}

	function UnInstallFiles()
	{
		if(Directory::isDirectoryExists($_SERVER["DOCUMENT_ROOT"]."/local/components/".$this->MODULE_ID."/"))
		{
			Directory::deleteDirectory(
				$_SERVER["DOCUMENT_ROOT"] . "/local/components/" . $this->MODULE_ID . "/",
			);
		}
		return true;
	}

	function DoInstall()
	{
		if (!check_bitrix_sessid())
			return false;
		if(CModule::IncludeModule("iblock"))
		{
			global $DB, $APPLICATION;
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();

			$arFields = array(
				'ID' => $this->IBLOCKTYPE_ID,
				'SECTIONS' => 'N',
				'IN_RSS' => 'N',
				'SORT' => 100,
				'LANG' => array(
					'en' => array(
						'NAME' => 'Streams',
						'ELEMENT_NAME' => 'Stream'
					)
				)
			);
			$obBlocktype = new CIBlockType;
			$DB->StartTransaction();
			$res = $obBlocktype->Add($arFields);
			if (!$res)
			{
				$DB->Rollback();
				echo 'Error: ' . $obBlocktype->LAST_ERROR . '<br>';
			} else
			{
				$ib = new CIBlock;
				$arFields = array(
					"ACTIVE" => "Y",
					"SITE_ID" => "s1",
					"NAME" => "Streams",
					"IBLOCK_TYPE_ID" => $this->IBLOCKTYPE_ID,
					"DESCRIPTION" => "",
					"DESCRIPTION_TYPE" => "text",
				);
				$ID = $ib->Add($arFields);
				if (!$ID)
				{
					$DB->Rollback();
					echo 'Error1: ' . $ib->LAST_ERROR . '<br>';
				}

				$ibp = new CIBlockProperty;
				$arFields = array(
					"NAME" => "Стрим сервис",
					"ACTIVE" => "Y",
					"IS_REQUIRED" => "Y",
					"SORT" => 1, // Сортировка
					"CODE" => "STREAM_SERVICE",
					"PROPERTY_TYPE" => "L", // Список
					"LIST_TYPE" => "L",
					"FILTRABLE" => "Y", // Выводить на странице списка элементов поле для фильтрации по этому свойству
					"VALUES" => array(
						"twitch" => "Twitch",
						"goodgame" => "GoodGame",
					),
					"IBLOCK_ID" => $ID
				);
				$propId = $ibp->Add($arFields);
				$ibp = new CIBlockProperty;
				$arFields = array(
					"NAME" => "ID стрима",
					"ACTIVE" => "Y",
					"IS_REQUIRED" => "Y",
					"SORT" => 2, // Сортировка
					"CODE" => "STREAM_ID",
					"PROPERTY_TYPE" => "S",
					"FILTRABLE" => "Y",
					"IBLOCK_ID" => $ID
				);
				$propId = $ibp->Add($arFields);
				$ibp = new CIBlockProperty;
				$arFields = array(
					"NAME" => "Online?",
					"ACTIVE" => "Y",
					"IS_REQUIRED" => "N",
					"SORT" => 3, // Сортировка
					"CODE" => "STREAM_IS_ONLINE",
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "C",
					"FILTRABLE" => "Y",
					"VALUES" => array(
						"online" => "online",
					),
					"IBLOCK_ID" => $ID
				);
				$propId = $ibp->Add($arFields);
				RegisterModule($this->MODULE_ID);
				Option::set($this->MODULE_ID, "iblock_id", $ID);
				CAgent::AddAgent("CStreams::updateOnlineStreams();",$this->MODULE_ID, "N", 60);
				$DB->Commit();
			}
		}
	}

	function DoUninstall()
	{
		if (!check_bitrix_sessid())
			return false;
		if(CModule::IncludeModule("iblock"))
		{
			global $DB, $APPLICATION;
			$DB->StartTransaction();
			$obBlocktype = new CIBlockType;
			if ($obBlocktype->GetByID($this->IBLOCKTYPE_ID) && !$obBlocktype->Delete($this->IBLOCKTYPE_ID))
			{
				$DB->Rollback();
				echo 'Error: ' . $obBlocktype->LAST_ERROR . '<br>';
			}
			UnRegisterModule($this->MODULE_ID);
			CAgent::RemoveAgent("CStreams::updateOnlineStreams();", $this->MODULE_ID);
			$DB->Commit();
			$this->UnInstallDB();
			$this->UnInstallEvents();
			$this->UnInstallFiles();
		}
	}
}
?>
<?
$module_id = "ukrequest";

$RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($RIGHT >= "R") :
// Подключаем языковой файл для главного модуля
// Подключаем локальный языковой файл
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
	IncludeModuleLangFile(__FILE__);
// Список опций
	$arAllOptions = array(
		array("max_title_size", GetMessage("UKREQUEST_MAX_TITLE_SIZE"), array("text", 6)),
		array("max_files", GetMessage("UKREQUEST_MAX_FILES"), array("text", 6)),
		array("max_file_size", GetMessage("UKREQUEST_MAX_FILE_SIZE"), array("text", 6)),
		array("max_content_size", GetMessage("UKREQUEST_MAX_CONTENT_SIZE"), array("text", 6)),
		array("responsible_user", GetMessage("UKREQUEST_RESPONSIBLE_USER"), array("text:user")),
		array("observer_users", GetMessage("UKREQUEST_OBSERVER_USERS"), array("text:users")),
	);

	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
		array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	);
	
	$tabControl = new CAdminTabControl("tabControl", $aTabs);

	CModule::IncludeModule($module_id);

	if ($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $RIGHT == "W" && check_bitrix_sessid())
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ukrequest/prolog.php");

		if (strlen($RestoreDefaults) > 0)
		{
			COption::RemoveOption($module_id);
		}
		else
		{
			foreach ($arAllOptions as $arOption)
			{
				$name = $arOption[0];
				$val = $_REQUEST[$name];
				if ($arOption[2][0] == "checkbox" && $val != "Y")
					$val = "N";
				COption::SetOptionString($module_id , $name, $val, $arOption[1]);
			}
		}

		ob_start();
		$Update = $Update.$Apply;
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
		ob_end_clean();

		if (strlen($_REQUEST["back_url_settings"]) > 0)
		{
			if ((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
				LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
			else
				LocalRedirect($_REQUEST["back_url_settings"]);
		}
		else
		{
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
		}
	}

	?>
	<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?
		$tabControl->Begin();
		$tabControl->BeginNextTab();
		$arNotes = array();
		//Опции на первой вкладке
		foreach ($arAllOptions as $arOption):
			$val = COption::GetOptionString($module_id, $arOption[0]);
			$type = $arOption[2];
			if ($type[0] == "text:users" || $type[0] == "text:user") {
                $val = unserialize($val);
            }
			if (isset($arOption[3]))
				$arNotes[] = $arOption[3];
			?>
			<tr>
				<td width="40%" nowrap <? if ($type[0] == "textarea")
					echo 'class="adm-detail-valign-top"' ?>>
					<? if (isset($arOption[3])): ?>
						<span class="required"><sup><? echo count($arNotes) ?></sup></span>
					<? endif; ?>
					<label for="<? echo htmlspecialcharsbx($arOption[0]) ?>"><? echo $arOption[1] ?>
						:</label>
				<td width="60%">
					<? if ($type[0] == "checkbox"): ?>
						<input
							type="checkbox"
							name="<? echo htmlspecialcharsbx($arOption[0]) ?>"
							id="<? echo htmlspecialcharsbx($arOption[0]) ?>"
							value="Y"<? if ($val == "Y") echo " checked"; ?>>
					<? elseif ($type[0] == "text"): ?>
						<input
							type="text"
							size="<? echo $type[1] ?>"
							maxlength="255"
							value="<? echo htmlspecialcharsbx($val) ?>"
							name="<? echo htmlspecialcharsbx($arOption[0]) ?>"
							id="<? echo htmlspecialcharsbx($arOption[0]) ?>">
					<? elseif ($type[0] == "text:users"): ?>
                        <?
                        $arUser = array();
                        $val = array_diff($val, array(''));
                        foreach ($val as $id) {
                            $us = $USER->GetByID($id);
                            $user = $us->GetNext();
                            $arUser[] = $user["LAST_NAME"] . " " . $user["NAME"] . " [" . $id . "]";
                        }
                        $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                                'INPUT_NAME' => $arOption[0],
                                'INPUT_NAME_STRING' => $arOption[0] . "_string",
                                'INPUT_NAME_SUSPICIOUS' => $arOption[0] . "_suspicious",
                                'TEXTAREA_MIN_HEIGHT' => 50,
                                'TEXTAREA_MAX_HEIGHT' => 200,
                                'INPUT_VALUE_STRING' => implode("\n", $arUser),
                                'EXTERNAL' => 'A',
                                'SOCNET_GROUP_ID' => ""
                            )
                        );

                        ?>
					<? elseif ($type[0] == "text:user"): ?>
                        <?
                        $arUser = array();
                        $val = array_diff($val, array(''));
                        foreach ($val as $id) {
                            $us = $USER->GetByID($id);
                            $user = $us->GetNext();
                            $arUser[] = $user["LAST_NAME"] . " " . $user["NAME"] . " [" . $id . "]";
                        }
                        $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                                'INPUT_NAME' => $arOption[0],
                                'INPUT_NAME_STRING' => $arOption[0] . "_string",
                                'INPUT_NAME_SUSPICIOUS' => $arOption[0] . "_suspicious",
                                'TEXTAREA_MIN_HEIGHT' => 50,
                                'TEXTAREA_MAX_HEIGHT' => 200,
                                'INPUT_VALUE_STRING' => implode("\n", $arUser),
                                'EXTERNAL' => 'A',
                                'SOCNET_GROUP_ID' => ""
                            )
                        );

                        ?>
					<?
					<?
					elseif ($type[0] == "textarea"): ?>
						<textarea
							rows="<? echo $type[1] ?>"
							cols="<? echo $type[2] ?>"
							name="<? echo htmlspecialcharsbx($arOption[0]) ?>"
							id="<? echo htmlspecialcharsbx($arOption[0]) ?>"
						><? echo htmlspecialcharsbx($val) ?></textarea>
					<?endif ?>
				</td>
			</tr>
		<? endforeach ?>
		
		<? $tabControl->BeginNextTab(); 
		// Опции на второй вкладке
		?>
		<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php"); ?>
		<? $tabControl->Buttons(); ?>
		<input <? if ($RIGHT < "W")
			echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>"
				title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
		<input <? if ($RIGHT < "W")
			echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>"
				title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
		<? if (strlen($_REQUEST["back_url_settings"]) > 0): ?>
			<input
				<? if ($RIGHT < "W") echo "disabled" ?>
				type="button"
				name="Cancel"
				value="<?=GetMessage("MAIN_OPT_CANCEL")?>"
				title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>"
				onclick="window.location='<? echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'"
			>
			<input
				type="hidden"
				name="back_url_settings"
				value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>"
			>
		<? endif ?>
		<input
			type="submit"
			name="RestoreDefaults"
			title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
			onclick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
			value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>"
		>
		<?=bitrix_sessid_post();?>
		<? $tabControl->End(); ?>
	</form>
	<?
	if (!empty($arNotes))
	{
		echo BeginNote();
		foreach ($arNotes as $i => $str)
		{
			?><span class="required"><sup><? echo $i + 1 ?></sup></span><? echo $str ?><br><?
		}
		echo EndNote();
	}
	?>
<? endif; ?>

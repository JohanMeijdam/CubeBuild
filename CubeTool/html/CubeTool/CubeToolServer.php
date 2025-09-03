<?php
session_start();
include 'CubeDbLogon.php';

set_error_handler("CubeError");
set_exception_handler("CubeException");
$RequestText = file_get_contents('php://input');
$RequestObj = json_decode($RequestText, false);
$ResponseText = '[';

switch ($RequestObj->Service) {
case 'GetDirItpItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_ITP';
	$conn->query("CALL itp.get_itp_root_items ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetItpList':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_ITP';
	$conn->query("CALL itp.get_itp_list ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetItpItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_ITE';
	$conn->query("CALL itp.get_itp_ite_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkItpName = $row["fk_itp_name"];
		$RowObj->Key->Sequence = $row["sequence"];
		$RowObj->Display = $row["suffix"].' ('.$row["domain"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateItp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_ITP';
	$conn->query("CALL itp.insert_itp ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteItp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_ITP';
	$conn->query("CALL itp.delete_itp ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetIte':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_ITE';
	$conn->query("CALL itp.get_ite ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->Sequence??"null").")");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->Suffix = $row["suffix"];
		$RowObj->Data->Domain = $row["domain"];
		$RowObj->Data->Length = $row["length"];
		$RowObj->Data->Decimals = $row["decimals"];
		$RowObj->Data->CaseSensitive = $row["case_sensitive"];
		$RowObj->Data->DefaultValue = $row["default_value"];
		$RowObj->Data->SpacesAllowed = $row["spaces_allowed"];
		$RowObj->Data->Presentation = $row["presentation"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetIteItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_VAL';
	$conn->query("CALL itp.get_ite_val_items ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->Sequence??"null").")");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkItpName = $row["fk_itp_name"];
		$RowObj->Key->FkIteSequence = $row["fk_ite_sequence"];
		$RowObj->Key->Code = $row["code"];
		$RowObj->Display = $row["code"].' '.$row["prompt"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateIte':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_ITE';
	$conn->query("CALL itp.insert_ite ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->Suffix."','".$RequestObj->Parameters->Type->Domain."',".($RequestObj->Parameters->Type->Length??"null").",".($RequestObj->Parameters->Type->Decimals??"null").",'".$RequestObj->Parameters->Type->CaseSensitive."','".$RequestObj->Parameters->Type->DefaultValue."','".$RequestObj->Parameters->Type->SpacesAllowed."','".$RequestObj->Parameters->Type->Presentation."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkItpName = $row["fk_itp_name"];
		$RowObj->Key->Sequence = $row["sequence"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateIte':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_ITE';
	$conn->query("CALL itp.update_ite ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->Suffix."','".$RequestObj->Parameters->Type->Domain."',".($RequestObj->Parameters->Type->Length??"null").",".($RequestObj->Parameters->Type->Decimals??"null").",'".$RequestObj->Parameters->Type->CaseSensitive."','".$RequestObj->Parameters->Type->DefaultValue."','".$RequestObj->Parameters->Type->SpacesAllowed."','".$RequestObj->Parameters->Type->Presentation."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteIte':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_ITE';
	$conn->query("CALL itp.delete_ite ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->Sequence??"null").")");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetVal':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_VAL';
	$conn->query("CALL itp.get_val ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->FkIteSequence??"null").",'".$RequestObj->Parameters->Type->Code."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->Prompt = $row["prompt"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveVal':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_VAL';
	$conn->query("CALL itp.move_val ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->FkIteSequence??"null").",'".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Ref->FkItpName."',".($RequestObj->Parameters->Ref->FkIteSequence??"null").",'".$RequestObj->Parameters->Ref->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateVal':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_VAL';
	$conn->query("CALL itp.insert_val ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->FkIteSequence??"null").",'".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->Prompt."','".$RequestObj->Parameters->Ref->FkItpName."',".($RequestObj->Parameters->Ref->FkIteSequence??"null").",'".$RequestObj->Parameters->Ref->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateVal':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_VAL';
	$conn->query("CALL itp.update_val ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->FkIteSequence??"null").",'".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->Prompt."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteVal':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_VAL';
	$conn->query("CALL itp.delete_val ('".$RequestObj->Parameters->Type->FkItpName."',".($RequestObj->Parameters->Type->FkIteSequence??"null").",'".$RequestObj->Parameters->Type->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetDirBotItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_BOT';
	$conn->query("CALL bot.get_bot_root_items ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["cube_tsg_type"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetBotList':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_BOT';
	$conn->query("CALL bot.get_bot_list ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["cube_tsg_type"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetBot':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_BOT';
	$conn->query("CALL bot.get_bot ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->CubeTsgType = $row["cube_tsg_type"];
		$RowObj->Data->Directory = $row["directory"];
		$RowObj->Data->ApiUrl = $row["api_url"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetBotItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TYP';
	$conn->query("CALL bot.get_bot_typ_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["code"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CountBotRestrictedItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_TYP';
	$conn->query("CALL bot.count_bot_typ ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveBot':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_BOT';
	$conn->query("CALL bot.move_bot ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateBot':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_BOT';
	$conn->query("CALL bot.insert_bot ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->Directory."','".$RequestObj->Parameters->Type->ApiUrl."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateBot':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_BOT';
	$conn->query("CALL bot.update_bot ('".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->Directory."','".$RequestObj->Parameters->Type->ApiUrl."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteBot':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_BOT';
	$conn->query("CALL bot.delete_bot ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTypListAll':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TYP';
	$conn->query("CALL bot.get_typ_list_all ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["code"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTypForBotListAll':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TYP';
	$conn->query("CALL bot.get_typ_for_bot_list_all ('".$RequestObj->Parameters->Ref->FkBotName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["code"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTypForTypListAll':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TYP';
	$conn->query("CALL bot.get_typ_for_typ_list_all (".($RequestObj->Parameters->Option->CubeScopeLevel??"null").",'".$RequestObj->Parameters->Ref->FkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["code"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTyp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_TYP';
	$conn->query("CALL bot.get_typ ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->FkTypName = $row["fk_typ_name"];
		$RowObj->Data->Code = $row["code"];
		$RowObj->Data->FlagPartialKey = $row["flag_partial_key"];
		$RowObj->Data->FlagRecursive = $row["flag_recursive"];
		$RowObj->Data->RecursiveCardinality = $row["recursive_cardinality"];
		$RowObj->Data->Cardinality = $row["cardinality"];
		$RowObj->Data->SortOrder = $row["sort_order"];
		$RowObj->Data->Icon = $row["icon"];
		$RowObj->Data->Transferable = $row["transferable"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTypFkey':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_FKEY_TYP';
	$conn->query("CALL bot.get_typ_fkey ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTypItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TSG';
	$conn->query("CALL bot.get_typ_tsg_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->Code = $row["code"];
		$RowObj->Display = '('.$row["code"].')'.' '.$row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_ATB';
	$conn->query("CALL bot.get_typ_atb_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_REF';
	$conn->query("CALL bot.get_typ_ref_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->Sequence = $row["sequence"];
		$RowObj->Key->XkBotName = $row["xk_bot_name"];
		$RowObj->Key->XkTypName = $row["xk_typ_name"];
		$RowObj->Display = $row["name"].' ('.$row["cube_tsg_int_ext"].')'.' '.$row["xk_bot_name"].' '.$row["xk_typ_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_RTT';
	$conn->query("CALL bot.get_typ_rtt_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Key->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Key->XkTspCode = $row["xk_tsp_code"];
		$RowObj->Display = $row["xf_tsp_typ_name"].' '.$row["xf_tsp_tsg_code"].' '.$row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_JSN';
	$conn->query("CALL bot.get_typ_jsn_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->Name = $row["name"];
		$RowObj->Key->Location = $row["location"];
		$RowObj->Key->XfAtbTypName = $row["xf_atb_typ_name"];
		$RowObj->Key->XkAtbName = $row["xk_atb_name"];
		$RowObj->Key->XkTypName = $row["xk_typ_name"];
		$RowObj->Display = '('.$row["cube_tsg_obj_arr"].')'.' ('.$row["cube_tsg_type"].')'.' '.$row["name"].' '.$row["location"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_DCT';
	$conn->query("CALL bot.get_typ_dct_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Display = ' ';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TYP';
	$conn->query("CALL bot.get_typ_typ_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["code"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CountTypRestrictedItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_JSN';
	$conn->query("CALL bot.count_typ_jsn ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_DCT';
	$conn->query("CALL bot.count_typ_dct ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveTyp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_TYP';
	$conn->query("CALL bot.move_typ ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateTyp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_TYP';
	$conn->query("CALL bot.insert_typ ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->FlagPartialKey."','".$RequestObj->Parameters->Type->FlagRecursive."','".$RequestObj->Parameters->Type->RecursiveCardinality."','".$RequestObj->Parameters->Type->Cardinality."','".$RequestObj->Parameters->Type->SortOrder."','".$RequestObj->Parameters->Type->Icon."','".$RequestObj->Parameters->Type->Transferable."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateTyp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_TYP';
	$conn->query("CALL bot.update_typ ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->FlagPartialKey."','".$RequestObj->Parameters->Type->FlagRecursive."','".$RequestObj->Parameters->Type->RecursiveCardinality."','".$RequestObj->Parameters->Type->Cardinality."','".$RequestObj->Parameters->Type->SortOrder."','".$RequestObj->Parameters->Type->Icon."','".$RequestObj->Parameters->Type->Transferable."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteTyp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_TYP';
	$conn->query("CALL bot.delete_typ ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTsg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_TSG';
	$conn->query("CALL bot.get_tsg ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->FkTsgCode = $row["fk_tsg_code"];
		$RowObj->Data->Name = $row["name"];
		$RowObj->Data->PrimaryKey = $row["primary_key"];
		$RowObj->Data->XfAtbTypName = $row["xf_atb_typ_name"];
		$RowObj->Data->XkAtbName = $row["xk_atb_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTsgFkey':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_FKEY_TSG';
	$conn->query("CALL bot.get_tsg_fkey ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTsgItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TSP';
	$conn->query("CALL bot.get_tsg_tsp_items ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkTsgCode = $row["fk_tsg_code"];
		$RowObj->Key->Code = $row["code"];
		$RowObj->Display = '('.$row["code"].')'.' '.$row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TSG';
	$conn->query("CALL bot.get_tsg_tsg_items ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->Code = $row["code"];
		$RowObj->Display = '('.$row["code"].')'.' '.$row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CountTsgRestrictedItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_TSG';
	$conn->query("CALL bot.count_tsg_tsg ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveTsg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_TSG';
	$conn->query("CALL bot.move_tsg ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateTsg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_TSG';
	$conn->query("CALL bot.insert_tsg ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkTsgCode."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->PrimaryKey."','".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateTsg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_TSG';
	$conn->query("CALL bot.update_tsg ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkTsgCode."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->PrimaryKey."','".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteTsg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_TSG';
	$conn->query("CALL bot.delete_tsg ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTspForTypList':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TSP';
	$conn->query("CALL bot.get_tsp_for_typ_list (".($RequestObj->Parameters->Option->CubeScopeLevel??"null").",'".$RequestObj->Parameters->Ref->FkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkTsgCode = $row["fk_tsg_code"];
		$RowObj->Key->Code = $row["code"];
		$RowObj->Display = $row["fk_typ_name"].' '.$row["fk_tsg_code"].' ('.$row["code"].')'.' '.$row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTspForTsgList':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_TSP';
	$conn->query("CALL bot.get_tsp_for_tsg_list (".($RequestObj->Parameters->Option->CubeScopeLevel??"null").",'".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->FkTsgCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkTsgCode = $row["fk_tsg_code"];
		$RowObj->Key->Code = $row["code"];
		$RowObj->Display = $row["fk_typ_name"].' '.$row["fk_tsg_code"].' ('.$row["code"].')'.' '.$row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetTsp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_TSP';
	$conn->query("CALL bot.get_tsp ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkTsgCode."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->Name = $row["name"];
		$RowObj->Data->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Data->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Data->XkTspCode = $row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveTsp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_TSP';
	$conn->query("CALL bot.move_tsp ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkTsgCode."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->FkTsgCode."','".$RequestObj->Parameters->Ref->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateTsp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_TSP';
	$conn->query("CALL bot.insert_tsp ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkTsgCode."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->FkTsgCode."','".$RequestObj->Parameters->Ref->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateTsp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_TSP';
	$conn->query("CALL bot.update_tsp ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkTsgCode."','".$RequestObj->Parameters->Type->Code."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteTsp':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_TSP';
	$conn->query("CALL bot.delete_tsp ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkTsgCode."','".$RequestObj->Parameters->Type->Code."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetAtbForTypList':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_ATB';
	$conn->query("CALL bot.get_atb_for_typ_list (".($RequestObj->Parameters->Option->CubeScopeLevel??"null").",'".$RequestObj->Parameters->Ref->FkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["fk_typ_name"].' '.$row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetAtb':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_ATB';
	$conn->query("CALL bot.get_atb ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->PrimaryKey = $row["primary_key"];
		$RowObj->Data->CodeDisplayKey = $row["code_display_key"];
		$RowObj->Data->CodeForeignKey = $row["code_foreign_key"];
		$RowObj->Data->FlagHidden = $row["flag_hidden"];
		$RowObj->Data->DefaultValue = $row["default_value"];
		$RowObj->Data->Unchangeable = $row["unchangeable"];
		$RowObj->Data->XkItpName = $row["xk_itp_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetAtbFkey':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_FKEY_ATB';
	$conn->query("CALL bot.get_atb_fkey ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetAtbItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_DER';
	$conn->query("CALL bot.get_atb_der_items ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkAtbName = $row["fk_atb_name"];
		$RowObj->Display = '('.$row["cube_tsg_type"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_DCA';
	$conn->query("CALL bot.get_atb_dca_items ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkAtbName = $row["fk_atb_name"];
		$RowObj->Display = ' ';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_RTA';
	$conn->query("CALL bot.get_atb_rta_items ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkAtbName = $row["fk_atb_name"];
		$RowObj->Key->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Key->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Key->XkTspCode = $row["xk_tsp_code"];
		$RowObj->Display = $row["xf_tsp_typ_name"].' '.$row["xf_tsp_tsg_code"].' '.$row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CountAtbRestrictedItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_DER';
	$conn->query("CALL bot.count_atb_der ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_DCA';
	$conn->query("CALL bot.count_atb_dca ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveAtb':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_ATB';
	$conn->query("CALL bot.move_atb ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateAtb':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_ATB';
	$conn->query("CALL bot.insert_atb ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->PrimaryKey."','".$RequestObj->Parameters->Type->CodeDisplayKey."','".$RequestObj->Parameters->Type->CodeForeignKey."','".$RequestObj->Parameters->Type->FlagHidden."','".$RequestObj->Parameters->Type->DefaultValue."','".$RequestObj->Parameters->Type->Unchangeable."','".$RequestObj->Parameters->Type->XkItpName."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateAtb':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_ATB';
	$conn->query("CALL bot.update_atb ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->PrimaryKey."','".$RequestObj->Parameters->Type->CodeDisplayKey."','".$RequestObj->Parameters->Type->CodeForeignKey."','".$RequestObj->Parameters->Type->FlagHidden."','".$RequestObj->Parameters->Type->DefaultValue."','".$RequestObj->Parameters->Type->Unchangeable."','".$RequestObj->Parameters->Type->XkItpName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteAtb':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_ATB';
	$conn->query("CALL bot.delete_atb ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetDer':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_DER';
	$conn->query("CALL bot.get_der ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->CubeTsgType = $row["cube_tsg_type"];
		$RowObj->Data->AggregateFunction = $row["aggregate_function"];
		$RowObj->Data->XkTypName = $row["xk_typ_name"];
		$RowObj->Data->XkTypName1 = $row["xk_typ_name_1"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateDer':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_DER';
	$conn->query("CALL bot.insert_der ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->AggregateFunction."','".$RequestObj->Parameters->Type->XkTypName."','".$RequestObj->Parameters->Type->XkTypName1."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateDer':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_DER';
	$conn->query("CALL bot.update_der ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->AggregateFunction."','".$RequestObj->Parameters->Type->XkTypName."','".$RequestObj->Parameters->Type->XkTypName1."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteDer':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_DER';
	$conn->query("CALL bot.delete_der ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetDca':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_DCA';
	$conn->query("CALL bot.get_dca ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->Text = $row["text"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateDca':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_DCA';
	$conn->query("CALL bot.insert_dca ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->Text."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateDca':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_DCA';
	$conn->query("CALL bot.update_dca ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->Text."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteDca':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_DCA';
	$conn->query("CALL bot.delete_dca ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetRta':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_RTA';
	$conn->query("CALL bot.get_rta ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->IncludeOrExclude = $row["include_or_exclude"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateRta':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_RTA';
	$conn->query("CALL bot.insert_rta ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkAtbName = $row["fk_atb_name"];
		$RowObj->Key->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Key->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Key->XkTspCode = $row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateRta':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_RTA';
	$conn->query("CALL bot.update_rta ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteRta':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_RTA';
	$conn->query("CALL bot.delete_rta ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkAtbName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetRef':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_REF';
	$conn->query("CALL bot.get_ref ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->Name = $row["name"];
		$RowObj->Data->PrimaryKey = $row["primary_key"];
		$RowObj->Data->CodeDisplayKey = $row["code_display_key"];
		$RowObj->Data->Scope = $row["scope"];
		$RowObj->Data->Unchangeable = $row["unchangeable"];
		$RowObj->Data->WithinScopeExtension = $row["within_scope_extension"];
		$RowObj->Data->CubeTsgIntExt = $row["cube_tsg_int_ext"];
		$RowObj->Data->XkTypName1 = $row["xk_typ_name_1"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetRefFkey':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_FKEY_REF';
	$conn->query("CALL bot.get_ref_fkey ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetRefItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_DCR';
	$conn->query("CALL bot.get_ref_dcr_items ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkRefSequence = $row["fk_ref_sequence"];
		$RowObj->Key->FkRefBotName = $row["fk_ref_bot_name"];
		$RowObj->Key->FkRefTypName = $row["fk_ref_typ_name"];
		$RowObj->Display = ' ';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_RTR';
	$conn->query("CALL bot.get_ref_rtr_items ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkRefSequence = $row["fk_ref_sequence"];
		$RowObj->Key->FkRefBotName = $row["fk_ref_bot_name"];
		$RowObj->Key->FkRefTypName = $row["fk_ref_typ_name"];
		$RowObj->Key->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Key->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Key->XkTspCode = $row["xk_tsp_code"];
		$RowObj->Display = $row["xf_tsp_typ_name"].' '.$row["xf_tsp_tsg_code"].' '.$row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_RTS';
	$conn->query("CALL bot.get_ref_rts_items ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkRefSequence = $row["fk_ref_sequence"];
		$RowObj->Key->FkRefBotName = $row["fk_ref_bot_name"];
		$RowObj->Key->FkRefTypName = $row["fk_ref_typ_name"];
		$RowObj->Key->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Key->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Key->XkTspCode = $row["xk_tsp_code"];
		$RowObj->Display = $row["include_or_exclude"].' '.$row["xf_tsp_typ_name"].' '.$row["xf_tsp_tsg_code"].' '.$row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CountRefRestrictedItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_DCR';
	$conn->query("CALL bot.count_ref_dcr ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).',';
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_RTS';
	$conn->query("CALL bot.count_ref_rts ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveRef':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_REF';
	$conn->query("CALL bot.move_ref ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."','".$RequestObj->Parameters->Ref->FkTypName."',".($RequestObj->Parameters->Ref->Sequence??"null").",'".$RequestObj->Parameters->Ref->XkBotName."','".$RequestObj->Parameters->Ref->XkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateRef':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_REF';
	$conn->query("CALL bot.insert_ref ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->PrimaryKey."','".$RequestObj->Parameters->Type->CodeDisplayKey."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->Scope."','".$RequestObj->Parameters->Type->Unchangeable."','".$RequestObj->Parameters->Type->WithinScopeExtension."','".$RequestObj->Parameters->Type->CubeTsgIntExt."','".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."','".$RequestObj->Parameters->Type->XkTypName1."','".$RequestObj->Parameters->Ref->FkTypName."',".($RequestObj->Parameters->Ref->Sequence??"null").",'".$RequestObj->Parameters->Ref->XkBotName."','".$RequestObj->Parameters->Ref->XkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateRef':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_REF';
	$conn->query("CALL bot.update_ref ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->PrimaryKey."','".$RequestObj->Parameters->Type->CodeDisplayKey."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->Scope."','".$RequestObj->Parameters->Type->Unchangeable."','".$RequestObj->Parameters->Type->WithinScopeExtension."','".$RequestObj->Parameters->Type->CubeTsgIntExt."','".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."','".$RequestObj->Parameters->Type->XkTypName1."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteRef':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_REF';
	$conn->query("CALL bot.delete_ref ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->Sequence??"null").",'".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetDcr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_DCR';
	$conn->query("CALL bot.get_dcr ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->Text = $row["text"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateDcr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_DCR';
	$conn->query("CALL bot.insert_dcr ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->Text."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateDcr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_DCR';
	$conn->query("CALL bot.update_dcr ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->Text."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteDcr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_DCR';
	$conn->query("CALL bot.delete_dcr ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetRtr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_RTR';
	$conn->query("CALL bot.get_rtr ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->IncludeOrExclude = $row["include_or_exclude"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateRtr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_RTR';
	$conn->query("CALL bot.insert_rtr ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->FkRefSequence = $row["fk_ref_sequence"];
		$RowObj->Key->FkRefBotName = $row["fk_ref_bot_name"];
		$RowObj->Key->FkRefTypName = $row["fk_ref_typ_name"];
		$RowObj->Key->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Key->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Key->XkTspCode = $row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateRtr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_RTR';
	$conn->query("CALL bot.update_rtr ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteRtr':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_RTR';
	$conn->query("CALL bot.delete_rtr ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetRts':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_RTS';
	$conn->query("CALL bot.get_rts ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->IncludeOrExclude = $row["include_or_exclude"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateRts':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_RTS';
	$conn->query("CALL bot.insert_rts ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateRts':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_RTS';
	$conn->query("CALL bot.update_rts ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteRts':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_RTS';
	$conn->query("CALL bot.delete_rts ('".$RequestObj->Parameters->Type->FkTypName."',".($RequestObj->Parameters->Type->FkRefSequence??"null").",'".$RequestObj->Parameters->Type->FkRefBotName."','".$RequestObj->Parameters->Type->FkRefTypName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetRtt':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_RTT';
	$conn->query("CALL bot.get_rtt ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->IncludeOrExclude = $row["include_or_exclude"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateRtt':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_RTT';
	$conn->query("CALL bot.insert_rtt ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->XfTspTypName = $row["xf_tsp_typ_name"];
		$RowObj->Key->XfTspTsgCode = $row["xf_tsp_tsg_code"];
		$RowObj->Key->XkTspCode = $row["xk_tsp_code"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateRtt':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_RTT';
	$conn->query("CALL bot.update_rtt ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->IncludeOrExclude."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteRtt':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_RTT';
	$conn->query("CALL bot.delete_rtt ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->XfTspTypName."','".$RequestObj->Parameters->Type->XfTspTsgCode."','".$RequestObj->Parameters->Type->XkTspCode."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetJsn':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_JSN';
	$conn->query("CALL bot.get_jsn ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."',".($RequestObj->Parameters->Type->Location??"null").",'".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->FkJsnName = $row["fk_jsn_name"];
		$RowObj->Data->FkJsnLocation = $row["fk_jsn_location"];
		$RowObj->Data->FkJsnAtbTypName = $row["fk_jsn_atb_typ_name"];
		$RowObj->Data->FkJsnAtbName = $row["fk_jsn_atb_name"];
		$RowObj->Data->FkJsnTypName = $row["fk_jsn_typ_name"];
		$RowObj->Data->CubeTsgObjArr = $row["cube_tsg_obj_arr"];
		$RowObj->Data->CubeTsgType = $row["cube_tsg_type"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetJsnFkey':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_FKEY_JSN';
	$conn->query("CALL bot.get_jsn_fkey ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."',".($RequestObj->Parameters->Type->Location??"null").",'".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetJsnItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_JSN';
	$conn->query("CALL bot.get_jsn_jsn_items ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."',".($RequestObj->Parameters->Type->Location??"null").",'".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkTypName = $row["fk_typ_name"];
		$RowObj->Key->Name = $row["name"];
		$RowObj->Key->Location = $row["location"];
		$RowObj->Key->XfAtbTypName = $row["xf_atb_typ_name"];
		$RowObj->Key->XkAtbName = $row["xk_atb_name"];
		$RowObj->Key->XkTypName = $row["xk_typ_name"];
		$RowObj->Display = '('.$row["cube_tsg_obj_arr"].')'.' ('.$row["cube_tsg_type"].')'.' '.$row["name"].' '.$row["location"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveJsn':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_JSN';
	$conn->query("CALL bot.move_jsn ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."',".($RequestObj->Parameters->Type->Location??"null").",'".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Type->XkTypName."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->Name."',".($RequestObj->Parameters->Ref->Location??"null").",'".$RequestObj->Parameters->Ref->XfAtbTypName."','".$RequestObj->Parameters->Ref->XkAtbName."','".$RequestObj->Parameters->Ref->XkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateJsn':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_JSN';
	$conn->query("CALL bot.insert_jsn ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkJsnName."',".($RequestObj->Parameters->Type->FkJsnLocation??"null").",'".$RequestObj->Parameters->Type->FkJsnAtbTypName."','".$RequestObj->Parameters->Type->FkJsnAtbName."','".$RequestObj->Parameters->Type->FkJsnTypName."','".$RequestObj->Parameters->Type->CubeTsgObjArr."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->Name."',".($RequestObj->Parameters->Type->Location??"null").",'".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Type->XkTypName."','".$RequestObj->Parameters->Ref->FkTypName."','".$RequestObj->Parameters->Ref->Name."',".($RequestObj->Parameters->Ref->Location??"null").",'".$RequestObj->Parameters->Ref->XfAtbTypName."','".$RequestObj->Parameters->Ref->XkAtbName."','".$RequestObj->Parameters->Ref->XkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateJsn':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_JSN';
	$conn->query("CALL bot.update_jsn ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->FkJsnName."',".($RequestObj->Parameters->Type->FkJsnLocation??"null").",'".$RequestObj->Parameters->Type->FkJsnAtbTypName."','".$RequestObj->Parameters->Type->FkJsnAtbName."','".$RequestObj->Parameters->Type->FkJsnTypName."','".$RequestObj->Parameters->Type->CubeTsgObjArr."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->Name."',".($RequestObj->Parameters->Type->Location??"null").",'".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteJsn':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_JSN';
	$conn->query("CALL bot.delete_jsn ('".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Name."',".($RequestObj->Parameters->Type->Location??"null").",'".$RequestObj->Parameters->Type->XfAtbTypName."','".$RequestObj->Parameters->Type->XkAtbName."','".$RequestObj->Parameters->Type->XkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetDct':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_DCT';
	$conn->query("CALL bot.get_dct ('".$RequestObj->Parameters->Type->FkTypName."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->FkBotName = $row["fk_bot_name"];
		$RowObj->Data->Text = $row["text"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateDct':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_DCT';
	$conn->query("CALL bot.insert_dct ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Text."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateDct':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_DCT';
	$conn->query("CALL bot.update_dct ('".$RequestObj->Parameters->Type->FkBotName."','".$RequestObj->Parameters->Type->FkTypName."','".$RequestObj->Parameters->Type->Text."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteDct':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_DCT';
	$conn->query("CALL bot.delete_dct ('".$RequestObj->Parameters->Type->FkTypName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetDirSysItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_SYS';
	$conn->query("CALL sys.get_sys_root_items ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"].' ('.$row["cube_tsg_type"].')';
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetSys':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'SELECT_SYS';
	$conn->query("CALL sys.get_sys ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->CubeTsgType = $row["cube_tsg_type"];
		$RowObj->Data->Database = $row["database"];
		$RowObj->Data->Schema = $row["schema"];
		$RowObj->Data->Password = $row["password"];
		$RowObj->Data->TablePrefix = $row["table_prefix"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetSysItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_SBT';
	$conn->query("CALL sys.get_sys_sbt_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkSysName = $row["fk_sys_name"];
		$RowObj->Key->XkBotName = $row["xk_bot_name"];
		$RowObj->Display = $row["xk_bot_name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateSys':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_SYS';
	$conn->query("CALL sys.insert_sys ('".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->Database."','".$RequestObj->Parameters->Type->Schema."','".$RequestObj->Parameters->Type->Password."','".$RequestObj->Parameters->Type->TablePrefix."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'UpdateSys':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'UPDATE_SYS';
	$conn->query("CALL sys.update_sys ('".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Type->CubeTsgType."','".$RequestObj->Parameters->Type->Database."','".$RequestObj->Parameters->Type->Schema."','".$RequestObj->Parameters->Type->Password."','".$RequestObj->Parameters->Type->TablePrefix."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteSys':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_SYS';
	$conn->query("CALL sys.delete_sys ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveSbt':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_SBT';
	$conn->query("CALL sys.move_sbt ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkSysName."','".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Ref->FkSysName."','".$RequestObj->Parameters->Ref->XkBotName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateSbt':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_SBT';
	$conn->query("CALL sys.insert_sbt ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkSysName."','".$RequestObj->Parameters->Type->XkBotName."','".$RequestObj->Parameters->Ref->FkSysName."','".$RequestObj->Parameters->Ref->XkBotName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteSbt':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_SBT';
	$conn->query("CALL sys.delete_sbt ('".$RequestObj->Parameters->Type->FkSysName."','".$RequestObj->Parameters->Type->XkBotName."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetDirFunItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_FUN';
	$conn->query("CALL fun.get_fun_root_items ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CountFun':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'COUNT_FUN';
	$conn->query("CALL fun.count_fun ()");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	if ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Data = new \stdClass();
		$RowObj->Data->TypeCount = $row["type_count"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'GetFunItems':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'LIST_ARG';
	$conn->query("CALL fun.get_fun_arg_items ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseObj->Rows = array();
	$curs = $conn->query('FETCH ALL FROM cube_cursor;');
	while ($row = $curs->fetch(PDO::FETCH_ASSOC)) {
		$RowObj = new \stdClass();
		$RowObj->Key = new \stdClass();
		$RowObj->Key->FkFunName = $row["fk_fun_name"];
		$RowObj->Key->Name = $row["name"];
		$RowObj->Display = $row["name"];
		$ResponseObj->Rows[] = $RowObj;
	}
	$curs = $conn->query('CLOSE cube_cursor;');
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateFun':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_FUN';
	$conn->query("CALL fun.insert_fun ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteFun':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_FUN';
	$conn->query("CALL fun.delete_fun ('".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'MoveArg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'MOVE_ARG';
	$conn->query("CALL fun.move_arg ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkFunName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Ref->FkFunName."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'CreateArg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'CREATE_ARG';
	$conn->query("CALL fun.insert_arg ('".$RequestObj->Parameters->Option->CubePosAction."','".$RequestObj->Parameters->Type->FkFunName."','".$RequestObj->Parameters->Type->Name."','".$RequestObj->Parameters->Ref->FkFunName."','".$RequestObj->Parameters->Ref->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;
case 'DeleteArg':
	$conn->query("BEGIN;");
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'DELETE_ARG';
	$conn->query("CALL fun.delete_arg ('".$RequestObj->Parameters->Type->FkFunName."','".$RequestObj->Parameters->Type->Name."')");
	$ResponseText = $ResponseText.json_encode($ResponseObj).']';
	$conn->query("END;");
	break;

default:
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'ERROR';
	$ResponseObj->ErrorText = $RequestText;
	$ResponseText = '['.json_encode($ResponseObj).']';
}
echo $ResponseText;

function perform_db_request() {

	global $conn, $stid, $curs;

	$curs = oci_new_cursor($conn);
	oci_bind_by_name($stid,":p_cube_row",$curs,-1,OCI_B_CURSOR);
	$r = oci_execute($stid);
	if (!$r) {
		ProcessDbError($stid);
		return false;
	}
	//echo $r;
	$r = oci_execute($curs);
	if (!$r) {
		ProcessDbError($curs);
		return false;
	}
	return true;
}

function ProcessDbError($stid) {

	$e = oci_error($stid);
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'ERROR';
	$ResponseObj->ErrorText = 'ORA-error: '.$e['code'].': '.$e['message'];
	$ResponseText = '['.json_encode($ResponseObj).']';
	echo $ResponseText;
}

function CubeError($errno, $errstr) {
	if ($errno > 2) {
		$ResponseObj = new \stdClass();
		$ResponseObj->ResultName = 'ERROR';
		$ResponseObj->ErrorText = "[$errno] $errstr";
		$ResponseText = '['.json_encode($ResponseObj).']';
		echo $ResponseText;
		exit;
	}
}

function CubeException($exception) {
	$ResponseObj = new \stdClass();
	$ResponseObj->ResultName = 'ERROR';
	$ResponseObj->ErrorText = "$exception";
	$ResponseText = json_encode($ResponseObj);
	echo '['.$ResponseText.']';
	exit;
}
?>
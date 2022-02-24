<?php

require_once(__DIR__ . '/crest.php');

$user_id = $_REQUEST['data']['PARAMS']['FROM_USER_ID'];
$user = CRest::call('user.get',["ID" => $user_id]);
$user_name = $user['result'][0]['LAST_NAME']." ".$user['result'][0]['NAME'];
$dialog_id = $_REQUEST['data']['PARAMS']['DIALOG_ID'];
//$arrayw = print_r($_REQUEST['data']['PARAMS'], true);

switch(strtoupper($_REQUEST['event']))
{
	case 'ONIMBOTJOINCHAT':
		$report = DialogRest::getAnswer("Привет", $dialog_id);
		CRest::call(
			'imbot.message.add',
			[
				'DIALOG_ID' => $dialog_id,
				'MESSAGE' => $report['response'],
			]
		);
		//DialogRest::addLog("Привет", $user_id, $user_name, $report);
		break;
	case 'ONIMBOTMESSAGEADD':
		$g_chat = (strpos($dialog_id, "chat") === false) ? 0 : 1;
		$qery = $_REQUEST['data']['PARAMS']['MESSAGE'];
		$report = DialogRest::getAnswer($qery, $dialog_id);
		//$b = print_r($report, true);
		//$report = [];
		//$report['response'] = "test";
		//$b = print_r(DialogRest::addLog($_REQUEST['data']['PARAMS']['MESSAGE'], $user_id, $user_name, $report), true);
		$answer = ($g_chat == 1) ? "[USER=".$user_id."]".$user_name."[/USER] ".$report['response'] : $report['response'];
		CRest::call(
			'imbot.message.add',
			[
				"DIALOG_ID" => $dialog_id,
				"MESSAGE" => $answer,
			]
		);
		//DialogRest::addLog($qery, $user_id, $user_name, $report);
		break;
}
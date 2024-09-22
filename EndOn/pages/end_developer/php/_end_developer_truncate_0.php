<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_developer_truncate.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=>	'plugins/js/end_developer/end_developer_truncate.js',
	);

	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_developer/php/_end_developer_truncate_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_developer/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_developer_truncate_0.php',
	);

	$aValue = array(
		'a'	=> 'TRUNCATE',
		't' => NOWTIME,
	);
	$sJWT = sys_jwt_encode($aValue);
	$aUrl['sAct'] .= '&sJWT='.$sJWT;
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

	$aData = array(
		'Manager'	=> array(
			END_MANAGER_DATA		=> true,
			END_MANAGER_COOKIE	=> true,
			END_MENU_CTRL		=> true,
			SYS_GOOGLE_VERIFY		=> true,
		),
		'User'=> array(
			CLIENT_USER_DATA		=> true,
			CLIENT_USER_DETAIL	=> true,
			CLIENT_USER_HIDE		=> true,
			CLIENT_USER_LINK		=> true,
			CLIENT_USER_MONEY		=> true,
			CLIENT_USER_BANK		=> true,
			CLIENT_USER_COOKIE	=> true,
			CLIENT_USER_VERIFY	=> true,
			CLIENT_USER_KIND 		=> true,
			CLIENT_USER_FRIEND	=> true,
			CLIENT_USER_BLOCK		=> true,
		),
		'Money' => array(
			CLIENT_MONEY		=> true,
			CLIENT_PAYMENT		=> true,
			CLIENT_PAYMENT_TUNNEL	=> true,
		),
		'Job' => array(
			CLIENT_JOB			=> true,
			CLIENT_JOB_SCORE		=> true,
			CLIENT_JOB_TYPE		=> true,
			CLIENT_USER_JOB_FAVORITE=> true,
			CLIENT_LOCATION 		=> true,
		),
		'Chat' => array(
			CLIENT_GROUP_CTRL		=> true,
			CLIENT_GROUP_MSG		=> true,
			CLIENT_USER_GROUP_LIST	=> true,
		),
		'Log' => array(
			END_LOG			=> true,
			END_LOG_ACCOUNT		=> true,
			END_MANAGER_LOGIN		=> true,
			CLIENT_USER_LOGIN		=> true,
		),
		'Other'=> array(
			CLIENT_ANNOUNCE		=> true,
			CLIENT_ANNOUNCE_KIND	=> true,
			CLIENT_BROADCAST 		=> true,
			CLIENT_BROADCAST_KIND 	=> true,
			CLIENT_DISCUSS		=> true,
			CLIENT_DISCUSS_REPLY	=> true,
			CLIENT_IMAGE_CTRL		=> true,
			CLIENT_DATA_CTRL		=> true,
			CLIENT_SERVICE 		=> true,
			CLIENT_SNOOZE_KEYWORDS	=> true,
		),
		'Schedule'=> array(
			SYS_MOVE_RECORD		=> true,
			END_LOG_MOVE		=> true,
			END_LOG_ACCOUNT_MOVE	=> true,
			END_MANAGER_LOGIN_MOVE 	=> true,
			CLIENT_USER_LOGIN_MOVE 	=> true,
		),
	);

	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
?>
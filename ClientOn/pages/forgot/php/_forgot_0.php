<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/forgot.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/center/verify.js',
	);
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'		=> sys_web_encode($aMenuToNo['pages/login/php/_login_0.php']),
		'sAct'		=> sys_web_encode($aMenuToNo['pages/forgot/php/_forgot_0_act0.php']).'&run_page=1',
		'sHtml'		=> 'pages/forgot/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/forgot_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aValue = array(
		'a'		=> 'UPT',
		't'		=> NOWTIME,
	);
	$sJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'		=> 'GETVCODE',
		't'		=> NOWTIME,
	);
	$sAjaxJWT = sys_jwt_encode($aValue);

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';
	#宣告結束

	#程式邏輯區

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
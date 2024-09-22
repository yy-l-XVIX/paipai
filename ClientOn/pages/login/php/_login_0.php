<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/login.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0' => 'plugins/js/login/login.js',
	);

	$aValue = array(
		'a'		=> 'LOGIN',
		't'		=> NOWTIME,
		// 'nExp'	=> NOWTIME + JWTWAIT
	);

	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/login/php/_login_0_act0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue),
		'sForgot'	=> sys_web_encode($aMenuToNo['pages/forgot/php/_forgot_0.php']),
		'sRegister'	=> sys_web_encode($aMenuToNo['pages/register/php/_choose_0.php']),
		'sHtml'	=> 'pages/login/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/login_0.php',
	);

	$aData = array(
		'nRemember'	=> isset($_COOKIE['nRemember']) ? 1 : 0,
		'sAccount'	=> isset($_COOKIE['sAccount']) ? $_COOKIE['sAccount'] : '',
		'sCheck'	=> (isset($_COOKIE['nRemember']) && $_COOKIE['nRemember'] == 1) ? 'checked' : '',
		'sJWT'	=> sys_jwt_encode($aValue),
		'sBackUrl'	=> './?'.$aRequire['Param'],
		'nKid'	=> isset($_COOKIE['nKid']) ? $_COOKIE['nKid'] : 3,
	);

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sClass'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;

	#輸出json
	$sData = json_encode($aData);

	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
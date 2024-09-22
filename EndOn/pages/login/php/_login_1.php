<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/login.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sHtml'	=> 'pages/login/'.$aSystem['sHtml'].$aSystem['nVer'].'/login_1.php', # 本頁html
	);
	#url結束

	#參數宣告區
	$aValue = array(
		'a'		=> 'LOGIN',
		't'		=> NOWTIME,
		'nExp'	=> NOWTIME + JWTWAIT
	);
	$aData = array(
		'nRemember'	=> isset($_COOKIE['nRemember']) ? 1 : 0,
		'sAccount'	=> isset($_COOKIE['sAccount']) ? $_COOKIE['sAccount'] : '',
		'sCheck'	=> (isset($_COOKIE['nRemember']) && $_COOKIE['nRemember'] == 1) ? 'checked' : '',
		'sUrl'	=> sys_web_encode($aMenuToNo['pages/login/php/_login_1_act0.php']).'&run_page=1',
		'sJWT'	=> sys_jwt_encode($aValue),
		'sBackUrl'	=> './?'.$aRequire['Param']
	);
	#宣告結束

	#程式邏輯區
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/google.php');
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
		'sHtml'	=> 'pages/login/'.$aSystem['sHtml'].$aSystem['nVer'].'/google_0.php', # 本頁html
	);
	#url結束

	#參數宣告區
	$aValue = array(
		'a'		=> 'LOGOUT',
		'nExp'	=> NOWTIME + JWTWAIT,
		'sAccount'	=> $aAdm['sAccount'],
	);
	$sLogoutJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'		=> 'VERIFY',
		'nExp'	=> NOWTIME + JWTWAIT
	);
	$aData = array(
		'sAccount'	=> $aAdm['sAccount'],
		'sUrl'	=> sys_web_encode($aMenuToNo['pages/login/php/_google_0_act0.php']).'&run_page=1',
		'sLogout'	=> sys_web_encode($aMenuToNo['pages/login/php/_login_0_act0.php']).'&run_page=1&sJWT='.$sLogoutJWT,
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
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_manager_password.php');
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
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_password_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_manager_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_manager_password_0.php'
	);
	#url結束

	#參數宣告區
	$aData   = array();
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	$aValue = array(
		'a'		=> 'UPT',
		'nExp'	=> NOWTIME + JWTWAIT,
		'nId'		=> $aAdm['nId'],
	);
	$sJWTAct = sys_jwt_encode($aValue);
	#宣告結束

	#程式邏輯區



	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
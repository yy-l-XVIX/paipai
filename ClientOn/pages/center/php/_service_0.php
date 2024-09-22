<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/service.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/service.js',
	);

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'		=> sys_web_encode($aMenuToNo['pages/center/php/_service_0_act0.php']).'&run_page=1',
		'sHtml'		=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/service_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aValue=array(
		'a'	=> 'INS',
	);
	$sActJWT =sys_jwt_encode($aValue);
	$aUrl['sAct'] .= '&sJWT='.$sActJWT;

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';

	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	sName0,
					nLid
			FROM		'.CLIENT_SERVICE_KIND.'
			WHERE		sLang LIKE :sLang
			AND		nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nLid']] = $aRows['sName0'];
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
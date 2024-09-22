<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/bank_add.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0' => 'plugins/js/center/bank_add.js',
		'1' => 'plugins/js/File.js',
	);
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url

	$aValue = array(
		'a'		=> 'INS',
		't'		=> NOWTIME,
	);

	$aUrl   = array(
		'sFormAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_bank_add_0_act0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue),
		'sGo'		=> sys_web_encode($aMenuToNo['pages/center/php/_bank_list_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/bank_add_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBank = array();
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';

	$aJumpMsg['dataprocessing'] = array(
		'sBoxClass'	=>	'',
		'sShow'	=>	0,	# 是否直接顯示彈窗 0=>隱藏 , 1=>顯示
		'sTitle'	=>	'',	# 標題
		'sIcon'	=>	'',	# 成功=>success,失敗=>error
		'sMsg'	=>	DATAPROCESSING,# 資料處理中
		'sArticle'	=>	'',	# 較長文字
		'aButton'	=>	array(),
		'nClicktoClose'=>	0,	# 是否點擊任意一處即可關閉 0=>否 , 1=>是
	);
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	nId,
					sName0,
					sCode
			FROM		'.SYS_BANK.'
			WHERE		nOnline = 1
			AND		nType0 = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aBank[$aRows['nId']] = $aRows;
	}

	#
	$sSQL = '	SELECT 	nId
			FROM 	'.CLIENT_USER_BANK.'
			WHERE nOnline = 1
			AND 	nUid = :nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	$nCount = $Result->rowCount();
	if ($nCount >= $aSystem['aParam']['nCardLimit'])
	{
		$nErr = 1;
		$sErrMsg = aERR['BANKLIMIT'];
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);

	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['nClicktoClose'] = 1;
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		$aJumpMsg['0']['aButton']['0']['sClass'] = '';
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sGo'];
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
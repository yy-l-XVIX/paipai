<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/withdrawal.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=>	'plugins/js/withdrawal/withdrawal.js',
	);

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sBankAdd'		=> sys_web_encode($aMenuToNo['pages/center/php/_bank_add_0.php']),
		'sAct'		=> sys_web_encode($aMenuToNo['pages/withdrawal/php/_withdrawal_0_act0.php']).'&run_page=1',
		'sHtml'		=> 'pages/withdrawal/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/withdrawal_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBankData = array();
	$aSearchId = array();
	$aValue=array(
		'a'	=> 'INS'.$aUser['nId'],
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
	$sSQL = '	SELECT 	nId,
					sName0,
					sName2,
					nBid
			FROM 	'.CLIENT_USER_BANK.'
			WHERE nOnline = 1
			AND 	nUid = :nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aSearchId[$aRows['nBid']] = $aRows['nBid'];
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						sCode
				FROM 	'.SYS_BANK.'
				WHERE nOnline = 1
				AND 	nType0 = 1';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aBankData[$aRows['nId']] = $aRows;
		}
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
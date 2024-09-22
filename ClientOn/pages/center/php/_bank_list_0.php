<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/bank_list.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0' => 'plugins/js/center/bank_list.js',
	);
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sDel'	=> sys_web_encode($aMenuToNo['pages/center/php/_bank_add_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/bank_list_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBank = array();
	$aImage = array();
	$sKids = '0';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	nId,
					sName0
			FROM		'.SYS_BANK.'
			WHERE		1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aBank[$aRows['nId']] = $aRows;
	}

	$sSQL = '	SELECT	nId,
					sName0,
					sName1,
					sName2,
					nBid
			FROM		'.CLIENT_USER_BANK.'
			WHERE		nOnline = 1
			AND 		nUid = :nUid
			ORDER	BY	nId DESC';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sBank'] = $aBank[$aRows['nBid']]['sName0'];
		$aData[$aRows['nId']]['sImg'] = $aBank[$aRows['nBid']]['sName0'];
		$sKids .= ','.$aRows['nId'];

		$aValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			't'		=> NOWTIME,
		);
		$sLPJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sDelUrl'] = $aUrl['sDel'].'&nId='.$aRows['nId'].'&sJWT='.$sLPJWT;
	}

	$sSQL = '	SELECT	nId,
					nKid,
					sFile,
					sTable,
					nCreateTime
			FROM		'.CLIENT_IMAGE_CTRL.'
			WHERE		sTable = \''. CLIENT_USER_BANK .'\'
			AND		nKid IN ( '.$sKids.' )';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aImage[$aRows['nKid']] = base64Pic(IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile']);
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
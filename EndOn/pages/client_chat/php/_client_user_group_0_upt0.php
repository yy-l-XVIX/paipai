<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_group.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId', INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_chat/php/_client_user_group_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_chat/php/_client_user_group_0.php']),
		'sHtml'	=> 'pages/client_chat/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_group_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aStatus = aGROUP['aSTATUS'];
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		'nExp'	=> NOWTIME+JWTWAIT,
	);
	$sJWT = sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';
	#宣告結束

	#程式邏輯區

	$sSQL = '	SELECT	nId,
					sName0,
					nUid,
					nType0
			FROM	'.CLIENT_GROUP_CTRL .'
			WHERE	nId = :nId
			AND 	nType1 = 0
			AND 	nOnline != 99
			LIMIT	1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aData = $Result->fetch(PDO::FETCH_ASSOC);
	if($nId != 0)
	{
		if ($aData === false)
		{
			$nErr = 1;
			$sErrMsg = NODATA;
		}
	}
	else
	{
		$aData['sName0'] = '';
	}
	$aData['aMember'] = array();

	$sSQL = '	SELECT 	List_.nId,
					List_.nUid,
					List_.nStatus,
					User_.sAccount
			FROM 	'.CLIENT_USER_GROUP_LIST.' List_,
				'.CLIENT_USER_DATA.' User_
			WHERE List_.nGid = :nGid
			AND 	List_.nUid = User_.nId';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aMember'][$aRows['nUid']] = $aRows;
		$aValue = array(
			'a'		=> 'DELMEMBER'.$aRows['nId'],
			't'		=> NOWTIME,
		);
		$LPsJWT = sys_jwt_encode($aValue);
		$aData['aMember'][$aRows['nUid']]['sDel'] = $aUrl['sAct'].'&nLid='.$aRows['nId'].'&nId='.$nId.'&sJWT='.$LPsJWT;
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	else
	{
		$aJumpMsg['0']['sClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
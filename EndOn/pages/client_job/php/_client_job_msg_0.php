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
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_msg_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_0.php']).$aJWT['sBackParam'],
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_msg_0.php']),
		'sHtml'	=> 'pages/client_job/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_job_msg_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
	$aMemberData = array();
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		'nExp'	=> NOWTIME+JWTWAIT,
	);
	$sJWT = sys_jwt_encode($aValue);
	$sSearch = '&nPageNo='.$aPage['nNowNo'];

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aPage['aVar'] = array(
		'nId' => $nId,
	);

	$nErr = 0;
	$sErrMsg = '';
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT 	nId,
					sName0,
					nUid
			FROM 	'.CLIENT_GROUP_CTRL.'
			WHERE nId = :nId
			AND 	nType1 = 1
			AND 	nOnline != 99
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aGroup = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aGroup === false)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	$sSQL ='	SELECT 	1
			FROM 	'.CLIENT_GROUP_MSG.'
			WHERE nGid = :nGid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL ='	SELECT 	nId,
					nUid,
					nTargetUid,
					sMsg,
					sCreateTime
			FROM 	'.CLIENT_GROUP_MSG.'
			WHERE nGid = :nGid
			AND 	nOnline = 1
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sMsg'] = convertContent($aRows['sMsg']);
		if ($aRows['sMsg'] == '[:invite job:]')
		{
			$aRows['sMsg'] = '是否參加工作?';
		}
		$aData[$aRows['nId']] = $aRows;
		$LPaValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
		);
		$aData[$aRows['nId']]['sDelUrl'] = $aUrl['sAct'].'&sJWT='.sys_jwt_encode($LPaValue).'&nId='.$aRows['nId'].'&nJid='.$nId;

		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
		$aSearchId[$aRows['nTargetUid']] = $aRows['nTargetUid'];
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
		}
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
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
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
		$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
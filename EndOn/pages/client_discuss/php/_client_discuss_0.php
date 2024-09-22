<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_discuss.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/client_discuss/client_discuss.js',

	);
	#js結束

	#參數接收區
	$sAccount 	 = filter_input_str('sAccount',	INPUT_REQUEST, '');
	$sStartTime	 = filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime	 = filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$sSelDay	= filter_input_str('sSelDay',		INPUT_REQUEST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0.php']),
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0_upt0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/client_discuss/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_discuss_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
	$aBindArray = array();
	$aPage['aVar'] = array(
		'sAccount'	=> $sAccount,
		'sStartTime'=> $sStartTime,
		'sEndTime'	=> $sEndTime,
		'sSelDay'	=> $sSelDay,
	);
	$aDay = aDAY;
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	foreach ($aPage['aVar'] as $LPsParam => $LPsValue)
	{
		$sBackParam .= '&'.$LPsParam.'='.$LPsValue;
	}
	$aValue = array(
		'sBackParam' => $sBackParam,
	);
	$aUrl['sIns'] .= '&sJWT='.sys_jwt_encode($aValue);

	$sCondition = 'WHERE nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime ';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);
	if ($sAccount != '')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE sAccount LIKE :sAccount
				AND 	nOnline != 99';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sAccount.'%', PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}
		if (!empty($aSearchId))
		{
			$sCondition .= ' AND (nUid IN ('.implode(',', $aSearchId).') )';
		}
	}

	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_DISCUSS.'
			'.$sCondition.'
			AND 	nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = 'SELECT 	nId,
				nUid,
				sContent0,
				sCreateTime
		FROM 	'.CLIENT_DISCUSS.'
		'.$sCondition.'
		AND 	nOnline = 1
		ORDER BY nId DESC
		'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sContent0'] = convertContent($aRows['sContent0']);
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['aImgUrl'] = array();
		$aData[$aRows['nId']]['sUptUrl'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			'nId'		=> $aRows['nId'],
			'sBackParam'=> $sBackParam,
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sDelUrl'] = $aUrl['sDel'].'&sJWT='. $sJWT.'&nId='. $aRows['nId'];

		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
	}

	if (!empty($aData))
	{
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN ( '.implode(',', array_keys($aData)).' )
				AND 	sTable LIKE :sTable ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_DISCUSS, PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nKid']]['aImgUrl'][$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	nId IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
		}
	}
	foreach ($aDay as $LPsText => $LPaDate)
	{
		$aDay[$LPsText]['sSelect'] = '';
		if ($sSelDay == $LPsText)
		{
			$aDay[$LPsText]['sSelect'] = 'active';
		}
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_service.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/js_date/laydate.js',
		1	=> 'plugins/js/client_service/client_service.js',
	);
	#js結束

	#參數接收區
	$sAccount 	 = filter_input_str('sAccount',	INPUT_REQUEST, '');
	$sStartTime	 = filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime	 = filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$nKind 	 = filter_input_int('nKind',		INPUT_REQUEST, -1);
	$nStatus 	 = filter_input_int('nStatus',	INPUT_REQUEST, -1);
	$sSelDay	= filter_input_str('sSelDay',		INPUT_REQUEST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_service/php/_client_service_0_upt0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_service/php/_client_service_0.php']),
		'sHtml'	=> 'pages/client_service/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_service_0.php',

	);
	#url結束

	#參數宣告區
	$aData = array();
	$aStatus = array(
		'-1' => array(
			'sTitle' => aSERVICE['SELSTATUS'],
			'sSelect'=> '',
			'sClass'=> '',
		),
		'0' => array(
			'sTitle' => aSERVICE['STATUS0'],
			'sSelect'=> '',
			'sClass'=> 'FontBlue',
		),
		'10' => array(
			'sTitle' => aSERVICE['STATUS10'],
			'sSelect'=> '',
			'sClass'=> 'FontGreen',
		),
	);

	$aKind = array(
		'-1' => array(
			'sTitle' => aSERVICE['SELKIND'],
			'sSelect'=> '',
		),
	);
	$aDay = aDAY;
	$aPage['aVar'] = array(
		'sAccount'		=> $sAccount,
		'sStartTime'	=> $sStartTime,
		'sEndTime'		=> $sEndTime,
		'nKind'		=> $nKind,
		'nStatus'		=> $nStatus,
		'sSelDay'		=> $sSelDay,
	);
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	$sCondition = 'WHERE Service_.nCreateTime >= :nStartTime AND Service_.nCreateTime <= :nEndTime ';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);
	if($sAccount != '')
	{
		$sCondition .= ' AND User_.sAccount LIKE :sAccount';
		$aBindArray['sAccount'] = '%'.$sAccount.'%';
	}
	if ($nStatus != -1)
	{
		$aStatus[$nStatus]['sSelect'] = 'selected';
		$sCondition .= ' AND Service_.nStatus = :nStatus';
		$aBindArray['nStatus'] = $nStatus;
	}

	if ($nKind != -1)
	{
		$sCondition .= ' AND Service_.nKid = :nKid';
		$aBindArray['nKid'] = $nKind;
	}

	$sSQL = '	SELECT	nLid,
					sName0
			FROM	'.	CLIENT_SERVICE_KIND .'
			WHERE		sLang = :sLang
			AND		nOnline != 99
			ORDER	BY	nId DESC';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKind[$aRows['nLid']]['sTitle'] = $aRows['sName0'];
		$aKind[$aRows['nLid']]['sSelect'] = '';
	}
	$aKind[$nKind]['sSelect'] = 'selected';

	$sSQL = '	SELECT	1
			FROM	'.	CLIENT_SERVICE .' Service_
			JOIN	'.	CLIENT_USER_DATA.' User_
			ON		Service_.nUid = User_.nId
			'.$sCondition.'
			AND		User_.nStatus != 99
			ORDER	BY	Service_.nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT	User_.sAccount,
					Service_.nId,
					Service_.nUid,
					Service_.nKid,
					Service_.nStatus,
					Service_.sQuestion,
					Service_.sResponse,
					Service_.sCreateTime,
					Service_.sUpdateTime
			FROM	'.	CLIENT_SERVICE .' Service_
			JOIN	'.	CLIENT_USER_DATA.' User_
			ON		Service_.nUid = User_.nId
					'.$sCondition.'
			AND		User_.nStatus != 99
			ORDER	BY	Service_.nId DESC '.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sKind'] = $aKind[$aRows['nKid']]['sTitle'];
		$aData[$aRows['nId']]['sStatus'] = $aStatus[$aRows['nStatus']]['sTitle'];
		$aData[$aRows['nId']]['sImage'] = '';
		$aData[$aRows['nId']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
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
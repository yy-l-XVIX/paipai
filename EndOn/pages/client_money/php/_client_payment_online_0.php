<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_payment_online.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/js_date/laydate.js',
		1	=> 'plugins/js/client_money/client_payment_online.js'
	);
	#js結束

	#參數接收區
	$sStartTime		= filter_input_str('sStartTime', 	INPUT_REQUEST,date('Y-m-d 00:00:00'));
	$sEndTime		= filter_input_str('sEndTime', 	INPUT_REQUEST,date('Y-m-d 23:59:59'));
	$sAdmin		= filter_input_str('sAdmin', 		INPUT_REQUEST,'');
	$sMemberAccount	= filter_input_str('sMemberAccount',INPUT_REQUEST,'');
	$sOrder		= filter_input_str('sOrder',		INPUT_REQUEST,'');
	$nKid			= filter_input_int('nKid', 		INPUT_REQUEST,0);
	$nStatus		= filter_input_int('nStatus', 	INPUT_REQUEST,-1);
	$sSelDay 		= filter_input_str('sSelDay',		INPUT_REQUEST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_0.php']),
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_payment_online_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aPayment = array();
	$aTunnel = array();
	$aAdminData = array();
	$aMemberData = array();
	$aSearchId = array();
	$aBindArray = array();
	$aDay = aDAY;
	$aStatus = aPAYMENTONLINE['STATUS'];
	unset($aStatus['sTitle']);
	$aCountData = array(
		'nPageCount' => 0,
		'nTotalCount'=> 0,
		'nPageMoney' => 0,
		'nTotalMoney'=> 0,
	);

	$sCondition = '';
	$sExcelVar = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];

	$aPage['aVar'] = array(
		'sStartTime'	=> $sStartTime,
		'sEndTime'		=> $sEndTime,
		'sAdmin'		=> $sAdmin,
		'sMemberAccount'	=> $sMemberAccount,
		'sOrder'		=> $sOrder,
		'nKid'		=> $nKid,
		'nStatus'		=> $nStatus,
		'sSelDay'		=> $sSelDay,
	);


	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

	#宣告結束

	#程式邏輯區
	// xls params
	foreach ($aPage['aVar'] as $LPsKey => $LPsValue)
	{
		$sExcelVar .= '&'.$LPsKey.'='.$LPsValue;
		$sBackParam .= '&'.$LPsKey.'='.$LPsValue;
	}
	$aValue = array(
		'a'		=> 'EXCEL',
		'sBackParam'=> $sBackParam,
	);
	$aUrl['sExcel'] = $aUrl['sAct'] .$sExcelVar. '&sJWT='.sys_jwt_encode($aValue);

	$sCondition .= ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);

	if ($sOrder != '')
	{
		$sCondition .= ' AND sOrder LIKE :sOrder';
		$aBindArray['sOrder'] = '%'.$sOrder.'%';
	}
	if ($nKid > 0)
	{
		$sCondition .= ' AND nKid = :nKid';
		$aBindArray['nKid'] = $nKid;
	}
	if ($nStatus > -1)
	{
		$sCondition .= ' AND nStatus = :nStatus';
		$aBindArray['nStatus'] = $nStatus;
		$aStatus[$nStatus]['sSelect'] = 'selected';
	}
	if($sAdmin != '')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.END_MANAGER_DATA.'
				WHERE nOnline = 1
				AND 	sAccount LIKE :sAccount';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sAdmin.'%', PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}
		if (!empty($aSearchId))
		{
			$sCondition .= ' AND nAdmin0 IN ( '.implode(',', $aSearchId).' ) ';
			$aSearchId = array();
		}
	}
	if ($sMemberAccount != '')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	sAccount LIKE :sAccount';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sMemberAccount.'%', PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}
		if (!empty($aSearchId))
		{
			$sCondition .= ' AND nUid IN ( '.implode(',', $aSearchId).' ) ';
			$aSearchId = array();
		}
	}

	# 取金流 #
	$sSQL = '	SELECT	nId,
					sName0
			FROM		'. CLIENT_PAYMENT .'
			WHERE	nType0 = 2
			ORDER	BY nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aPayment[$aRows['nId']] = $aRows;
		$aPayment[$aRows['nId']]['sSelect'] = '';
		if($nKid == $aRows['nId'])
		{
			$aPayment[$aRows['nId']]['sSelect'] = 'selected';
		}
	}

	# 取通道 #
	$sSQL = '	SELECT	nId,
					nPid,
					sKey,
					sValue
			FROM		'. CLIENT_PAYMENT_TUNNEL .'
			WHERE		1
			ORDER	BY	nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aTunnel[$aRows['nPid']][$aRows['sKey']] = $aRows;
	}

	# 取單
	$sSQL = '	SELECT 	nMoney
			FROM 	'.CLIENT_MONEY.'
			WHERE nType0 = 2
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCountData['nTotalCount'] ++;
		$aCountData['nTotalMoney'] += $aRows['nMoney'];
	}
	$aPage['nDataAmount'] = $aCountData['nTotalCount'];

	$sSQL = '	SELECT 	nId,
					nUid,
					nMoney,
					nStatus,
					nKid,
					sOrder,
					sPayType,
					nFee,
					nAdmin0,
					sCreateTime,
					sUpdateTime
			FROM 	'.CLIENT_MONEY.'
			WHERE nType0 = 2
			'.$sCondition.'
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCountData['nPageCount'] ++;
		$aCountData['nPageMoney'] += $aRows['nMoney'];

		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sPayment'] 	= $aPayment[$aRows['nKid']]['sName0'];
		$aData[$aRows['nId']]['sTunnel'] 	= '';
		$aData[$aRows['nId']]['aStatus'] 	= $aStatus[$aRows['nStatus']];

		if (isset($aTunnel[$aRows['nKid']][$aRows['sPayType']]))
		{
			$aData[$aRows['nId']]['sTunnel'] = $aTunnel[$aRows['nKid']][$aRows['sPayType']]['sValue'];
		}
		if($aRows['nStatus'] == 1 && $aRows['nAdmin0'] > 0)
		{
			$aData[$aRows['nId']]['aStatus']['sText'] = aPAYMENTONLINE['HANDCONFIRM'];
		}

		$aValue = array(
			'a'		=> 'PASS'.$aRows['nId'],
			't'		=> NOWTIME,
			'sBackParam'=> $sBackParam,
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sPass'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.$sJWT;

		$aValue = array(
			'a'		=> 'CANCEL'.$aRows['nId'],
			't'		=> NOWTIME,
			'sBackParam'=> $sBackParam,
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sCancel'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.$sJWT;

		$aSearchId['aUser'][$aRows['nUid']] = $aRows['nUid'];
		$aSearchId['aAdmin'][$aRows['nAdmin0']] = $aRows['nAdmin0'];
	}

	if (!empty($aSearchId['aAdmin']))
	{
		$aAdminData['-1']['sAccount'] = '';
		$aAdminData['0']['sAccount'] = '';
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.END_MANAGER_DATA.'
				WHERE nId IN ('.implode(',', $aSearchId['aAdmin']).')';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aAdminData[$aRows['nId']] = $aRows;
		}
	}
	if (!empty($aSearchId['aUser']))
	{
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ('.implode(',', $aSearchId['aUser']).')';
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
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_manual.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/js_date/laydate.js',
		1	=> 'plugins/js/client_money/client_manual.js'
	);
	#js結束

	#參數接收區
	$sStartTime		= filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime		= filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$nKid			= filter_input_int('nKid',		INPUT_REQUEST, 0);
	$nType1		= filter_input_int('nType1',		INPUT_REQUEST, -1);
	$nType3		= filter_input_int('nType3',		INPUT_REQUEST, -1);
	$nStatus		= filter_input_int('nStatus',		INPUT_REQUEST, -1);
	$sAdmin		= filter_input_str('sAdmin',		INPUT_REQUEST, '');
	$sMemberAccount	= filter_input_str('sMemberAccount',INPUT_REQUEST, '');
	$sMemo		= filter_input_str('sMemo',		INPUT_REQUEST, '');
	$sSelDay 		= filter_input_str('sSelDay',		INPUT_REQUEST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_manual_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_manual_0.php']),
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_manual_0_upt0.php']),
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_manual_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aMemberData = array();
	$aAdminData = array();
	$aSearchId = array();
	$aBindArray = array();
	$aStatus = aMANUAL['STATUS'];
	$aType1 = aMANUAL['TYPE1'];
	$aType3 = aMANUAL['TYPE3'];
	$aDay = aDAY;
	$aCountData = array(
		'nPageInCount'	=> 0,
		'nPageOutCount'	=> 0,
		'nPageInMoney'	=> 0,
		'nPageOutMoney'	=> 0,
		'nTotalInCount'	=> 0,
		'nTotalOutCount'	=> 0,
		'nTotalInMoney'	=> 0,
		'nTotalOutMoney'	=> 0,
	);
	$aPage['aVar'] = array(
		'sStartTime'	=> $sStartTime,
		'sEndTime'		=> $sEndTime,
		'nKid'		=> $nKid,
		'nType1'		=> $nType1,
		'nType3'		=> $nType3,
		'nStatus'		=> $nStatus,
		'sAdmin'		=> $sAdmin,
		'sMemberAccount'	=> $sMemberAccount,
		'sMemo'		=> $sMemo,
		'sSelDay'		=> $sSelDay,
	);

	$aValue = array(
		'a'		=> 'EXCEL',
	);
	$sExcelJWT = sys_jwt_encode($aValue);
	$sExcelVar = '&sJWT='.$sExcelJWT;

	$sBackParam = '&nPageNo='.$aPage['nNowNo'];
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);


	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	unset($aType1['sTitle']);
	unset($aType3['sTitle']);
	unset($aStatus['sTitle']);

	// params
	foreach ($aPage['aVar'] as $LPsKey => $LPsValue)
	{
		$sExcelVar .= '&'.$LPsKey.'='.$LPsValue;
		$sBackParam .= '&'.$LPsKey.'='.$LPsValue;
	}
	$aUrl['sExcel'] = $aUrl['sAct'].$sExcelVar;
	$aValue = array(
		'sBackParam' => $sBackParam,
	);
	$aUrl['sIns'] .= '&sJWT='.sys_jwt_encode($aValue);

	if ($nKid > 0)
	{
		$sCondition .= ' AND nKid = :nKid ';
		$aBindArray['nKid'] = $nKid;
	}
	if ($nType1 > -1)
	{
		$sCondition .= ' AND nType1 = :nType1 ';
		$aBindArray['nType1'] = $nType1;
		$aType1[$nType1]['sSelect'] = 'selected';
	}
	if ($nType3 > -1)
	{
		$sCondition .= ' AND nType3 = :nType3 ';
		$aBindArray['nType3'] = $nType3;
		$aType3[$nType3]['sSelect'] = 'selected';
	}
	if ($nStatus > -1)
	{
		$sCondition .= ' AND nStatus = :nStatus ';
		$aBindArray['nStatus'] = $nStatus;
		$aStatus[$nStatus]['sSelect'] = 'selected';
	}
	if ($sAdmin != '')
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
	if($sMemo != '')
	{
		$sCondition .= ' AND sMemo LIKE :sMemo';
		$aBindArray['sMemo'] = '%'.$sMemo.'%';
	}

	$sSQL = '	SELECT	nId,
					nMoney,
					nType3
			FROM	'.CLIENT_MONEY .'
			WHERE	nType0 = 4
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		// 1 => 入款 2 => 出款
		if($aRows['nType3'] == 1)
		{
			$aCountData['nTotalInCount'] ++;
			$aCountData['nTotalInMoney'] += $aRows['nMoney'];
		}
		else
		{
			$aCountData['nTotalOutCount'] ++;
			$aCountData['nTotalOutMoney'] += $aRows['nMoney'];
		}
		$aPage['nDataAmount']++;
	}

	$sSQL = '	SELECT	nId,
					nUid,
					nMoney,
					nStatus,
					nType1,
					nType3,
					nAdmin0,
					sMemo,
					sCreateTime,
					sUpdateTime
			FROM	'.CLIENT_MONEY .'
			WHERE	nType0 = 4
			'.$sCondition .'
			ORDER	BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if($aRows['nType3'] == 1)
		{
			$aCountData['nPageInCount'] ++;
			$aCountData['nPageInMoney'] += $aRows['nMoney'];
		}
		else
		{
			$aCountData['nPageOutCount'] ++;
			$aCountData['nPageOutMoney'] += $aRows['nMoney'];
		}
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sAdmin0'] = '';

		$LPaValue = array(
			'a'		=> 'PASS'.$aRows['nId'],
			'nUid'	=> $aRows['nUid'],
			'sBackParam'=> $sBackParam,
		);
		$LPsJWT = sys_jwt_encode($LPaValue);
		$aData[$aRows['nId']]['sPass'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.$LPsJWT;

		$LPaValue = array(
			'a'		=> 'DENY'.$aRows['nId'],
			'nUid'	=> $aRows['nUid'],
			'sBackParam'=> $sBackParam,
		);
		$LPsJWT = sys_jwt_encode($LPaValue);
		$aData[$aRows['nId']]['sDeny'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.$LPsJWT;

		$aSearchId['aUser'][$aRows['nUid']] = $aRows['nUid'];
		$aSearchId['aAdmin'][$aRows['nAdmin0']] = $aRows['nAdmin0'];
	}
	if (!empty($aSearchId['aAdmin']))
	{
		$aAdminData['-1']['sAccount'] = '';
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
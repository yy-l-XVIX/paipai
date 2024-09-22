<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_log_account.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/end_report/end_log_account.js',
	);

	#參數接收區
	$sSelDay	= filter_input_str('sSelDay',		INPUT_REQUEST, 'TODAY');
	$sStartTime = filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$sAccount	= filter_input_str('sAccount',	INPUT_REQUEST, '');
	$nType0	= filter_input_int('nType0',		INPUT_REQUEST, -1);
	$nType2	= filter_input_int('nType2',		INPUT_REQUEST, 0); # 細項
	$nType3	= filter_input_int('nType3',		INPUT_REQUEST, -1);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_report/php/_end_log_account_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_report/php/_end_log_account_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_report/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_log_account_0.php',
	);
	#url結束
	$aValue = array(
		'a'		=> 'EXCEL',
		't'		=> NOWTIME,
	);
	$sExcelJWT = sys_jwt_encode($aValue);
	$sExcelVar = '&sJWT='.$sExcelJWT;
	$sExcelVar .= '&sSelDay='.$sSelDay.'&sStartTime='.$sStartTime.'&sEndTime='.$sEndTime.'&sAccount='.$sAccount.'&nType0='.$nType0.'&nType3='.$nType3;
	$aUrl['sExcel'] = $aUrl['sAct'].$sExcelVar;
	$nTotalCount = 0;

	$nStartTime = strtotime($sStartTime);
	$nEndTime 	= strtotime($sEndTime);
	$aPage['aVar'] = array(
		'sSelDay'		=> $sSelDay,
		'sStartTime'	=> $sStartTime,
		'sEndTime'		=> $sEndTime,
		'sAccount'		=> $sAccount,
		'nType0'		=> $nType0,
		'nType2'		=> $nType2,
		'nType3'		=> $nType3,
	);
	$aSearchUid = array(0=>0);
	$aSearch = array();
	$aHideMember = array();
	$aDay = aDAY;
	$aData = array(
		'aData'	=> array(),
		'aSubTotal' => array(
			'nBefore' 	=> 0,
			'nDelta'	=> 0,
			'nAfter'	=> 0,
		),
		'aTotal' 	=> array(
			'nBefore' 	=> 0,
			'nDelta'	=> 0,
			'nAfter'	=> 0,
		),
	);
	$aType0 = array(
		'-1'		=> array(
			'sName' 	=> aLOG['ALL'],
			'sSelect' 	=> '',
		),
		'1'	=> array(
			'sName' 	=> aLOG['MISSION'],
			'sSelect' 	=> '',
		),
		'2'	=> array(
			'sName' 	=> aLOG['CASHFLOW'],
			'sSelect' 	=> '',
		),
	);

	$aType3 = array(
		'-1'		=> array(
			'sName' 	=> aLOG['ALL'],
			'sSelect' 	=> '',
		),
		'0'	=> array(
			'sName' 	=> aLOG['TYPEMONEY'],
			'sSelect' 	=> '',
		),

	);
	$aType2 = array();
	$aMember[0] = array(
		'sAccount' => '',
		'nPa' => '',
	);
	$sCondition = ' WHERE nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$aBindValue = array(
		'nStartTime'=> $nStartTime,
		'nEndTime' 	=> $nEndTime,
	);
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	#宣告結束

	#程式邏輯區
	foreach (aTYPE2 as $LPnType2 => $LPsName)
	{
		$aType2[$LPnType2] = array(
			'sName' => $LPsName,
			'sSelect' => '',
		);
		if ($LPnType2 == $nType2)
		{
			$aType2[$LPnType2]['sSelect'] = 'selected';
		}
	}

	$sSQL = '	SELECT 	nUid
			FROM 	'.CLIENT_USER_HIDE.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aHideMember[$aRows['nUid']] = $aRows['nUid'];
	}

	if ($sAccount != '')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE sAccount LIKE :sAccount';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sAccount.'%', PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchUid[$aRows['nId']] = $aRows['nId'];
		}
		$sCondition .= ' AND nUid IN ( '.implode(',', $aSearchUid).' ) ';
	}
	if ($nType0 != -1)
	{
		$sCondition .= ' AND nType0 = :nType0';
		$aBindValue['nType0'] = $nType0;
	}
	if ($nType2 != 0)
	{
		$sCondition .= ' AND nType2 = :nType2';
		$aBindValue['nType2'] = $nType2;
	}
	if ($nType3 != -1)
	{
		$sCondition .= ' AND nType3 = :nType3';
		$aBindValue['nType3'] = $nType3;
	}
	if ( !empty($aHideMember) && $aAdm['nAdmType'] != 1)
	{
		$sCondition .= ' AND  nUid NOT IN ( '.implode(',', $aHideMember).' ) ';
	}

	$sSQL = '	SELECT	nBefore,
					nDelta,
					nAfter
			FROM 	'.END_LOG_ACCOUNT.'
			'.$sCondition.'
			ORDER BY nCreateTime DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindValue);
	sql_query($Result);
	$aPage['nDataAmount'] = $nTotalCount = $Result->rowCount();
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aTotal']['nBefore'] += $aRows['nBefore'];
		$aData['aTotal']['nDelta'] += $aRows['nDelta'];
		$aData['aTotal']['nAfter'] += $aRows['nAfter'];
	}

	$sSQL = '	SELECT 	nId,
					nUid,
					nKid,
					nFromUid,
					nType0,
					nType1,
					nType2,
					nType3,
					nBefore,
					nDelta,
					nAfter,
					sParams,
					sCreateTime
			FROM 	'.END_LOG_ACCOUNT.'
			'.$sCondition.'
			AND 	nType3 != 1
			ORDER BY nCreateTime DESC, nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindValue);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aData'][$aRows['nId']] = $aRows;
		$aData['aData'][$aRows['nId']]['sMemo'] = '';
		if ($aRows['nType0'] == 2)
		{
			$aSearch['aPayment'][$aRows['nType1']] = $aRows['nType1'];
		}
		$aSearch['aUid'][$aRows['nUid']] = $aRows['nUid'];
		$aSearch['aUid'][$aRows['nFromUid']] = $aRows['nFromUid'];
	}
	if (!empty($aSearch['aUid']))
	{
		$sSQL = '	SELECT	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE	nId IN ('.implode(',', $aSearch['aUid']).')';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aUserData[$aRows['nId']] = $aRows['sAccount'];
		}
	}

	foreach ($aData['aData'] as $LPnId => $LPaData)
	{
		$LPaParam = array();
		$aData['aData'][$LPnId]['sAccount'] = $aUserData[$LPaData['nUid']];
		$aData['aData'][$LPnId]['sType0'] =	$aType0[$LPaData['nType0']]['sName'];
		$aData['aData'][$LPnId]['sType1'] = '';
		$aData['aData'][$LPnId]['sType2'] = aTYPE2[$LPaData['nType2']];
		$aData['aData'][$LPnId]['sType3'] =	$aType3[$LPaData['nType3']]['sName'];
		$aData['aData'][$LPnId]['sFromAccount'] = '';
		if ($LPaData['sParams'] != '')
		{
			$LPaParam = json_decode($LPaData['sParams'],true);
		}
		// if ($LPaData['nType2'] == '201') // 推廣獎勵 呈現扣除稅金
		// {
		// 	$aData['aData'][$LPnId]['sMemo'] = aLOG['PROMOTAX'].' : '.$LPaParam['nPromoteBonusTax'];
		// }
		// if ($LPaData['nType0'] == 1 && isset($aMissionData[$LPaData['nType1']]))
		// {
		// 	$aData['aData'][$LPnId]['sType1'] = $aMissionData[$LPaData['nType1']];
		// }
		if($LPaData['nFromUid'] != 0 && isset($aUserData[$LPaData['nFromUid']]))
		{
			$aData['aData'][$LPnId]['sFromAccount'] = $aUserData[$LPaData['nFromUid']];
		}
		$aData['aSubTotal']['nBefore'] += $LPaData['nBefore'];
		$aData['aSubTotal']['nDelta'] += $LPaData['nDelta'];
		$aData['aSubTotal']['nAfter'] += $LPaData['nAfter'];
	}
	$aType0[$nType0]['sSelect'] = 'selected';
	$aType3[$nType3]['sSelect'] = 'selected';
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
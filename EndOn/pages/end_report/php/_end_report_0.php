<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_report.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/end_report/end_report.js',
	);

	#參數接收區
	$sStartTime = filter_input_str('sStartTime',	INPUT_POST, date('Y-m-d 00:00:00'));
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_POST, date('Y-m-d 23:59:59'));
	$sSelDay 	= filter_input_str('sSelDay',		INPUT_POST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sWithdrawal'=>sys_web_encode($aMenuToNo['pages/client_money/php/_client_withdrawal_0.php']).'&sStartTime='.$sStartTime.'&sEndTime='.$sEndTime,
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_report/php/_end_report_0.php']),
		'sHtml'	=> 'pages/end_report/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_report_0.php',
	);
	#url結束

	$nStartTime = strtotime($sStartTime);
	$nEndTime 	= strtotime($sEndTime);
	$aHideMember = array();
	$aDay = aDAY;
	$aIDtoUKid = array();
	$aData = array(
		'aWithdrawal' => array(
			0 => array(
				'sUrl' => $aUrl['sWithdrawal'].'&nStatus=0',
				'sName0' => aSTATUS[0],
				'nCount' => 0,
				'nMoney' => 0,
				'nFee' => 0,
			),
			1 => array(
				'sUrl' => $aUrl['sWithdrawal'].'&nStatus=1',
				'sName0' => aSTATUS[1],
				'nCount' => 0,
				'nMoney' => 0,
				'nFee' => 0,
			),
			99 => array(
				'sUrl' => $aUrl['sWithdrawal'].'&nStatus=99',
				'sName0' => aSTATUS[99],
				'nCount' => 0,
				'nMoney' => 0,
				'nFee' => 0,
			),
		),
		'aUserKind' => array(),
	);
	$aTotal = array(
		'aWithdrawal' => array(
			'nCount' => 0,
			'nMoney' => 0,
			'nFee' => 0,
		),
		'aUserKind' => array(
			'nPromoMoney'	=> 0,
			'nPromoTax'		=> 0,
			'nTotalCount'	=> 0,
			'nTotalMoney'	=> 0,
			'nCompanyCount'	=> 0,
			'nCompanyMoney'	=> 0,
			'nCompanyFee'	=> 0,
			'nOnlineCount'	=> 0,
			'nOnlineMoney'	=> 0,
			'nOnlineFee'	=> 0,
			'nPointCount'	=> 0,
			'nPointMoney'	=> 0,
			'nPointFee'		=> 0,
		),
	);
	$sCondition = ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$aBindValue = array(
		'nStartTime'=> $nStartTime,
		'nEndTime' 	=> $nEndTime,
	);
	#宣告結束

	#程式邏輯區
	# 篩選隱藏會員
	$sSQL = '	SELECT 	nUid
			FROM 	'.CLIENT_USER_HIDE.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aHideMember[$aRows['nUid']] = $aRows['nUid'];
	}
	if ( !empty($aHideMember) && $aAdm['nAdmType'] != 1)
	{
		$sCondition .= ' AND  nUid NOT IN ( '.implode(',', $aHideMember).' ) ';
	}

	// user_kind 會員方案
	$sSQL = '	SELECT 	nId,
					nLid,
					sName0,
					nPrice
			FROM 	'.CLIENT_USER_KIND.'
			WHERE nOnline != 99
			AND 	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aUserKind'][$aRows['nLid']] = $aRows;
		$aData['aUserKind'][$aRows['nLid']]['nPromoMoney'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nPromoTax'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nTotalCount'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nTotalMoney'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nCompanyCount'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nCompanyMoney'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nCompanyFee'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nOnlineCount'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nOnlineMoney'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nOnlineFee'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nPointCount'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nPointMoney'] = 0;
		$aData['aUserKind'][$aRows['nLid']]['nPointFee'] = 0;
	}

	$sSQL = '	SELECT 	nId,
					nUKid,
					nMoney,
					nStatus,
					nType0,
					nFee
			FROM 	'.CLIENT_MONEY.'
			WHERE nType2 = 1
			AND 	nType0 != 4
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindValue);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if ($aRows['nType0'] == 3)
		{
			$aData['aWithdrawal'][$aRows['nStatus']]['nCount'] ++;
			$aData['aWithdrawal'][$aRows['nStatus']]['nMoney'] += $aRows['nMoney'];
			$aData['aWithdrawal'][$aRows['nStatus']]['nFee'] += $aRows['nFee'];

			$aTotal['aWithdrawal']['nCount'] ++;
			$aTotal['aWithdrawal']['nMoney'] += $aRows['nMoney'];
			$aTotal['aWithdrawal']['nFee'] += $aRows['nFee'];
		}
		else if ($aRows['nStatus'] == 1)
		{
			switch ($aRows['nType0'])
			{
				case '1':
					$aData['aUserKind'][$aRows['nUKid']]['nCompanyCount'] ++ ;
					$aData['aUserKind'][$aRows['nUKid']]['nCompanyMoney'] += $aRows['nMoney'];
					$aData['aUserKind'][$aRows['nUKid']]['nCompanyFee'] += $aRows['nFee'];
					$aTotal['aUserKind']['nCompanyCount'] ++ ;
					$aTotal['aUserKind']['nCompanyMoney'] += $aRows['nMoney'];
					$aTotal['aUserKind']['nCompanyFee'] += $aRows['nFee'];
					break;
				case '2':
					$aData['aUserKind'][$aRows['nUKid']]['nOnlineCount'] ++ ;
					$aData['aUserKind'][$aRows['nUKid']]['nOnlineMoney'] += $aRows['nMoney'];
					$aData['aUserKind'][$aRows['nUKid']]['nOnlineFee'] += $aRows['nFee'];
					$aTotal['aUserKind']['nOnlineCount'] ++ ;
					$aTotal['aUserKind']['nOnlineMoney'] += $aRows['nMoney'];
					$aTotal['aUserKind']['nOnlineFee'] += $aRows['nFee'];
					break;
				case '5':
					$aData['aUserKind'][$aRows['nUKid']]['nPointCount'] ++ ;
					$aData['aUserKind'][$aRows['nUKid']]['nPointMoney'] += $aRows['nMoney'];
					$aData['aUserKind'][$aRows['nUKid']]['nPointFee'] += $aRows['nFee'];
					$aTotal['aUserKind']['nPointCount'] ++ ;
					$aTotal['aUserKind']['nPointMoney'] += $aRows['nMoney'];
					$aTotal['aUserKind']['nPointFee'] += $aRows['nFee'];
					break;
			}

			$aIDtoUKid[$aRows['nId']] = $aRows['nUKid'];

			$aData['aUserKind'][$aRows['nUKid']]['nTotalCount'] ++;
			$aData['aUserKind'][$aRows['nUKid']]['nTotalMoney'] += $aRows['nMoney'];
			$aTotal['aUserKind']['nTotalCount'] ++;
			$aTotal['aUserKind']['nTotalMoney'] += $aRows['nMoney'];
		}
	}

	$sSQL = '	SELECT 	nId,
					nKid,
					sParams,
					nDelta
			FROM 	'.END_LOG_ACCOUNT.'
			WHERE nType2 = 201
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindValue);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aParams = json_decode($aRows['sParams'],true);

		$aData['aUserKind'][$aParams['nUkid']]['nPromoMoney'] += $aRows['nDelta'];
		$aTotal['aUserKind']['nPromoMoney'] += $aRows['nDelta'];
		if (isset($aParams['nPromoteBonusTax']))
		{
			$aData['aUserKind'][$aParams['nUkid']]['nPromoTax'] += $aParams['nPromoteBonusTax'];
			$aTotal['aUserKind']['nPromoTax'] += $aParams['nPromoteBonusTax'];
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
	// print_r($aData['aUserKind']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/transaction_record.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0' => 'plugins/js/js_date/laydate.js',
		'1' => 'plugins/js/center/transaction_record.js',
	);

	#參數接收區
	$nType2 	= filter_input_int('nType2',		INPUT_REQUEST, '202'); # 202 會員提領 210 轉帳入款 211 轉帳出款
	$sAccount 	= filter_input_str('sAccount',	INPUT_REQUEST, '');
	$sStartTime = filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d'));
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d'));
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_transaction_record_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/transaction_record_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
	$aTotalData = array(
		'nSubTotal' => 0,
		'nTotal'	=> 0,
	);
	$aPage['aVar'] = array(
		'nType2'	=> $nType2,
		'sAccount'	=> $sAccount,
		'sStartTime'=> $sStartTime,
		'sEndTime'	=> $sEndTime,
	);
	$aType2 = aRECORD['aTYPE2'];
	$aBindArray = array(
		'nStartTime'	=> strtotime($sStartTime.' 00:00:00'),
		'nEndTime'		=> strtotime($sEndTime.' 23:59:59'),
	);
	$sCondition = ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	#宣告結束

	#程式邏輯區
	if ($aSystem['aParam']['nTransferSetting'] == 0) #關閉會員轉帳
	{
		unset($aType2[210]);
		unset($aType2[211]);
	}
	if ($sAccount != '' && ($nType2 == 211 || $nType2 == 210))
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE sAccount LIKE :sAccount
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sAccount.'%', PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}

		if (!empty($aSearchId))
		{
			$sCondition .= ' AND nFromUid IN ('.implode(',',$aSearchId).')';
		}
	}
	if (!isset($aType2[$nType2]))
	{
		$nType2 = 202;
	}
	$aType2[$nType2]['sSelect'] = 'active';
	if (true)
	{
		$sType2 = $nType2;
		if ($nType2 == 202)
		{
			$sType2 = '202,203'; // 提領扣款 提領審核失敗還款
		}

		$sSQL = '	SELECT 	nDelta
				FROM 	'.END_LOG_ACCOUNT.'
				WHERE nUid = :nUid
				AND 	nType0 = 2
				AND 	nType2 IN ('.$sType2.')
				AND 	nType3 = 0
				'.$sCondition;
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_build_value($Result,$aBindArray);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aTotalData['nTotal'] += $aRows['nDelta'];
		}

		$sSQL = '	SELECT 	nId,
						nUid,
						nType2,
						nDelta as nMoney,
						nAfter,
						sParams,
						nCreateTime
				FROM 	'.END_LOG_ACCOUNT.'
				WHERE nUid = :nUid
				AND 	nType0 = 2
				AND 	nType2 IN ('.$sType2.')
				AND 	nType3 = 0
				'.$sCondition.'
				ORDER BY nId DESC
				'.sql_limit($nPageStart, $aPage['nPageSize']);
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_build_value($Result,$aBindArray);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aTotalData['nSubTotal'] += $aRows['nMoney'];
			$aData[$aRows['nId']] = $aRows;
			$aData[$aRows['nId']]['sType2'] = aTYPE2[$aRows['nType2']];
			$aData[$aRows['nId']]['sMemo'] = '';
			if ($aRows['sParams'] != '')
			{
				$aData[$aRows['nId']]['aParam'] = json_decode($aRows['sParams'],true);
				$aData[$aRows['nId']]['sMemo'] = $aData[$aRows['nId']]['aParam']['sMemo'];
			}
		}
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
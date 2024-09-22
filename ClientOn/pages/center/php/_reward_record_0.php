<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/reward_record.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();

	#參數接收區
	$sAccount 	= filter_input_str('sAccount',	INPUT_REQUEST, '');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_reward_record_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/reward_record_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
	$aMemberData = array();
	$aTotalData = array(
		'nSubTotal' => 0,
		'nTotal'	=> 0,
	);
	$aPage['aVar'] = array(
		'sAccount'	=> $sAccount,
	);
	$aBindArray = array();
	$sCondition = '';
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];

	#宣告結束

	#程式邏輯區
	if ($sAccount != '')
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

	$sSQL = '	SELECT 	nDelta
			FROM 	'.END_LOG_ACCOUNT.'
			WHERE nUid = :nUid
			AND 	nType0 = 2
			AND 	nType2 = 201
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
					nFromUid,
					nDelta,
					nAfter,
					sParams,
					nCreateTime
			FROM 	'.END_LOG_ACCOUNT.'
			WHERE nUid = :nUid
			AND 	nType0 = 2
			AND 	nType2 = 201
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
		$aTotalData['nSubTotal'] += $aRows['nDelta'];
		$aData[$aRows['nId']] = $aRows;
		$aSearchId[$aRows['nFromUid']] = $aRows['nFromUid'];
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
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
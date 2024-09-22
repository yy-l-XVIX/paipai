<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/pointRecord.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/pointRecord.js',
	);

	#參數接收區
	$nType0 = filter_input_int('nStatus',	INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_point_record_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/point_record_0.php',
	);
	#url結束

	#參數宣告區
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aData = array();
	$aBind = array();
	$aType0 = aRECORD['aTYPE0'];

	$sCondition = '';
	$nCount = 0;
	$nTotal = 0;
	#宣告結束

	#程式邏輯區
	if ($nType0 != 0)
	{
		$sCondition .= ' AND nType0 = :nType0';
		$aBind['nType0'] = $nType0;
		$aType0[$nType0]['sSelect'] = 'selected';
	}

	$sSQL = '	SELECT	nId
			FROM		'.END_LOG_ACCOUNT.'
			WHERE		nUid = :nUid
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result,$aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$nCount++;
	}
	$aPage['nDataAmount'] = $nCount;

	$sSQL = '	SELECT	nId,
					nType0,
					nType2,
					nDelta,
					sCreateTime
			FROM		'.END_LOG_ACCOUNT.'
			WHERE		nUid = :nUid
			'.$sCondition.'
			ORDER	BY	nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result,$aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sType2'] = aTYPE2[$aRows['nType2']];
		$nTotal += $aRows['nDelta'];
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
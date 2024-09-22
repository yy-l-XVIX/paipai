<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_snooze_keywords.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sName	= filter_input_str('sName', INPUT_REQUEST,'');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_developer/php/_client_snooze_keywords_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_developer/php/_client_snooze_keywords_0.php']),
		'sHtml'	=> 'pages/end_developer/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_snooze_keywords_0.php'
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aPage['aVar']= array(
		'sName' => $sName,
	);
	$aValue = array(
			'a'	=> 'INS',
		);
	$sJWT = sys_jwt_encode($aValue);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$nCount = 0;

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	if($sName != '')
	{
		$sCondition .= ' AND sName0 LIKE :sName0 ';
		$aBindArray['sName0'] = '%'.$sName.'%';
	}

	$sSQL = '	SELECT	1
			FROM	'.CLIENT_SNOOZE_KEYWORDS .'
			WHERE	nOnline = 1
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$nCount++;
	}
	$aPage['nDataAmount'] = $nCount;

	$sSQL = '	SELECT	nId,
					sName0
			FROM	'.	CLIENT_SNOOZE_KEYWORDS .'
			WHERE		nOnline = 1
			'.$sCondition. '
			ORDER	BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;

		$LPaValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
		);
		$aData[$aRows['nId']]['sDel'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue);
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
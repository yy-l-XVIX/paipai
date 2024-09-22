<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_service/php/_client_service_kind_0_upt0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/client_service/php/_client_service_kind_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_service/php/_client_service_kind_0.php']),
		'sHtml'	=> 'pages/client_service/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_service_kind_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$nCount = 0;
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sSubject = '工單分類';
	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	$aOnline = aONLINE;
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	1
			FROM	'.	CLIENT_SERVICE_KIND .'
			WHERE		nOnline != 99
			AND		sLang = :sLang
			ORDER	BY	nId DESC';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT	nId,
					sName0,
					nLid,
					nOnline,
					sCreateTime,
					sUpdateTime
			FROM	'.	CLIENT_SERVICE_KIND .'
			WHERE		nOnline != 99
			AND		sLang = :sLang
			ORDER	BY	nId DESC '.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nLid']] = $aRows;

		$aData[$aRows['nLid']]['sIns'] = $aUrl['sIns'].'&nLid='.$aRows['nLid'];
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nLid'],
			't'		=> NOWTIME,
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nLid']]['sDel'] = $aUrl['sDel'].'&nLid='.$aRows['nLid'].'&sJWT='.$sJWT;
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
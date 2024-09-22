<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_payment_online_tunnel.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nPid		= filter_input_int('nPid',	INPUT_REQUEST, -1);
	$nOnline	= filter_input_int('nOnline',	INPUT_REQUEST, -1);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0_upt0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0.php']),
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_payment_online_tunnel_0.php',
	);
	#url結束

	#參數宣告區
	$aPayment = array();
	$aData = array();
	$aBind = array();
	$aOnline = aONLINE;
	$nCount = 0;
	$sCondition = '';
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aPage['aVar'] = array(
		'nPid'	=> $nPid,
		'nOnline'	=> $nOnline,
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
	if($nPid >= 0)
	{
		$sCondition .= ' AND nPid = :nPid';
		$aBind['nPid'] = $nPid;
	}

	if($nOnline >= 0)
	{
		$sCondition .= ' AND nOnline = :nOnline';
		$aBind['nOnline'] = $nOnline;
		$aOnline[$nOnline]['sSelect'] = 'selected';
	}

	$sSQL = '	SELECT	nId,
					sName0,
					nPid
			FROM	'. CLIENT_PAYMENT .'
			WHERE	nType0 = 2
			AND	nOnline != 99
			ORDER	BY nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aPayment[$aRows['nId']] = $aRows;
		$aPayment[$aRows['nId']]['sSelect'] = '';
		if($nPid == $aRows['nId'])
		{
			$aPayment[$aRows['nId']]['sSelect'] = 'selected';
		}
	}

	$sSQL = '	SELECT	nId,
					nPid,
					sKey,
					sValue,
					nMin,
					nMax,
					nOnline,
					sUpdateTime
			FROM	'. CLIENT_PAYMENT_TUNNEL .'
			WHERE	nOnline != 99
			' . $sCondition . '
			ORDER	BY nId DESC ';
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nPid']][$aRows['nId']] = $aRows;

		$aData[$aRows['nPid']][$aRows['nId']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			't'		=> NOWTIME,
		);
		$sLPJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nPid']][$aRows['nId']]['sDel'] = $aUrl['sDel'].'&nId='.$aRows['nId'].'&sJWT='.$sLPJWT;
	}

	// $aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
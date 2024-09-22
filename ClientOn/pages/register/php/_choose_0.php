<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/choose.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/register/choose.js',
	);
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct0'	=> sys_web_encode($aMenuToNo['pages/register/php/_choose_0_act0.php']).'&run_page=1',
		'sAct1'	=> sys_web_encode($aMenuToNo['pages/register/php/_choose_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/register/php/_terms_0.php']),
		'sHtml'	=> 'pages/register/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/choose_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$sExpired0 = '';
	$sExpired1 = '';
	$sPromoCode = '';
	if (isset($aJWT['aUser'])) # 已經登入
	{
		$sExpired0 = $aJWT['aUser']['sExpired0'];
		$sExpired1 = $aJWT['aUser']['sExpired1'];
		$aUrl['sPage'] = sys_web_encode($aMenuToNo['pages/recharge/php/_company_charge_0.php']);
	}

	if (isset($aJWT['sPromoCode'])) # 推薦代碼
	{
		$sPromoCode = $aJWT['sPromoCode'];
	}
	$aValue = array(
		'a'	=> 'FREEUSE',
	);
	$aUrl['sAct0'] .= '&sJWT='.sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'OPENKID',
	);
	$aUrl['sAct1'] .= '&sJWT='.sys_jwt_encode($aValue);

	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['nClicktoClose'] = 0;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sClass'] = '';

	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT 	nLid,
					sName1,
					nType0,
					nFreeDays,
					nFreeStartTime,
					nFreeEndTime,
					sContent0
			FROM 	'.CLIENT_USER_KIND.'
			WHERE nOnline = 1
			AND 	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nLid']] = $aRows;
		if ($aRows['nLid'] == 1) # boss
		{
			$aData[$aRows['nLid']]['sExpired'] = $sExpired1;
		}
		else
		{
			$aData[$aRows['nLid']]['sExpired'] = $sExpired0;
		}

		$aValue = array(
			'nKid'	=> $aRows['nLid'],
			'nExp'	=> NOWTIME + JWTWAIT,
			'sPromoCode'=> $sPromoCode,
		);
		$LPsJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nLid']]['sUrl'] = $aUrl['sPage'].'&sJWT='.$LPsJWT;
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
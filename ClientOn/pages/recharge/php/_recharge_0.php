<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/recharge.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/recharge/recharge.js',
	);
	#js結束

	#參數接收區
	$nPid			= filter_input_int('nPid',		INPUT_REQUEST, 0);
	$nKid			= filter_input_int('nKid',		INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'		=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		'sAct'		=> sys_web_encode($aMenuToNo['pages/recharge/php/_recharge_0_act0.php']).'&run_page=1',
		'sPage'		=> sys_web_encode($aMenuToNo['pages/recharge/php/_recharge_0.php']),
		'sHtml'		=> 'pages/recharge/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/recharge_0.php',
		'online'		=> sys_web_encode($aMenuToNo['pages/recharge/php/_recharge_0.php']),
		'company'		=> sys_web_encode($aMenuToNo['pages/recharge/php/_company_charge_0.php']),
		'money'		=> sys_web_encode($aMenuToNo['pages/recharge/php/_point_charge_0.php']),
	);
	#url結束

	#參數宣告區
	if (isset($aJWT['nKid']))
	{
		$nKid = $aJWT['nKid'];
	}
	$aData = array(
		'sName0'	=> '',
		'nMoney'	=> 0,
		'aPayment'	=> array(),
		'aTunnel'	=> array(),
	);
	$aValue = array(
		'a'		=> 'INS',

	);
	$sJWTAct = sys_jwt_encode($aValue);
	$aValue = array(
		'nKid'	=> $nKid,
	);
	$sJWTKid = sys_jwt_encode($aValue);

	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;

	#宣告結束

	#程式邏輯區
	$aRechargeTunnel = explode(',',$aSystem['aParam']['sRechargeTunnel']);
	if (!in_array('1', $aRechargeTunnel)) // 1:線上入款 2:公司入款 3:點數扣款
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	foreach ($aRechargeTunnel as $LPnType)
	{
		$aPayMethod[$LPnType] = aRECHARGE['aPAYMETHOD'][$LPnType];
		$aPayMethod[$LPnType]['sUrl'] = $aUrl[$aPayMethod[$LPnType]['sType']].'&sJWT='.$sJWTKid;
		$aPayMethod[$LPnType]['sSelect'] = '';
		if ($LPnType == 1)
		{
			$aPayMethod[$LPnType]['sSelect'] = 'selected';
		}
	}
	$sSQL = '	SELECT 	nLid,
					sName0,
					nPrice
			FROM 	'.CLIENT_USER_KIND.'
			WHERE nOnline = 1
			AND 	nLid = :nLid
			AND 	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $nKid, PDO::PARAM_INT);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows === false)
	{
		$nErr = 1;
		$sErrMsg = aERROR['KIND'];
	}
	$aData['sName0'] = $aRows['sName0'];
	$aData['nMoney'] = $aRows['nPrice'];


	$sSQL = '	SELECT	nId,
					sName0,
					sName1
			FROM		'.CLIENT_PAYMENT.'
			WHERE		nOnline = 1
			AND 		nType0 = 2';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nPid, PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aPayment'][$aRows['nId']] = $aRows;
		$aData['aPayment'][$aRows['nId']]['sSelect'] = '';
	}

	if (empty($aData['aPayment']))
	{
		$nErr = 1;
		$sErrMsg = aERROR['NOPAYMENT'];
	}

	if($nPid != 0)
	{
		$aData['aPayment'][$nPid]['sSelect'] = 'selected';
		$sSQL = '	SELECT	nId,
						sKey,
						sValue,
						nMax,
						nMin
				FROM		'.CLIENT_PAYMENT_TUNNEL.'
				WHERE		nOnline = 1
				AND 		nPid = :nPid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nPid', $nPid, PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aTunnel'][$aRows['nId']] = $aRows;
			$aData['aTunnel'][$aRows['nId']]['sSelect'] = '';
		}
		if (empty($aData['aTunnel']))
		{
			$nErr = 1;
			$sErrMsg = aERROR['NOTUNNEL'];
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sClass'] = '';
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
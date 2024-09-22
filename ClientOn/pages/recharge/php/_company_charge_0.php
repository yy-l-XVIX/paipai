<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/company_charge.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/recharge/company_charge.js',
	);
	#js結束

	#參數接收區
	$nKid			= filter_input_int('nKid',		INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'		=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		'sAct'		=> sys_web_encode($aMenuToNo['pages/recharge/php/_company_charge_0_act0.php']).'&run_page=1',
		'sPage'		=> sys_web_encode($aMenuToNo['pages/recharge/php/_point_charge_0.php']),
		'sHtml'		=> 'pages/recharge/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/company_charge_0.php',
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
	$aData = array();
	$aPayMethod = array();
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

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';

	#宣告結束

	#程式邏輯區
	$aRechargeTunnel = explode(',',$aSystem['aParam']['sRechargeTunnel']);
	if (!in_array('2', $aRechargeTunnel)) // 1:線上入款 2:公司入款 3:點數扣款
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	foreach ($aRechargeTunnel as $LPnType)
	{
		$aPayMethod[$LPnType] = aRECHARGE['aPAYMETHOD'][$LPnType];
		$aPayMethod[$LPnType]['sUrl'] = $aUrl[$aPayMethod[$LPnType]['sType']].'&sJWT='.$sJWTKid;
		$aPayMethod[$LPnType]['sSelect'] = '';
		if ($LPnType == 2)
		{
			$aPayMethod[$LPnType]['sSelect'] = 'selected';
		}
	}
	# 公司入款
	$sSQL = '	SELECT	Payment_.nId,
					Payment_.sName0 as sPaymentName0,
					Payment_.sAccount0,
					Payment_.nBid,
					Payment_.nDayLimitTimes,
					Payment_.nTotalLimitMoney,
					Payment_.nTotalLimitTimes,
					Payment_.nTotalMoney,
					Payment_.nTotalTimes,
					Payment_.nDayTimes,
					Bank_.sName0 as sBankName0
			FROM		'.CLIENT_PAYMENT.' Payment_,
					'.SYS_BANK.' Bank_
			WHERE		Payment_.nOnline = 1
			AND 		Payment_.nType0 = 1
			AND 		Payment_.nBid = Bank_.nId';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if (	($aRows['nTotalLimitMoney'] > 0 && $aRows['nTotalLimitMoney'] < ($aRows['nTotalMoney'] + $nFinalPrice) ) ||
			($aRows['nTotalLimitTimes'] > 0 && $aRows['nTotalLimitTimes'] < ($aRows['nTotalTimes'] + 1 ) ) ||
			($aRows['nDayLimitTimes'] > 0 && $aRows['nDayLimitTimes'] < ($aRows['nDayTimes'] + 1 ) ) )
		{
			# 超過 總提單金額上限 / 總提單次數上限 不顯示
			continue;
		}
		if (isset($aRecordData[$aRows['nId']]) && $aRows['nDayLimitTimes'] > 0 && $aRows['nDayLimitTimes'] < $aRecordData[$aRows['nId']]['nOrderCount'])
		{
			 # 超過 每日提單次數上限 不顯示
			continue;
		}

		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sStatus'] = '';
		$aData[$aRows['nId']]['sClass'] = 'StatusOk';# StatusOk StatusFail
	}
	if (empty($aData))
	{
		$nErr = 1;
		$sErrMsg = aERROR['NOBANK'];
	}
	else
	{
		$nKey = array_rand($aData,1);
		$aData = $aData[$nKey];
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
	$aData['nTotalMoney'] = $aRows['nPrice']+$aSystem['aParam']['nRechargeFee'];


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
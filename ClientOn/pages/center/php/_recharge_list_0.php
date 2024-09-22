<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/recharge_list.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/rechargeList.js',
	);

	#參數接收區
	$nUkid 	= filter_input_int('nUkid',		INPUT_REQUEST, 0);
	$sPayType	= filter_input_str('sPayType',	INPUT_REQUEST, '');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_recharge_list_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/recharge_list_0.php',
	);
	#url結束

	#參數宣告區
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aData = array();

	// $aPayType['money'] = array(
	// 	'sValue'	=> aRECHARGELIST['POINTCHARGE'],
	// 	'sSelect'	=> '',
	// );
	$aPayType['company'] = array(
		'sValue'	=> aRECHARGELIST['COMPANYCHARGE'],
		'sSelect'	=> '',
	);
	$aKindData = array();
	$aTotalData = array(
		'nSubTotal' => 0,
		'nTotal'	=> 0,
	);
	$aPage['aVar'] = array(
		'nUkid'	=> $nUkid,
		'sPayType'	=> $sPayType,
	);
	$aBindArray = array();
	$aStatus = aRECHARGELIST['aSTATUS'];
	$sCondition = '';
	$nCount = 0;

	#宣告結束

	#程式邏輯區
	if ($nUkid != 0)
	{
		$sCondition .= ' AND nUkid = :nUkid';
		$aBindArray['nUkid'] = $nUkid;
	}
	if ($sPayType != '')
	{
		if ($sPayType == 'money') // 點數扣款
		{
			$sCondition .= ' AND nType0 = 5';
			$aPayType['money']['sSelect'] = 'selected';
		}
		elseif ($sPayType == 'company')
		{
			$sCondition .= ' AND nType0 = 1';
			$aPayType['company']['sSelect'] = 'selected';
		}
		else
		{
			$sCondition .= ' AND sPayType LIKE :sPayType';
			$aBindArray['sPayType'] = $sPayType;
		}
	}
	# 方案
	$sSQL = '	SELECT 	nLid,
					sName0
			FROM 	'.CLIENT_USER_KIND.'
			WHERE nOnline = 1
			AND 	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang',$aSystem['sLang'],PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKindData[$aRows['nLid']] = $aRows;
		$aKindData[$aRows['nLid']]['sSelect'] = '';
		if ($aRows['nLid'] == $nUkid)
		{
			$aKindData[$aRows['nLid']]['sSelect'] = 'selected';
		}
	}
	# 付款方式
	$sSQL = '	SELECT 	Tunnel_.sKey,
					Tunnel_.sValue,
					Payment_.sName1
			FROM 	'.CLIENT_PAYMENT_TUNNEL.' Tunnel_,
				'.CLIENT_PAYMENT.' Payment_
			WHERE Tunnel_.nOnline = 1
			AND 	Payment_.nOnline = 1
			AND 	Payment_.nType0 = 2
			AND 	Payment_.nId = Tunnel_.nPid';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aPayType[$aRows['sKey']] = $aRows;
		$aPayType[$aRows['sKey']]['sSelect'] = '';
		if ($aRows['sKey'] == $sPayType)
		{
			$aPayType[$aRows['sKey']]['sSelect'] = 'selected';
		}
	}

	$sSQL = '	SELECT	nId,
					nMoney
			FROM		'.CLIENT_MONEY.'
			WHERE		nType0 IN (1, 2, 5)
			AND		nUid = :nUid
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$nCount++;
		$aTotalData['nTotal'] += $aRows['nMoney'];
	}
	$aPage['nDataAmount'] = $nCount;

	$sSQL = '	SELECT	nId,
					nStatus,
					nMoney,
					nUkid,
					sPaymentName1,
					sPayType,
					nType0,
					nUpdateTime
			FROM		'.CLIENT_MONEY.'
			WHERE		nType0 IN (1, 2, 5)
			AND		nUid = :nUid
			'.$sCondition.'
			ORDER	BY	nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sUpdateDate'] = date('Y-m-d',$aRows['nUpdateTime']);
		$aData[$aRows['nId']]['sUpdateTime'] = date('H:i:s',$aRows['nUpdateTime']);
		if ($aRows['nType0'] == 1)
		{
			$aData[$aRows['nId']]['sPayTypeName'] = aRECHARGELIST['COMPANYCHARGE'];
		}
		if ($aRows['nType0'] == 2 && isset($aPayType[$aRows['sPayType']]))
		{
			$aData[$aRows['nId']]['sPayTypeName'] = $aPayType[$aRows['sPayType']]['sValue'];
		}
		if ($aRows['nType0'] == 5)
		{
			$aData[$aRows['nId']]['sPayTypeName'] = aRECHARGELIST['POINTCHARGE'];
		}

		$aTotalData['nSubTotal'] += $aRows['nMoney'];
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
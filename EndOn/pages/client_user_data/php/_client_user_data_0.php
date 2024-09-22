<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_data.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sSearchType = filter_input_str('sSearchType',	INPUT_REQUEST, 'sAccount');
	$nInclude    = filter_input_int('nInclude',	INPUT_REQUEST, 0);
	$sSearch 	 = filter_input_str('sSearch',	INPUT_REQUEST, '');
	$nStatus	 = filter_input_int('nStatus',	INPUT_REQUEST, -1);
	$nKind	 = filter_input_int('nKind',		INPUT_REQUEST, -1);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0_upt0.php']),
		'sInsPwd'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0_upt1.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_data_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$sTmpPa = '0';
	$sInclude = '';
	$aStatus = aSTATUS;
	$aPaData = array(
		'0'	=> array(
			'sAccount' => '',
			'sUrl' => '',
		),
	);
	$aSearchType = array(
		'sAccount' => array(
			'sTitle'	=> ACCOUNT,
			'sSelect'	=> '',
		),
		'sName0' => array(
			'sTitle'	=> aUSER['NAME0'],
			'sSelect'	=> '',
		),
	);

	$aKind = array(
		'-1' => array(
			'sTitle' => aUSER['SELKIND'],
			'sSelect'=> '',
		),
	);

	$aPage['aVar'] = array(
		'sSearchType'	=> $sSearchType,
		'sSearch'		=> $sSearch,
		'nStatus'		=> $nStatus,
		'nKind'		=> $nKind,
		'nInclude'		=> $nInclude,
	);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CDELETE.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	foreach ($aPage['aVar'] as $LPsKey => $LPsValue)
	{
		$sBackParam .= '&'.$LPsKey.'='.$LPsValue;
	}
	$aValue = array(
		'sBackParam'=> $sBackParam,
	);
	$aUrl['sIns'] .= '&sJWT='.sys_jwt_encode($aValue);
	$aUrl['sInsPwd'] .= '&sJWT='.sys_jwt_encode($aValue);

	if ($sSearch != '' && isset($aSearchType[$sSearchType]))
	{
		if($nInclude === 1)
		{
			$sInclude = 'checked';
			$sCondition .= ' AND ( User_.'.$sSearchType.' LIKE :'.$sSearchType.' OR Link_.nPa =
						(SELECT nId FROM '.CLIENT_USER_DATA.' WHERE '.$sSearchType.' LIKE :'.$sSearchType.' LIMIT 1) )';
		}
		else
		{
			$sCondition .= ' AND User_.'.$sSearchType.' LIKE :'.$sSearchType.'';
		}
		$aSearchType[$sSearchType]['sSelect'] = 'selected';
		$aBindArray[$sSearchType] = '%'.$sSearch.'%';
	}
	if ($nStatus != -1)
	{
		$aStatus[$nStatus]['sSelect'] = 'selected';
		$sCondition .= ' AND User_.nStatus = :nStatus';
		$aBindArray['nStatus'] = $nStatus;
	}
	if ($nKind != -1)
	{
		$sCondition .= ' AND User_.sKid LIKE :sKid';
		$aBindArray['sKid'] = '%'.$nKind.'%';
	}

	$sSQL = '	SELECT 	nId,
					nLid,
					sName0
			FROM 		'.CLIENT_USER_KIND.'
			WHERE 	nOnline = 1
			AND		sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKind[$aRows['nLid']]['sTitle'] = $aRows['sName0'];
		$aKind[$aRows['nLid']]['sSelect'] = '';
	}
	if (isset($aKind[$nKind]))
	{
		$aKind[$nKind]['sSelect'] = 'selected';
	}

	$sSQL = '	SELECT 	1
			FROM  	'.CLIENT_USER_LINK.' Link_
			JOIN		'.CLIENT_USER_DATA.' User_
			ON		User_.nId = Link_.nUid
			WHERE  	User_.nOnline != 99
			AND		Link_.nEndTime = 0
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = 'SELECT 	User_.nId,
				User_.sAccount,
				User_.sName0,
				User_.sKid,
				User_.nStatus,
				User_.sPromoCode,
				User_.sCreateTime,
				User_.sUpdateTime,
				Money_.nMoney,
				Link_.nLevel,
				Link_.nPa
		FROM  	'.CLIENT_USER_DATA.' User_
		JOIN		'.CLIENT_USER_MONEY.' Money_
		ON		User_.nId = Money_.nUid
		JOIN		'.CLIENT_USER_LINK.' Link_
		ON		User_.nId = Link_.nUid
		WHERE 	User_.nOnline != 99
		AND 		Link_.nEndTime = 0
				'.$sCondition.'
		ORDER BY nId DESC
		'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sKind'] = '';
		$aRows['aKid'] = explode(',', $aRows['sKid']);
		foreach ($aRows['aKid'] as $LPnKid)
		{
			$aData[$aRows['nId']]['sKind'] .= '<span> '.$aKind[$LPnKid]['sTitle'].' </span>';
		}
		$aData[$aRows['nId']]['sStatus'] = $aStatus[$aRows['nStatus']]['sTitle'];
		$aData[$aRows['nId']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$aData[$aRows['nId']]['sInsPwd'] = $aUrl['sInsPwd'].'&nId='.$aRows['nId'];
		$LPaValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			'sBackParam'=> $sBackParam,
		);
		$aData[$aRows['nId']]['sDel'] = $aUrl['sAct'].'&sJWT='. sys_jwt_encode($LPaValue).'&nId='. $aRows['nId'];

		$sTmpPa .= ','.$aRows['nPa'];
	}

	$sSQL = '	SELECT 	nId,
					sAccount
			FROM  	client_user_data
			WHERE 	nId IN ('.$sTmpPa.') ';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aPaData[$aRows['nId']] = $aRows;
		$aPaData[$aRows['nId']]['sUrl'] = $aUrl['sPage'] .'&nInclude=1&sSearch='. $aRows['sAccount'];
	}


	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
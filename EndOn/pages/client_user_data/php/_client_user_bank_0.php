<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_bank.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sAccount 	= filter_input_str('sAccount',	INPUT_REQUEST, '');
	$sName0 	= filter_input_str('sName0',		INPUT_REQUEST, '');
	$nOnline	= filter_input_int('nOnline',		INPUT_REQUEST, -1);
	$nBid	 	= filter_input_int('nBid',		INPUT_REQUEST, -1);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_upt0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0.php']),
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_bank_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aOnline = aONLINE;
	$aBank = array(
		'-1' => array(
			'sTitle' => aBANK['SELBANK'],
			'sSelect'=> '',
		),
	);
	$aPage['aVar'] = array(
		'sAccount'		=> $sAccount,
		'sName0'		=> $sName0,
		'nOnline'		=> $nOnline,
		'nBid'		=> $nBid,
	);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];


	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
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

	if ($nOnline != -1)
	{
		$aOnline[$nOnline]['sSelect'] = 'selected';
		$sCondition .= ' AND Bank_.nOnline = :nOnline';
		$aBindArray['nOnline'] = $nOnline;
	}
	if ($nBid != -1)
	{
		$sCondition .= ' AND Bank_.nBid = :nBid';
		$aBindArray['nBid'] = $nBid;
	}
	if ($sAccount != '')
	{
		$sCondition .= '	AND User_.sAccount LIKE :sAccount ';
		$aBindArray['sAccount'] = '%'.$sAccount.'%';
	}
	if ($sName0 != '')
	{
		$sCondition .= '	AND Bank_.sName0 LIKE :sName0 ';
		$aBindArray['sName0'] = '%'.$sName0.'%';
	}

	$sSQL = '	SELECT 	nId,
					sName0
			FROM 		'.SYS_BANK.'
			WHERE 	nOnline = 1
			ORDER BY 	nId ASC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aBank[$aRows['nId']]['sTitle'] = $aRows['sName0'];
		$aBank[$aRows['nId']]['sSelect'] = '';
	}
	if (isset($aBank[$nBid]))
	{
		$aBank[$nBid]['sSelect'] = 'selected';
	}

	$sSQL = '	SELECT 	1
			FROM  	'.CLIENT_USER_BANK.' Bank_
			JOIN		'.CLIENT_USER_DATA.' User_
			ON		User_.nId = Bank_.nUid
			WHERE  	User_.nOnline != 99
			AND		Bank_.nOnline != 99
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = 'SELECT 	User_.sAccount,
				Bank_.nId,
				Bank_.nBid,
				Bank_.nOnline,
				Bank_.sName0,
				Bank_.sName1,
				Bank_.sName2,
				Bank_.sCreateTime,
				Bank_.sUpdateTime
		FROM  	'.CLIENT_USER_BANK.' Bank_
		JOIN		'.CLIENT_USER_DATA.' User_
		ON		User_.nId = Bank_.nUid
		WHERE 	User_.nOnline != 99
		AND		Bank_.nOnline != 99
				'.$sCondition.'
		ORDER BY 	Bank_.nId DESC
		'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sBank'] = $aBank[$aRows['nBid']]['sTitle'];
		$aData[$aRows['nId']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			'sBackParam'=> $sBackParam,
		);
		$sLPJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sDel'] = $aUrl['sDel'].'&nId='.$aRows['nId'].'&sJWT='.$sLPJWT;
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);

	// print_r($aOnline);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_manager_data.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sAccount 	 = filter_input_str('sAccount',	INPUT_REQUEST, '');
	$nOnline	 = filter_input_int('nOnline',	INPUT_REQUEST, -1);
	$nAdmType	 = filter_input_int('nAdmType',	INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_upt0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_manager_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_manager_data_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aAdmType = array(
		'0' => array(
			'sName0' => aMANAGER['ALL'],
			'sSelect'=> '',
		),
	);
	$aOnline = aONLINE;
	$aPage['aVar'] = array(
		'sAccount'		=> $sAccount,
		'nOnline'		=> $nOnline,
		'nAdmType'		=> $nAdmType,
	);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = aMANAGER['CONFIRM0'];
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	foreach ($aPage['aVar'] as $LPsParam => $LPsValue)
	{
		$sBackParam .= '&'.$LPsParam.'='.$LPsValue;
	}
	$aValue = array(
		'sBackParam' => $sBackParam,
	);
	$aUrl['sIns'] .= '&sJWT='.sys_jwt_encode($aValue);

	if ($sAccount != '')
	{
		$sCondition .= ' AND sAccount LIKE :sAccount';
		$aBindArray['sAccount'] = '%'.$sAccount.'%';
	}
	if ($nOnline != -1 && isset($aOnline[$nOnline]))
	{
		$sCondition .= ' AND nOnline = :nOnline';
		$aBindArray['nOnline'] = $nOnline;
		$aOnline[$nOnline]['sSelect'] = 'selected';
	}
	if ($nAdmType != 0)
	{
		$sCondition .= ' AND nAdmType = :nAdmType';
		$aBindArray['nAdmType'] = $nAdmType;
	}
	if ($aAdm['nAdmType'] != 1)
	{
		$sCondition .= ' AND nAdmType >= '.$aAdm['nAdmType'];
	}

	$sSQL = 'SELECT 	nId,
				sName0
		FROM 	'.END_PERMISSION.'
		WHERE nOnline != 99
		AND 	nId >= '.$aAdm['nAdmType'].'
		ORDER BY nId ASC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aAdmType[$aRows['nId']] = $aRows;
		$aAdmType[$aRows['nId']]['sSelect'] = '';
	}
	if (isset($aAdmType[$nAdmType]))
	{
		$aAdmType[$nAdmType]['sSelect'] = 'selected';
	}


	$sSQL = '	SELECT 	1
			FROM 	'.END_MANAGER_DATA.'
			WHERE nOnline != 99
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT 	nId,
					sAccount,
					nOnline,
					sCreateTime,
					sUpdateTime,
					nAdmType
			FROM 	'.END_MANAGER_DATA.'
			WHERE nOnline != 99
			'.$sCondition.'
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sAdmType'] = $aAdmType[$aRows['nAdmType']]['sName0'];
		$aData[$aRows['nId']]['sUptUrl'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$LPaValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			'nId'		=> $aRows['nId'],
			'sBackParam'=> $sBackParam,
		);
		$aData[$aRows['nId']]['sDelUrl'] = $aUrl['sDel'].'&sJWT='. sys_jwt_encode($LPaValue).'&nId='. $aRows['nId'];
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
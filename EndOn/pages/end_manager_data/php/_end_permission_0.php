<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_permission.php');
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
	$aUrl   = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0_upt0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_manager_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_permission_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aPage['aVar'] = array(
		'sAccount'		=> $sAccount,
		'nOnline'		=> $nOnline,
		'nAdmType'		=> $nAdmType,
	);
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = aPERMISSION['CONFIRM0'];
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	if ($sAccount != '')
	{
		$sCondition .= ' AND Manager_.sAccount = :sAccount';
		$aBindArray['sAccount'] = $sAccount;
	}
	if ($nOnline != -1 && isset($aOnline[$nOnline]))
	{
		$sCondition .= ' AND Manager_.nOnline = :nOnline';
		$aBindArray['nOnline'] = $nOnline;
		$aOnline[$nOnline]['sSelect'] = 'selected';
	}
	if ($nAdmType != 0)
	{
		$sCondition .= ' AND Detail_.sValue0 = :nAdmType';
		$aBindArray['nAdmType'] = $nAdmType;
	}

	$sSQL = '	SELECT 	1
			FROM 	'.END_PERMISSION.'
			WHERE nOnline != 99';
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT 	nId,
					sName0,
					sCreateTime,
					sUpdateTime
			FROM 	'.END_PERMISSION.'
			WHERE nOnline != 99
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sUptUrl'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			't'		=> NOWTIME,
			'nId'		=> $aRows['nId'],
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sDelUrl'] = $aUrl['sDel'].'&sJWT='. $sJWT.'&nId='. $aRows['nId'];
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_menu_list.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sListName0	= filter_input_str('sListName0',	INPUT_REQUEST, '');
	$nOnline	= filter_input_int('nOnline',		INPUT_REQUEST, -1);
	$nMid		= filter_input_int('nMid',		INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0_upt0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_menu/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_menu_list_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aMenuKind = array(
		'0' => array(
			'sMenuName0' => aLIST['ALL'],
			'sSelect'=> '',
		),
	);
	$aOnline = array(
		'-1' => array(
			'sTitle' => aLIST['ALL'],
			'sSelect'=> '',
			'sClass'=> '',
		),
		'0' => array(
			'sTitle' => aLIST['OFFLINE'],
			'sSelect'=> '',
			'sClass'=> 'FontRed',
		),
		'1' => array(
			'sTitle' => aLIST['ONLINE'],
			'sSelect'=> '',
			'sClass'=> '',
		),
	);
	$aType0 = array(
		'0' => array(
			'sTitle' => aLIST['NO'],
			'sSelect'=> '',
		),
		'1' => array(
			'sTitle' => aLIST['YES'],
			'sSelect'=> '',
		),
	);
	$aPage['aVar'] = array(
		'sListName0'	=> $sListName0,
		'nOnline'		=> $nOnline,
		'nMid'		=> $nMid,
	);
	$nPageStart = $aPage['nNowNo']*$aPage['nPageSize'] - $aPage['nPageSize'] ;
	$sCondition = '';
	$sSyncUrl = '';
	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	if ($sListName0 != '')
	{
		$sCondition .= ' AND sListName0 LIKE :sListName0';
		$aBindArray['sListName0'] = '%'.$sListName0.'%';
	}
	if ($nOnline != -1 && isset($aOnline[$nOnline]))
	{
		$sCondition .= ' AND nOnline = :nOnline';
		$aBindArray['nOnline'] = $nOnline;
		$aOnline[$nOnline]['sSelect'] = 'selected';
	}

	$sSQL = '	SELECT 	nId,
					sMenuName0
			FROM 	'.END_MENU_KIND.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aMenuKind[$aRows['nId']]['sMenuName0'] = $aRows['sMenuName0'];
		$aMenuKind[$aRows['nId']]['sSelect'] = '';
	}
	if ($nMid != 0 && isset($aMenuKind[$nMid]))
	{
		$sCondition .= ' AND nMid = :nMid';
		$aBindArray['nMid'] = $nMid;
		$aMenuKind[$nMid]['sSelect'] = 'selected';
	}

	$sSQL = '	SELECT 	1
			FROM 	'.END_MENU_LIST.'
			WHERE nOnline != 99
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT 	nId,
					sListName0,
					sListTable0,
					nOnline,
					nSort,
					nType0
			FROM 	'.END_MENU_LIST.'
			WHERE nOnline != 99
			'.$sCondition.'
			ORDER BY nSort DESC,nId DESC
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
		$aData[$aRows['nId']]['sDelUrl'] =$aUrl['sDel'].'&sJWT='. $sJWT.'&nId='. $aRows['nId'].'&nt='. NOWTIME;
	}

	$aValue = array(
		'a'		=> 'SYNC',
		't'		=> NOWTIME
	);
	$sJWT = sys_jwt_encode($aValue);
	$sSyncUrl = $aUrl['sDel'].'&sJWT='. $sJWT.'&nt='. NOWTIME;
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
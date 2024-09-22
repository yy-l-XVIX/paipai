<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_menu_kind.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nPid 	= filter_input_int('nPid',		INPUT_REQUEST, 0);
	$sMenuName0	= filter_input_str('sMenuName0',	INPUT_REQUEST, '');
	$nOnline	= filter_input_int('nOnline',		INPUT_REQUEST, -1);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0_upt0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_menu/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_menu_kind_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aOnline = array(
		'-1' => array(
			'sTitle' => aMENU['ALL'],
			'sSelect'=> '',
			'sClass'=> '',
		),
		'0' => array(
			'sTitle' => aMENU['OFFLINE'],
			'sSelect'=> '',
			'sClass'=> 'FontRed',
		),
		'1' => array(
			'sTitle' => aMENU['ONLINE'],
			'sSelect'=> '',
			'sClass'=> '',
		),
	);
	$aPage['aVar'] = array(
		'sMenuName0'	=> $sMenuName0,
		'nOnline'		=> $nOnline,
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
	if ($sMenuName0 != '')
	{
		$sCondition .= ' AND sMenuName0 LIKE :sMenuName0';
		$aBindArray['sMenuName0'] = '%'.$sMenuName0.'%';
	}
	if ($nOnline != -1 && isset($aOnline[$nOnline]))
	{
		$sCondition .= ' AND nOnline = :nOnline';
		$aBindArray['nOnline'] = $nOnline;
		$aOnline[$nOnline]['sSelect'] = 'selected';
	}

	$sSQL = '	SELECT 	1
			FROM 	'.END_MENU_KIND.'
			WHERE nOnline != 99
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT 	nId,
					sMenuName0,
					sMenuTable0,
					nOnline,
					nSort
			FROM 	'.END_MENU_KIND.'
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
		$aData[$aRows['nId']]['sUptUrl'] =$aUrl['sIns'].'&nId='.$aRows['nId'];
		$aValue = array(
			'a'		=> 'DEL',
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
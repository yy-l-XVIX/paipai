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
	$nId	= filter_input_int('nId',		INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_menu/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_menu_list_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aMenuKind = array();
	$aData = array();
	$aOnline = array(
		'1' => array(
			'sTitle' => aLIST['ONLINE'],
			'sSelect'=> '',
		),
		'0' => array(
			'sTitle' => aLIST['OFFLINE'],
			'sSelect'=> '',
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
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		't'		=> NOWTIME,
		'nId'		=> $nId,
	);
	$sJWTAct = sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT 	nId,
					nMid,
					sListName0,
					sListTable0,
					nSort,
					nType0,
					nOnline
			FROM 	'.END_MENU_LIST.'
			WHERE nId = :nId
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData = $aRows;
	}
	if (empty($aData))
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	else
	{
		$aOnline[$aData['nOnline']]['sSelect'] = 'selected';
		$aType0[$aData['nType0']]['sSelect'] = 'checked';
	}

	$sSQL = '	SELECT 	nId,
					sMenuName0
			FROM 	'.END_MENU_KIND.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if ($aData['nMid'] == 0)
		{
			$aData['nMid'] = $aRows['nId'];
		}
		$aMenuKind[$aRows['nId']]['sMenuName0'] = $aRows['sMenuName0'];
		$aMenuKind[$aRows['nId']]['sSelect'] = '';
	}
	$aMenuKind[$aData['nMid']]['sSelect'] = 'selected';

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	else
	{
		$aJumpMsg['0']['nClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = aLIST['CONFIRM1'];
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
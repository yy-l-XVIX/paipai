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
	$nId	= filter_input_int('nId',		INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_menu/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_menu_kind_0_upt0.php'
	);
	#url結束

	#參數宣告區
	$aData = array(
		'nId'			=> $nId,
		'sMenuName0' 	=> '',
		'sMenuTable0' 	=> '',
		'nSort' 		=> '',
		'nOnline'		=> 1,
	);
	$aOnline = array(
		'0' => array(
			'sTitle' => aMENU['OFFLINE'],
			'sSelect'=> '',
		),
		'1' => array(
			'sTitle' => aMENU['ONLINE'],
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
					sMenuName0,
					sMenuTable0,
					nSort,
					nOnline
			FROM 	'.END_MENU_KIND.'
			WHERE nId = :nId
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aData = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aData === false && $nId != 0)
	{
		$nErr=1;
		$sErrMsg=NODATA;
	}
	if (isset($aOnline[$aData['nOnline']]))
	{
		$aOnline[$aData['nOnline']]['sSelect'] = 'selected';
	}
	#程式邏輯結束

	#輸出
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
		$aJumpMsg['0']['sClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = aMENU['CONFIRM1'];
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
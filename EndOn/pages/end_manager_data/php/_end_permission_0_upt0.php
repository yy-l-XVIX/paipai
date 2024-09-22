<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_permission.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0' => 'plugins/js/end_manager_data/end_permission.js'
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId',		INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0_upt0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_manager_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_permission_0_upt0.php'
	);
	#url結束

	#參數宣告區
	$aData = array(
		'nId'			=> $nId,
		'sName0'		=> '',
		'aControl'		=> array(),
	);
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		'nExp'	=> NOWTIME + JWTWAIT,
		'nId'		=> $nId,
	);
	$sJWTAct = sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';
	#宣告結束

	#程式邏輯區
	#sControl 1_1,2,3,6|2_4,5
	$sSQL = 'SELECT 	nId,
				sName0,
				sControl
		FROM 	'.END_PERMISSION.'
		WHERE nId = :nId
		LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$nCount = $Result->rowCount();
	if ($nCount == 0 && $nId != 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData = $aRows;
		$aData['aControl'] = array();
		$aTempCtrl = explode('|',$aRows['sControl']);
		foreach ($aTempCtrl as $LPsCtrl)
		{
			$LPaTemp = explode('_',$LPsCtrl);
			$aData['aControl'][$LPaTemp[0]] = explode(',',$LPaTemp[1]);
		}
	}
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
		$aJumpMsg['0']['sMsg'] = aPERMISSION['CONFIRM1'];
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
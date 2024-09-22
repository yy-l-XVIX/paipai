<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/transfer.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/transfer.js',
	);

	#參數接收區
	$sAccount = filter_input_str('sAccount',	INPUT_GET,'');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_account_record_0.php']),
		'sTransferChoose'	=> sys_web_encode($aMenuToNo['pages/center/php/_transfer_choose_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_transfer_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/transfer_0.php',

	);
	#url結束

	#參數宣告區
	$aData = array();
	$aValue=array(
		'a'	=> 'TRANS'.$aUser['nId'],
	);
	$sActJWT =sys_jwt_encode($aValue);
	$aUrl['sAct'] .= '&sJWT='.$sActJWT;
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';

	#宣告結束

	#程式邏輯區
	if ($aSystem['aParam']['nTransferSetting'] == 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
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
		$aJumpMsg['0']['aButton']['0']['sClass'] = '';
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}

	#輸出結束
?>
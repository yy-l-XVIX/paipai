<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_manual.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sAccount		= filter_input_str('sAccount', INPUT_POST,'');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_manual_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_manual_0.php']).$aJWT['sBackParam'],
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_manual_0_upt0.php']),
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_manual_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBank = array();

	$nErr = 0;
	$sErrMsg = '';
	$aType1 = aMANUAL['TYPE1'];
	$aType3 = aMANUAL['TYPE3'];
	#宣告結束

	#程式邏輯區

	unset($aType1['sTitle']);
	unset($aType3['sTitle']);

	$sSQL = '	SELECT	User_.nId,
					User_.sAccount,
					Money_.nMoney
			FROM	'.	CLIENT_USER_DATA .' User_,
				'.	CLIENT_USER_MONEY .' Money_
			WHERE		User_.nOnline != 99
			AND		User_.sAccount LIKE :sAccount
			AND		User_.nId = Money_.nUid
			LIMIT		1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
	sql_query($Result);
	$aData = $Result->fetch(PDO::FETCH_ASSOC);
	if($aData === false && $sAccount != '')
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	$aValue = array(
		'a'		=> 'INS',
		'nUid'	=> $aData['nId'],
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'sBackParam' => $aJWT['sBackParam'],
	);
	$aUrl['sPage'] .= '&sJWT='.sys_jwt_encode($aValue);
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
		$aJumpMsg['0']['sClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
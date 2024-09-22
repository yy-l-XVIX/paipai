<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/sys_bank.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId', INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_sys_bank_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_sys_bank_0.php']).$aJWT['sBackParam'],
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/sys_bank_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		't'		=> NOWTIME,
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sJWT = sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';
	$aOnline = aONLINE;
	$aBankType = aSYSBANK['TYPE0'];
	#宣告結束

	#程式邏輯區

	unset($aBankType['sTitle']);

	$sSQL = '	SELECT	nId,
					sName0,
					nType0,
					nOnline,
					sCode,
					sCreateTime,
					sUpdateTime
			FROM	'.	SYS_BANK .'
			WHERE		nId = :nId
			AND		nOnline != 99
			LIMIT		1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aData = $Result->fetch(PDO::FETCH_ASSOC);

	if($nId != 0)
	{
		if ($aData === false)
		{
			$nErr = 1;
			$sErrMsg = NODATA;
		}
		$aOnline[$aData['nOnline']]['sSelect'] = 'selected';
		$aBankType[$aData['nType0']]['sSelect'] = 'selected';
	}
	else
	{
		$aData['sName0'] = '';
		$aData['sCode'] = '';
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
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_kind.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/client_user_data/client_user_kind.js',
	);
	#js結束

	#參數接收區
	$nLid		= filter_input_int('nLid', INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_kind_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_kind_0.php']),
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_kind_0_upt0.php'
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aValue = array(
		'a'		=> ($nLid == 0)?'INS':'UPT'.$nLid,
		't'		=> NOWTIME,
	);
	$sJWT = sys_jwt_encode($aValue);
	$nCount = 0;
	$nErr = 0;
	$sErrMsg = '';
	$aOnline = aONLINE;
	$aType0 = aUSERKIND['aTYPE0'];
	$aType1 = aUSERKIND['aTYPE1'];
	#宣告結束

	#程式邏輯區

	$sSQL = '	SELECT	nId,
					sName0,
					sContent0,
					sFreeStartTime,
					sFreeEndTime,
					sLang,
					nLid,
					nPrice,
					sPromoteBonus,
					sPromoteBonusTax,
					nType0,
					nType1,
					nFreeDays,
					nOnline,
					sCreateTime,
					sUpdateTime
			FROM	'.	CLIENT_USER_KIND .'
			WHERE		nLid = :nLid
			AND		nOnline != 99';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['sLang']] = $aRows;
		$nCount ++;
	}

	if($nCount == 0 && $nLid != 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	if($nLid != 0)
	{
		$aOnline[$aData[$aSystem['sLang']]['nOnline']]['sSelect'] = 'selected';
		$aType0[$aData[$aSystem['sLang']]['nType0']]['sSelect'] = 'selected';
		$aType1[$aData[$aSystem['sLang']]['nType1']]['sSelect'] = 'selected';
	}

	foreach(aLANG as $LPsLang => $LPsText)
	{
		if(!isset($aData[$LPsLang]))
		{
			$aData[$LPsLang] = array(
				'sName0'	=> '',
				'sContent0'	=> '',
				'sLang'	=> $LPsLang,
				'nLid'	=> 0,
				'nPrice'	=> 0,
				'sPromoteBonus'=> '',
				'sPromoteBonusTax'=> '',
				'nType0'	=> 0,
				'nType1'	=> 0,
				'nFreeDays'	=> 0,
				'nOnline'	=> 1,
				'sFreeStartTime'	=> '',
				'sFreeEndTime'	=> '',
			);
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if($nErr == 1)
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
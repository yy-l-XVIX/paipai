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
	$nType0	= filter_input_int('nType0',	INPUT_REQUEST, -1);
	$nOnline	= filter_input_int('nOnline',	INPUT_REQUEST, -1);
	$sName0	= filter_input_str('sName0',	INPUT_REQUEST, '');
	$sCode	= filter_input_str('sCode',	INPUT_REQUEST, '');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_sys_bank_0_upt0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_sys_bank_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_sys_bank_0.php']),
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/sys_bank_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBind = array();
	$nCount = 0;
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];
	$aOnline = aONLINE;
	$aType0 = aSYSBANK['TYPE0'];
	$aPage['aVar'] = array(
		'nType0'	=> $nType0,
		'nOnline'	=> $nOnline,
		'sName0'	=> $sName0,
		'sCode'	=> $sCode,
	);

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	foreach ($aPage['aVar'] as $LPsParam => $LPsValue)
	{
		$sBackParam .= '&'.$LPsParam.'='.$LPsValue;
	}
	$aValue = array(
		'sBackParam' => $sBackParam,
	);
	$aUrl['sIns'] .= '&sJWT='.sys_jwt_encode($aValue);

	if($nType0 > -1)
	{
		$sCondition .= ' AND nType0 = :nType0 ';
		$aBind['nType0'] = $nType0;
		$aType0[$nType0]['sSelect'] = 'selected';
	}

	if($nOnline > -1)
	{
		$sCondition .= ' AND nOnline = :nOnline ';
		$aBind['nOnline'] = $nOnline;
		$aOnline[$nOnline]['sSelect'] = 'selected';
	}

	if($sName0 != '')
	{
		$sCondition .= ' AND sName0 LIKE :sName0 ';
		$aBind['sName0'] = '%'.$sName0.'%';
	}

	if($sCode != '')
	{
		$sCondition .= ' AND sCode LIKE :sCode ';
		$aBind['sCode'] = '%'.$sCode.'%';
	}
	unset($aType0['sTitle']);

	$sSQL = '	SELECT	nId
			FROM	'.	SYS_BANK .'
			WHERE		nOnline != 99
			' . $sCondition . '
			ORDER	BY	nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$nCount ++;
	}
	$aPage['nDataAmount'] = $nCount;

	$sSQL = '	SELECT	nId,
					sName0,
					nType0,
					nOnline,
					sCode,
					sCreateTime,
					sUpdateTime
			FROM	'.SYS_BANK .'
			WHERE	nOnline != 99
			' . $sCondition . '
			ORDER	BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;

		$aData[$aRows['nId']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			't'		=> NOWTIME,
			'sBackParam'=> $sBackParam,
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sDel'] = $aUrl['sDel'].'&nId='.$aRows['nId'].'&sJWT='.$sJWT;
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
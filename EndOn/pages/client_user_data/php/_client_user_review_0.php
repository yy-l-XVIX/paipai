<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_review.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區

	$sAccount 		= filter_input_str('sAccount',		INPUT_REQUEST, '');
	$nPendingStatus 	= filter_input_int('nPendingStatus',	INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_review_0_upt0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_review_0.php']),
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_review_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aSearchId = array();
	$aPendingStatus = aREVIEW['aPENDINGSTATUS'];
	$aType3 = aREVIEW['aTYPE3'];
	$aPage['aVar'] = array(
		'sAccount'		=> $sAccount,
		'nPendingStatus'	=> $nPendingStatus,
	);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	foreach ($aPage['aVar'] as $LPsKey => $LPsValue)
	{
		$sBackParam .= '&'.$LPsKey.'='.$LPsValue;
	}
	$aValue = array(
		'sBackParam'=> $sBackParam,
	);
	$aUrl['sIns'] .= '&sJWT='.sys_jwt_encode($aValue);

	if ($sAccount != '')
	{
		$sCondition .= '	AND sAccount LIKE :sAccount ';
		$aBindArray['sAccount'] = '%'.$sAccount.'%';
	}
	if ($nPendingStatus > -1)
	{
		$sCondition .= '	AND nPendingStatus = :nPendingStatus ';
		$aBindArray['nPendingStatus'] = $nPendingStatus;
	}
	if (isset($aPendingStatus[$nPendingStatus]))
	{
		$aPendingStatus[$nPendingStatus]['sSelect'] = 'selected';
	}


	$sSQL = '	SELECT 	1
			FROM  '.CLIENT_USER_DATA.'
			WHERE  nOnline != 99
			AND 	nStatus = 11
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT 	nId,
					sAccount,
					sName1,
					nType3,
					nPendingStatus,
					sCreateTime
			FROM  '.CLIENT_USER_DATA.'
			WHERE nOnline != 99
			AND 	nStatus = 11
			'.$sCondition.'
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sHeadImg'] = DEFAULTHEADIMG;
		$aData[$aRows['nId']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nId'];

		$aSearchId[$aRows['nId']] = $aRows['nId'];
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						nType0,
						sTable,
						nCreateTime,
						sCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN (' .implode(',', $aSearchId). ')
				AND 	sTable LIKE :sTable';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', 'client_user_data', PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nKid']]['sHeadImg'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
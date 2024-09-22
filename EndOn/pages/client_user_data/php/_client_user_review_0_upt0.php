<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_review.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/client_user_data/client_user_review.js',
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_review_0.php']).$aJWT['sBackParam'],
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_review_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_review_0_upt0.php',

	);
	#url結束

	#參數宣告區
	$aData = array();
	$aPhoto = array();
	$aVideo = array();
	$aKindData = array();
	$aDataPending = array();
	$aBankName = array();
	$aSearchId = array();
	$aType3 = aREVIEW['aTYPE3'];
	unset($aType3['-1']);
	$aValue = array(
		'a'		=> 'UPT'.$nId,
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sJWTAct = sys_jwt_encode($aValue);

	$nErr = 0;
	$sErrMsg = '';

	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	nLid,
					sName0
			FROM	'.	CLIENT_USER_KIND .'
			WHERE		sLang LIKE :sLang
			AND		nOnline != 99';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKindData[$aRows['nLid']] = $aRows;
	}

	$sSQL = ' 	SELECT 	nId,
					sKid,
					sAccount,
					sName1,
					sPendingStatus,
					sCreateTime
			FROM 	'.CLIENT_USER_DATA.'
			WHERE nId = :nId
			AND 	nStatus = 11
			AND 	nOnline != 99';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aData = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aData === false)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	else
	{
		$aData['aKid'] = array();
		if ($aData['sKid'] != '')
		{
			$aData['aKid'] = explode(',', $aData['sKid']);
		}
	}
	$aUserPendingStatus = explode(',', $aData['sPendingStatus']);		// 資料審核狀態
	$aPendingField = explode(',', $aSystem['aParam']['sPendingField']);	// 需要審核欄位
	foreach ($aPendingField as $LPnIndex => $LPsField)
	{
		$aDataPending[$LPsField] = array(
			'1' => '',
			'99' => '',
		);
		$LPnStatus = $aUserPendingStatus[$LPnIndex];
		if (isset($aDataPending[$LPsField][$LPnStatus]))
		{
			$aDataPending[$LPsField][$LPnStatus] = 'checked';
		}
	}

	$sSQL = ' 	SELECT 	nId,
					sIdNumber,
					sBirthday,
					nBirthday
			FROM 	'.CLIENT_USER_DETAIL.'
			WHERE nUid = :nUid
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aData['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['sIdNumber']	= $aRows['sIdNumber'];
		$aData['sBirthday']	= $aRows['sBirthday'];
		$aData['nBirthday']	= $aRows['nBirthday'];
		$aData['nAge'] 		= '';
		$aData['sImageUrl0']	= '';
		$aData['sImageUrl1']	= '';
		$aData['aBank'] 		= array();

		if ($aRows['nBirthday']>0)
		{
			$aData['nAge'] = date('Y') - date('Y',$aRows['nBirthday']) - 1;
			if (date('n') > date('n',$aRows['nBirthday']) || (date('n') == date('n',$aRows['nBirthday']) && date('j') == date('j',$aRows['nBirthday'])))
			{
				$aData['nAge'] ++;
			}
		}

	}

	// 銀行帳戶
	$sSQL = '	SELECT 	BankCard_.nId,
					BankCard_.nBid,
					BankCard_.nOnline,
					BankCard_.sName0,
					BankCard_.sName1,
					BankCard_.sName2,
					BankCard_.sCreateTime,
					Img_.nCreateTime,
					Img_.sTable,
					Img_.sFile
			FROM 	'.CLIENT_USER_BANK.' BankCard_,
				'.CLIENT_IMAGE_CTRL.' Img_
			WHERE BankCard_.nUid = :nUid
			AND 	BankCard_.nOnline = 1
			AND 	Img_.sTable LIKE :sTable
			AND 	BankCard_.nId = Img_.nKid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aData['nId'], PDO::PARAM_INT);
	$Result->bindValue(':sTable', CLIENT_USER_BANK, PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aBank'][$aRows['nId']] = $aRows;
		$aData['aBank'][$aRows['nId']]['sImageUrl'] = base64Pic(IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile']);

		$aSearchId[$aRows['nBid']] = $aRows['nBid'];
	}
	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						sCode
				FROM 	'.SYS_BANK.'
				WHERE nId IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aBankName[$aRows['nId']] = $aRows;
		}
	}

	$sSQL = '	SELECT	nId,
					nKid,
					sFile,
					sTable,
					nType0,
					nCreateTime
			FROM	'.	CLIENT_IMAGE_CTRL .'
			WHERE	nKid = :nKid
			AND 	sTable IN ( \'client_user_id\',\'client_user_photo\',\'client_user_video\')';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nKid', $aData['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if ($aRows['sTable'] == 'client_user_id')
		{
			$aData['sImageUrl'.$aRows['nType0']] = base64Pic(IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile']);
		}
		if ($aRows['sTable'] == 'client_user_photo')
		{
			$aPhoto[$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
		if ($aRows['sTable'] == 'client_user_video')
		{
			$aVideo[$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}

	}
	// $sSQL = '	SELECT	nId,
	// 				nKid,
	// 				sFile,
	// 				sTable,
	// 				nType0,
	// 				nCreateTime
	// 		FROM	'.	CLIENT_IMAGE_CTRL .'
	// 		WHERE	nKid = :nKid
	// 		AND 	sTable LIKE :sTable';
	// $Result = $oPdo->prepare($sSQL);
	// $Result->bindValue(':nKid', $nId, PDO::PARAM_INT);
	// $Result->bindValue(':sTable', 'client_user_id', PDO::PARAM_STR);
	// sql_query($Result);
	// while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	// {
	// 	$aData['sImageUrl'.$aRows['nType0']] = base64Pic(IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile']);
	// }

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
		$aJumpMsg['0']['sMsg'] =aREVIEW['CONFIRM'];
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
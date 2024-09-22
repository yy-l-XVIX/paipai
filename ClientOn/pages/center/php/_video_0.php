<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/video.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/File.js',
		'1'	=> 'plugins/js/center/video.js',
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_GET, $aUser['nId']);
	$sRole 	= filter_input_str('sRole',	INPUT_GET,''); // boss | staff
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_video_0_act0.php']).'&run_page=1',
		'sSetting'	=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nId,
		'sPhoto'	=> sys_web_encode($aMenuToNo['pages/center/php/_photo_0.php']).'&nId='.$nId,
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/video_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aUserKind = array();
	$aDataPending = array();
	$aPendingField = explode(',', $aSystem['aParam']['sPendingField']);	// 需要審核欄位
	$aCodeLid = array(); // code to nLid
	$aChangeRole = array(
		'sText' => '',
		'sUrl' => '',
	);

	$bEdit = false;
	$aValue = array(
		'a' 	 => 'UPT'.$aUser['nId'],
		// 'nExp' => NOWTIME+JWTWAIT,
	);
	$sJWT=sys_jwt_encode($aValue);
	$aValue = array(
		'a' 	 => 'DELVIDEO'.$aUser['nId'],
	);
	$sDelVideoJWT=sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 0;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = '';

	$aJumpMsg['dataprocessing'] = array(
		'sBoxClass'	=>	'',
		'sShow'	=>	0,	# 是否直接顯示彈窗 0=>隱藏 , 1=>顯示
		'sTitle'	=>	'',	# 標題
		'sIcon'	=>	'',	# 成功=>success,失敗=>error
		'sMsg'	=>	DATAPROCESSING,# 資料處理中
		'sArticle'	=>	'',	# 較長文字
		'aButton'	=>	array(),
		'nClicktoClose'=>	0,	# 是否點擊任意一處即可關閉 0=>否 , 1=>是
	);
	#宣告結束

	#程式邏輯區
	if ($aUser['nId'] == $nId) // 可編輯自己
	{
		$bEdit = true;
	}


	// user_kind
	$sSQL = '	SELECT	nLid,
					sName0,
					sCode
			FROM	'.CLIENT_USER_KIND.'
			WHERE	nOnline = 1
			AND	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang',$aSystem['sLang'],PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aUserKind[$aRows['nLid']] = $aRows;
		$aCodeLid[$aRows['sCode']] = $aRows['nLid'];
	}

	$sSQL = '	SELECT 	sAccount,
					sName0,
					nKid,
					sKid,
					nStatus,
					nType4
			FROM 	'.CLIENT_USER_DATA.'
			WHERE nId = :nId
			AND 	nOnline = 1
			AND 	sKid LIKE :sKid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
	$Result->bindValue(':sKid','%'.$aCodeLid['staff'].'%',PDO::PARAM_STR); // 人才有影片
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData = $aRows;
		$aData['aKid'] = explode(',', $aRows['sKid']);
		$aData['aVideo'] = array();
		$aData['sHeadImage'] = DEFAULTHEADIMG;
		$aData['sWorkStatus'] = '';
		$aData['sSelfieBoxClass']= $aUserKind[$aRows['nKid']]['sCode'];
		$aData['sRole'] = $aUserKind[$aRows['nKid']]['sCode'];

		// 頭像class
		switch($aRows['nStatus'])
		{
			case 2:
				$aData['sWorkStatus'] = 'off';
			break;
			case 1:
				$aData['sWorkStatus'] = 'ing';
			break;
		}
		if ($sRole == '')
		{
			$sRole = $aUserKind[$aRows['nKid']]['sCode'];
		}
	}
	if (empty($aData))
	{
		$nErr = 1;
		$sErrMsg = aVIDEO['NOMEMBER'];
	}
	else
	{

		// 我的頭
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid = :nKid
				AND 	sTable LIKE :sTable
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nKid', $nId, PDO::PARAM_INT);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows!== false)
		{
			$aData['sHeadImage'] =  IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
		if ($sRole != $aData['sRole'] && sizeof($aData['aKid']) > 1) // 有兩個方案可以切換
		{
			$aData['sRole'] = $sRole;
			$aData['nKid'] = $aCodeLid[$sRole];
			$aData['sSelfieBoxClass']= $sRole;
		}

		foreach ($aData['aKid'] as $LPnKid)
		{
			if ($aData['nKid'] != $LPnKid)
			{
				$aChangeRole['sText'] = $aUserKind[$LPnKid]['sName0'];
				$aChangeRole['sUrl'] = $aUrl['sInf'].'&sRole='.$aUserKind[$LPnKid]['sCode'];
			}
		}
		// 我傳的影片
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nType0,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid = :nKid
				AND 	sTable LIKE :sTable';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nKid', $nId, PDO::PARAM_INT);
		$Result->bindValue(':sTable', 'client_user_video', PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aVideo'][$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}


		// 資料審核狀態
		$sSQL = '	SELECT	nPendingStatus,
						sPendingStatus
				FROM	'.	CLIENT_USER_DATA .'
				WHERE		nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		$aUserPendingStatus = explode(',', $aRows['sPendingStatus']);		// 資料審核狀態
		$aPendingField = explode(',', $aSystem['aParam']['sPendingField']);	// 需要審核欄位
		foreach ($aPendingField as $LPnIndex => $LPsField)
		{
			$aDataPending[$LPsField] = '';
			if ($nId == $aUser['nId'])
			{
				$LPnStatus = $aUserPendingStatus[$LPnIndex];
				if ($aUser['nStatus'] == 11)
				{
					if ($LPnStatus == 1)
					{
						$aDataPending[$LPsField] = '<span class="FontGreen"><i class="far fa-check-circle"></i></span>';
					}
					if ($LPnStatus == 99)
					{
						$aDataPending[$LPsField] = '<span class="FontRed"><i class="far fa-times-circle"></i></span>';
					}
				}
			}
		}
	}
	if ($sRole == 'boss')// 雇主沒有照片 // 人才不可以看別人 2021-02-01 改人才可以看別人
	{
		$nErr = 1;
		$sErrMsg = PARAMSERR;
	}
	else if($sUserCurrentRole == 'boss' && $aData['nType4'] == 0)
	{
		// 我是不是這個人的雇主
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nOnline = 1
				AND 	nType0 = 1
				AND 	nType1 = 1
				AND 	nUid = :nUid
				AND 	nId IN ( SELECT nGid FROM '.CLIENT_USER_GROUP_LIST.' WHERE nUid = :nId )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aTempGroup[$aRows['nId']] = $aRows['nId'];
		}
		if (!empty($aTempGroup)) // 未結案工作的雇主可看
		{
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_JOB.'
					WHERE nStatus < 1
					AND 	nGid IN ( '.implode(',',$aTempGroup).' )';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$nId,PDO::PARAM_INT);
			sql_query($Result);
			$nCount = $Result->rowCount();
		}
		if ($nCount > 0)
		{
			$aData['nType4'] = 1;
		}

	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
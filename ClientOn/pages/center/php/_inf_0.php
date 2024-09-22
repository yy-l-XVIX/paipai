<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/inf.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/File.js',
		'1'	=> 'plugins/js/center/inf.js',
	);

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_GET, $aUser['nId']);
	$sRole 	= filter_input_str('sRole',	INPUT_GET,''); // boss | staff

	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0_act0.php']).'&run_page=1',
		'sComment'	=> sys_web_encode($aMenuToNo['pages/center/php/_comments_0.php']),
		'sSetting'	=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nId,
		'sPhoto'	=> sys_web_encode($aMenuToNo['pages/center/php/_photo_0.php']).'&nId='.$nId,
		'sVideo'	=> sys_web_encode($aMenuToNo['pages/center/php/_video_0.php']).'&nId='.$nId,
		'sShare'	=> sys_web_encode($aMenuToNo['pages/center/php/_share_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/inf_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aUserKind = array();
	$aPendingField = explode(',', $aSystem['aParam']['sPendingField']);	// 需要審核欄位
	$aCodeLid = array(); // code to nLid
	$aChangeRole = array(
		'sText' => '',
		'sUrl' => '',
	);

	$bEdit = false;
	$nCount = 0;
	$aValue = array(
		'a' 	 => 'UPT'.$aUser['nId'],
		// 'nExp' => NOWTIME+JWTWAIT,
	);
	$sJWT=sys_jwt_encode($aValue);
	$aValue = array(
		'nExpireTime' 	=> NOWTIME+1200, # 20分鐘到期
		'nUid'		=> $nId,
	);
	$aUrl['sShare'] .= '&sJWT='.sys_jwt_encode($aValue);
	$sShareUrl = WEBSITE['SHAREURL'].substr($aUrl['sShare'],2);
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
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
	// share 產生亂碼
	$sShareUrl = str_replace('[[::sRandomText::]]',substr(md5(time()),5,6),$sShareUrl);

	if ($nId == $aUser['nId'])
	{
		$bEdit = true; // 自己可以編輯
	}
	// if ($sUserCurrentRole == 'staff' && $aUser['nId'] != $nId)
	// {
	// 	$nErr = 1;
	// 	$sErrMsg = PARAMSERR;  // 人才不可以看別人 2021-02-01 改人才可以看別人
	// }

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

	// 取會員資料
	$sSQL = '	SELECT 	User_.nId,
					User_.sAccount,
					User_.sName0,
					User_.nKid,
					User_.sKid,
					User_.sPhone,
					User_.sWechat,
					User_.sEmail,
					User_.nType0,
					User_.nType1,
					User_.nType2,
					User_.nType4,
					User_.nStatus,
					Detail_.sHeight,
					Detail_.sWeight,
					Detail_.nBirthday,
					Detail_.sSize,
					Detail_.sContent0,
					Detail_.sContent1
			FROM 	'.CLIENT_USER_DATA.' User_,
				'.CLIENT_USER_DETAIL.' Detail_
			WHERE User_.nId = :nId
			AND 	User_.nOnline = 1
			AND 	User_.nId = Detail_.nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData = $aRows;
		$aData['nAge'] = '';
		if ($aRows['nBirthday']>0)
		{
			$aData['nAge'] = date('Y') - date('Y',$aRows['nBirthday']) - 1;
			if (date('n') > date('n',$aRows['nBirthday']) || (date('n') == date('n',$aRows['nBirthday']) && date('j') == date('j',$aRows['nBirthday'])))
			{
				$aData['nAge'] ++;
			}
		}
		$aData['aKid'] = explode(',', $aRows['sKid']);
		$aData['nScore'] = 0;
		$aData['sHeadImage'] = DEFAULTHEADIMG;
		$aData['sWorkStatus'] = '';
		$aData['sSelfieBoxClass']= $aUserKind[$aRows['nKid']]['sCode'];
		$aData['sRole'] = $aUserKind[$aRows['nKid']]['sCode'];

		$LPaValue = array(
			'a'	=> 'ADDFRIEND'.$aUser['nId'],
		);
		$aData['aFriendBtn'] = array(
			'bFriend'	=> false,
			'sClass'	=> '',
			'sText'	=> aINF['ADDFRIEND'], // 加好友
			'sUrl'	=> $aUrl['sAct'].'&nFUid='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue),
		);
		$LPaValue = array(
			'a'	=> 'ADDBLOCK'.$aUser['nId'],
		);
		$aData['aBlockBtn'] = array(
			'bBlock'	=> false,
			'sClass'	=> '',
			'sText'	=> aINF['BLOCK'], // 封鎖好友
			'sUrl'	=> $aUrl['sAct'].'&nBUid='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue),
		);
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
		$sErrMsg = aINF['NOMEMBER'];
	}
	else
	{
		// 頭像
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
			$aData['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
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

		// boss 取評分
		if ($aData['sRole'] == 'boss')
		{
			$sSQL = '	SELECT	nScore
					FROM		'.CLIENT_JOB_SCORE.'
					WHERE		nGid IN ( SELECT nId FROM '.CLIENT_GROUP_CTRL.' WHERE nUid = :nUid AND nType1 = 1 AND nOnline = 1)
					AND 		nStatus = 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$nId,PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aData['nScore'] += $aRows['nScore'];
				$nCount ++;
			}
			if ($nCount > 0)
			{
				$aData['nScore'] = floor($aData['nScore']/$nCount);#平均分數
			}
		}

		// friend
		if ($nId != $aUser['nId'])
		{
			// 是不是好友
			$sSQL = '	SELECT	1
					FROM		'.CLIENT_USER_FRIEND.'
					WHERE		nUid = :nUid
					AND		nFUid = :nFUid
					AND		nStatus = 1
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
			$Result->bindValue(':nFUid',$nId,PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if($aRows !== false)
			{
				$aData['aFriendBtn']['bFriend'] = true;
				$aData['aFriendBtn']['sClass'] = 'disable';
				$aData['aFriendBtn']['sText'] = aINF['DELFRIEND'];
				$LPaValue = array(
					'a'	=> 'DELFRIEND'.$aUser['nId'], // 刪好友
				);
				$aData['aFriendBtn']['sUrl'] = $aUrl['sAct'].'&nFUid='.$nId.'&sJWT='.sys_jwt_encode($LPaValue);
			}
			// 是不是封鎖
			$sSQL = '	SELECT	1
					FROM		'.CLIENT_USER_BLOCK.'
					WHERE		nUid = :nUid
					AND		nBUid = :nBUid
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
			$Result->bindValue(':nBUid',$nId,PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if($aRows !== false)
			{
				$aData['aBlockBtn']['bBlock'] = true;
				$aData['aBlockBtn']['sClass'] = 'disable';
				$aData['aBlockBtn']['sText'] = aINF['UNBLOCK'];
				$LPaValue = array(
					'a'	=> 'DELBLOCK'.$aUser['nId'], // 解封鎖
				);
				$aData['aBlockBtn']['sUrl'] = $aUrl['sAct'].'&nBUid='.$nId.'&sJWT='.sys_jwt_encode($LPaValue);
			}
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

		 if($sUserCurrentRole == 'boss' && $sRole == 'staff' && $aData['nType4'] == 0)
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
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['nClicktoClose'] = 1;
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		$aJumpMsg['0']['aButton']['0']['sClass'] = '';
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
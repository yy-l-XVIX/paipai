<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/friend.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/friend.js',
	);

	#參數接收區
	$nFetch	= filter_input_int('nFetch',	INPUT_REQUEST,0);
	$sSearch	= filter_input_str('sSearch',INPUT_REQUEST,'');
	#參數結束

	#參數宣告區

	$aData = array(
		'0'	=> array(),	// 待確認好友
		'1'	=> array(),	// 已確認好友
	);
	$aSearchId = array();
	$aMemberData = array();
	$aBindArray = array();
	$aReturn = array();
	$aBlock = array(0=>0);
	$aValue = array(
		'a'		=> 'AGREEFRIEND'.$aUser['nId'],
		't'		=> NOWTIME,
	);
	$sAgreeJWT =sys_jwt_encode($aValue);

	$aValue = array(
		'a'		=> 'DENYFRIEND'.$aUser['nId'],
		't'		=> NOWTIME,
	);
	$sDenyJWT =sys_jwt_encode($aValue);
	$aValue = array(
		'a'		=> 'CHECKCHAT',
	);
	$sChatJWT =sys_jwt_encode($aValue);
	$aValue = array(
		'sBackUrl'=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']),
	);
	$sInfJWT =sys_jwt_encode($aValue);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	#宣告結束

	#給此頁使用的url
	$aUrl = array(
		'sChat'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0_act0.php']).'&run_page=1&sJWT='.$sChatJWT,
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&sJWT='.$sInfJWT,
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']),
		'sFetch'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']).'&nFetch=1&sSearch='.$sSearch,
		'sAgree'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0_act0.php']).'&run_page=1&sJWT='.$sAgreeJWT,
		'sDeny'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0_act0.php']).'&run_page=1&sJWT='.$sDenyJWT,
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/friend_0.php',
	);
	#url結束

	#程式邏輯區
	if ($sSearch != '')
	{
		$sCondition .= ' AND (Friend_.sName0 LIKE :sName0 OR User_.sName0 LIKE :sName0) ';
		$aBindArray['sName0'] = '%'.$sSearch.'%';
	}
	// 封鎖名單不顯示
	$sSQL = '	SELECT	nBUid
			FROM		'.CLIENT_USER_BLOCK.'
			WHERE		nUid = :nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aBlock[$aRows['nBUid']] = $aRows['nBUid'];
	}

	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_USER_FRIEND.' Friend_,
				'.CLIENT_USER_DATA.' User_
			WHERE Friend_.nUid = :nUid
			AND 	Friend_.nStatus IN ( 0,1 )
			AND 	Friend_.nFUid NOT IN ( '.implode(',', $aBlock).' )
			AND 	User_.nOnline = 1
			'.$sCondition.'
			AND 	User_.nId = Friend_.nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	Friend_.nId,
					Friend_.nFUid,
					Friend_.nStatus,
					Friend_.sName0 as sSetName0,
					User_.sName0,
					User_.nKid,
					User_.nStatus as nUserStatus
			FROM 	'.CLIENT_USER_FRIEND.' Friend_,
				'.CLIENT_USER_DATA.' User_
			WHERE Friend_.nUid = :nUid
			AND 	Friend_.nStatus IN ( 0,1 )
			AND 	Friend_.nFUid NOT IN ( '.implode(',', $aBlock).' )
			AND 	User_.nOnline = 1
			'.$sCondition.'
			AND 	User_.nId = Friend_.nFUid
			ORDER BY Friend_.nStatus ASC, Friend_.nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nStatus']][$aRows['nId']] = $aRows;
		$aSearchId[$aRows['nFUid']] = $aRows['nFUid'];
		$aMemberData[$aRows['nFUid']] = array(
			'nId'			=> $aRows['nFUid'],
			'sName0'		=> $aRows['sName0'],
			'nKid'		=> $aRows['nKid'],
			'nStatus'		=> $aRows['nUserStatus'],
			'sUserInfoUrl'	=> $aUrl['sInf'].'&nId='.$aRows['nFUid'],
			'sHeadImage'	=> DEFAULTHEADIMG,
			'sRoleClass'	=> '',
			'sStatusClass'	=> '',
		);
		$aValue = array(
			'a'	=> 'UPTNAME',
			'nId'	=> $aRows['nId'],
		);
		$aData[$aRows['nStatus']][$aRows['nId']]['sUptName'] = sys_web_encode($aMenuToNo['pages/center/php/_friend_0_act0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue);

		if($aRows['nKid'] == 1)
		{
			$aMemberData[$aRows['nFUid']]['sRoleClass'] = 'boss';
		}
		// 上班下班
		if($aRows['nKid'] == 3)
		{
			$sTempClass = '';

			if($aRows['nUserStatus'] == 2)
			{
				$sTempClass = 'off';
			}
			if($aRows['nUserStatus'] == 3)
			{
				$sTempClass = 'ing';
			}

			$aMemberData[$aRows['nFUid']]['sStatusClass'] = '<div class="selfieStatus '.$sTempClass.'"></div>';
		}
		if ($aRows['sSetName0'] != '')
		{
			$aMemberData[$aRows['nFUid']]['sName0'] = $aRows['sSetName0'];
		}
	}

	if (!empty($aSearchId))
	{
		#頭
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN ( '.implode(',', $aSearchId).' )
				AND 	sTable LIKE :sTable ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	#程式邏輯結束

	#輸出json.

	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	if ($nFetch == 1)
	{
		foreach ($aData as $LPnStatus => $LPaFriendList)
		{
			foreach ($LPaFriendList as $LPnId => $LPaFriend)
			{
				$LPaFriend['sName0'] 		= $aMemberData[$LPaFriend['nFUid']]['sName0'];
				$LPaFriend['nKid'] 		= $aMemberData[$LPaFriend['nFUid']]['nKid'];
				$LPaFriend['nUserStatus'] 	= $aMemberData[$LPaFriend['nFUid']]['nStatus'];
				$LPaFriend['sUserInfoUrl'] 	= $aMemberData[$LPaFriend['nFUid']]['sUserInfoUrl'];
				$LPaFriend['sHeadImage'] 	= $aMemberData[$LPaFriend['nFUid']]['sHeadImage'];
				$LPaFriend['sRoleClass'] 	= $aMemberData[$LPaFriend['nFUid']]['sRoleClass'];
				$LPaFriend['sStatusClass'] 	= $aMemberData[$LPaFriend['nFUid']]['sStatusClass'];

				$aReturn['aData'][$LPnStatus][] = $LPaFriend;
			}
		}
		$aReturn['nStatus'] = 1;
		$aReturn['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
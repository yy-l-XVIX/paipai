<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/center.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0' => 'plugins/js/center/center.js',
	);

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sCenter'		=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		'sSaved'		=> sys_web_encode($aMenuToNo['pages/center/php/_saved_0.php']),
		'sJobRecord'	=> sys_web_encode($aMenuToNo['pages/center/php/_job_record_0.php']),
		'sAccountRecord'	=> sys_web_encode($aMenuToNo['pages/center/php/_recharge_list_0.php']),
		'sRegister'		=> sys_web_encode($aMenuToNo['pages/register/php/_choose_0.php']),
		'sSetting'		=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']),
		'sInf'		=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$aUser['nId'],
		'sTerm'		=> sys_web_encode($aMenuToNo['pages/center/php/_terms_1.php']),
		'sPromo'		=> sys_web_encode($aMenuToNo['pages/center/php/_promotion_0.php']),
		'sService'		=> sys_web_encode($aMenuToNo['pages/center/php/_service_list_0.php']),
		'sFriend'		=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']),
		'sBlock'		=> sys_web_encode($aMenuToNo['pages/center/php/_block_0.php']),
		'sMemberList'	=> sys_web_encode($aMenuToNo['pages/center/php/_member_list_0.php']),
		'sLogout'		=> sys_web_encode($aMenuToNo['pages/login/php/_login_0_act0.php']),
		'sHtml'		=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/center_0.php',
		'sAct'		=> sys_web_encode($aMenuToNo['pages/center/php/_center_0_act0.php']),
	);
	#url結束

	#參數宣告區
	$aUserKid = array();
	$aStatus = aCENTER['aSTATUS'];

	$aValue = array(
		'a'		=> 'LOGOUT',
		'nExp'	=> NOWTIME + JWTWAIT,
		'sAccount'	=> $aUser['sAccount'],
		'nStatus'	=> 0,
	);
	$aUrl['sLogout'] .= '&run_page=1&sJWT='.sys_jwt_encode($aValue);
	$aValue = array(
		'aUser' 	=> array(
			'aKid'	=> $aUser['aKid'],
			'sExpired0'	=> $aUser['sExpired0'],
			'sExpired1'	=> $aUser['sExpired1'],
		),
	);
	$aUrl['sRegister'] .= '&sJWT='.sys_jwt_encode($aValue);
	$sBossClass = 'disable';
	$sStaffClass = 'disable';
	$nHavClass = 0;
	$sBothClass = '';
	$sStatusClass = '';
	$aUser['sHeadImage'] = DEFAULTHEADIMG;

	#宣告結束

	#程式邏輯區
	foreach ($aStatus as $LPnStatus => $LPaStatus)
	{
		if ($aUser['nStatus'] == $LPnStatus)
		{
			$aStatus[$LPnStatus]['sSelect'] = 'active';
		}
		$LPaValue = array(
			'a'		=> 'CHANGEWORK',
			'nStatus'	=> $LPnStatus,
		);
		$aStatus[$LPnStatus]['sActUrl'] = $aUrl['sAct'].'&sJWT='.sys_jwt_encode($LPaValue);
	}
	switch ($aUser['nStatus'])
	{
		case '2':
			$sStatusClass = 'off';
			break;
		case '1':
			$sStatusClass = 'ing';
			break;
	}

	$sSQL = '	SELECT	nLid,
					sName0
			FROM		'.CLIENT_USER_KIND.'
			WHERE		nOnline = 1
			AND		sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang',$aSystem['sLang'],PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aUserKid[$aRows['nLid']] = $aRows;
		$aUserKid[$aRows['nLid']] = $aRows;
		$aUserKid[$aRows['nLid']]['sClass'] = 'disable';

		if ($aRows['nLid'] == $aUser['nKid'])
		{
			$aUser['sRoleName'] = $aRows['sName0'];
		}
	}
	# 我的頭
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
	$Result->bindValue(':nKid', $aUser['nId'], PDO::PARAM_INT);
	$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows!== false)
	{
		$aUser['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
	}

	// 好友邀請
	$sSQL = '	SELECT	1
			FROM		'.CLIENT_USER_FRIEND.'
			WHERE		nUid = :nUid
			AND		nStatus = 0';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	$nFriend = $Result->rowCount();

	foreach ($aUserKid as $LPnKid => $LPaUserKid)
	{
		if (strpos($aUser['sKid'], (string)$LPnKid) !== false)
		{
			$aUserKid[$LPnKid]['sClass'] = 'active';
			$nHavClass++;
		}

		if ($LPnKid != $aUser['nKid']) # 切換身分
		{
			$aUrl['sCenter'] .= '&nChangeKid='.$LPnKid;
		}
	}

	if($nHavClass == 2)
	{
		$sBothClass = 'active';
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/chat_group_upt.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/chat/chat_group_upt.js',
	);
	#js結束

	#參數接收區
	$nGid = filter_input_int('nGid',	INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nGid,
		'sPage'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_upt_0.php']).'&nGid='.$nGid,
		'sAct'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_upt_0_act0.php']).'&run_page=1',
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sAdd'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_add_0.php']).'&nGroupEdit=1&nId='.$nGid,
		'sAnnounce'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_announce_0.php']).'&nId='.$nGid,
		'sMoreMember'=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_member_0.php']).'&nId='.$nGid,
		'sHtml'	=> 'pages/chat/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/chat_group_upt_0.php',
	);
	#url結束

	#參數宣告區
	$bMoreMember = false; // 是否需要呈現 "更多群組成員"
	$nMemberCount = 0;
	$aData = array();
	$aMyFriend = array();
	$aValue = array(
		'sBackUrl'=> $aUrl['sPage'],
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);
	$aUrl['sAdd'] .= '&sJWT='.sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['1']= $aJumpMsg['0'];
	$aJumpMsg['1']['sMsg'] = '123';
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';
	$aJumpMsg['1']['aButton']['0']['sUrl'] = 'javascript:void(0);';
	// 防呆
	$aJumpMsg['stupidout']= $aJumpMsg['0'];
	$aJumpMsg['stupidout']['sMsg'] = 123;
	$aJumpMsg['stupidout']['nClicktoClose'] = 0;
	$aJumpMsg['stupidout']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['stupidout']['aButton']['0']['sClass'] = 'JqDoAction JqReplaceO';
	$aJumpMsg['stupidout']['aButton']['0']['sUrl'] = 'javascript:void(0);';
	$aJumpMsg['stupidout']['aButton']['1']['sText'] = CANCEL;
	$aJumpMsg['stupidout']['aButton']['1']['sClass'] = 'JqClose cancel';
	#宣告結束

	#程式邏輯區

	// 群組資訊(名稱 種類 建立人 私聊對象 )
	$sSQL = '	SELECT 	nId,
					nUid,
					nTargetUid,
					nType0,
					sName0
			FROM 	'.CLIENT_GROUP_CTRL.'
			WHERE nId = :nId
			AND 	(nOnline > 0 AND nOnline < 99)
			AND 	nType1 = 0';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nGid, PDO::PARAM_INT);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows === false)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	else
	{
		$aData = $aRows;
		$aData['sContent0'] = '';
		$aData['aMember'] = array();

		// 群組內成員
		$sSQL = '	SELECT 	nId,
						nUid
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
		sql_query($Result);
		$nMemberCount = $Result->rowCount();
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aMember'][$aRows['nUid']] = $aRows;
			$aData['aMember'][$aRows['nUid']]['sName0'] = '';
			$aData['aMember'][$aRows['nUid']]['sRole'] = 'staff';
			$aData['aMember'][$aRows['nUid']]['sHeadImage'] = DEFAULTHEADIMG;
			$aData['aMember'][$aRows['nUid']]['sInfUrl'] = $aUrl['sInf'].'&nId='.$aRows['nUid'];
			if (sizeof($aData['aMember']) == 14) // 畫面最多顯示14位成員
			{
				$bMoreMember = true;
				break;
			}
		}

		// 會員資訊(暱稱 身分)
		$sSQL = '	SELECT 	nId,
						nKid,
						sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', array_keys($aData['aMember'])).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aMember'][$aRows['nId']]['sName0'] = $aRows['sName0'];
			if ($aRows['nKid'] == 1)
			{
				$aData['aMember'][$aRows['nId']]['sRole'] = 'boss';
			}
		}

		// 會員頭像
		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', array_keys($aData['aMember'])).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aMember'][$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}

		// 我的好友(更換為自行設定暱稱)
		$sSQL = '	SELECT 	nFUid,
						sName0
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nUid
				AND 	sName0 != \'\'
				AND 	nFUid IN ( '.implode(',',array_keys($aData['aMember'])).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aMember'][$aRows['nFUid']]['sName0'] = $aRows['sName0'];
		}

		if ($aData['nType0'] == 0)
		{
			// 私聊呈現另一個會員名稱
			if ($aData['nUid'] != $aUser['nId'])
			{
				$aData['sName0'] = $aData['aMember'][$aData['nUid']]['sName0'];
			}
			if ($aData['nTargetUid'] != $aUser['nId'])
			{
				$aData['sName0'] = $aData['aMember'][$aData['nTargetUid']]['sName0'];
			}
			$aData['sHeadName'] = $aData['sName0'];
		}
		else
		{
			// 群組顯示人數
			$aData['sHeadName'] = $aData['sName0'] . ' ( '.$nMemberCount.' )';
		}

		// 退出群組
		$aValue = array(
			'a'		=> 'EXITGROUP',
			'nGid'	=> $nGid,
			'nGLid'	=> $aData['aMember'][$aUser['nId']]['nId'], // client_user_group_list nId
		);
		$aData['sExitGroupUrl'] = $aUrl['sAct'] .= '&sJWT='.sys_jwt_encode($aValue);
		// 移除成員
		$aValue['a'] = 'KICKOUT';
		$aData['sKickOutUrl'] = $aUrl['sAct'] .= '&sJWT='.sys_jwt_encode($aValue);
		// 刪除對話紀錄
		$aValue['a'] = 'DELMSG';
		$aData['sDelMsgUrl'] = $aUrl['sAct'] .= '&sJWT='.sys_jwt_encode($aValue);

		// 刪除群組 (不使用)
		// $aValue['a'] = 'DELETEGROUP';
		// $aData['sDelGroupUrl'] = $aUrl['sAct'] .= '&sJWT='.sys_jwt_encode($aValue);
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		$aJumpMsg['0']['aButton']['0']['sClass'] = '';
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']);
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束

?>
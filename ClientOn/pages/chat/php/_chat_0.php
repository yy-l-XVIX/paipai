<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/chat.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/photon/Photon-Javascript_SDK.js',
		'1'	=> 'plugins/js/photon/Photon_Interface.js',
		'2'	=> 'plugins/js/BaseCmdLogic.js',
		'3'	=> 'plugins/js/Socket.js',
		'4'	=> 'plugins/js/chat/chat.js',
		'5'	=> 'plugins/js/EmojiInsert.js',
		'6'	=> 'plugins/js/SnoozeKeywords.js',
		'7'	=> 'plugins/js/FileWithDelete.js',
	);
	#js結束

	#參數接收區
	$nGid 	= filter_input_int('nGid',	INPUT_GET,0);
	$nFetch 	= filter_input_int('nFetch',	INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nGid,
		'sUpt'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_upt_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/chat/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/chat_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aMember = array();
	$aSearchId = array();
	$aBlockUid = myBlockUid($aUser['nId']);
	/**
	 * 回傳陣列 JSON
	 * @var Int nStatus
	 * 	回傳狀態值
	 * 	1 => 正常 其餘待補
	 * @var String sMsg
	 * 	回傳訊息
	 * @var Array aData
	 * 	回傳陣列
	 * @var Int nAlertType
	 * 	回傳訊息提示類型
	 * 	0 => 不需提示框
	 * @var String sUrl
	 * 	回傳後導頁檔案
	 */
	$aReturn = array(
		'nStatus'		=> 0,
		'sMsg'		=> 'Error',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> '',
	);
	$nLatestId = 0;
	$nGroupMember = 0;
	$sGroupName0 = '';
	$sJoinACT = '';
	$sDenyACT = '';
	$sAnnounce = '';
	$sTempA = isset($_COOKIE['a']) ? $_COOKIE['a'] : ''; // 檢查新公告用
	$aValue = array(
		'a'=> 'UPLOADFILE',
	);
	$sImgJWT = sys_jwt_encode($aValue);

	$aValue = array(
		'sBackUrl'=> $aUrl['sPage'],
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);

	$nErr = 0;
	$sErrMsg = '';
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';
	$aJumpMsg['1']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']);
	#宣告結束

	#程式邏輯區
	// 讓他講話
	if (isset($aJWT['a']) && $aJWT['a'] == $sTempA)
	{
		$sAnnounce = $aJWT['sMsg'];
		setcookie('a','',COOKIE['CLOSE']);
	}

	//檢查群組 (nType0 0單人 1群組)
	$sSQL = '	SELECT 	nId,
					nUid,
					nOnline,
					nType0,
					sName0,
					nUid,
					nTargetUid
			FROM 	'.CLIENT_GROUP_CTRL.'
			WHERE nId = :nId
			AND 	(nOnline > 0 AND nOnline < 99)
			AND 	nType1 = 0
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nGid, PDO::PARAM_INT);
	sql_query($Result);
	$aGroup = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aGroup === false)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	else
	{
		// 群組成元
		$sSQL = '	SELECT 	nId,
						nUid,
						nStatus,
						nCreateTime
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nUid']] = $aRows['nUid'];
			$aMember[$aRows['nUid']]['nId'] = $aRows['nUid'];
			$aMember[$aRows['nUid']]['nCreateTime'] = $aRows['nCreateTime'];
			$aMember[$aRows['nUid']]['nGroupStatus'] = $aRows['nStatus'];
			if ($aRows['nUid'] == $aUser['nId'] && $aRows['nStatus'] == 0)
			{
				$aValue = array(
					'a' 		=> 'JOIN'.$aUser['nId'],
					'nId'		=> $aRows['nId'],
				);
				$sJoinACT = $aUrl['sAct'].'&sJWT='.sys_jwt_encode($aValue);
				$aValue = array(
					'a'		=> 'DENY'.$aUser['nId'],
					'nId'		=> $aRows['nId'],
				);
				$sDenyACT = $aUrl['sAct'].'&sJWT='.sys_jwt_encode($aValue);
			}

			$nGroupMember++;

		}
		// 我不在群組內 離開
		if (!isset($aMember[$aUser['nId']]))
		{
			$nErr = 1;
			$sErrMsg = NODATA;
		}
		else if ($aMember[$aUser['nId']]['nGroupStatus'] == 1) //已參加群組者才查紀錄
		{
			// 聊天紀錄
			// 加入之前的訊息不給看 nCreateTime <= client_group_list.nCreatTime
			$sSQL = '	SELECT 	1
					FROM 	'.CLIENT_GROUP_MSG.'
					WHERE nGid = :nGid
					AND 	nOnline = 1
					AND 	nCreateTime >= :nCreateTime';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
			$Result->bindValue(':nCreateTime',	$aMember[$aUser['nId']]['nCreateTime'], PDO::PARAM_INT);
			sql_query($Result);
			$aPage['nDataAmount'] = $Result->rowCount();
			$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
			if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
			{
				$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
			}

			$sSQL = '	SELECT	nId,
							nUid,
							sMsg,
							sCreateTime
					FROM		'.CLIENT_GROUP_MSG.'
					WHERE	nGid = :nGid
					AND 	nOnline = 1
					AND 	nCreateTime >= :nCreateTime
					ORDER	BY nCreateTime DESC
					'.sql_limit($nPageStart, $aPage['nPageSize']);
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
			$Result->bindValue(':nCreateTime',	$aMember[$aUser['nId']]['nCreateTime'], PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aRows['sMsg'] = convertContent($aRows['sMsg']);
				$aData[$aRows['nId']] = $aRows;
				$aData[$aRows['nId']]['sInfUrl'] = $aUrl['sInf'].'&nId='.$aRows['nUid']; #'javascript:void(0)';

				if ($nLatestId == 0)
				{
					$nLatestId = $aRows['nId'];
				}
				$aSearchId[$aRows['nUid']] = $aRows['nUid'];
			}
			$aData = array_reverse($aData);
		}
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						nKid,
						sName0,
						sAccount,
						sPassword
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aSearchId).' )
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMember[$aRows['nId']]['nKid'] = $aRows['nKid'];
			$aMember[$aRows['nId']]['sName0'] = $aRows['sName0'];
			$aMember[$aRows['nId']]['sAccount'] = $aRows['sAccount'];

			$aMember[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			$aMember[$aRows['nId']]['sRole'] = 'staff';
			if ($aRows['nKid'] == 1)
			{
				$aMember[$aRows['nId']]['sRole'] = 'boss';
			}
			if ($aUser['nId'] == $aRows['nId'])
			{
				$aMember[$aRows['nId']]['sPassword'] = $aRows['sPassword'];
				$aValue = array(
					'a'		=> 'LOGIN',
					'nGid'	=> $nGid,		// 群組id
					'nUid'	=> $aUser['nId'],
					'sAccount' 	=> $aUser['sAccount'],
					'sPassword'	=> $aRows['sPassword'],
				);
				$aMember[$aRows['nId']]['sToken'] = sys_jwt_encode($aValue);
			}
		}

		// 我的好友(自行設定暱稱)
		$sSQL = '	SELECT 	nFUid,
						sName0
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nUid
				AND 	sName0 != \'\'
				AND 	nFUid IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMember[$aRows['nFUid']]['sName0'] = $aRows['sName0'];
		}

		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', $aSearchId).' ) ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMember[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	if ($aGroup['nType0'] == 0)
	{
		// 私聊呈現另一個會員名稱
		if ($aGroup['nUid'] != $aUser['nId'])
		{
			$aGroup['sName0'] = $aMember[$aGroup['nUid']]['sName0'];
		}
		if ($aGroup['nTargetUid'] != $aUser['nId'])
		{
			$aGroup['sName0'] = $aMember[$aGroup['nTargetUid']]['sName0'];
		}
		$sGroupName0 = $aGroup['sName0'];
	}
	else
	{
		// 群組顯示人數
		$sGroupName0 = $aGroup['sName0'] . ' ( '.$nGroupMember.' )';
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['1']['sMsg'] = $sErrMsg;
		$aJumpMsg['1']['sShow'] = 1;
	}
	else
	{
		$aJumpMsg['1']['aButton']['0']['sUrl'] = $aUrl['sPage'];
		setcookie('aGroup['.$nGid.']',$nLatestId,(NOWTIME + 3600*24*360));
		$aRequire['Require'] = $aUrl['sHtml'];
		if ($nFetch == 1)
		{
			// sInfUrl sHeadImage sName0 sMsg sCreateTime
			foreach ($aData as $LPnId => $LPaDetail)
			{
				$LPaDetail['sHeadImage'] = $aMember[$LPaDetail['nUid']]['sHeadImage'];
				$LPaDetail['sName0'] = $aMember[$LPaDetail['nUid']]['sName0'];
				$aReturn['aData']['aData'][] = $LPaDetail;
			}
			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = 'success'.sizeof($aData);
			$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];
			echo json_encode($aReturn);
			exit;
		}
	}
	#輸出結束
?>
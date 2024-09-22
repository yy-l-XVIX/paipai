<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/chat/chat_group_announce.js',
	);
	#js結束

	#參數接收區
	$nId = filter_input_int('nId',	INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_upt_0.php']).'&nGid='.$nId,
		'sAct'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_upt_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/chat/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/chat_group_announce_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'nId'		  => 0,
		'sContent0'	  => '',
		'sUpdateTime' => '',
	);
	$aMemberData = array();
	$nUid = 0;
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = '';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';

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
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows === false)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	else
	{
		$nUid = $aRows['nUid'];

		// 公告內容
		$sSQL = '	SELECT 	nId,
						sContent0,
						sUpdateTime
				FROM 	'.CLIENT_GROUP_ANNOUNCE.'
				WHERE nGid = :nGid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows !== false)
		{
			$aData = $aRows;
		}

		$aData['sActUrl'] = '';
		if ($nUid == $aUser['nId'])
		{
			$aValue = array(
				'a'	=> 'ANNOUNCE',
				'nGid'=> $nId
			);
			$aData['sActUrl'] = $aUrl['sAct'].'&sJWT='.sys_jwt_encode($aValue);
		}

		// 建立者資訊
		$sSQL = '	SELECT 	nId,
						sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $nUid, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData = $aRows;
			$aMemberData['sImgUrl'] = DEFAULTHEADIMG;
		}

		// 會員頭像
		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		$Result->bindValue(':nUid', $nUid, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}

		// 我的好友(更換為自行設定暱稱)
		$sSQL = '	SELECT 	nFUid,
						sName0
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nMyUid
				AND 	sName0 != \'\'
				AND 	nFUid = :nFUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nMyUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nFUid', $nUid, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData['sName0'] = $aRows['sName0'];
		}
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
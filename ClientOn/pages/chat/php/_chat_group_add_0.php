<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/chat_group_add.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/chat/chat_group_add.js',
	);
	#js結束

	#參數接收區
	$nId 		= filter_input_int('nId',		INPUT_GET,0);
	$nGroupEdit = filter_input_int('nGroupEdit',	INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_add_0_act0.php']),
		'sHtml'	=> 'pages/chat/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/chat_group_add_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBlockUid = myBlockUid($aUser['nId']);
	$aNotSearchMember = array();
	$sName0 = '';
	$sCondition = '';
	$sFriend = '0';
	$nErr = 0;
	$sErrMsg = '';
	$aValue = array(
		'a'		=> 'INS',
	);
	$sJWT = sys_jwt_encode($aValue);

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';
	#宣告結束

	#程式邏輯區
	if ($nId != 0 && $nGroupEdit == 1) // 編輯群組
	{
		$sSQL = '	SELECT 	nId,
						sName0
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	(nOnline > 0 AND nOnline < 99)
				AND 	nType1 = 0
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr = 1;
			$sErrMsg = NODATA;
		}

		$sName0 = $aRows['sName0'];
		$aValue['a'] = 'UPT'.$nId;
		$sJWT = sys_jwt_encode($aValue);

		// 群組內成員
		$sSQL = '	SELECT 	nUid
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aNotSearchMember[$aRows['nUid']] = $aRows['nUid'];
		}
	}
	$aNotSearchMember = $aNotSearchMember+$aBlockUid;
	// 封鎖名單不顯示

	if (!empty($aNotSearchMember))
	{
		$sCondition = ' AND nFUid NOT IN ( '.implode(',', $aNotSearchMember).' ) ';
	}

	// 我的好友 (不包含 封鎖好友 & 已在群組內成員)
	$sSQL = '	SELECT	nId,
					nFUid
			FROM		'.CLIENT_USER_FRIEND.'
			WHERE		nUid = :nUid
			AND		nStatus = 1
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nFUid']] = $aRows;
		$aData[$aRows['nFUid']]['sName0'] = '';
		$aData[$aRows['nFUid']]['sImgUrl'] = DEFAULTHEADIMG;
		$sFriend .= ','.$aRows['nFUid'];
	}
	// 會員資訊
	$sSQL = '	SELECT	nId,
					sName0,
					nKid
			FROM	'.CLIENT_USER_DATA.'
			WHERE	nId IN ( '. $sFriend .' )
			AND 	nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']]['sName0'] = $aRows['sName0'];
		$aData[$aRows['nId']]['sRole'] = '';
		if ($aRows['nKid'] == 1)
		{
			$aData[$aRows['nId']]['sRole'] = 'boss';
		}
	}

	// 好友暱稱
	// 我的好友(自行設定暱稱)
	$sSQL = '	SELECT 	nFUid,
					sName0
			FROM 	'.CLIENT_USER_FRIEND.'
			WHERE nUid = :nUid
			AND 	sName0 != \'\'
			AND 	nFUid IN ('. $sFriend .')';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nFUid']]['sName0'] = $aRows['sName0'];
	}

	$sSQL = '	SELECT 	nId,
					nKid,
					sFile,
					sTable,
					nCreateTime
			FROM 	'.CLIENT_IMAGE_CTRL.'
			WHERE sTable LIKE :sTable
			AND 	nKid IN ( '. $sFriend .' )';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nKid']]['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['1']['sMsg'] = $sErrMsg;
		$aJumpMsg['1']['sShow'] = 1;
		$aJumpMsg['1']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']);
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}

	#輸出結束
?>
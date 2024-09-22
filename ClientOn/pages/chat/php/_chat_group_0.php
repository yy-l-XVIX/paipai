<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/chat_group.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/chat/chat_group.js',
	);
	#js結束

	#參數接收區
	$nFetch	= filter_input_int('nFetch',	INPUT_REQUEST,0);
	$sName0 	= filter_input_str('sName0',	INPUT_REQUEST, '');
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']),
		'sFetch'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']).'&nFetch=1&sName0='.$sName0,
		'sGroupAdd'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_add_0.php']),
		'sChat'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']),
		'sChatAct'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/chat/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/chat_group_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aMyGroupId = array();
	$aTempData = array();
	$aSortData = array();
	$aMemberData = array();
	$aBindArray = array();
	$aSearchId = array();
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
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> '',
	);
	$sCondition = '';
	$aPage['nPageSize'] = 10;
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];


	$aJumpMsg['0']['sMsg'] = aCHAT['DELCONFIRM'];
	$aJumpMsg['0']['nClicktoClose'] = 0;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqSelfDel JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = 'javascript:void(0);';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	#宣告結束

	#程式邏輯區
	// 我的聊天群組
	$sSQL = '	SELECT	nGid
			FROM	'.CLIENT_USER_GROUP_LIST.'
			WHERE nUid = :nUid
			AND 	nStatus < 2
			AND	nGid IN ( SELECT nId FROM '.CLIENT_GROUP_CTRL.' WHERE nType1 = 0)';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aMyGroupId[$aRows['nGid']] = $aRows['nGid'];
	}

	if (!empty($aMyGroupId))
	{
		if ($sName0 != '')
		{
			// 符合暱稱的會員id
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_USER_DATA.'
					WHERE sName0 LIKE :sName0
					AND 	nOnline = 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sName0', '%'.$sName0.'%', PDO::PARAM_STR);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aSearchId[$aRows['nId']] = $aRows['nId'];
			}
			// 符合暱稱的好友uid
			$sSQL = '	SELECT 	nFUid
					FROM 	'.CLIENT_USER_FRIEND.'
					WHERE sName0 LIKE :sName0
					AND 	nUid = :nUid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sName0', '%'.$sName0.'%', PDO::PARAM_STR);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aSearchId[$aRows['nFUid']] = $aRows['nFUid'];
			}
			if (!empty($aSearchId))
			{
				$sSearch = implode(',',$aSearchId);
				$aSearchId = array();
				// 符合的人所在群組
				$sSQL = '	SELECT 	nGid
						FROM 	'.CLIENT_USER_GROUP_LIST.'
						WHERE nUid IN ('.$sSearch.')
						AND 	nGid IN ('.implode(',',$aMyGroupId).')';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
				{
					$aSearchId[$aRows['nGid']] = $aRows['nGid'];
				}
			}

			if (!empty($aSearchId))
			{
				$sCondition = ' AND (sName0 LIKE :sName0 OR nId IN ('.implode(',',$aSearchId).'))';
			}
			else
			{
				$sCondition = ' AND sName0 LIKE :sName0 ';
			}
			$aBindArray['sName0'] = '%'.$sName0.'%';

			$aSearchId = array();
		}

		$sSQL = '	SELECT 	1
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nType1 = 0
				AND 	nOnline IN (1,2)
				'.$sCondition.'
				AND nId IN ('.implode(',',$aMyGroupId).')';
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aBindArray);
		sql_query($Result);
		$aPage['nDataAmount'] = $Result->rowCount();
		$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
		if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
		{
			$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
		}

		// 我的聊天群組+群組資訊(名稱 種類 建立人 私聊對象)
		$sSQL = '	SELECT 	nId,
						sName0,
						nUid,
						nTargetUid,
						nType0,
						nOnline
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nType1 = 0
				AND 	nOnline IN (1,2)
				'.$sCondition.'
				AND nId IN ('.implode(',',$aMyGroupId).')
				'.sql_limit($nPageStart, $aPage['nPageSize']);
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aBindArray);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aTempData[$aRows['nId']] = $aRows;
			$aTempData[$aRows['nId']]['sInsUrl'] = $aUrl['sChat'].'&nGid='.$aRows['nId'];
			$aTempData[$aRows['nId']]['aMember'] = array();
			$aTempData[$aRows['nId']]['sSelfNotice'] = '';

			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}
	}

	if (!empty($aSearchId))
	{
		// 群組內的成員
		$sSQL = '	SELECT	nId,
						nGid,
						nUid,
						nStatus
				FROM	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid IN ( '.implode(',',$aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			// 呈現頭像用
			if (sizeof($aTempData[$aRows['nGid']]['aMember']) < 10)
			{
				$aMemberData[$aRows['nUid']] = array();
				if (($aRows['nUid'] != $aUser['nId'] && $aTempData[$aRows['nGid']]['nType0'] == 0) || $aTempData[$aRows['nGid']]['nType0'] == 1)
				{
					// 私人群組呈現對方頭像
					$aTempData[$aRows['nGid']]['aMember'][] = $aRows['nUid'];
				}

			}
			if ($aRows['nUid'] == $aUser['nId'])
			{
				if ($aRows['nStatus'] == 0)
				{
					$aTempData[$aRows['nGid']]['sSelfNotice'] = 'active';
				}

				// 刪除群組訊息
				$aValue = array(
					'a' 	=> 'SELFDELGROUP',
					'nId'	=> $aRows['nId'],
				);
				$aTempData[$aRows['nGid']]['sDelUrl'] = $aUrl['sChatAct'].'&sJWT='.sys_jwt_encode($aValue);
			}
		}

		// 群組根據訊息時間排序
		$sSQL = '	SELECT 	nGid
				FROM 	'.CLIENT_GROUP_MSG.'
				WHERE nGid IN ( '.implode(',',$aSearchId).' )
				ORDER BY nCreateTime DESC';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			if (!in_array($aRows['nGid'], $aSortData))
			{
				$aSortData[] = $aRows['nGid'];
				unset($aSearchId[$aRows['nGid']]);
			}
		}
		// 沒聊天過補最後
		foreach ($aSearchId as $LPnGid)
		{
			$aSortData[] = $LPnGid;
		}
	}

	if (!empty($aMemberData))
	{
		// 會員資訊(暱稱 )
		$sSQL = '	SELECT 	nId,
						sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', array_keys($aMemberData)).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			// // 我的好友
			// if (isset($aMyFriend[$aRows['nId']]))
			// {
			// 	// 替換成自己設定的暱稱
			// 	$aRows['sName0'] = $aMyFriend[$aRows['nId']];
			// }
			$aMemberData[$aRows['nId']] = $aRows;
			$aMemberData[$aRows['nId']]['sImgUrl'] = DEFAULTHEADIMG;
		}

		// 我的好友(自行設定暱稱)
		$sSQL = '	SELECT 	nFUid,
						sName0
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nUid
				AND 	sName0 != \'\'
				AND 	nFUid IN ( '.implode(',', array_keys($aMemberData)).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nFUid']]['sName0'] = $aRows['sName0'];
		}

		// 會員頭像
		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', array_keys($aMemberData)).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nKid']]['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	// 處理前台呈現
	foreach ($aSortData as $LPnGid)
	{
		if (!isset($aTempData[$LPnGid]))
		{
			continue;
		}

		$aData[$LPnGid] = $aTempData[$LPnGid];
		$LPaDetail = $aTempData[$LPnGid];

		if ($LPaDetail['nType0'] == 0)
		{
			// 私聊呈現另一個會員名稱
			if ($LPaDetail['nUid'] != $aUser['nId'])
			{
				$aData[$LPnGid]['sName0'] = $aMemberData[$LPaDetail['nUid']]['sName0'];
			}
			if ($LPaDetail['nTargetUid'] != $aUser['nId'])
			{
				$aData[$LPnGid]['sName0'] = $aMemberData[$LPaDetail['nTargetUid']]['sName0'];
			}
		}

		// 群組頭像表格處理
		$LPsGroupImgHtml = '';
		$LPnPeople = sizeof($LPaDetail['aMember']);
		$LPaTableSet = array(
			'sHeight'		=> 'height:48px;',
			'nFolderCol'	=> 1,
			'nFolderTotal'	=> $LPnPeople,
		);

		if ($LPnPeople > 1)
		{
			$LPaTableSet['nFolderCol'] = 2;
		}
		if ($LPnPeople == 3)
		{
			$LPaTableSet['nFolderTotal'] = 4; // 補空白
			$LPaTableSet['sHeight'] = 'height:24px;';
		}
		if ($LPnPeople >= 4 && $LPnPeople <= 8)
		{
			$LPaTableSet['nFolderTotal'] = 4;
			$LPaTableSet['sHeight'] = 'height:24px;';
		}
		if ($LPnPeople >= 9)
		{
			$LPaTableSet['nFolderCol'] = 3;
			$LPaTableSet['nFolderTotal'] = 9;
			$LPaTableSet['sHeight'] = 'height:16px;';
		}

		for ($i=1; $i<=$LPaTableSet['nFolderTotal']; $i++)
		{
			$LPsHeadImgUrl = '';
			if (isset($LPaDetail['aMember'][($i-1)]))
			{
				$LPsHeadImgUrl = $aMemberData[$LPaDetail['aMember'][($i-1)]]['sImgUrl'];
			}
			if($i % $LPaTableSet['nFolderCol'] == 1)
			{
				$LPsGroupImgHtml .= '<tr>';
			}
			$LPsGroupImgHtml .= '<td style="width:calc(100%/'.$LPaTableSet['nFolderCol'].');">';
			$LPsGroupImgHtml .= '<div class="chatGroupFolderPic BG" style="'.$LPaTableSet['sHeight'].'background-image: url('.$LPsHeadImgUrl.');"></div>';
			$LPsGroupImgHtml .= '</td>';
			if($i % $LPaTableSet['nFolderCol'] == 0)
			{
				$LPsGroupImgHtml .= '</tr>';
			}
		}
		if($LPaTableSet['nFolderTotal'] % $LPaTableSet['nFolderCol'] != 0)
		{
			for($nAdd=1; $nAdd<=($LPaTableSet['nFolderCol']-($LPaTableSet['nFolderTotal'] % $LPaTableSet['nFolderCol'])); $nAdd++)
			{
				$LPsGroupImgHtml .= '<td style="width:calc(100%/'.$LPaTableSet['nFolderCol'].');"></td>';
			}
			$LPsGroupImgHtml .= '</tr>';
		}

		$aData[$LPnGid]['sGroupImgHtml'] = $LPsGroupImgHtml;

		if ($nFetch == 1)
		{
			$aData[$LPnGid]['sGroupImgHtml'] = '<table class="chatGroupFolderTable"><tbody>'.$LPsGroupImgHtml.'</tbody></table>';
			$aReturn['aData']['aData'][] = $aData[$LPnGid];
		}
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	if ($nFetch == 1)
	{
		$aReturn['nStatus'] = 1;
		$aReturn['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
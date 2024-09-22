<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/chat_group_add.php');

	$nId 			= filter_input_int('nId',		INPUT_POST, 0);
	$sName0 		= filter_input_str('sName0',		INPUT_POST, '',50);
	$sSelectFriend 	= filter_input_str('sSelectFriend',	INPUT_POST, '');

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
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> '',
	);
	$aEditLog = array();
	$aFriend = array();
	$aBlockUid = myBlockUid($aUser['nId']);
	$nGroupMen = 0;

	if ($aJWT['a'] == 'INS')
	{
		if ($sName0 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['NAME0'];
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nUid' 		=> (int) $aUser['nId'],
				'sName0' 		=> (string) $sName0,
				'nOnline' 		=> (int) 1,
				'nType0' 		=> (int) 1,
				'nType1' 		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.CLIENT_GROUP_CTRL.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = array();
			$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew']['nId'] = $nLastId;

			// 選擇的friend是不是我的friend
			$sSQL = '	SELECT 	nFUid
					FROM 	'.CLIENT_USER_FRIEND.'
					WHERE nUid = :nUid
					AND 	nFUid IN ( '.$sSelectFriend.' )';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				if (isset($aBlockUid[$aRows['nFUid']]))
				{
					continue;
				}
				$aFriend[$aRows['nFUid']] = true;
			}
			// 自己
			$aFriend[$aUser['nId']] = true;
			// friend加入群組
			foreach ($aFriend as $LPnUid => $LPbTrue)
			{
				if($LPnUid == 0 )
				{
					continue;
				}

				$oPdo->beginTransaction();

				$aSQL_Array = array(
					'nGid'		=> (int) $nLastId,
					'nUid'		=> (int) $LPnUid,
					'nStatus'		=> (int) ($LPnUid == $aUser['nId']) ? 1 : 0, // 0=>待同意
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	INSERT INTO '.CLIENT_USER_GROUP_LIST.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);
				$LPnLastId = $oPdo->lastInsertId();

				$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'][$LPnLastId] = $aSQL_Array;
				$oPdo->commit();

				$nGroupMen ++;
				if ($aSystem['aParam']['nMaxGroupMen'] == $nGroupMen)
				{
					// 到達人數上限
					break;
				}
			}
			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = array();

			// 紀錄動作
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100901,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = INSV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nLastId;
		}
	}

	if ($aJWT['a'] == 'UPT'.$nId) // 多人
	{
		$sSQL = '	SELECT 	nId,
						nType0,
						sName0
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nOnline = 1
				AND 	nType1 = 0
				AND 	nType0 = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}
		if ($aRows['nType0'] == 1 && $sName0 == '') // 多人才可以編輯群組名稱
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['NAME0'];
		}

		//群組人數
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$nGroupMen++;
		}
		if ($sSelectFriend != '0' && $nGroupMen == $aSystem['aParam']['nMaxGroupMen'])
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['MAXMEN'].$aSystem['aParam']['nMaxGroupMen'].aGROUP['MEN'];
		}

		if ($aReturn['nStatus'] == 1)
		{
			// 更新群組名稱
			if ($aRows['sName0'] != $sName0)
			{
				$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aRows;

				$aSQL_Array = array(
					'sName0' 		=> (string) $sName0,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	UPDATE '.CLIENT_GROUP_CTRL.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
						WHERE	nId = :nId LIMIT 1 ';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
			}

			if ($nGroupMen < $aSystem['aParam']['nMaxGroupMen'])
			{

				// 選擇的friend是不是我的friend
				$sSQL = '	SELECT 	nFUid
						FROM 	'.CLIENT_USER_FRIEND.'
						WHERE nUid = :nUid
						AND 	nFUid IN ( '.$sSelectFriend.' )';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				sql_query($Result);
				while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
				{
					if (isset($aBlockUid[$aRows['nFUid']]))
					{
						continue;
					}
					$aFriend[$aRows['nFUid']] = true;
				}
				// friend進入群組
				foreach ($aFriend as $LPnUid => $LPbTrue)
				{
					if($LPnUid == 0 )
					{
						continue;
					}

					$oPdo->beginTransaction();

					$aSQL_Array = array(
						'nGid'		=> (int) $nId,
						'nUid'		=> (int) $LPnUid,
						'nStatus'		=> (int) ($LPnUid == $aUser['nId']) ? 1 : 0, // 0=>待同意,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);

					$sSQL = '	INSERT INTO '.CLIENT_USER_GROUP_LIST.' '.sql_build_array('INSERT', $aSQL_Array);
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result,$aSQL_Array);
					sql_query($Result);
					$LPnLastId = $oPdo->lastInsertId();

					$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'][$LPnLastId] = $aSQL_Array;

					$oPdo->commit();

					$nGroupMen ++;
					if ($aSystem['aParam']['nMaxGroupMen'] == $nGroupMen)
					{
						// 到達人數上限
						break;
					}
				}
			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100902,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = UPTV;
			// $aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']);
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nId;
		}
	}

	echo json_encode($aReturn);
	exit;
?>
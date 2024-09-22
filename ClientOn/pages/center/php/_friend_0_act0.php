<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/friend.php');

	$nId	= filter_input_int('nId',	INPUT_GET, 0);
	$nUid	= filter_input_int('nUid',	INPUT_GET, 0);
	$sName0 = filter_input_str('sName0',INPUT_GET, '',50);
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
		'sUrl'		=> ''
	);

	// 同意好友邀請
	if ($aJWT['a'] == 'AGREEFRIEND'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']);

		// 查是否已經有被邀請
		$sSQL = '	SELECT	nId,
						nUid,
						nFUid
				FROM		'.CLIENT_USER_FRIEND.'
				WHERE		nUid = :nUid
				AND		nId = :nId
				AND		nStatus = 0
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['sMsg'] = NODATA;

		}
		else
		{
			$aOld = $aRows;

			$aSQL_Array = array(
				'nStatus'		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE	'.CLIENT_USER_FRIEND.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_FRIEND]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aOld['nFUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100003,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);


			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = aFRIEND['AGREE'];

		}
		echo json_encode($aReturn);
		exit;
	}

	// 拒絕好友邀請
	if ($aJWT['a'] == 'DENYFRIEND'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']);

		// 查是否已經有被邀請
		$sSQL = '	SELECT	nId,
						nUid,
						nFUid
				FROM		'.CLIENT_USER_FRIEND.'
				WHERE		nUid = :nUid
				AND		nId = :nId
				AND		nStatus = 0
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['sMsg'] = NODATA;

		}
		else
		{
			$aOld = $aRows;

			$aSQL_Array = array(
				'nStatus'		=> (int) 2,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE	'.CLIENT_USER_FRIEND.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_FRIEND]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aOld['nFUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100004,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);


			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = aFRIEND['DENY'];

		}
		echo json_encode($aReturn);
		exit;
	}

	// 刪好友
	if ($aJWT['a'] == 'DELFRIEND'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_friend_0_upt0.php']);

		// 查是否已經是朋友
		$sSQL = '	SELECT	nId,
						nUid,
						nFUid
				FROM		'.CLIENT_USER_FRIEND.'
				WHERE		nUid = :nUid
				AND		nId = :nId
				AND		nStatus = 1
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['sMsg'] = NODATA;

		}
		else
		{
			$aOld = $aRows;

			$sSQL = '	DELETE FROM	'. CLIENT_USER_FRIEND . '
					WHERE 	nId = :nId
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aOld['nId'], PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_USER_FRIEND]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_FRIEND]['aNew'] = array();

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aOld['nFUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100005,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);


			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = DELV;

		}
		echo json_encode($aReturn);
		exit;
	}

	// 聊天
	if ($aJWT['a'] == 'CHECKCHAT')
	{
		$sSQL = '	SELECT 	1
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nUid
				AND 	nFUid = :nFUid
				AND 	nStatus != 2';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nFUid', $nUid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}
		else
		{
			$aReturn['nStatus'] = 1;
		}

		if ($aReturn['nStatus'] == 1)
		{
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_GROUP_CTRL.'
					WHERE nType0 = 0
					AND	nType1 = 0
					AND 	nOnline > 0 AND nOnline < 99
					AND 	((nUid = :nUid AND nTargetUid = :nFUid)
					OR 	(nUid = :nFUid AND nTargetUid = :nUid))
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			$Result->bindValue(':nFUid', $nUid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false) // 沒有聊天過 建立新群組
			{
				$sSQL = '	SELECT 	sName0
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId = :nId
						AND 	nOnline = 1
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nUid, PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);

				# 建立新群
				$aSQL_Array = array(
					'sName0'		=> (string) $aUser['sName0'].','.$aRows['sName0'],
					'nUid'		=> (int) $aUser['nId'],
					'nTargetUid'	=> (int) $nUid,
					'nOnline'		=> (int) 1,
					'nType0'		=> (int) 0, // 0單人 1多人
					'nType1'		=> (int) 0, // 0聊天 1工作
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	INSERT INTO '.CLIENT_GROUP_CTRL.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);
				$nGid = $oPdo->lastInsertId();

				$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = array();
				$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_GROUP_CTRL]['aNew']['nId'] = $nGid;

				// 建立群組內紀錄
				$aSQL_Array = array(
					'nGid'		=> (int) $nGid,
					'nUid'		=> (int) $aUser['nId'],
					'nStatus'		=> (int) 1,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	INSERT INTO '.CLIENT_USER_GROUP_LIST.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = array();
				$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'][] = $aSQL_Array;

				$aSQL_Array = array(
					'nGid'		=> (int) $nGid,
					'nUid'		=> (int) $nUid,
					'nStatus'		=> (int) 1,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	INSERT INTO '.CLIENT_USER_GROUP_LIST.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'][] = $aSQL_Array;

				$aActionLog = array(
					'nWho'		=> (int) $aUser['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nGid,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 7100006,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);
			}
			else
			{
				$nGid = $aRows['nId'];
				// 檢查自己是否在群組
				$sSQL = '	SELECT	nId,
								nStatus
						FROM 	'.CLIENT_USER_GROUP_LIST.'
						WHERE nGid = :nGid
						AND	nUid = :nUid
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);
				if ($aRows === false)
				{
					// 否 => 建立自己在群組紀錄 前往聊天群組
					$aSQL_Array = array(
						'nGid'		=> (int) $nGid,
						'nUid'		=> (int) $aUser['nId'],
						'nStatus'		=> (int) 1,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					$sSQL = '	INSERT INTO '.CLIENT_USER_GROUP_LIST.' '.sql_build_array('INSERT', $aSQL_Array);
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result,$aSQL_Array);
					sql_query($Result);
				}
				else if ($aRows['nStatus'] == 2)
				{
					$aSQL_Array = array(
						'nStatus'		=> (int) 1,
						'nUpdateTime'	=> (int) NOWTIME,
						'sUpdateTime'	=> (string) NOWDATE,
					);

					$sSQL = '	UPDATE '.CLIENT_USER_GROUP_LIST.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE	nId =:nId AND nStatus = 2';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
				}
			}

			$aReturn['nStatus'] = 1;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nGid;
		}

		echo json_encode($aReturn);
		exit;
	}

	// 編輯暱稱
	if ($aJWT['a'] == 'UPTNAME')
	{
		$nId = $aJWT['nId'];
		$sSQL = '	SELECT 	nId,
						nFUid,
						sName0
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nUid
				AND 	nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);

		$sSQL = '	SELECT	sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aOld['nFUid'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);

		$aReturn['aData']['sName0'] = $sName0;
		if ($sName0 == '')
		{
			$aReturn['aData']['sName0'] = $aRows['sName0'];
		}

		if ($aOld !== false && $sName0 != $aOld['sName0'])
		{
			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'nUpdateTime'	=> (int)	NOWTIME,
				'sUpdateTime'	=> (string)	NOWDATE,
			);
			$sSQL = '	UPDATE	'.CLIENT_USER_FRIEND.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_FRIEND]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aOld['nFUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100007,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = UPTV;
		}

		echo json_encode($aReturn);
		exit;
	}
?>
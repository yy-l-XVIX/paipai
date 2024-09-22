<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/chat_group_upt.php');

	$nUid 	= filter_input_int('nUid',		INPUT_GET, 0);
	$sContent0	= filter_input_str('sContent0',	INPUT_POST,'');

	$nGid = $aJWT['nGid'];

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
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']),
	);
	$aEditLog = array();
	$aMember = array();

	// 自己離開群組 (delete from client_user_group_list)
	if ($aJWT['a'] == 'EXITGROUP')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nUid = :nUid
				AND 	nGid = :nGid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}

		if ($aReturn['nStatus'] == 1)
		{
			$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
					WHERE nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
			sql_query($Result);

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aRows['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100903,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$aReturn['sMsg'] = aGROUP['LEAVESUCCESS'];
		}
	}

	// 刪除群組(建立者才可以刪除) 不使用此功能
	// if ($aJWT['a'] == 'DELETEGROUP')
	// {
	// 	$sSQL = '	SELECT 	nId,
	// 					sName0,
	// 					nOnline
	// 			FROM 	'.CLIENT_GROUP_CTRL.'
	// 			WHERE nId = :nId
	// 			AND 	nUid = :nUid
	// 			AND 	nOnline = 1
	// 			AND 	nType1 = 0
	// 			LIMIT 1';
	// 	$Result = $oPdo->prepare($sSQL);
	// 	$Result->bindValue(':nId', $nGid, PDO::PARAM_INT);
	// 	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	// 	sql_query($Result);
	// 	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	// 	if ($aRows === false)
	// 	{
	// 		$aReturn['nStatus'] = 0;
	// 		$aReturn['sMsg'] = NODATA;
	// 	}
	// 	$sSQL = '	SELECT	nId,
	// 					nGid,
	// 					nUid,
	// 					nStatus
	// 			FROM	'.	CLIENT_USER_GROUP_LIST .'
	// 			WHERE		nGid = :nGid';
	// 	$Result = $oPdo->prepare($sSQL);
	// 	$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
	// 	sql_query($Result);
	// 	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	// 	{
	// 		$aMember[$aRows['nId']] = $aRows;
	// 	}

	// 	if ($aReturn['nStatus'] == 1)
	// 	{
	// 		// update client_group_ctrl to 99
	// 		$aSQL_Array = array(
	// 			'nOnline' 		=> (int) 99,
	// 			'nUpdateTime'	=> (int) NOWTIME,
	// 			'sUpdateTime'	=> (string) NOWDATE,
	// 		);
	// 		$sSQL = '	UPDATE '.CLIENT_GROUP_CTRL.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
	// 				WHERE	nId = :nId LIMIT 1 ';
	// 		$Result = $oPdo->prepare($sSQL);
	// 		$Result->bindValue(':nId', $nGid, PDO::PARAM_INT);
	// 		sql_build_value($Result,$aSQL_Array);
	// 		sql_query($Result);

	// 		$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aRows;
	// 		$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;

	// 		// delete this group member from client_user_group_list
	// 		$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
	// 				WHERE nGid = :nGid';
	// 		$Result = $oPdo->prepare($sSQL);
	// 		$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
	// 		sql_query($Result);

	// 		$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = $aMember;
	// 		$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = array();

	// 		$aActionLog = array(
	// 			'nWho'		=> (int) $aUser['nId'],
	// 			'nWhom'		=> (int) 0,
	// 			'sWhomAccount'	=> (string) '',
	// 			'nKid'		=> (int) $aRows['nId'],
	// 			'sIp'			=> (string) USERIP,
	// 			'nLogCode'		=> (int) 7100904,
	// 			'sParam'		=> (string) json_encode($aEditLog),
	// 			'nType0'		=> (int) 0,
	// 			'nCreateTime'	=> (int) NOWTIME,
	// 			'sCreateTime'	=> (string) NOWDATE,
	// 		);
	// 		DoActionLog($aActionLog);
	// 		$aReturn['sMsg'] = aGROUP['DELSUCCESS'];
	// 	}
	// }

	// 踢出群組(建立者才可以踢人 delete from client_user_group_list)
	if ($aJWT['a'] == 'KICKOUT')
	{
		$aReturn['sUrl'] = '';
		$nGid = $aJWT['nGid'];

		// 檢查是不是自己建的工作
		$sSQL = '	SELECT 	nId,
						nUid,
						nType0
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nUid = :nUid
				AND 	nType1 = 0
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nGid,PDO::PARAM_INT);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA.'<br>';
		}
		else if ($aRows['nType0'] == 0) // 私聊無法踢出人員
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA.'<br>';
		}
		// 會員是否在群組內
		$sSQL = '	SELECT 	nId,
						nUid,
						nGid,
						sCreateTime
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid
				AND 	nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOld === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['NOTHISMEMBER'].'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			// 從群組移除
			$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
					WHERE nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aOld['nId'], PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = array();

			// 紀錄log
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $nUid,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100907,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();

			$sSQL = '	SELECT 	nFUid,
							sName0
					FROM 	'.CLIENT_USER_FRIEND.'
					WHERE nUid = :nUid
					AND 	sName0 != \'\'
					AND 	nFUid = :nFUid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			$Result->bindValue(':nFUid',$nUid,PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false)
			{
				$aReturn['sMsg'] = str_replace('[::sName0::]',$aRows['sName0'],aGROUP['SUCCESSKICK']); // 成功將 [::sName0::] 退出群組
			}
			else
			{
				$sSQL = '	SELECT 	sName0
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId = :nId
						AND 	nOnline = 1
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId',$nUid,PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);

				$aReturn['sMsg'] = str_replace('[::sName0::]',$aRows['sName0'],aGROUP['SUCCESSKICK']); // 成功將 [::sName0::] 退出群組
			}
		}
	}

	// 刪除聊天紀錄
	if ($aJWT['a'] == 'DELMSG')
	{
		$sSQL = '	SELECT	nId,
						nUid,
						nStatus,
						nCreateTime
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nUid = :nUid
				AND 	nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aJWT['nGLid'], PDO::PARAM_INT);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nCreateTime' => (int) NOWTIME,
				'sCreateTime' => (string) NOWDATE,
				'nUpdateTime' => (int) NOWTIME,
				'sUpdateTime' => (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_USER_GROUP_LIST.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aJWT['nGLid'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = $aRows;
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = $aSQL_Array;

			// 紀錄動作
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aJWT['nGLid'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100908,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$aReturn['sMsg'] = DELV;
			$aReturn['sUrl'] = '';
		}
	}

	// 更新群組公告(群聊才有此功能)
	if ($aJWT['a'] == 'ANNOUNCE')
	{
		// 檢查是不是自己建的工作
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nUid = :nUid
				AND 	nType1 = 0
				AND 	nType0 = 1
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nGid,PDO::PARAM_INT);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA.'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$sContent0 = nl2br($sContent0);
			$sSQL = '	SELECT 	nId,
							sContent0,
							sUpdateTime
					FROM 	'.CLIENT_GROUP_ANNOUNCE.'
					WHERE nGid = :nGid
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false)
			{
				// 有紀錄 :更新
				$aSQL_Array = array(
					'sContent0' 	=> (string) $sContent0,
					'nUpdateTime'	=> (int)	NOWTIME,
					'sUpdateTime'	=> (string)	NOWDATE,
				);
				$sSQL = '	UPDATE '.CLIENT_GROUP_ANNOUNCE.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId',$aRows['nId'],PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aRows;
				$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
			}
			else
			{
				$aSQL_Array = array(
					'nGid'		=> (int)	$nGid,
					'sContent0'		=> (string) $sContent0,
					'nCreateTime'	=> (int)	NOWTIME,
					'sCreateTime'	=> (string)	NOWDATE,
					'nUpdateTime'	=> (int)	NOWTIME,
					'sUpdateTime'	=> (string)	NOWDATE,
				);
				$sSQL = 'INSERT INTO '.CLIENT_GROUP_ANNOUNCE.' ' . sql_build_array('INSERT', $aSQL_Array );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
				$nLastId = $oPdo->lastInsertId();

				$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = array();
				$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_GROUP_CTRL]['aNew']['nId'] = $nLastId;
			}

			// 紀錄動作
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nGid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100909,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aValue = array(
				'a'	=> 'SAY'.NOWTIME,
				'sMsg'=> '<b>更新公告 :</b><br>'.$sContent0,
			);
			// error_log(print_r($aValue,true));
			setcookie('a','SAY'.NOWTIME,COOKIE['REMEMBER']);
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nGid.'&sJWT='.sys_jwt_encode($aValue);
			$aReturn['sMsg'] = UPTV;
		}
	}

	echo json_encode($aReturn);
	exit;
?>
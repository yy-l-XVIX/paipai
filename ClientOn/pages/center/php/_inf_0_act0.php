<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/inf.php');

	$nFUid	= filter_input_int('nFUid',		INPUT_GET, 0);
	$nBUid	= filter_input_int('nBUid',		INPUT_GET, 0);

	$sHeight 	= filter_input_str('sHeight',		INPUT_POST,'',10);
	$sWeight 	= filter_input_str('sWeight',		INPUT_POST,'',10);
	$sSize 	= filter_input_str('sSize',		INPUT_POST,'',12);
	$sContent0 	= filter_input_str('sContent0',	INPUT_POST,'',255);
	$sContent1 	= filter_input_str('sContent1',	INPUT_POST,'',255);
	$sPhone 	= filter_input_str('sPhone',		INPUT_POST,'',20);
	$sWechat 	= filter_input_str('sWechat',		INPUT_POST,'',25);
	$sEmail 	= filter_input_str('sEmail',		INPUT_POST,'',255);
	$nType0 	= filter_input_int('nType0',		INPUT_POST,0);
	$nType1 	= filter_input_int('nType1',		INPUT_POST,0);
	$nType2 	= filter_input_int('nType2',		INPUT_POST,0);
	$nType4	= filter_input_int('nType4',		INPUT_POST,0); // 隱私設定

	$aData = array();
	$aJobData = array();
	$sSearchId = '0';
	$sMsg = '';
	$nFriend = 0;
	$aLog = array();
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

	// 加好友
	if ($aJWT['a'] == 'ADDFRIEND'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nFUid;

		// 查是否已經是朋友
		$sSQL = '	SELECT	1
				FROM		'.CLIENT_USER_FRIEND.'
				WHERE		nUid = :nUid
				AND		nFUid = :nFUid
				AND		nStatus = 1
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nFUid', $nFUid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows !== false)
		{
			$aReturn['sMsg'] = aERROR['FRIEND'];
		}
		else
		{
			$sSQL = '	SELECT	nId,
							sAccount,
							sName0
					FROM		'.CLIENT_USER_DATA.'
					WHERE		nId = :nFUid
					AND		nOnline = 1
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nFUid', $nFUid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);

			if($aRows !== false)
			{
				$sSQL = '	SELECT	nId,
								nUid,
								nFUid,
								nStatus
						FROM		'.CLIENT_USER_FRIEND.'
						WHERE		nStatus IN (0,1)
						AND		((nUid = :nUid
						AND		nFUid = :nFUid)
						OR		(nUid = :nFUid
						AND		nFUid = :nUid))';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				$Result->bindValue(':nFUid', $nFUid, PDO::PARAM_INT);
				sql_query($Result);
				while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
				{
					$aCheck[$aRows['nId']] = $aRows;
					$nFriend ++;
				}

				// 雙方都還沒加過好友
				if($nFriend == 0)
				{
					$aSQL_Array = array(
						'nUid'		=> (int) $aUser['nId'],
						'nFUid'		=> (int) $nFUid,
						'nStatus'		=> (int) 1,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
						'nUpdateTime'	=> (int) NOWTIME,
						'sUpdateTime'	=> (string) NOWDATE,
					);

					$sSQL = '	INSERT INTO '.CLIENT_USER_FRIEND.' '.sql_build_array('INSERT', $aSQL_Array);
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result,$aSQL_Array);
					sql_query($Result);
					$nLastId = $oPdo->lastInsertId();


					$aLog[0]['nKid'] = $nLastId;
					$aLog[0]['nLogCode'] = 7100001; // 加好友
					$aLog[0]['aEditLog'][CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;

					$aSQL_Array = array(
						'nUid'		=> (int) $nFUid,
						'nFUid'		=> (int) $aUser['nId'],
						'nStatus'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
						'nUpdateTime'	=> (int) NOWTIME,
						'sUpdateTime'	=> (string) NOWDATE,
					);

					$sSQL = '	INSERT INTO '.CLIENT_USER_FRIEND.' '.sql_build_array('INSERT', $aSQL_Array);
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result,$aSQL_Array);
					sql_query($Result);
					$nLastId = $oPdo->lastInsertId();

					$aLog[1]['nKid'] = $nLastId;
					$aLog[1]['nLogCode'] = 7100002; // 發送好友邀請
					$aLog[1]['aEditLog'][CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;

				}

				// 狀況1:A+B後，A把B刪掉 此時A無好友資料，B狀態為0或1
				// 狀況2:B+A後，B把A刪掉 此時B無好友資料，A狀態為0(前面以判斷過A跟B不是好友)
				if($nFriend == 1)
				{
					foreach($aCheck as $LPnId => $LPaDetail)
					{
						// 狀況1
						if($LPaDetail['nFUid'] == $aUser['nId'])
						{
							// 新增A to B 的好友資料
							$aSQL_Array = array(
								'nUid'		=> (int) $aUser['nId'],
								'nFUid'		=> (int) $nFUid,
								'nStatus'		=> (int) 1,
								'nCreateTime'	=> (int) NOWTIME,
								'sCreateTime'	=> (string) NOWDATE,
								'nUpdateTime'	=> (int) NOWTIME,
								'sUpdateTime'	=> (string) NOWDATE,
							);

							$sSQL = '	INSERT INTO '.CLIENT_USER_FRIEND.' '.sql_build_array('INSERT', $aSQL_Array);
							$Result = $oPdo->prepare($sSQL);
							sql_build_value($Result,$aSQL_Array);
							sql_query($Result);
							$nLastId = $oPdo->lastInsertId();

							$aLog[0]['nKid'] = $nLastId;
							$aLog[0]['nLogCode'] = 7100001; // 加好友
							$aLog[0]['aEditLog'][CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;
						}


						// 狀況2
						if($LPaDetail['nUid'] == $aUser['nId'] && $LPaDetail['nStatus'] == 0)
						{
							// 更新A to B好友資料
							$aSQL_Array = array(
								'nStatus'		=> (int) 1,
								'nUpdateTime'	=> (int) NOWTIME,
								'sUpdateTime'	=> (string) NOWDATE,
							);

							$sSQL = '	UPDATE client_user_friend SET '.sql_build_array('UPDATE', $aSQL_Array).'
									WHERE	nId = :nId LIMIT 1 ';
							$Result = $oPdo->prepare($sSQL);
							$Result->bindValue(':nId', $LPnId, PDO::PARAM_INT);
							sql_build_value($Result,$aSQL_Array);
							sql_query($Result);

							$aLog[0]['nKid'] = $LPnId;
							$aLog[0]['nLogCode'] = 7100001; // 加好友
							$aLog[0]['aEditLog'][CLIENT_USER_FRIEND]['aOld'] = $aCheck[$LPnId];
							$aLog[0]['aEditLog'][CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;



							// 新增B to A 的好友資料
							$aSQL_Array = array(
								'nUid'		=> (int) $nFUid,
								'nFUid'		=> (int) $aUser['nId'],
								'nStatus'		=> (int) 0,
								'nCreateTime'	=> (int) NOWTIME,
								'sCreateTime'	=> (string) NOWDATE,
								'nUpdateTime'	=> (int) NOWTIME,
								'sUpdateTime'	=> (string) NOWDATE,
							);

							$sSQL = '	INSERT INTO '.CLIENT_USER_FRIEND.' '.sql_build_array('INSERT', $aSQL_Array);
							$Result = $oPdo->prepare($sSQL);
							sql_build_value($Result,$aSQL_Array);
							sql_query($Result);
							$nLastId = $oPdo->lastInsertId();

							$aLog[1]['nKid'] = $nLastId;
							$aLog[1]['nLogCode'] = 7100002; // 發送好友邀請
							$aLog[1]['aEditLog'][CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;

						}
					}
				}

				// B+A好友 A未確認
				if($nFriend == 2)
				{
					foreach($aCheck as $LPnId => $LPaDetail)
					{
						if($LPaDetail['nUid'] == $aUser['nId'] && $LPaDetail['nStatus'] != 1)
						{
							$aSQL_Array = array(
								'nStatus'		=> (int) 1,
								'nUpdateTime'	=> (int) NOWTIME,
								'sUpdateTime'	=> (string) NOWDATE,
							);

							$sSQL = '	UPDATE client_user_friend SET '.sql_build_array('UPDATE', $aSQL_Array).'
									WHERE	nId = :nId LIMIT 1 ';
							$Result = $oPdo->prepare($sSQL);
							$Result->bindValue(':nId', $LPnId, PDO::PARAM_INT);
							sql_build_value($Result,$aSQL_Array);
							sql_query($Result);

							$aLog[0]['nKid'] = $LPnId;
							$aLog[0]['nLogCode'] = 7100001; // 加好友
							$aLog[0]['aEditLog'][CLIENT_USER_FRIEND]['aOld'] = $aCheck[$LPnId];
							$aLog[0]['aEditLog'][CLIENT_USER_FRIEND]['aNew'] = $aSQL_Array;
						}
					}
				}

				foreach($aLog as $LPaLog)
				{
					$aActionLog = array(
						'nWho'		=> (int) $aUser['nId'],
						'nWhom'		=> (int) $nFUid,
						'sWhomAccount'	=> (string) '',
						'nKid'		=> (int) $LPaLog['nKid'],
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) $LPaLog['nLogCode'],
						'sParam'		=> (string) json_encode($LPaLog['aEditLog']),
						'nType0'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					DoActionLog($aActionLog);
				}


				$aReturn['nStatus'] = 1;
				$aReturn['sMsg'] = aINF['ADDSUCCESS'];
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']);
			}
			else
			{
				$aReturn['sMsg'] = aERROR['NOMEMBER'];
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
			}
		}
		echo json_encode($aReturn);
		exit;
	}

	// 刪好友
	if ($aJWT['a'] == 'DELFRIEND'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nFUid;

		// 查是否已經是朋友
		$sSQL = '	SELECT	nId,
						nUid,
						nFUid
				FROM		'.CLIENT_USER_FRIEND.'
				WHERE		nUid = :nUid
				AND		nFUid = :nFUid
				AND		nStatus = 1
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nFUid', $nFUid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['sMsg'] = aERROR['NOTFRIEND'];
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
				'nWhom'		=> (int) $nFUid,
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
			$aReturn['sMsg'] = aINF['DELSUCCESS'];

		}
		echo json_encode($aReturn);
		exit;
	}

	// 加封鎖
	if ($aJWT['a'] == 'ADDBLOCK'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nBUid;

		// 查是否已經在封鎖名單
		$sSQL = '	SELECT	1
				FROM		'.CLIENT_USER_BLOCK.'
				WHERE		nUid = :nUid
				AND		nBUid = :nBUid
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows !== false)
		{
			$aReturn['sMsg'] = aERROR['BLOCKED'];
		}
		else
		{
			$sSQL = '	SELECT	nId,
							sAccount,
							sName0
					FROM		'.CLIENT_USER_DATA.'
					WHERE		nId = :nBUid
					AND		nOnline = 1
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if($aRows !== false)
			{
				// 新增封鎖紀錄
				$aSQL_Array = array(
					'nUid'		=> (int) $aUser['nId'],
					'nBUid'		=> (int) $nBUid,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);

				$sSQL = '	INSERT INTO '.CLIENT_USER_BLOCK.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);
				$nLastId = $oPdo->lastInsertId();

				$aEditLog[CLIENT_USER_BLOCK]['aNew'] = $aSQL_Array;

				// 從自己工作群移除
				$sSQL = '	SELECT 	nId
						FROM 	'.CLIENT_GROUP_CTRL.'
						WHERE	nUid = :nUid
						AND 	nType1 = 1
						AND 	nOnline != 99';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				sql_query($Result);
				while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
				{
					$aJobData[$aRows['nId']] = $aRows['nId'];
				}
				if (!empty($aJobData))
				{
					$oPdo->beginTransaction();

					$sSQL = '	SELECT 	nId
							FROM 	'.CLIENT_USER_GROUP_LIST.'
							WHERE nGid IN ( '.implode(',',$aJobData).' )
							AND 	nUid = :nUid
							FOR UPDATE';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nUid', $nBUid, PDO::PARAM_INT);
					sql_query($Result);
					while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
					{
						$sSearchId .= ','.$aRows['nId'];
					}
					// 從群組移除
					$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
							WHERE nId IN ( '.$sSearchId.' )';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);

					$aEditLog[CLIENT_USER_GROUP_LIST]['aOld']['nId'] = $sSearchId;
					$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = array();

					$sSearchId = '0';
					foreach ($aJobData as $LPnGid)
					{
						// 是否為上工人選[未結案]
						$sSQL = '	SELECT 	nId,
										nGid,
										nLid,
										sEmploye,
										nStatus
								FROM 	'.CLIENT_JOB.'
								WHERE nGid = :nGid
								AND 	nStatus = 0
								LIMIT 1 FOR UPDATE';
						$Result = $oPdo->prepare($sSQL);
						$Result->bindValue(':nGid', $LPnGid, PDO::PARAM_INT);
						sql_query($Result);
						$aRows = $Result->fetch(PDO::FETCH_ASSOC);
						if ($aRows !== false && strpos($aRows['sEmploye'], str_pad($nBUid,9,0,STR_PAD_LEFT)) !== false ) // 未結案 已結案則不變動
						{
							// 從已確定人選內移除
							$aEmploye = explode(',',$aRows['sEmploye']);
							unset($aEmploye[array_search (str_pad($nBUid,9,0,STR_PAD_LEFT), $aEmploye)]);
							$aSQL_Array = array(
								'sEmploye'		=> (string) implode(',', $aEmploye),
								'sUpdateTime'	=> (string) NOWDATE,
								'nUpdateTime'	=> (int) NOWTIME,
							);
							$sSQL = '	UPDATE '.CLIENT_JOB.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
									WHERE	nId = :nId LIMIT 1';
							$Result = $oPdo->prepare($sSQL);
							$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
							sql_build_value($Result, $aSQL_Array);
							sql_query($Result);

							$aEditLog[CLIENT_JOB]['aOld'][$aRows['nId']] = $aRows;
							$aEditLog[CLIENT_JOB]['aNew'][$aRows['nId']] = $aSQL_Array;
						}
					}

					$oPdo->commit();
				}

				// 私聊群組 nOnline 1 => 2
				$sSQL = '	SELECT 	nId
						FROM 	'.CLIENT_GROUP_CTRL.'
						WHERE nType0 = 0
						AND	nType1 = 0
						AND 	nOnline = 1
						AND 	((nUid = :nUid AND nTargetUid = :nBUid)
						OR 	(nUid = :nBUid AND nTargetUid = :nUid))
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				$Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);
				// $sSQL = '	SELECT	Group_.nId
				// 		FROM	'.CLIENT_GROUP_CTRL.' Group_,
				// 			'.CLIENT_USER_GROUP_LIST.' List_
				// 		WHERE	Group_.nType0 = 0
				// 		AND	Group_.nType1 = 0
				// 		AND 	Group_.nOnline = 1
				// 		AND	( List_.nUid = :nUid OR List_.nUid = :nBUid )
				// 		AND	Group_.nId = List_.nGid
				// 		GROUP BY List_.nGid
				// 		HAVING count(List_.nGid) = 2';
				// $Result = $oPdo->prepare($sSQL);
				// $Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				// $Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
				// sql_query($Result);
				// $aRows = $Result->fetch(PDO::FETCH_ASSOC);
				if ($aRows !== false)
				{
					$aSQL_Array = array(
						'nOnline'		=> (int) 2,
						'sUpdateTime'	=> (string) NOWDATE,
						'nUpdateTime'	=> (int) NOWTIME,
					);
					$sSQL = '	UPDATE '.CLIENT_GROUP_CTRL.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE	nId = :nId LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);

					$aEditLog[CLIENT_GROUP_CTRL]['aOld'][$aRows['nId']] = $aRows;
					$aEditLog[CLIENT_GROUP_CTRL]['aNew'][$aRows['nId']] = $aSQL_Array;
				}

				$aActionLog = array(
					'nWho'		=> (int) $aUser['nId'],
					'nWhom'		=> (int) $nBUid,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nLastId,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 7100101,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$aReturn['nStatus'] = 1;
				$aReturn['sMsg'] = aINF['BLOCKSUCCESS'];

			}
			else
			{
				$aReturn['sMsg'] = aERROR['NOMEMBER'];
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
			}
		}
		echo json_encode($aReturn);
		exit;
	}

	// 解封鎖
	if ($aJWT['a'] == 'DELBLOCK'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nBUid;

		// 查是否已經在封鎖名單
		$sSQL = '	SELECT	nId,
						nUid,
						nBUid
				FROM		'.CLIENT_USER_BLOCK.'
				WHERE		nUid = :nUid
				AND		nBUid = :nBUid
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['sMsg'] = aERROR['NOTBLOCKED'];
		}
		else
		{
			$aOld = $aRows;

			$sSQL = '	DELETE FROM	'. CLIENT_USER_BLOCK . '
					WHERE 	nId = :nId
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aOld['nId'], PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_USER_BLOCK]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_BLOCK]['aNew'] = array();

			$sSQL = '	SELECT 	1
					FROM 	'. CLIENT_USER_BLOCK . '
					WHERE (nUid = :nUid AND nBUid = :nBUid)
					OR 	(nUid = :nBUid AND nBUid = :nUid)';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			$Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false) //(需要雙方都解除封鎖)
			{
				// 私聊群組 nOnline 2 => 1
				$sSQL = '	SELECT 	nId
						FROM 	'.CLIENT_GROUP_CTRL.'
						WHERE nType0 = 0
						AND	nType1 = 0
						AND 	nOnline > 0 AND nOnline < 99
						AND 	((nUid = :nUid AND nTargetUid = :nBUid)
						OR 	(nUid = :nBUid AND nTargetUid = :nUid))
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				$Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);

				// $sSQL = '	SELECT	Group_.nId
				// 		FROM	'.CLIENT_GROUP_CTRL.' Group_,
				// 			'.CLIENT_USER_GROUP_LIST.' List_
				// 		WHERE	Group_.nType0 = 0
				// 		AND	Group_.nType1 = 0
				// 		AND 	Group_.nOnline = 2
				// 		AND	( List_.nUid = :nUid OR List_.nUid = :nBUid )
				// 		AND	Group_.nId = List_.nGid
				// 		GROUP BY List_.nGid
				// 		HAVING count(List_.nGid) = 2';
				// $Result = $oPdo->prepare($sSQL);
				// $Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				// $Result->bindValue(':nBUid', $nBUid, PDO::PARAM_INT);
				// sql_query($Result);
				// $aRows = $Result->fetch(PDO::FETCH_ASSOC);

				if ($aRows !== false)
				{
					$aSQL_Array = array(
						'nOnline'		=> (int) 1,
						'sUpdateTime'	=> (string) NOWDATE,
						'nUpdateTime'	=> (int) NOWTIME,
					);
					$sSQL = '	UPDATE '.CLIENT_GROUP_CTRL.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE	nId = :nId LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);

					$aEditLog[CLIENT_GROUP_CTRL]['aOld'][$aRows['nId']] = $aRows;
					$aEditLog[CLIENT_GROUP_CTRL]['aNew'][$aRows['nId']] = $aSQL_Array;
				}
			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $nBUid,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = aINF['UNBLOCKSUCCESS'];
		}
		echo json_encode($aReturn);
		exit;
	}

	// 更新基本資料
	if ($aJWT['a'] == 'UPT'.$aUser['nId'])
	{
		$sSQL = '	SELECT 	nId,
						sPhone,
						sWechat,
						sEmail,
						nType0,
						nType1,
						nType2
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		$aLog[CLIENT_USER_DATA]['aOld'] = $aRows;

		$sSQL = '	SELECT 	sHeight,
						sSize,
						sContent0,
						sContent1
				FROM 	'.CLIENT_USER_DETAIL.'
				WHERE nUid = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		$aLog[CLIENT_USER_DETAIL]['aOld'] = $aRows;

		$aSQL_Array = array(
			'sHeight' 	=> (string) $sHeight,
			'sWeight'	=> (string) $sWeight,
			'sSize' 	=> (string) $sSize,
			'sContent0'	=> (string) $sContent0,
			'sContent1'	=> (string) $sContent1,
		);
		$sSQL = '	UPDATE '.CLIENT_USER_DETAIL.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
				WHERE	nUid = :nUid LIMIT 1 ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_build_value($Result,$aSQL_Array);
		sql_query($Result);

		$aLog[CLIENT_USER_DETAIL]['aNew'] = $aSQL_Array;

		$aSQL_Array = array(
			'sPhone'	=> (string) $sPhone,
			'sWechat'	=> (string) $sWechat,
			'sEmail'	=> (string) $sEmail,
			'nType0'	=> (int) $nType0,
			'nType1'	=> (int) $nType1,
			'nType2'	=> (int) $nType2,
			'nType4'	=> (int) $nType4,
		);
		$sSQL = '	UPDATE '.CLIENT_USER_DATA.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
				WHERE	nId = :nId LIMIT 1 ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
		sql_build_value($Result,$aSQL_Array);
		sql_query($Result);

		$aLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

		$aActionLog = array(
			'nWho'		=> (int) $aUser['nId'],
			'nWhom'		=> (int) $aUser['nId'],
			'sWhomAccount'	=> (string) '',
			'nKid'		=> (int) $aUser['nId'],
			'sIp'			=> (string) USERIP,
			'nLogCode'		=> (int) 7100306,
			'sParam'		=> (string) json_encode($aLog),
			'nType0'		=> (int) 0,
			'nCreateTime'	=> (int) NOWTIME,
			'sCreateTime'	=> (string) NOWDATE,
		);
		DoActionLog($aActionLog);

		// 變更頭像
		if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
		{
			$aFile['sTable'] = CLIENT_USER_DATA;
			$aFile['aFile'] = $_FILES['sFile'];
			$aFileInfo = goImage($aFile);
			if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
				echo json_encode($aReturn);
				exit;
			}
			else
			{
				$aTmp = explode('.',$aFileInfo['sFilename']);
				$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
				$sFname = $aFileInfo['sFilename'];
			}

			// 刪除舊的圖片資訊
			$sSQL = '	DELETE
					FROM 		' . CLIENT_IMAGE_CTRL . '
					WHERE 	nKid = :nUid
					AND		sTable LIKE :sTable';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue('nUid', $aUser['nId'], PDO::PARAM_INT);
			$Result->bindValue('sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
			sql_query($Result);

			$aSQL_Array = array(
				'nKid'		=> (int) $aUser['nId'],
				'sTable'		=> (string) CLIENT_USER_DATA,
				'sFile'		=> (string) $sFname,
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'  	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nImageLastId = $oPdo->lastInsertId();

			#紀錄動作 - 新增
			$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nImageLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100307,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
		}

		$aReturn['sMsg'] = UPTV;
		echo json_encode($aReturn);
		exit;
	}

?>
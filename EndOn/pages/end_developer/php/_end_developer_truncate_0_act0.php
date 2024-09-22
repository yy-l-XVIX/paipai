<?php
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_developer_truncate.php');

	$sPassword	= filter_input_str('sPassword',INPUT_POST, '');
	$sUserAccount= filter_input_str('sUserAccount',INPUT_POST, '');
	$sManagerAccount= filter_input_str('sManagerAccount',INPUT_POST, '');
	$aPostTable	= (isset($_POST['aPost'])) ? $_POST['aPost'] : array();
	$sPostTable = implode(',', array_keys($aPostTable));
	$aAllowTable = array(
		END_MANAGER_DATA		=> true,
		END_MANAGER_COOKIE	=> true,
		END_MENU_CTRL		=> true,
		SYS_GOOGLE_VERIFY		=> true,
		CLIENT_USER_DATA		=> true,
		CLIENT_USER_DETAIL	=> true,
		CLIENT_USER_HIDE		=> true,
		CLIENT_USER_LINK		=> true,
		CLIENT_USER_MONEY		=> true,
		CLIENT_USER_BANK		=> true,
		CLIENT_USER_COOKIE	=> true,
		CLIENT_USER_VERIFY	=> true,
		// CLIENT_USER_KIND		=> true,
		CLIENT_USER_FRIEND	=> true,
		CLIENT_USER_BLOCK		=> true,
		CLIENT_MONEY		=> true,
		CLIENT_PAYMENT		=> true,
		CLIENT_PAYMENT_TUNNEL	=> true,
		CLIENT_JOB			=> true,
		CLIENT_JOB_SCORE		=> true,
		CLIENT_JOB_TYPE		=> true,
		CLIENT_USER_JOB_FAVORITE=> true,
		CLIENT_LOCATION		=> true,
		CLIENT_GROUP_MSG		=> true,
		CLIENT_GROUP_CTRL		=> true,
		CLIENT_USER_GROUP_LIST	=> true,
		END_LOG			=> true,
		END_LOG_ACCOUNT		=> true,
		END_MANAGER_LOGIN		=> true,
		CLIENT_USER_LOGIN		=> true,
		// CLIENT_ANNOUNCE		=> true,
		CLIENT_ANNOUNCE_KIND	=> true,
		CLIENT_BROADCAST		=> true,
		CLIENT_BROADCAST_KIND	=> true,
		CLIENT_DISCUSS		=> true,
		CLIENT_DISCUSS_REPLY	=> true,
		CLIENT_IMAGE_CTRL		=> true,
		CLIENT_DATA_CTRL		=> true,
		SYS_MOVE_RECORD		=> true,
		END_LOG_MOVE		=> true,
		END_LOG_ACCOUNT_MOVE	=> true,
		END_MANAGER_LOGIN_MOVE	=> true,
		CLIENT_USER_LOGIN_MOVE	=> true,
		CLIENT_SERVICE 		=> true,
		CLIENT_SNOOZE_KEYWORDS	=> true,
		// CLIENT_SERVICE_KIND 	=> true,

	);
	$aKeepUser = array(
		'aAid'	=> array(1=>1,),
		'aUid'	=> array(1=>1,),
	);
	$sUserAccount = 'mmg001,'.$sUserAccount;
	$sUserAccount = trim($sUserAccount,',');
	$sManagerAccount = 'admroot,'.$sManagerAccount;
	$sManagerAccount = trim($sManagerAccount,',');

	$nErr = 0;
	$sMsg = '';
	$aEditLog = $aPostTable;
	if ($aJWT['a'] == 'TRUNCATE')
	{
		if (oCypher::ReHash($sPassword) != $aAdm['sPassword'])
		{
			$nErr = 1;
			$sMsg = aERROR['PASSWORD'];
		}
		if (empty($aPostTable))
		{
			$nErr = 1;
			$sMsg = aERROR['EMPTY'];
		}
		if ($sUserAccount != '')
		{
			$sUserAccount = '\''.str_replace(',', '\',\'', $sUserAccount).'\'';
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_USER_DATA.'
					WHERE sAccount IN ( '.$sUserAccount.' )
					AND 	nStatus != 99';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aKeepUser['aUid'][$aRows['nId']] = $aRows['nId'];
			}
			if (sizeof($aKeepUser['aUid']) != sizeof(explode(',', $sUserAccount)))
			{
				$nErr = 1;
				#echo sizeof($aKeepUser['aUid']).'<br>';
				#echo sizeof(explode(',', $sUserAccount));
				$sMsg = aERROR['USER'];
			}
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_USER_LINK.'
					WHERE nEndTime = 0
					AND 	nUid IN ( '.implode(',', $aKeepUser['aUid']).' )';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aKeepUser['aLid'][$aRows['nId']] = $aRows['nId'];
			}
		}
		if ($sManagerAccount != '')
		{
			$sManagerAccount = '\''.str_replace(',', '\',\'', $sManagerAccount).'\'';
			$sSQL = '	SELECT 	nId
					FROM 	'.END_MANAGER_DATA.'
					WHERE sAccount IN ( '.$sManagerAccount.' )
					AND 	nStatus != 99';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aKeepUser['aAid'][$aRows['nId']] = $aRows['nId'];
			}
			if (sizeof($aKeepUser['aAid']) != sizeof(explode(',', $sManagerAccount)))
			{
				$nErr = 1;
				// echo sizeof($aKeepUser['aUid']).'<br>';
				// echo sizeof(explode(',', $sUserAccount));
				$sMsg = aERROR['MANAGER'];
			}
		}
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_developer/php/_end_developer_truncate_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$oPdo->beginTransaction();

			if (isset($aPostTable['end_manager_data'])) # 保留 admroot 帳號
			{
				$sSQL = 'DELETE FROM '.END_MANAGER_DATA.' WHERE nId NOT IN ('.implode(',', $aKeepUser['aAid']).') ';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.END_MANAGER_DATA.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				$sSQL = 'DELETE FROM '.SYS_GOOGLE_VERIFY.' WHERE nUid NOT IN ('.implode(',', $aKeepUser['aAid']).') AND sTable LIKE :sTable';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':sTable',END_MANAGER_DATA, PDO::PARAM_STR);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.SYS_GOOGLE_VERIFY.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				$sSQL = 'DELETE FROM '.END_MENU_CTRL.' WHERE nUid NOT IN ('.implode(',', $aKeepUser['aAid']).') ';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.END_MENU_CTRL.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				unset($aPostTable[END_MENU_CTRL]);
				unset($aPostTable[END_MANAGER_DATA]);
				unset($aPostTable[SYS_GOOGLE_VERIFY]);

			}

			if (isset($aPostTable['client_user_data']))
			{
				$sSQL = 'DELETE FROM '.CLIENT_USER_DATA.' WHERE nId NOT IN ('.implode(',', $aKeepUser['aUid']).')';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.CLIENT_USER_DATA.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				$sSQL = 'DELETE FROM '.CLIENT_USER_DETAIL.' WHERE nUid NOT IN ('.implode(',', $aKeepUser['aUid']).')';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.CLIENT_USER_DETAIL.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				$sSQL = 'DELETE FROM '.CLIENT_USER_HIDE.' WHERE nUid NOT IN ('.implode(',', $aKeepUser['aUid']).') ';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.CLIENT_USER_HIDE.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				$sSQL = 'DELETE FROM '.CLIENT_USER_LINK.' WHERE nUid NOT IN ('.implode(',', $aKeepUser['aUid']).')';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.CLIENT_USER_LINK.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				$sSQL = 'DELETE FROM '.CLIENT_USER_MONEY.' WHERE nUid NOT IN ('.implode(',', $aKeepUser['aUid']).') ';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				$sSQL = 'ALTER TABLE '.CLIENT_USER_MONEY.' AUTO_INCREMENT = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				foreach ($aKeepUser['aUid'] as $LPnUid)
				{
					# 會員金額歸 0
					$aNewMoney = array(
						'Money' => 0,
					);
					$aSQL_Array = oTransfer::PointUpdate($LPnUid,$aNewMoney,1,true);
					if($aSQL_Array !== false)
					{
						$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
								WHERE	nUid = :nUid LIMIT 1';
						$Result = $oPdo->prepare($sSQL);
						$Result->bindValue(':nUid', $LPnUid, PDO::PARAM_INT);
						sql_build_value($Result, $aSQL_Array);
						sql_query($Result);
					}
				}

				unset($aPostTable[CLIENT_USER_DATA]);
				unset($aPostTable[CLIENT_USER_DETAIL]);
				unset($aPostTable[CLIENT_USER_HIDE]);
				unset($aPostTable[CLIENT_USER_LINK]);
				unset($aPostTable[CLIENT_USER_MONEY]);
			}

			foreach ($aPostTable as $LPsTable => $LPn)
			{
				if (!isset($aAllowTable[$LPsTable]))
				{
					unset($aPostTable[$LPsTable]);
					continue;
				}

				if ($LPsTable == 'client_image_ctrl')
				{
					$sSQL = 'DELETE FROM '.CLIENT_IMAGE_CTRL.' ';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);
					$sSQL = 'ALTER TABLE '.CLIENT_IMAGE_CTRL.' AUTO_INCREMENT = 1';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);

					continue;
				}
				if ($LPsTable == 'client_payment')
				{
					$sSQL = 'DELETE FROM '.CLIENT_IMAGE_CTRL.' WHERE nType0 = 1';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);
					$sSQL = 'ALTER TABLE '.CLIENT_IMAGE_CTRL.' AUTO_INCREMENT = 1';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);

					continue;
				}

				if (	$LPsTable == 'client_announce_kind' ||
					$LPsTable == 'client_broadcast_kind'||
					$LPsTable == 'client_job_type' 	||
					$LPsTable == 'client_location' 	||
					$LPsTable == 'client_user_kind'	||
					$LPsTable == 'client_job_type'
				)
				{
					$sSQL = 'DELETE FROM '.$LPsTable.' WHERE nOnline = 99';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);
					$sSQL = 'ALTER TABLE '.$LPsTable.' AUTO_INCREMENT = 1';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);

					continue;
				}

				$sSQL = 'TRUNCATE TABLE '.$LPsTable;
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
			}

			$oPdo->commit();

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) 0,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8100001,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aJumpMsg['0']['sMsg'] = aTRUNCATE['DEALV'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_developer/php/_end_developer_truncate_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
?>
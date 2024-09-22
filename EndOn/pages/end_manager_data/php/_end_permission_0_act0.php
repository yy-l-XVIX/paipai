<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'. $aSystem['sLang'] .'/end_permission.php');

	$nId				= filter_input_int('nId',	INPUT_REQUEST, 0);
	$sName0			= filter_input_str('sName0',	INPUT_POST, '', 20);

	$sControl = '';
	$aSetControl = array();
	$nErr	= 0;
	$sMsg = '';
	$aActionLog = array();
	$aEditLog = array(
		END_PERMISSION => array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	if (isset($_POST['aControl']))
	{
		$aSetControl = $_POST['aControl'];
	}

	if ($aJWT['a'] == 'INS')
	{
		if ($sName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYNAME'].'<br>';
		}
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0_upt0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			foreach($aSetControl as $LPnMkid => $aMlid)
			{
				$sControl .= $LPnMkid.'_';
				foreach($aMlid as $nMlid)
				{
					$sControl .= $nMlid.',';
				}
				$sControl = substr($sControl,0,-1).'|';
			}
			$sControl = substr($sControl,0,-1); # 1_2,3|4_5,6

			$aSQL_Array = array(
				'sName0'		=> $sName0,
				'sControl'		=> $sControl,
				'nOnline'		=> 1,
				'nCreateTime'	=> NOWTIME,
				'sCreateTime'	=> NOWDATE,
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);
			$sSQL = 'INSERT INTO '.END_PERMISSION.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			# 紀錄動作 - 新增
			$aEditLog[END_PERMISSION]['aNew'] = $aSQL_Array;
			$aEditLog[END_PERMISSION]['aNew']['nId'] = $nLastId;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8101001,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aJumpMsg['0']['sMsg'] = aPERMISSION['INSERTSUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	sName0,
						sControl,
						nOnline
				FROM 	'.END_PERMISSION.'
				WHERE nId = :nId
				AND	nOnline != 99
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	= aERROR['NODATA'].'<br>';
		}
		$aEditLog[END_PERMISSION]['aOld'] = $aRows;
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0_upt0.php']).'&nId='.$nId;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			foreach($aSetControl as $LPnMkid => $aMlid)
			{
				$sControl .= $LPnMkid.'_';
				foreach($aMlid as $nMlid)
				{
					$sControl .= $nMlid.',';
				}
				$sControl = substr($sControl,0,-1).'|';
			}
			$sControl = substr($sControl,0,-1); # 1_2,3|4_5,6

			$aSQL_Array = array(
				'sName0'		=> $sName0,
				'sControl'		=> $sControl,
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);
			$sSQL = '	UPDATE	'.END_PERMISSION.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 連動改最更層級admroot 權限
			if ($nId == 1)
			{
				if (!empty($aSetControl))
				{
					$sSQL = 'DELETE	FROM '.END_MENU_CTRL.' WHERE nUid = 1 ';
					$Result = $oPdo->prepare($sSQL);
					sql_query($Result);
				}
				foreach ($aSetControl as $LPnMkid => $LPaMlid)
				{
					foreach ($LPaMlid as $LPnMlid)
					{
						$aSQL_Array = array(
							'nUid'		=> 1,
							'nMkid'		=> $LPnMkid,
							'nMlid'		=> $LPnMlid,
							'nCreateTime'	=> NOWTIME,
							'sCreateTime'	=> NOWDATE,
						);

						$sSQL = 'INSERT INTO '.END_MENU_CTRL.' ' . sql_build_array('INSERT', $aSQL_Array );
						$Result = $oPdo->prepare($sSQL);
						sql_build_value($Result, $aSQL_Array);
						sql_query($Result);
					}
				}
			}

			# 紀錄動作 - 更新
			$aEditLog[END_PERMISSION]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8101002,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aJumpMsg['0']['sMsg'] = aPERMISSION['UPDATESUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nOnline
				FROM 	'.END_PERMISSION.'
				WHERE nId = :nId
				AND	nOnline != 99
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aJumpMsg['0']['sMsg'] = aERROR['NODATA'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0.php']).'&nId='.$nId;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aEditLog[END_PERMISSION]['aOld'] = $aRows;
			$aSQL_Array = array(
				'nOnline'		=> 99,
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);
			$sSQL = '	UPDATE	'.END_PERMISSION.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 刪除
			$aEditLog[END_PERMISSION]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8101003,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aJumpMsg['0']['sMsg'] = aPERMISSION['DELETESUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_permission_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
?>
<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/id.php');

	$nId 			= filter_input_int('nId',	INPUT_GET,0);
	$nFileCount 	= filter_input_int('nFileCount',	INPUT_POST,0);

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
		'nAlertType'	=> 1,
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/center/php/_id_0.php'])
	);
	$aData = array();
	$aPendingField = array_flip(explode(',', $aSystem['aParam']['sPendingField']));	// 需要審核欄位
	$nIdCount = 0;

	if ($aJWT['a'] == 'INS')
	{
		if (sizeof($_FILES) != 2)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['IMAGE'];
		}
		foreach ($_FILES as $LPsFile => $LPaDetail)
		{
			if ($LPaDetail['error'] == 4)
			{
				$nIdCount ++;
			}
		}
		if ($nIdCount > 0)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['IMAGE'];
		}
		if($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			$sSQL = '	SELECT 	nId,
							nType3,
							nPendingStatus,
							sPendingStatus
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nId = :nId
					LIMIT 1 FOR UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows['nType3'] == 1)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aERROR['UPLOADED'];
				echo json_encode($aReturn);
				exit;
			}

			# update client_user_data nType3=>1 已上傳
			$aSQL_Array = array(
				'nType3'		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE 	'.CLIENT_USER_DATA.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE		nId = :nId
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_USER_DATA]['aNew']['nId'] = $aUser['nId'];

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aUser['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100801,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			for($nI = 0;$nI<2;$nI++)
			{
				if (isset($_FILES['sFile'.$nI]) && $_FILES['sFile'.$nI]['name']<>'')
				{
					$aFile['sTable'] = 'client_user_id';
					$aFile['aFile'] = $_FILES['sFile'.$nI];
					$aFileInfo = goImage($aFile);
					if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
					{
						$oPdo->rollback();
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

					$aSQL_Array = array(
						'nKid'		=> (int) $aUser['nId'],
						'sTable'		=> (string) 'client_user_id',
						'sFile'		=> (string) $sFname,
						'nType0'		=> (int) $nI,
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
						'nKid'		=> (int) $aUser['nId'],
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) 7100801,
						'sParam'		=> (string) json_encode($aEditLog),
						'nType0'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					DoActionLog($aActionLog);
				}
			}

			$oPdo->commit();
			$aReturn['sMsg'] = aID['INSSUCCESS'];
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']);
		}
	}

	if ($aJWT['a'] == 'UPT')
	{

		$sSQL = '	SELECT 	nId,
						nKid,
						nType0,
						nCreateTime,
						sTable,
						sFile
				FROM 		'.CLIENT_IMAGE_CTRL.'
				WHERE 	nKid = :nUid
				AND		sTable = \'client_user_id\'';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nType0']] = $aRows;
		}

		if (empty($aData))
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA;
		}
		if (sizeof($_FILES) != 2)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['IMAGE'];
		}
		foreach ($_FILES as $LPsFile => $LPaDetail)
		{
			if ($LPaDetail['error'] == 4)
			{
				$nIdCount ++;
			}
		}
		if ($nIdCount == 2)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['NOIMAGE'];
		}

		if($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			$sSQL = '	SELECT 	nId,
							nType3,
							nPendingStatus,
							sPendingStatus
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nId = :nId
					LIMIT 1 FOR UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			$aUserPendingStatus = explode(',', $aRows['sPendingStatus']);		// 資料審核狀態

			# update client_user_data nType3=>1 已上傳
			$aSQL_Array = array(
				'nType3'		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			if ($aUser['nStatus'] == 11)
			{
				if ($aUserPendingStatus[$aPendingField['sIdImage']] == 99) // 被拒絕更正資料
				{
					$aUserPendingStatus[$aPendingField['sIdImage']] = 0; // 改成未審核
				}
				$aSQL_Array['sPendingStatus'] = implode(',', $aUserPendingStatus);
				if (!in_array(99, $aUserPendingStatus)) // 都沒有被拒絕
				{
					$aSQL_Array['nPendingStatus'] = 0; // 審核狀態再次變為未審核
				}
			}

			$sSQL = '	UPDATE 	'.CLIENT_USER_DATA.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE		nId = :nId
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_USER_DATA]['aNew']['nId'] = $aUser['nId'];

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aUser['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100802,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			for($nI = 0;$nI<2;$nI++)
			{
				if (isset($_FILES['sFile'.$nI]) && $_FILES['sFile'.$nI]['name']<>'')
				{
					$aFile['sTable'] = 'client_user_id';
					$aFile['aFile'] = $_FILES['sFile'.$nI];
					$aFileInfo = goImage($aFile);
					if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
					{
						$oPdo->rollback();
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

						// 刪除舊的圖片資訊
						$sSQL = '	DELETE
								FROM 	' . CLIENT_IMAGE_CTRL . '
								WHERE nKid = :nUid
								AND	sTable = \'client_user_id\'
								AND 	nType0 = :nType0
								LIMIT 1';
						$Result = $oPdo->prepare($sSQL);
						$Result->bindValue('nUid', $aUser['nId'], PDO::PARAM_INT);
						$Result->bindValue('nType0', $nI, PDO::PARAM_INT);
						sql_query($Result);

						$LPaImgData = array(
							'sImgUrl'	=> date('Y/m/d/',$aData[$nI]['nCreateTime']).$aData[$nI]['sTable'].'/'.$aData[$nI]['sFile'],
							'delImg'	=> 1,
							'sUrl'	=> $aFile['sUrl']
						);
						delImage($LPaImgData);
					}

					$aSQL_Array = array(
						'nKid'		=> (int) $aUser['nId'],
						'sTable'		=> (string) 'client_user_id',
						'sFile'		=> (string) $sFname,
						'nType0'		=> (int) $nI,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'  	=> (string) NOWDATE,
					);

					$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$nImageLastId = $oPdo->lastInsertId();

					#紀錄動作 - 新增
					$aEditLog[CLIENT_IMAGE_CTRL]['aOld'] = $aData[$nI];
					$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
					$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;
					$aActionLog = array(
						'nWho'		=> (int) $aUser['nId'],
						'nWhom'		=> (int) 0,
						'sWhomAccount'	=> (string) '',
						'nKid'		=> (int) $aUser['nId'],
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) 7100802,
						'sParam'		=> (string) json_encode($aEditLog),
						'nType0'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					DoActionLog($aActionLog);
				}
			}

			$oPdo->commit();
			$aReturn['sMsg'] = aID['UPTSUCCESS'];
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']);
		}
	}
	echo json_encode($aReturn);
	exit;
?>
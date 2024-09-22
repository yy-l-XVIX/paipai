<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/my_job.php');

	$nGid 	= filter_input_int('nJid',	INPUT_REQUEST, 0);
	$nUid 	= filter_input_int('nUid',	INPUT_GET, 0);

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
		'sUrl'		=> ''
	);
	$aEditLog = array();

	// 工作結案(雇主)
	if ($aJWT['a'] == 'CLOSEJOB')
	{
		// 檢查是不是自己建的
		$sSQL = '	SELECT 	nId,
						nUid
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nUid = :nUid
				AND 	nType1 = 1
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
			$aReturn['sMsg'] = NODATA.'<br>';
		}

		$oPdo->beginTransaction();
		$sSQL = '	SELECT 	nId,
						nGid,
						sName0,
						sEmploye,
						nStatus,
						sCreateTime,
						sUpdateTime
				FROM 	'.CLIENT_JOB.'
				WHERE nGid = :nGid
				AND 	nStatus = 0
				LIMIT 1
				FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}
		if ($sUserCurrentRole != 'boss')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = PARAMSERR.'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nStatus' 		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_JOB.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_JOB]['aOld'] = $aRows;
			$aEditLog[CLIENT_JOB]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_JOB]['aNew']['nId'] = $aRows['nId'];

			// 參加人才建立尚未評分紀錄
			$aEmploye = explode(',', $aRows['sEmploye']);
			foreach ($aEmploye as $LPsUid)
			{
				$aSQL_Array = array(
					'nUid'		=> (int) $LPsUid,
					'nGid'		=> (int) $nGid,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = 'INSERT INTO '.CLIENT_JOB_SCORE.' ' . sql_build_array('INSERT', $aSQL_Array );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
				$nLastId = $oPdo->lastInsertId();

				$aEditLog[CLIENT_JOB_SCORE]['aNew'][$nLastId] = $aSQL_Array;
			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aRows['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100603,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = $aRows['sName0'].' '.aJOB['CLOSEJOB'];
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']).'&nId='.$nGid;
		}
		$oPdo->commit();
	}
	// 踢出群組(雇主)
	if ($aJWT['a'] == 'KICKOUT')
	{
		$nGid = $aJWT['nGid'];

		// 檢查是不是自己建的工作
		$sSQL = '	SELECT 	nId,
						nUid
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nUid = :nUid
				AND 	nType1 = 1
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

			// 是否為上工人選
			$sSQL = '	SELECT 	nId,
							nGid,
							nLid,
							sEmploye,
							nStatus
					FROM 	'.CLIENT_JOB.'
					WHERE nGid = :nGid
					LIMIT 1 FOR UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false && strpos($aRows['sEmploye'], str_pad($nUid,9,0,STR_PAD_LEFT)) !== false && $aRows['nStatus'] == 0) // 未結案 已結案則不變動
			{
				// 從已確定人選內移除
				$aEmploye = explode(',',$aRows['sEmploye']);
				unset($aEmploye[array_search (str_pad($nUid,9,0,STR_PAD_LEFT), $aEmploye)]);
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

				$aEditLog[CLIENT_JOB]['aOld'] = $aRows;
				$aEditLog[CLIENT_JOB]['aNew'] = $aSQL_Array;
			}
			// 紀錄log
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $nUid,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100604,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();

			$sSQL = '	SELECT 	sName0
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nId = :nId
					AND 	nOnline = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId',$nUid,PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);

			$aReturn['sMsg'] = str_replace('[::sName0::]',$aRows['sName0'],aJOB['SUCCESSKICK']); // 成功將 [::sName0::] 退出群組
			$aReturn['sUrl'] = '';
		}
	}

	// 接受工作(人才)
	if ($aJWT['a'] == 'ACCEPT'.$aUser['nId'])
	{
		// 雇主無法接受工作
		if ($sUserCurrentRole == 'boss')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['BOSSNOJOB'].'<br>';
		}
		// 人才已過期
		if ($sUserCurrentRole == 'staff' && strtotime($aUser['sExpired0']) < NOWTIME)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['EXPIRED'].'<br>';
		}

		$sSQL = '	SELECT 	nId,
						nUid
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nType1 = 1
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}

		$oPdo->beginTransaction();
		$sSQL = '	SELECT 	nId,
						sName0,
						nStatus,
						nEmploye,
						sEmploye
				FROM 	'.CLIENT_JOB.'
				WHERE nGid = :nGid
				AND 	nStatus IN (0,1)
				LIMIT 1 FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}
		if (strpos($aRows['sEmploye'],str_pad($aUser['nId'],9,0,STR_PAD_LEFT)) !== false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['JOINED'].'<br>';
		}
		$aRows['aEmploye'] = array();
		if ($aRows['sEmploye'] != '')
		{
			$aRows['aEmploye'] = explode(',', $aRows['sEmploye']);
		}
		if ($aRows['nEmploye'] < sizeof($aRows['aEmploye'])+1)#工作人數已滿
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['EMPLOYEMAX'].'<br>';
		}
		if ($aRows['nStatus'] == '1')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['CLOSED'].'<br>';
		}
		$sSQL = '	SELECT 	nId,
						nUid,
						nTargetUid,
						nStatus0
				FROM	'.CLIENT_GROUP_MSG.'
				WHERE	nGid = :nGid
				AND 	nTargetUid = :nUid
				AND 	sMsg = \'[:invite job:]\'
				AND 	nStatus0 = 0
				LIMIT 1 FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',	$nGid,		PDO::PARAM_INT);
		$Result->bindValue(':nUid',	$aUser['nId'],	PDO::PARAM_INT);
		sql_query($Result);
		$aOldMsg = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOldMsg === false)
		{
			$aReturn['nStatus'] = 0;
		}
		if ($aReturn['nStatus'] == 1)
		{
			$sEmploye = trim($aRows['sEmploye'].','.str_pad($aUser['nId'],9,0,STR_PAD_LEFT),',');

			$aSQL_Array = array(
				'sEmploye'		=> (string) $sEmploye,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_JOB.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_JOB]['aOld'] = $aRows;
			$aEditLog[CLIENT_JOB]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_JOB]['aNew']['nId'] = $aRows['nId'];

			$aSQL_Array = array(
				'nStatus0'		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_GROUP_MSG.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId
					LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aOldMsg['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_GROUP_MSG]['aOld'] = $aOldMsg;
			$aEditLog[CLIENT_GROUP_MSG]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aRows['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101005,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = aJOB['JOINSUCCESS'];
			$aReturn['aData']['nBossId'] = $aOldMsg['nUid'];
			$aReturn['aData']['sSendMsg'] = $aUser['sName0'].aJOB['JOINSUCCESS'];

		}
		$oPdo->commit();
	}
	// 不接受工作(人才)
	if ($aJWT['a'] == 'REJECT'.$aUser['nId'])
	{
		// 雇主無法接受工作
		if ($sUserCurrentRole == 'boss')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['BOSSNOJOB'].'<br>';
		}
		// 人才已過期
		if ($sUserCurrentRole == 'staff' && strtotime($aUser['sExpired0']) < NOWTIME)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['EXPIRED'].'<br>';
		}

		$sSQL = '	SELECT 	nId,
						nUid
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nType1 = 1
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}

		$oPdo->beginTransaction();
		$sSQL = '	SELECT 	nId,
						sName0,
						nStatus,
						sEmploye
				FROM 	'.CLIENT_JOB.'
				WHERE nGid = :nGid
				AND 	nStatus IN (0,1)
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}
		else if ($aRows['nStatus'] == '1')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['CLOSED'].'<br>';
		}
		else if (strpos($aRows['sEmploye'],str_pad($aUser['nId'],9,0,STR_PAD_LEFT)) !== false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['JOINED'].'<br>';
		}

		$sSQL = '	SELECT 	nId,
						nUid,
						nTargetUid,
						nStatus0
				FROM	'.CLIENT_GROUP_MSG.'
				WHERE	nGid = :nGid
				AND 	nTargetUid = :nUid
				AND 	sMsg = \'[:invite job:]\'
				AND 	nStatus0 = 0
				LIMIT 1 FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',	$nGid,	PDO::PARAM_INT);
		$Result->bindValue(':nUid',	$aUser['nId'],	PDO::PARAM_INT);
		sql_query($Result);
		$aOldMsg = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOldMsg === false)
		{
			$aReturn['nStatus'] = 0;
		}
		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nStatus0'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_GROUP_MSG.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId
					LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aOldMsg['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_GROUP_MSG]['aOld'] = $aOldMsg;
			$aEditLog[CLIENT_GROUP_MSG]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aRows['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101006,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = aJOB['REJECTSUCCESS'];
			$aReturn['aData']['nBossId'] = $aOldMsg['nUid'];
			$aReturn['aData']['sSendMsg'] = $aUser['sName0'].aJOB['REJECTSUCCESS'];
		}
		$oPdo->commit();
	}

	if ($aJWT['a'] == 'UPLOADFILE')
	{
		if (!empty($_FILES['aFile']))
		{
			for ($i=0; $i < $aSystem['aParam']['nPostImage']; $i++)
			{
				if (isset($_FILES['aFile']['name'][$i]) && $_FILES['aFile']['name'][$i] != '' && $_FILES['aFile']['error'][$i] != 4)
				{
					$aFile['sTable'] = CLIENT_GROUP_MSG;
					$aFile['aFile'] = array(
						'name'	=> $_FILES['aFile']['name'][$i],
						'type'	=> $_FILES['aFile']['type'][$i],
						'tmp_name'	=> $_FILES['aFile']['tmp_name'][$i],
						'error'	=> $_FILES['aFile']['error'][$i],
						'size'	=> $_FILES['aFile']['size'][$i],
					);
					$aFileInfo = goImage($aFile);

					if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
					{

						// $oPdo->rollback();
						$aReturn['nStatus'] = 0;
						$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
						// $aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/discuss/php/_post_0.php']);
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
						'nKid'		=> (int) 0,//$nLastId,
						'sTable'		=> (string) CLIENT_GROUP_MSG,
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

					$aReturn['aData'][$nImageLastId] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',NOWTIME).CLIENT_GROUP_MSG.'/'.$sFname;
				}
			}
		}
	}


	echo json_encode($aReturn);
	exit;
?>
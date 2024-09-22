<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/setting.php');

	$nLid 	= filter_input_int('nLid',		INPUT_POST,0);
	$sName0 	= filter_input_str('sName0',		INPUT_POST,'',50);
	$sName1 	= filter_input_str('sName1',		INPUT_POST,'',50);
	$sIdNumber 	= filter_input_str('sIdNumber',	INPUT_POST,'',50);
	$sBirthday 	= filter_input_str('sBirthday',	INPUT_POST,'',50);

	$aData = array();
	$aPendingField = array_flip(explode(',', $aSystem['aParam']['sPendingField']));	// 需要審核欄位
	$aOld = array();
	$sMsg = '';
	$aEditLog = array();
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

	// 更新用戶資料
	if ($aJWT['a'] == 'UPT'.$aUser['nId'])
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						sName1,
						nLid,
						sLocationTime,
						nPendingStatus,
						sPendingStatus
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		$aOld[CLIENT_USER_DATA] = $aRows;
		$sLocationTime = $aRows['sLocationTime'];
		$aUserPendingStatus = explode(',', $aRows['sPendingStatus']);		// 資料審核狀態

		$sSQL = '	SELECT 	nId,
						nUid,
						sIdNumber,
						sBirthday,
						nBirthday
				FROM 	'.CLIENT_USER_DETAIL.'
				WHERE nUid = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		$aOld[CLIENT_USER_DETAIL] = $aRows;

		if ($nLid != 0)
		{
			$sSQL = '	SELECT 	nLid
					FROM 	'.CLIENT_LOCATION.'
					WHERE nOnline = 1
					AND 	nLid = :nLid
					AND 	sLang LIKE :sLang
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
			$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aERROR['LID'];
			}
		}

		// 欄位檢查 start
		// 2021-04-26 YL 身分證不使用
		// if ($aOld[CLIENT_USER_DETAIL]['sIdNumber'] == '')
		// {
		// 	$sIdNumber 	= strtoupper($sIdNumber);
		// 	if ($sIdNumber == '')
		// 	{
		// 		$aReturn['nStatus'] = 0;
		// 		$aReturn['sMsg']	.= aERROR['IDEMPTY'].'<br>';
		// 	}
		// 	elseif (!preg_match("/^[A-Z]{1}[12ABCD]{1}[0-9]{8}$/", $sIdNumber))
		// 	{
		// 		$aReturn['nStatus'] = 0;
		// 		$aReturn['sMsg']	.= aERROR['IDFORMATE'].'<br>';
		// 	}
		// 	else
		// 	{
		// 		// id 是否重複
		// 		$sSQL = '	SELECT 	1
		// 				FROM 	'.CLIENT_USER_DETAIL.'
		// 				WHERE nUid != :nUid
		// 				AND 	sIdNumber = :sIdNumber
		// 				LIMIT 1';
		// 		$Result = $oPdo->prepare($sSQL);
		// 		$Result->bindValue(':nUid', 		$aUser['nId'],	PDO::PARAM_INT);
		// 		$Result->bindValue(':sIdNumber', 	$sIdNumber,		PDO::PARAM_STR);
		// 		sql_query($Result);
		// 		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		// 		if ($aRows !== false)
		// 		{
		// 			$aReturn['nStatus'] = 0;
		// 			$aReturn['sMsg'] = aERROR['IDDUPLICATE'];
		// 		}
		// 	}
		// }
		if ($aOld[CLIENT_USER_DETAIL]['sBirthday'] == '')
		{
			if ($sBirthday == '')
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg']	.= aERROR['BIRTHDAYEMPTY'].'<br>';
			}
		}

		if($sName0 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg']	.= aERROR['NAME0EMPTY'].'<br>';
		}
		elseif (mb_strlen($sName0) > 20)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg']	.= aERROR['NAME0LENGTH'].'<br>';
		}
		// 真實姓名不給改 2021-01-28
		// if($sName1 == '')
		// {
		// 	$aReturn['nStatus'] = 0;
		// 	$aReturn['sMsg']	.= aERROR['NAME1EMPTY'].'<br>';
		// }
		// elseif (mb_strlen($sName1) > 20)
		// {
		// 	$aReturn['nStatus'] = 0;
		// 	$aReturn['sMsg']	.= aERROR['NAME1LENGTH'].'<br>';
		// }

		// 欄位檢查 end
		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'sName0' 	=> (string) $sName0,
				// 'sName1' 	=> (string) $sName1, // 真實姓名不給改 2021-01-28
				'nUpdateTime'=> (int) NOWTIME,
				'sUpdateTime'=> (string) NOWDATE,
			);
			if ($aUser['nLid'] == 0 || ($aUser['nLid'] != 0 && strtotime($sLocationTime.'+30 day') <= NOWTIME)) // 修改所在地
			{
				$aSQL_Array['nLid'] = $nLid;
				$aSQL_Array['nLocationTime'] = NOWTIME;
				$aSQL_Array['sLocationTime'] = NOWDATE;
			}
			if ($aUser['nStatus'] == 11)
			{
				if ($aUserPendingStatus[$aPendingField['sName1']] == 99 && $sName1 != $aOld[CLIENT_USER_DATA]['sName1']) // 被拒絕更正資料
				{
					$aUserPendingStatus[$aPendingField['sName1']] = 0; // 改成未審核
				}
				if ($aUserPendingStatus[$aPendingField['sBirthday']] == 99 && $sBirthday != $aOld[CLIENT_USER_DETAIL]['sBirthday']) // 被拒絕更正資料
				{
					$aUserPendingStatus[$aPendingField['sBirthday']] = 0; // 改成未審核
				}
				if ($aUserPendingStatus[$aPendingField['sIdNumber']] == 99 && $sIdNumber != $aOld[CLIENT_USER_DETAIL]['sIdNumber']) // 被拒絕更正資料
				{
					$aUserPendingStatus[$aPendingField['sIdNumber']] = 0; // 改成未審核
				}
				$aSQL_Array['sPendingStatus'] = implode(',', $aUserPendingStatus);
				if (!in_array(99, $aUserPendingStatus)) // 都沒有被拒絕
				{
					$aSQL_Array['nPendingStatus'] = 0; // 審核狀態再次變為未審核
				}
			}


			$sSQL = '	UPDATE '.CLIENT_USER_DATA.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aOld[CLIENT_USER_DATA];
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			$aSQL_Array = array();
			if ($sIdNumber != '' && $sIdNumber != $aOld[CLIENT_USER_DETAIL]['sIdNumber'])
			{
				$aSQL_Array['sIdNumber'] = (string) $sIdNumber;
			}
			if ($sBirthday != '' && $sBirthday != $aOld[CLIENT_USER_DETAIL]['sBirthday'])
			{
				$aSQL_Array['sBirthday'] = (string) $sBirthday;
				$aSQL_Array['nBirthday'] = (int)	strtotime($sBirthday.' 00:00:00');
			}
			if (!empty($aSQL_Array))
			{

				$sSQL = '	UPDATE '.CLIENT_USER_DETAIL.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
						WHERE	nUid = :nUid LIMIT 1 ';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_USER_DETAIL]['aOld'] = $aOld[CLIENT_USER_DETAIL];
				$aEditLog[CLIENT_USER_DETAIL]['aNew'] = $aSQL_Array;
			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aUser['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100305,
				'sParam'		=> (string) json_encode($aEditLog),
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
		}

		echo json_encode($aReturn);
		exit;
	}
?>
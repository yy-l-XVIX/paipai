<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/video.php');

	$nId 		= filter_input_int('nId',		INPUT_REQUEST,0);

	$aData = array();
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
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> '',
	);
	$nIsUpload = 0;
	$aData = array();
	$aPendingField = array_flip(explode(',', $aSystem['aParam']['sPendingField']));	// 需要審核欄位

	// 更新基本資料
	if ($aJWT['a'] == 'UPT'.$aUser['nId'])
	{
		// 變更頭像
		if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
		{
			$aFile['sTable'] = CLIENT_USER_DATA;
			$aFile['aFile'] = $_FILES['sFile'];
			$aFileInfo = goImage($aFile);
			if($aFileInfo['error'] == 'error')
			{
				$aReturn['sMsg'] .= aIMGERROR['ERROR'].'<br>';
				$aReturn['nStatus'] = 0;
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

			$nIsUpload ++;
			$aReturn['sMsg'] .= aVIDEO['HEADUPTV'].'<br>';
		}

		// 上傳影片
		if (isset($_FILES['sFileVideo']) && $_FILES['sFileVideo']['name']<>'')
		{
			# 檢查上傳數量
			$sSQL = '	SELECT	1
					FROM	'.	CLIENT_IMAGE_CTRL .'
					WHERE	nKid = :nKid
					AND 	sTable LIKE :sTable';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nKid', $aUser['nId'], PDO::PARAM_INT);
			$Result->bindValue(':sTable', 'client_user_video', PDO::PARAM_STR);
			sql_query($Result);
			$nCount = $Result->rowCount();
			if ($nCount >= $aSystem['aParam']['nVideoLimit'])
			{
				$aReturn['sMsg'] .= aVIDEO['MAXLIMIT'].$aSystem['aParam']['nVideoLimit'].'<br>';
				$aReturn['nStatus'] = 0;
				echo json_encode($aReturn);
				exit;
			}

			$aFileVideo['sTable'] = 'client_user_video';
			$aFileVideo['aFile'] = $_FILES['sFileVideo'];
			$aFileInfo = goImage($aFileVideo,1);

			if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
			{
				// $oPdo->rollback();
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
				echo json_encode($aReturn);
				exit;
			}
			else
			{
				$aTmp = explode('.',$aFileInfo['sFilename']);
				#$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
				$sFname = $aFileInfo['sFilename'];
			}

			$aSQL_Array = array(
				'nKid'		=> (int) $aUser['nId'],
				'sTable'		=> (string) 'client_user_video',
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
				'nLogCode'		=> (int) 7100310,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$nIsUpload ++;
			$aReturn['sMsg'] .= aVIDEO['UPTV'].'<br>';
		}

		if ($aUser['nStatus'] == 11)
		{
			$sSQL = '	SELECT 	nId,
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

			# update client_user_data
			$aSQL_Array = array(
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			if ($aUserPendingStatus[$aPendingField['sVideo']] == 99) // 被拒絕更正資料
			{
				$aUserPendingStatus[$aPendingField['sVideo']] = 0; // 改成未審核
			}
			$aSQL_Array['sPendingStatus'] = implode(',', $aUserPendingStatus);
			if (!in_array(99, $aUserPendingStatus)) // 都沒有被拒絕
			{
				$aSQL_Array['nPendingStatus'] = 0; // 審核狀態再次變為未審核
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

		}

		if ($nIsUpload == 0)
		{
			$aReturn['sMsg'] = aVIDEO['NOUPT'];
		}

		echo json_encode($aReturn);
		exit;
	}

	if ($aJWT['a'] == 'DELVIDEO'.$aUser['nId'])
	{

		$sSQL = '	SELECT 	nId,
						nKid,
						sTable,
						nType0,
						nCreateTime,
						sFile
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE nKid = :nKid
				AND 	nId = :nId
				AND 	sTable LIKE :sTable
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue('nKid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue('nId', $nId, PDO::PARAM_INT);
		$Result->bindValue('sTable', 'client_user_video', PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}

		if ($aReturn['nStatus'] == 1)
		{
			// 刪除舊的圖片資訊
			$sSQL = '	DELETE
					FROM 		' . CLIENT_IMAGE_CTRL . '
					WHERE 	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue('nId', $nId, PDO::PARAM_INT);
			sql_query($Result);

			$aData = array(
				'sImgUrl'	=> date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'],
				'delImg'	=> 1,
				'sUrl'	=> $aFile['sUrl']
			);
			delImage($aData);

			#紀錄動作 - 刪除
			$aEditLog[CLIENT_IMAGE_CTRL]['aOld'] = $aRows;
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100311,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = DELV;
		}
		echo json_encode($aReturn);
		exit;
	}


	//刪除照片

?>
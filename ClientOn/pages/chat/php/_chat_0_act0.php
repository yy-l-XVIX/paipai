<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/chat.php');

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
	$nGid = 0;

	if ($aJWT['a'] == 'JOIN'.$aUser['nId'])
	{
		#check again
		$sSQL = '	SELECT 	nId,
						nUid,
						nGid,
						nStatus
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nUid = :nUid
				AND 	nId = :nId';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aJWT['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}
		if ($aRows['nStatus'] == 1)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aCHAT['JOINED'];
		}

		if ($aReturn['nStatus'] == 1)
		{
			$nGid = $aRows['nGid'];
			$aSQL_Array = array(
				'nStatus' 		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_USER_GROUP_LIST.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aRows;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;

			// 紀錄動作
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aRows['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100905,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = UPTV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nGid;
		}
	}

	if ($aJWT['a'] == 'DENY'.$aUser['nId'])
	{
		#check again
		$sSQL = '	SELECT 	nId,
						nUid,
						nGid,
						nStatus
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nUid = :nUid
				AND 	nId = :nId';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aJWT['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}
		if ($aRows['nStatus'] == 1)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aCHAT['JOINED'];
		}

		if ($aReturn['nStatus'] == 1)
		{
			$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
					WHERE nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aRows;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = array();

			// 紀錄動作
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aRows['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100906,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = UPTV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']);
		}
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

	if ($aJWT['a'] == 'SELFDELGROUP')
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
		$Result->bindValue(':nId', $aJWT['nId'], PDO::PARAM_INT);
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
				'nStatus' 	=> (int) 2,
				'nCreateTime' => (int) NOWTIME,
				'sCreateTime' => (string) NOWDATE,
				'nUpdateTime' => (int) NOWTIME,
				'sUpdateTime' => (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_USER_GROUP_LIST.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aJWT['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = $aRows;
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = $aSQL_Array;

			// 紀錄動作
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aJWT['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100904,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
		}
	}

	echo json_encode($aReturn);
	exit;
?>
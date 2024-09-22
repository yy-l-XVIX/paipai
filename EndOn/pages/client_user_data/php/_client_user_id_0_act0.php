<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');

	$nId		= filter_input_int('nId',		INPUT_REQUEST, 0);
	$nImgKid	= filter_input_int('nImgKid',		INPUT_REQUEST,0);
	$nType3	= filter_input_int('nType3',		INPUT_POST, 0);

	$nErr = 0;
	$sMsg = '';
	$aValid = array();
	if ($aJWT['a'] == 'INS')
	{}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nType3
				FROM 		'.CLIENT_USER_DATA.'
				WHERE 	nId = :nId
				AND		nOnline != 99
				LIMIT 	1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOld === false)
		{
			$nErr	= 1;
			$sMsg	= NODATA.'<br>';
		}

		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_id_0_upt0.php']).'&nId='.$nId;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{

			$aSQL_Array = array(
				'nType3'		=> (int) $nType3,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			$sSQL = '	UPDATE	'.CLIENT_USER_DATA.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 更新
			$aSQL_Array = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nId,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103302,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aSQL_Array);

			// 上圖
			unset($aEditLog['CLIENT_USER_DATA']);
			for($nI = 0;$nI<2;$nI++)
			{
				if (isset($_FILES['sFile'.$nI]) && $_FILES['sFile'.$nI]['name']<>'')
				{
					$aFile['sTable'] = 'client_user_id';
					$aFile['aFile'] = $_FILES['sFile'.$nI];
					$aFileInfo = goImage($aFile);
					if($aFileInfo['error'] == 'error')
					{
						$aReturn['sMsg'] .= aImgErr['error'].'<br>';
						$aReturn['nStatus'] = 1;
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
						'nKid'		=> (int) $nId,
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
						'nWho'		=> (int) $aAdm['nId'],
						'nWhom'		=> (int) 0,
						'sWhomAccount'	=> (string) '',
						'nKid'		=> (int) $nImageLastId,
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) 8103303,
						'sParam'		=> (string) json_encode($aEditLog),
						'nType0'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					DoActionLog($aActionLog);
				}
			}

			$aJumpMsg['0']['sMsg'] = UPTV;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_id_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

	if ($aJWT['a'] == 'DELIMG'.$nImgKid)
	{
		$sSQL = '	SELECT	nKid,
						sTable,
						sFile,
						nCreateTime,
						nType0
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE		sTable LIKE :sTable
				AND		nKid = :nImgKid
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nImgKid', $nImgKid, PDO::PARAM_INT);
		$Result->bindValue(':sTable', 'client_user_id', PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aJumpMsg['0']['sMsg'] = NODATA;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_id_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$sSQL = '	DELETE FROM	'. CLIENT_IMAGE_CTRL . '
					WHERE 	nKid = :nImgKid
					AND		sTable LIKE :sTable
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nImgKid', $nImgKid, PDO::PARAM_INT);
			$Result->bindValue(':sTable', 'client_user_id', PDO::PARAM_STR);
			sql_query($Result);

			$aEditLog[CLIENT_IMAGE_CTRL]['aOld'] = $aRows;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nImgKid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103304,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aData = array(
				'sImgUrl'	=> date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'],
				'delImg'	=> 1,
				'sUrl'	=> $aFile['sUrl']
			);
			delImage($aData);

			$aJumpMsg['0']['sMsg'] = DELV;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_id_0_upt0.php']).'&nId='.$nId;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}

	}

?>
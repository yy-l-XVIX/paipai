<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_broadcast.php');
	#require結束

	#參數接收區
	$nLid		= filter_input_int('nLid',		INPUT_REQUEST,0);
	$nKid		= filter_input_int('nKid',		INPUT_POST,0);
	$nImgKid	= filter_input_int('nImgKid',		INPUT_REQUEST,0);
	$nFileCount	= filter_input_int('nFileCount',	INPUT_POST,0);
	$nSync	= filter_input_int('nSync',		INPUT_POST,0);
	$nOnline	= filter_input_int('nOnline',		INPUT_POST,0);
	$aName 	= array();
	if(isset($_POST['sName0']))
	{
		$aName = $_POST['sName0'];
	}
	#參數結束

	#參數宣告區
	$aId = array();
	$aData = array();
	$aEditLog = array(
		CLIENT_IMAGE_CTRL	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
		CLIENT_BROADCAST	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	#宣告結束

	#程式邏輯區
	if ($aJWT['a'] == 'INS')
	{
		$nLid = 0;#開始預設 = 0;
		foreach(aLANG as $LPsLang => $LPsText)
		{
			$oPdo->beginTransaction();
			$aSQL_Array = array(
				'sName0'		=> (string) $aName[$LPsLang],
				'nKid'		=> (int) $nKid,
				'nLid'		=> (int) $nLid,
				'sLang'		=> (string) $LPsLang,
				'nSync'		=> (int) $nSync,
				'nOnline'		=> (int) $nOnline,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '. CLIENT_BROADCAST . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			if($nLid == 0)
			{
				$nLid = $nLastId;

				# 更新 TW nLid
				$aSQL_Array = array(
					'nLid' => (int) $nLid,
				);
				$sSQL = '	UPDATE '. CLIENT_BROADCAST . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nLid, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
			}

			for($LPnI=0;$LPnI < $nFileCount; $LPnI++)
			{
				if (isset($_FILES['sFile'.$LPsLang.$LPnI]) && $_FILES['sFile'.$LPsLang.$LPnI]['name']<>'')
				{
					$aFile['sTable'] = CLIENT_BROADCAST;
					$aFile['aFile'] = $_FILES["sFile".$LPsLang.$LPnI];
					$aFileInfo = goImage($aFile);
					if($aFileInfo['error'] == 'error')
					{
						$aJumpMsg['0']['sMsg'] = aImgErr['error'];
						$aJumpMsg['0']['sShow'] = 1;
						$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']);
						$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
					}
					else
					{
						$aTmp = explode('.',$aFileInfo['sFilename']);
						$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
						$aFname[$LPnI] = $aFileInfo['sFilename'];
					}

					$aSQL_Array = array(
						'nKid'		=> (int) $nLastId,
						'sTable'		=> (string) CLIENT_BROADCAST,
						'sFile'		=> (string) $aFname[$LPnI],
						'nType0'		=> (int) $LPnI,
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
						'nKid'		=> (int) $nLastId,
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) 8102304,
						'sParam'		=> (string) json_encode($aEditLog),
						'nType0'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					DoActionLog($aActionLog);
				}
			}

			#紀錄動作 - 新增
			$aEditLog[CLIENT_BROADCAST]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_BROADCAST]['aNew']['nId'] = $nLastId;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8102301,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();
		}

		$aJumpMsg['0']['sMsg'] = INSV;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}

	if ($aJWT['a'] == 'UPT'.$nLid)
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						nKid,
						nOnline,
						nSync,
						sLang
				FROM 		'. CLIENT_BROADCAST .'
				WHERE 	nLid = :nLid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid',$nLid,PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aId[$aRows['sLang']] = $aRows['nId'];
			$aData[$aRows['nId']] = $aRows;
		}

		foreach(aLANG as $LPsLang => $LPsText)
		{
			$oPdo->beginTransaction();

			$aSQL_Array = array(
				'sName0'		=> (string) $aName[$LPsLang],
				'nKid'		=> (int) $nKid,
				'nSync'		=> (int) $nSync,
				'nOnline'		=> (int) $nOnline,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			if(isset($aId[$LPsLang]))
			{
				$sSQL = '	UPDATE '. CLIENT_BROADCAST . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aId[$LPsLang], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
			}
			//沒的話新增
			else
			{
				$aSQL_Array['nCreateTime']	= (int) NOWTIME;
				$aSQL_Array['sCreateTime']	= (string) NOWDATE;
				$aSQL_Array['sLang']		= (string) $LPsLang;
				$aSQL_Array['nLid']		= (int) $nLid;

				$sSQL = 'INSERT INTO '. CLIENT_BROADCAST .' ' . sql_build_array('INSERT', $aSQL_Array );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
				$aId[$LPsLang] = $oPdo->lastInsertId();
				$aData[$aId[$LPsLang]] = $aSQL_Array;
			}

			for($LPnI=0;$LPnI < $nFileCount; $LPnI++)
			{
				if (isset($_FILES['sFile'.$LPsLang.$LPnI]) && $_FILES['sFile'.$LPsLang.$LPnI]['name']<>'')
				{
					$aFile['sTable'] = CLIENT_BROADCAST;
					$aFile['aFile'] = $_FILES["sFile".$LPsLang.$LPnI];
					$aFileInfo = goImage($aFile);
					if($aFileInfo['error'] == 'error')
					{
						$aJumpMsg['0']['sMsg'] = aImgErr['error'];
						$aJumpMsg['0']['sShow'] = 1;
						$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']);
						$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
					}
					else
					{
						$aTmp = explode('.',$aFileInfo['sFilename']);
						$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
						$aFname[$LPnI] = $aFileInfo['sFilename'];
					}

					$aSQL_Array = array(
						'nKid'		=> (int) $aId[$LPsLang],
						'sTable'		=> (string) CLIENT_BROADCAST,
						'sFile'		=> (string) $aFname[$LPnI],
						'nType0'		=> (int) $LPnI,
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
						'nKid'		=> (int) $aId[$LPsLang],
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) 8102304,
						'sParam'		=> (string) json_encode($aEditLog),
						'nType0'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					DoActionLog($aActionLog);
				}
			}

			#紀錄動作 - 更新
			$aEditLog[CLIENT_BROADCAST]['aOld'] = $aData[$aId[$LPsLang]];
			$aEditLog[CLIENT_BROADCAST]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_BROADCAST]['aNew']['nId'] = $aId[$LPsLang];
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aId[$LPsLang],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8102302,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();
		}

		$aJumpMsg['0']['sMsg'] = UPTV;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}

	if ($aJWT['a'] == 'DEL'.$nLid)
	{
		$sSQL = '	SELECT 	nId,
						nLid,
						nOnline,
						nUpdateTime,
						sUpdateTime
				FROM 		'.CLIENT_BROADCAST.'
				WHERE 	nOnline != 99
				AND 		nLid = :nLid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nId']] = $aRows;
		}

		if (empty($aData))
		{
			$aJumpMsg['0']['sMsg'] = NODATA;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aEditLog[CLIENT_BROADCAST]['aOld'] = $aData;
			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_BROADCAST . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nLid = :nLid ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_BROADCAST]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8102303,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aJumpMsg['0']['sMsg'] = DELV;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']);
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
				WHERE		sTable LIKE \''. CLIENT_BROADCAST .'\'
				AND		nKid = :nImgKid
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nImgKid', $nImgKid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);

		if($aRows === false)
		{
			$aJumpMsg['0']['sMsg'] = NODATA;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$sSQL = '	DELETE FROM	'. CLIENT_IMAGE_CTRL . '
					WHERE 	nKid = :nImgKid
					AND		sTable LIKE \'' . CLIENT_BROADCAST . '\'
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nImgKid', $nImgKid, PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_IMAGE_CTRL]['aOld'] = $aRows;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nImgKid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8102305,
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
			// print_r($aData);exit;
			delImage($aData);

			$aJumpMsg['0']['sMsg'] = DELV;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0_upt0.php']).'&nLid='.$nLid;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}

	}
	#程式邏輯結束
?>
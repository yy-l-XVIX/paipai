<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_location.php');
	#require??

	#參數接收區
	$nLid		= filter_input_int('nLid',	INPUT_REQUEST,0);
	$nImgKid	= filter_input_int('nImgKid',		INPUT_REQUEST,0);
	$nOnline	= filter_input_int('nOnline',	INPUT_POST, 1);
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
		CLIENT_LOCATION	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_job/php/_client_location_0.php']).'&nLid='.$nLid.'&nOnline='.$nOnline;
	#宣告結束

	#程式邏輯區
	if ($aJWT['a'] == 'INS')
	{

		$nLid = 0;
		foreach(aLANG as $LPsLang => $LPsText)
		{
			$oPdo->beginTransaction();

			$aSQL_Array = array(
				'sName0'		=> (string) $aName[$LPsLang],
				'nLid'		=> (int) $nLid,
				'sLang'		=> (string) $LPsLang,
				'nOnline'		=> (int) $nOnline,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '. CLIENT_LOCATION . ' ' . sql_build_array('INSERT', $aSQL_Array );
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
				$sSQL = '	UPDATE '. CLIENT_LOCATION . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nLid, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
			}

			$aEditLog[CLIENT_LOCATION]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_LOCATION]['aNew']['nId'] = $nLastId;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105001,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = INSV;

			for($LPnI=0;$LPnI < 1; $LPnI++)
			{
				if (isset($_FILES['sFile'.$LPsLang.$LPnI]) && $_FILES['sFile'.$LPsLang.$LPnI]['name']<>'')
				{
					$aFile['sTable'] = CLIENT_LOCATION;
					$aFile['aFile'] = $_FILES["sFile".$LPsLang.$LPnI];
					$aFileInfo = goImage($aFile);
					if($aFileInfo['error'] == 'error')
					{
						$oPdo->rollBack();
						$aJumpMsg['0']['sMsg'] = aImgErr['error'];
						$aJumpMsg['0']['sShow'] = 1;
						$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_job/php/_client_location_0.php']);
						$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;

						break 2;
					}
					else
					{
						$aTmp = explode('.',$aFileInfo['sFilename']);
						$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
						$aFname[$LPnI] = $aFileInfo['sFilename'];
					}

					$aSQL_Array = array(
						'nKid'		=> (int) $nLastId,
						'sTable'		=> (string) CLIENT_LOCATION,
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
						'nKid'		=> (int) $nImageLastId,
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) 8105004,
						'sParam'		=> (string) json_encode($aEditLog),
						'nType0'		=> (int) 0,
						'nCreateTime'	=> (int) NOWTIME,
						'sCreateTime'	=> (string) NOWDATE,
					);
					DoActionLog($aActionLog);
				}
			}
			$oPdo->commit();
		}
	}

	if ($aJWT['a'] == 'UPT'.$nLid)
	{
		$sSQL = '	SELECT 	nId,
						sLang
				FROM 		'. CLIENT_LOCATION .'
				WHERE 	nLid = :nLid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid',$nLid,PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aId[$aRows['sLang']] = $aRows['nId'];
		}
		if (empty($aId))
		{
			$nErr = 1;
			$sMsg = NODATA;
		}

		if ($nErr == 0)
		{
			foreach(aLANG as $LPsLang => $LPsText)
			{
				$oPdo->beginTransaction();

				$aSQL_Array = array(
					'sName0'		=> (string) $aName[$LPsLang],
					'nOnline'		=> (int) $nOnline,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);

				if(isset($aId[$LPsLang]))
				{
					$sSQL = '	UPDATE '. CLIENT_LOCATION . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
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

					$sSQL = 'INSERT INTO '. CLIENT_LOCATION .' ' . sql_build_array('INSERT', $aSQL_Array );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$aId[$LPsLang] = $oPdo->lastInsertId();
				}

				#紀錄動作 - 更新
				$aEditLog[CLIENT_LOCATION]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_LOCATION]['aNew']['nId'] = $aId[$LPsLang];
				$aActionLog = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $aId[$LPsLang],
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8105002,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$sMsg = UPTV;

				for($LPnI=0;$LPnI < 1; $LPnI++)
				{
					if (isset($_FILES['sFile'.$LPsLang.$LPnI]) && $_FILES['sFile'.$LPsLang.$LPnI]['name']<>'')
					{
						$aFile['sTable'] = CLIENT_LOCATION;
						$aFile['aFile'] = $_FILES["sFile".$LPsLang.$LPnI];
						$aFileInfo = goImage($aFile);
						if($aFileInfo['error'] == 'error')
						{
							$oPdo->rollBack();
							$aJumpMsg['0']['sMsg'] = aImgErr['error'];
							$aJumpMsg['0']['sShow'] = 1;
							$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_job/php/_client_location_0.php']);
							$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;

							break 2;
						}
						else
						{
							$aTmp = explode('.',$aFileInfo['sFilename']);
							$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
							$aFname[$LPnI] = $aFileInfo['sFilename'];
						}

						$aSQL_Array = array(
							'nKid'		=> (int) $aId[$LPsLang],
							'sTable'		=> (string) CLIENT_LOCATION,
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
							'nKid'		=> (int) $nImageLastId,
							'sIp'			=> (string) USERIP,
							'nLogCode'		=> (int) 8105004,
							'sParam'		=> (string) json_encode($aEditLog),
							'nType0'		=> (int) 0,
							'nCreateTime'	=> (int) NOWTIME,
							'sCreateTime'	=> (string) NOWDATE,
						);
						DoActionLog($aActionLog);
					}
				}

				$oPdo->commit();
			}
		}
	}

	if ($aJWT['a'] == 'DEL'.$nLid)
	{
		$sSQL = '	SELECT 	nId,
						nLid,
						nOnline,
						nUpdateTime,
						sUpdateTime
				FROM 		'.CLIENT_LOCATION.'
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
			$nErr = 1;
			$sMsg = NODATA;
		}
		else
		{
			$aEditLog[CLIENT_LOCATION]['aOld'] = $aData;
			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_LOCATION . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nLid = :nLid ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_LOCATION]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105003,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = DELV;

		}
	}

	if ($aJWT['a'] == 'DELIMG'.$nImgKid)
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_job/php/_client_location_0_upt0.php']).'&nLid='.$nLid;
		$sSQL = '	SELECT	nKid,
						sTable,
						sFile,
						nCreateTime,
						nType0
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE		nKid = :nImgKid
				AND		sTable LIKE :sTable
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nImgKid', $nImgKid, PDO::PARAM_INT);
		$Result->bindValue(':sTable', CLIENT_LOCATION, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr = 1;
			$sMsg = NODATA;
		}

		if ($nErr == 0)
		{
			$sSQL = '	DELETE FROM	'. CLIENT_IMAGE_CTRL . '
					WHERE 	nKid = :nImgKid
					AND		sTable LIKE :sTable
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nImgKid', $nImgKid, PDO::PARAM_INT);
			$Result->bindValue(':sTable', CLIENT_LOCATION, PDO::PARAM_STR);
			sql_query($Result);

			$aEditLog[CLIENT_IMAGE_CTRL]['aOld'] = $aRows;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nImgKid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105005,
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

			$sMsg = DELV;
		}
	}
	#程式邏輯結束

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_bank.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');

	$nId		= filter_input_int('nId',		INPUT_REQUEST, 0);
	$nImgKid	= filter_input_int('nImgKid',		INPUT_REQUEST,0);
	$sAccount	= filter_input_str('sAccount',	INPUT_POST, '', 20);
	$nBid		= filter_input_int('nBid',		INPUT_POST, 0);
	$nOnline	= filter_input_int('nOnline',		INPUT_POST, 0);
	$sName0	= filter_input_str('sName0',		INPUT_POST, '', 20);
	$sName1	= filter_input_str('sName1',		INPUT_POST, '', 20);
	$sName2	= filter_input_str('sName2',		INPUT_POST, '', 20);

	$nErr = 0;
	$sMsg = '';
	$aValue = array(
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sBackParamJWT = sys_jwt_encode($aValue);
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0.php']).$aJWT['sBackParam'];
	$aValid = array();
	if ($aJWT['a'] == 'INS')
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_upt0.php']).'&sJWT='.$sBackParamJWT;

		$sSQL = '	SELECT 	nId
				FROM 		'.CLIENT_USER_DATA.'
				WHERE 	nOnline != 99
				AND 		sAccount LIKE :sAccount';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	.=  aBANK['USERNOEXIST'].'<br>';
		}
		else
		{
			$nUid = $aRows['nId'];
		}

		$sSQL = '	SELECT 	1
				FROM 		'.SYS_BANK.'
				WHERE 	nOnline = 1
				AND 		nId = :nBid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nBid', $nBid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	.= aBANK['BANKNOEXIST'].'<br>';
		}

		if ($sName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aBANK['NAMEUNDEFINE0'].'<br>';
		}

		if ($sName1 == '')
		{
			$nErr	= 1;
			$sMsg	.= aBANK['NAMEUNDEFINE1'].'<br>';
		}

		if ($sName2 == '')
		{
			$nErr	= 1;
			$sMsg	.= aBANK['NAMEUNDEFINE2'].'<br>';
		}
		if ($_FILES['sFile']['name'] == '')
		{
			$nErr	= 1;
			$sMsg	.= aBANK['IMAGEREQUIRE'].'<br>';
		}

		if ($nErr == 0)
		{
			$oPdo->beginTransaction();
			$aSQL_Array = array(
				'nUid'		=> (int) $nUid,
				'nBid'		=> (int) $nBid,
				'sName0'		=> (string) $sName0,
				'sName1'		=> (string) $sName1,
				'sName2'		=> (string) $sName2,
				'nOnline'		=> (int) $nOnline,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '. CLIENT_USER_BANK . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_USER_BANK]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_USER_BANK]['aNew']['nId'] = $nLastId;

			$aData = array(
				'nKid'	=> $nLastId,
				'sTable'	=> CLIENT_USER_BANK,
				'sName0'	=> $sName0,
				'NOWTIME'	=> NOWTIME
			);
			$sKey = cDataEncrypt::update($aData,false);

			$aSQL_Array = array(
				'nKid'		=> (int) $nLastId,
				'sTable'		=> (string) CLIENT_USER_BANK,
				'nEncryptTime'	=> (int) NOWTIME,
				'sEncryptKey'	=> (string) $sKey,
			);

			$sSQL = 'INSERT INTO '. CLIENT_DATA_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nUid,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103201,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			// 上圖
			unset($aEditLog[CLIENT_USER_BANK]);
			$sMsg = INSV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0.php']).$aJWT['sBackParam'];
			if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
			{
				$aFile['sTable'] = CLIENT_USER_BANK;
				$aFile['aFile'] = $_FILES['sFile'];
				$aFileInfo = goImage($aFile);

				if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
				{
					$oPdo->rollback();
					$nErr = 1;
					$sMsg = aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
					$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_upt0.php']).'&sJWT='.$sBackParamJWT;
				}
				else
				{
					$aTmp = explode('.',$aFileInfo['sFilename']);
					$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
					$sFname = $aFileInfo['sFilename'];


					$aSQL_Array = array(
						'nKid'		=> (int) $nLastId,
						'sTable'		=> (string) CLIENT_USER_BANK,
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
						'nWho'		=> (int) $aAdm['nId'],
						'nWhom'		=> (int) 0,
						'sWhomAccount'	=> (string) '',
						'nKid'		=> (int) $nImageLastId,
						'sIp'			=> (string) USERIP,
						'nLogCode'		=> (int) 8103204,
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

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_upt0.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;
		$sSQL = '	SELECT 	nId,
						nUid,
						nBid,
						sName0,
						sName1,
						sName2,
						nOnline
				FROM 		'.CLIENT_USER_BANK.'
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

		$sSQL = '	SELECT 	1
				FROM 		'.SYS_BANK.'
				WHERE 	nOnline = 1
				AND 		nId = :nBid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nBid', $nBid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	.= aBANK['BANKNOEXIST'].'<br>';
		}

		if ($sName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aBANK['NAMEUNDEFINE0'].'<br>';
		}

		if ($sName1 == '')
		{
			$nErr	= 1;
			$sMsg	.= aBANK['NAMEUNDEFINE1'].'<br>';
		}

		if ($sName2 == '')
		{
			$nErr	= 1;
			$sMsg	.= aBANK['NAMEUNDEFINE2'].'<br>';
		}

		if ($nErr == 0)
		{
			$aValid = array(
				'nKid'	=> $nId,
				'sTable'	=> CLIENT_USER_BANK,
				'sName0'	=> $sName0,
				'sNameOld'	=> $aOld['sName0'],
				'NOWTIME'	=> NOWTIME
			);
			$sKey = cDataEncrypt::update($aValid);

			if($sKey !== false)
			{
				$oPdo->beginTransaction();
				$aSQL_Array = array(
					'nBid'		=> (int) $nBid,
					'sName0'		=> (string) $sName0,
					'sName1'		=> (string) $sName1,
					'sName2'		=> (string) $sName2,
					'nOnline'		=> (int) $nOnline,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);

				$aEditLog[CLIENT_USER_BANK]['aOld'] = $aOld;
				$aEditLog[CLIENT_USER_BANK]['aNew'] = $aSQL_Array;

				$sSQL = '	UPDATE	'.CLIENT_USER_BANK.'
						SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
						WHERE	nId = :nId
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aSQL_Array = array(
					'nKid'		=> (int) $aOld['nId'],
					'sTable'		=> (string) CLIENT_USER_BANK,
					'nEncryptTime'	=> (int) NOWTIME,
					'sEncryptKey'	=> (string) $sKey,
				);

				$sSQL = '	UPDATE '. CLIENT_DATA_CTRL . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nKid = :nKid AND sTable LIKE :sTable LIMIT 1 ';
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				# 紀錄動作 - 更新
				$aSQL_Array = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) $aOld['nUid'],
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nId,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8103202,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aSQL_Array);

				// 上圖
				unset($aEditLog[CLIENT_USER_BANK]);

				$sMsg = UPTV;
				$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0.php']).$aJWT['sBackParam'];
				if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
				{
					$aFile['sTable'] = CLIENT_USER_BANK;
					$aFile['aFile'] = $_FILES['sFile'];
					$aFileInfo = goImage($aFile);

					if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
					{
						$oPdo->rollback();
						$nErr = 1;
						$sMsg = aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
						$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_upt0.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;
					}
					else
					{
						$aTmp = explode('.',$aFileInfo['sFilename']);
						$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
						$sFname = $aFileInfo['sFilename'];


						$aSQL_Array = array(
							'nKid'		=> (int) $nId,
							'sTable'		=> (string) CLIENT_USER_BANK,
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
							'nWho'		=> (int) $aAdm['nId'],
							'nWhom'		=> (int) 0,
							'sWhomAccount'	=> (string) '',
							'nKid'		=> (int) $nImageLastId,
							'sIp'			=> (string) USERIP,
							'nLogCode'		=> (int) 8103204,
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
			else
			{
				$nErr = 1;
				$sMsg = NODATA;
			}
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nUid,
						nOnline
				FROM 		'.CLIENT_USER_BANK.'
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
			$sMsg	= NODATA;
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nOnline'		=> 99,
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);

			$sSQL = '	UPDATE	'.CLIENT_USER_BANK.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 刪除
			$aEditLog[CLIENT_USER_BANK]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_BANK]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $aOld['nUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) 0,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103203,
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
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_upt0.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;
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
		$Result->bindValue(':sTable', CLIENT_USER_BANK, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
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
			$Result->bindValue(':sTable', CLIENT_USER_BANK, PDO::PARAM_STR);
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
			delImage($aData);
			$sMsg = DELV;

		}
	}

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
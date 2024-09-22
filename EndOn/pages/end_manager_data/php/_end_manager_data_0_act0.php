<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'. $aSystem['sLang'] .'/end_manager_data.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Plugins/GoogleAuthenticator/googleClass.php');

	$nId				= filter_input_int('nId',		INPUT_REQUEST, 0);
	$sAccount			= filter_input_str('sAccount',	INPUT_POST, '', 20);
	$sPassword			= filter_input_str('sPassword',	INPUT_POST, '', 32);
	$sNewPassword		= filter_input_str('sNewPassword',	INPUT_POST, '', 32);
	$sConfirmPassword		= filter_input_str('sConfirmPassword',INPUT_POST, '', 32);
	$nOnline			= filter_input_int('nOnline',		INPUT_POST, 0);
	$nAdmType			= filter_input_int('nAdmType',	INPUT_POST, 0);
	$nGoogle			= filter_input_int('nGoogle',		INPUT_POST, 0); # 0 =>不啟用 1=> 啟用
	$sName0			= filter_input_str('sName0',		INPUT_POST, '',50);
	$sIp				= filter_input_str('sIp',		INPUT_POST, '',255);
	$nType1			= filter_input_int('nType1',		INPUT_POST, 0);
	$nLid				= filter_input_int('nLid',		INPUT_POST, 0);

	$oGg 	= new PHPGangsta_GoogleAuthenticator;
	$aControl = array();
	$aSetControl = array();
	$aActionLog = array();
	if (isset($_POST['aControl']))
	{
		$aSetControl = $_POST['aControl'];
	}

	$nErr	= 0;
	$sMsg = '';
	$sChangePage = '';
	$aLogCode = array(
		END_MANAGER_DATA.'Ins' =>'8101101',
		END_MANAGER_DATA.'Upt' =>'8101102',
		END_MANAGER_DATA.'Del' =>'8101103',
		END_MENU_CTRL.'Ins' =>'8101105',
		END_MENU_CTRL.'Upt' =>'8101105',
		END_MENU_CTRL.'Del' =>'8101106',
	);
	$aEditLog = array(
		END_MANAGER_DATA => array(
			'aOld' =>array(),
			'aNew' =>array(),
		),
		SYS_GOOGLE_VERIFY => array(
			'aOld' =>array(),
			'aNew' =>array(),
		),
		END_MENU_CTRL => array(
			'aOld' =>array(),
			'aNew' =>array(),
		),
	);
	$aValue = array(
		'sBackParam' => $aJWT['sBackParam'],
	);
	$sBackParamJWT = sys_jwt_encode($aValue);
	$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_upt0.php']).'&sJWT='.$sBackParamJWT;

	if ($aJWT['a'] == 'INS')
	{
		$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_upt0.php']);
		if ($sAccount == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYACCOUNT'].'<div class="MarginBottom10"></div>';
		}
		else
		{
			$sSQL = '	SELECT 	1
					FROM 	'.END_MANAGER_DATA.'
					WHERE nOnline != 99
					AND 	sAccount LIKE :sAccount';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false)
			{
				$nErr	= 1;
				$sMsg	.= $sAccount.aERROR['USED'].'<div class="MarginBottom10"></div>';
			}
		}
		$nLeng = strlen($sAccount);
		if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sAccount) || $nLeng < 6 || $nLeng > 16)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['ACCOUNTFORMATE'].'<div class="MarginBottom10"></div>';
		}

		if ($sNewPassword == '' || $sConfirmPassword == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYPASSWORD'].'<div class="MarginBottom10"></div>';
		}
		$nLeng = strlen($sNewPassword);
		if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sNewPassword) || $nLeng < 6 || $nLeng > 16)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['PASSWORDFORMATE'].'<div class="MarginBottom10"></div>';
		}
		if ($sNewPassword <> $sConfirmPassword)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['UNMATCH'].'<div class="MarginBottom10"></div>';
		}

		if ($nErr == 0)
		{
			$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0.php']).'&nOnline='.$nOnline.'&nAdmType='.$nAdmType;

			$aSQL_Array = array(
				'sAccount'		=> (string) $sAccount,
				'sPassword'		=> (string) oCypher::ReHash($sNewPassword),
				'nOnline'		=> (int) $nOnline,
				'nAdmType'		=> (int) $nAdmType,
				'sName0'		=> (string) $sName0,
				'sIp'			=> (string) $sIp,
				'nLid'		=> (int) $nLid,
				'nType1'		=> (int) $nType1,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.END_MANAGER_DATA.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nUid = $oPdo->lastInsertId();
			$aEditLog[END_MANAGER_DATA]['aNew'] = $aSQL_Array;
			$aEditLog[END_MANAGER_DATA]['aNew']['nId'] = $nUid;

			# sys_google_verify
			$aSQL_Array = array(
				'nUid'		=> (int) $nUid,
				'sKey'		=> (string) '',
				'sTable'		=> (string) END_MANAGER_DATA,
				'nOnline'		=> (int) 0,
				'nStatus'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			if ($nGoogle == 1) # 啟用google
			{
				$aSQL_Array['sKey'] = $oGg->createSecret();
				$aSQL_Array['nOnline'] = 1;
				$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_upt0.php']).'&nId='.$nLastId;
			}

			$sSQL = 'INSERT INTO '.SYS_GOOGLE_VERIFY.' ' . sql_build_array('INSERT', $aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();
			$aEditLog[SYS_GOOGLE_VERIFY]['aNew'] = $aSQL_Array;
			$aEditLog[SYS_GOOGLE_VERIFY]['aNew']['nId'] = $nLastId;


			$sSQL = '	SELECT 	sControl
					FROM 	'.END_PERMISSION.'
					WHERE nId = :nId';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nAdmType, PDO::PARAM_INT);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aTempCtrl = explode('|',$aRows['sControl']);
				foreach ($aTempCtrl as $LPsCtrl)
				{
					$LPaTemp = explode('_',$LPsCtrl);
					$aControl[$LPaTemp[0]] = $LPaTemp[1];
				}
			}

			foreach ($aSetControl as $LPnMkid => $LPaMlid)
			{
				foreach ($LPaMlid as $LPnMlid)
				{
					if (!isset($aControl[$LPnMkid]) || strpos($aControl[$LPnMkid],$LPnMlid) === false)
					{
						continue;
					}

					$aSQL_Array = array(
						'nUid'		=> $nUid,
						'nMkid'		=> $LPnMkid,
						'nMlid'		=> $LPnMlid,
						'nCreateTime'	=> NOWTIME,
						'sCreateTime'	=> NOWDATE,
					);

					$sSQL = 'INSERT INTO '.END_MENU_CTRL.' ' . sql_build_array('INSERT', $aSQL_Array );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$LPnLastId = $oPdo->lastInsertId();
					$aEditLog[END_MENU_CTRL]['aNew'][$LPnLastId] = $aSQL_Array;
				}
			}

			# 紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nUid,
				'sWhomAccount'	=> (string) $sAccount,
				'nKid'		=> (int) $nUid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8101101,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = aMANAGER['INSERTSUCCESS'];
		}
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	sAccount,
						sPassword,
						nOnline,
						sName0,
						sIp,
						nLid,
						nType1,
						nAdmType
				FROM 	'.END_MANAGER_DATA.'
				WHERE nId = :nId
				AND	nOnline != 99
				AND 	nAdmType >= :nAdmType
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		$Result->bindValue(':nAdmType', $aAdm['nAdmType'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	= aERROR['NODATA'].'<div class="MarginBottom10"></div>';
		}
		$aEditLog[END_MANAGER_DATA]['aOld'] = $aRows;

		if (oCypher::ReHash($sPassword) <> $aAdm['sPassword'])
		{
			$nErr	= 1;
			$sMsg	.= aERROR['ERRORPASSWORD'].'<div class="MarginBottom10"></div>';
		}
		if ($sNewPassword != '' || $sConfirmPassword != '')
		{
			$nLeng = strlen($sNewPassword);
			if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sNewPassword) || $nLeng < 6 || $nLeng > 16)
			{
				$nErr	= 1;
				$sMsg	.= aERROR['PASSWORDFORMATE'].'<div class="MarginBottom10"></div>';
			}
			if ($sNewPassword <> $sConfirmPassword)
			{
				$nErr	= 1;
				$sMsg	.= aERROR['UNMATCH'].'<div class="MarginBottom10"></div>';
			}
		}

		if ($nErr == 0)
		{
			$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0.php']).$aJWT['sBackParam'];

			$aSQL_Array = array(
				'nOnline'		=> (int) $nOnline,
				'nAdmType'		=> (int) $nAdmType,
				'sName0'		=> (string) $sName0,
				'sIp'			=> (string) $sIp,
				'nLid'		=> (int) $nLid,
				'nType1'		=> (int) $nType1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			if ($aAdm['nAdmType'] != 1)
			{
				unset($aSQL_Array['nType1']);
			}
			if ($sNewPassword != '' && oCypher::ReHash($sNewPassword) != $aEditLog[END_MANAGER_DATA]['aOld']['sPassword'])
			{
				$aSQL_Array['sPassword'] = oCypher::ReHash($sNewPassword);
			}

			$sSQL = '	UPDATE	'.END_MANAGER_DATA.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND 	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$aEditLog[END_MANAGER_DATA]['aNew'] = $aSQL_Array;

			# sys_google_verify (admroot 可以改)
			if ($aAdm['nAdmType'] == 1)
			{
				$sSQL = '	SELECT 	nId,
								sKey,
								nStatus,
								nOnline
						FROM 	'.SYS_GOOGLE_VERIFY.'
						WHERE nUid = :nUid
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);
				$aEditLog[SYS_GOOGLE_VERIFY]['aOld'] = $aRows;
				if ($aRows['nOnline'] == 1 && $nGoogle == 0) # 使用者改不啟用
				{
					$aSQL_Array = array(
						'nOnline'		=> (int) 0,
						'nStatus'		=> (int) 0,
						'sKey'		=> (string) '',
						'nUpdateTime'	=> (int) NOWTIME,
						'sUpdateTime'	=> (string) NOWDATE,
					);
					$sSQL = '	UPDATE '.SYS_GOOGLE_VERIFY.'
							SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE nUid = :nUid
							LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$aEditLog[SYS_GOOGLE_VERIFY]['aNew'] = $aSQL_Array;
				}
				if ($aRows['nOnline'] == 0 && $nGoogle == 1) # 使用者改啟用
				{
					$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_upt0.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;

					$aSQL_Array = array(
						'nOnline'		=> (int) 1,
						'sKey'		=> (string) $oGg->createSecret(),
						'nUpdateTime'	=> (int) NOWTIME,
						'sUpdateTime'	=> (string) NOWDATE,
					);
					$sSQL = '	UPDATE '.SYS_GOOGLE_VERIFY.'
							SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE nUid = :nUid
							LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$aEditLog[SYS_GOOGLE_VERIFY]['aNew'] = $aSQL_Array;
				}

				$aSQL_Array = array(
					'nGoogle'		=> (int) $nGoogle,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	UPDATE '.END_MANAGER_COOKIE.'
						SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE nUid = :nUid';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
			}

			$sSQL = '	SELECT 	sControl
					FROM 	'.END_PERMISSION.'
					WHERE nId = :nId';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nAdmType, PDO::PARAM_INT);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aTempCtrl = explode('|',$aRows['sControl']);
				foreach ($aTempCtrl as $LPsCtrl)
				{
					$LPaTemp = explode('_',$LPsCtrl);
					$aControl[$LPaTemp[0]] = $LPaTemp[1];
				}
			}
			if (!empty($aSetControl))
			{
				$sSQL = '	SELECT 	nId,
								nUid,
								nMkid,
								nMlid
						FROM 	'.END_MENU_CTRL.'
						WHERE nUid = :nUid';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
				sql_query($Result);
				while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
				{
					$aEditLog[END_MENU_CTRL]['aOld'][$aRows['nId']] = $aRows;
				}

				$sSQL = 'DELETE	FROM '.END_MENU_CTRL.' WHERE nUid = :nUid ';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
				sql_query($Result);
			}

			foreach ($aSetControl as $LPnMkid => $LPaMlid)
			{
				foreach ($LPaMlid as $LPnMlid)
				{
					if (!isset($aControl[$LPnMkid]) || strpos($aControl[$LPnMkid],$LPnMlid) === false)
					{
						continue;
					}

					$aSQL_Array = array(
						'nUid'		=> $nId,
						'nMkid'		=> $LPnMkid,
						'nMlid'		=> $LPnMlid,
						'nCreateTime'	=> NOWTIME,
						'sCreateTime'	=> NOWDATE,
					);

					$sSQL = 'INSERT INTO '.END_MENU_CTRL.' ' . sql_build_array('INSERT', $aSQL_Array );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$LPnLastId = $oPdo->lastInsertId();
					$aEditLog[END_MENU_CTRL]['aNew'][$LPnLastId] = $aSQL_Array;
				}
			}

			# 紀錄動作 - 更新
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nId,
				'sWhomAccount'	=> (string) $sAccount,
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8101102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0.php']).$aJWT['sBackParam'];
			$sMsg =  aMANAGER['UPDATESUCCESS'];
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sChangePage = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0.php']).$aJWT['sBackParam'];
		$aEditLog = array(
			END_MANAGER_DATA => array(
				'aOld' => array(),
				'aNew' => array(),
			),
		);
		$sSQL = '	SELECT 	sAccount,
						nOnline
				FROM 	'.END_MANAGER_DATA.'
				WHERE nId = :nId
				AND	nOnline != 99
				AND 	nAdmType >= :nAdmType
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		$Result->bindValue(':nAdmType', $aAdm['nAdmType'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	= aERROR['NODATA'];
		}
		$aEditLog[END_MANAGER_DATA]['aOld'] = $aRows;
		$sAccount = $aRows['sAccount'];

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nOnline'		=> 99,
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);

			$sSQL = '	UPDATE	'.END_MANAGER_DATA.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 刪除
			$aEditLog[END_MANAGER_DATA]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nId,
				'sWhomAccount'	=> (string) $sAccount,
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8101103,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = aMANAGER['DELETESUCCESS'];
		}
	}

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sChangePage;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
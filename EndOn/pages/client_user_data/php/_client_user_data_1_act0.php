<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/ClientUserClass.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_data.php');

	$nId			= filter_input_int('nId',		INPUT_REQUEST, 0);
	$sAccount		= filter_input_str('sAccount',	INPUT_POST, '', 20);
	$sName0		= filter_input_str('sName0',		INPUT_POST, '', 20);
	$sName1		= filter_input_str('sName1',		INPUT_POST, '', 20);
	$sPhone		= filter_input_str('sPhone',		INPUT_POST, '', 20);
	$sWechat		= filter_input_str('sWechat',		INPUT_POST, '', 20);
	$sEmail		= filter_input_str('sEmail',		INPUT_POST, '', 255);
	$nLid			= filter_input_int('nLid', 		INPUT_POST, 0);
	$sExpired0		= filter_input_str('sExpired0',	INPUT_POST, '', 20);
	$sExpired1		= filter_input_str('sExpired1',	INPUT_POST, '', 20);
	$sHeight 		= filter_input_str('sHeight',		INPUT_POST, '', 10);
	$sWeight 		= filter_input_str('sWeight',		INPUT_POST, '', 10);
	$sIdNumber 		= filter_input_str('sIdNumber',	INPUT_POST, '', 11);
	$sBirthday 		= filter_input_str('sBirthday',	INPUT_POST, '', 11);
	$sSize		= filter_input_str('sSize',		INPUT_POST, '', 12);
	$sContent0		= filter_input_str('sContent0',	INPUT_POST, '', 255);
	$sContent1		= filter_input_str('sContent1',	INPUT_POST, '', 255);
	$sPassword		= filter_input_str('sPassword',	INPUT_POST, '', 32);
	$sTransPassword	= filter_input_str('sTransPassword',INPUT_POST, '', 32);
	$nStatus		= filter_input_int('nStatus',		INPUT_POST, 0);
	$sPa			= filter_input_str('sPa',		INPUT_POST, '', 20);
	$nType		= filter_input_int('nType',		INPUT_POST, 0); #隱藏會員
	$sKid = '';
	if (isset($_POST['aKid']) && !empty($_POST['aKid']))
	{
		$sKid = implode(',', $_POST['aKid']);
	}

	$nPaId = 0;
	$nErr	= 0;
	$sMsg = '';
	$aValue = array(
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sBackParamJWT = sys_jwt_encode($aValue);
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_1.php']).$aJWT['sBackParam'];
	$oUser = new oClientUser();

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_1_upt0.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;
		$sSQL = '	SELECT 	nId,
						sAccount,
						sKid,
						sName0,
						sName1,
						sPhone,
						sWechat,
						sEmail,
						nStatus
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId
				AND	nOnline != 99
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	= aERROR['NOMEMBER'].'<br>';
		}
		$aOld[CLIENT_USER_DATA] = $aRows;

		$sSQL = '	SELECT 	nId,
						sHeight,
						sWeight,
						sIdNumber,
						sBirthday,
						nBirthday,
						sSize,
						sContent0,
						sContent1
				FROM 	'.CLIENT_USER_DETAIL.'
				WHERE nUid = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aOld[CLIENT_USER_DETAIL] = $Result->fetch(PDO::FETCH_ASSOC);

		// 欄位檢查 start
		if ($sIdNumber == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['IDEMPTY'].'<br>';
		}
		elseif (!preg_match("/^[A-Z]{1}[12ABCD]{1}[0-9]{8}$/", $sIdNumber))
		{
			$nErr	= 1;
			$sMsg	.= aERROR['IDFORMATE'].'<br>';
		}
		if ($sPhone!= '' && !preg_match('/^09[0-9]{8}$/', $sPhone))
		{
			$nErr	= 1;
			$sMsg	.= aERROR['PHONEFORMATE'].'<br>';
		}
		if ($sEmail != '' && !preg_match('/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/', $sEmail))
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMAILFORMATE'].'<br>';
		}
		// if ($sWechat == '')
		// {
		// 	$nErr	= 1;
		//	$sMsg	.= aERROR['WECHATFORMATE'].'<br>';
		// }
		if ($sBirthday == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['BIRTHDAYEMPTY'].'<br>';
		}
		// 欄位檢查 end
/*
		if ($sPhone !='' && !preg_match('/^09[0-9]{8}$/', $sPhone))
		{
			$nErr	= 1;
			$sMsg	.= aUSER['PHONEERROR'].'<br>';
		}
		// if($sName0 == '' || mb_strlen($sName0) > 20)
		// {
		// 	$nErr	= 1;
		// 	$sMsg	.= aUSER['NAME0ERROR'].'<br>';
		// }
		// if($sName1 == '' || mb_strlen($sName1) > 20)
		// {
		// 	$nErr	= 1;
		// 	$sMsg	.= aUSER['NAME1ERROR'].'<br>';
		// }
		// if ($sWechat == '')
		// {
		// 	$nErr	= 1;
		// 	$sMsg	.= aUSER['WECHATERROR'].'<br>';
		// }
		if ($sEmail != '' && !preg_match('/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/', $sEmail))
		{
			$nErr	= 1;
			$sMsg	.= aUSER['EMAILERROR'].'<br>';
		}
*/
		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nLid'		=> (int) $nLid,
				'sPhone'		=> (string) $sPhone,
				'sWechat'		=> (string) $sWechat,
				'sEmail'		=> (string) $sEmail,
				'nStatus'		=> (int) $nStatus,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aOld[CLIENT_USER_DATA];
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			$sSQL = '	UPDATE	'.CLIENT_USER_DATA.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aSQL_Array = array(
				'sHeight'		=> (string) $sHeight,
				'sWeight'		=> (string) $sWeight,
				'sIdNumber'		=> (string)	$sIdNumber,
				'sBirthday'		=> (string)	$sBirthday,
				'nBirthday'		=> (int)	strtotime($sBirthday.' 00:00:00'),
				'sSize'		=> (string) $sSize,
				'sContent0'		=> (string) $sContent0,
				'sContent1'		=> (string) $sContent1,
			);
			$sSQL = '	UPDATE	'.CLIENT_USER_DETAIL.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nUid = :nUid
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$aEditLog[CLIENT_USER_DETAIL]['aOld'] = $aOld[CLIENT_USER_DETAIL];
			$aEditLog[CLIENT_USER_DETAIL]['aNew'] = $aSQL_Array;

			# 紀錄動作 - 更新
			$aSQL_Array = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nId,
				'sWhomAccount'	=> (string) $aOld[CLIENT_USER_DATA]['sAccount'],
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aSQL_Array);

			$sMsg = UPTV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_1.php']).$aJWT['sBackParam'];
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_1.php']).$aJWT['sBackParam'];
		$sSQL = '	SELECT 	nId,
						nOnline,
						sAccount,
						nStatus,
						nExpired0,
						nExpired1
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId
				AND	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOld === false)
		{
			$nErr	= 1;
			$sMsg	= aERROR['NOMEMBER'].'<br>';
		}
		if ($aOld['nExpired0'] > 0 || $aOld['nExpired1'] > 0)
		{
			$nErr	= 1;
			$sMsg	= aERROR['CANTDEL'].'<br>';
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nOnline' 		=> (int)	99,
				'nUpdateTime'	=> (int)	NOWTIME,
				'sUpdateTime'	=> (string)	NOWDATE,
			);

			$sSQL = '	UPDATE	'.CLIENT_USER_DATA.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			# 紀錄動作 - 刪除
			$aSQL_Array = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nId,
				'sWhomAccount'	=> (string) $aOld['sAccount'],
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103103,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aSQL_Array);

			$sMsg = DELV;
		}
	}

	// if ($aJWT['a'] == 'ACTIVATE'.$aJWT['nId']) #8103105 會員審核通過
	// {
	// 	$nId = $aJWT['nId'];
	// 	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_1_upt0.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;
	// 	$sSQL = '	SELECT 	nId,
	// 					sAccount,
	// 					nStatus
	// 			FROM 	'.CLIENT_USER_DATA.'
	// 			WHERE nId = :nId
	// 			AND	nOnline != 99
	// 			LIMIT 1 ';
	// 	$Result = $oPdo->prepare($sSQL);
	// 	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	// 	sql_query($Result);
	// 	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	// 	if ($aRows === false)
	// 	{
	// 		$nErr	= 1;
	// 		$sMsg	= NODATA.'<br>';
	// 	}

	// 	if ($nErr == 0)
	// 	{
	// 		$aSQL_Array = array(
	// 			'nStatus'		=> (int)0,
	// 			'nUpdateTime'	=> (int)NOWTIME,
	// 			'sUpdateTime'	=> (string)NOWDATE,
	// 		);
	// 		$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
	// 		$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

	// 		$sSQL = '	UPDATE	'.CLIENT_USER_DATA.'
	// 				SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
	// 				WHERE	nId = :nId
	// 				LIMIT 1';
	// 		$Result = $oPdo->prepare($sSQL);
	// 		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	// 		sql_build_value($Result, $aSQL_Array);
	// 		sql_query($Result);

	// 		# 紀錄動作 - 更新
	// 		$aSQL_Array = array(
	// 			'nWho'		=> (int) $aAdm['nId'],
	// 			'nWhom'		=> (int) $nId,
	// 			'sWhomAccount'	=> (string) $aRows['sAccount'],
	// 			'nKid'		=> (int) $nId,
	// 			'sIp'			=> (string) USERIP,
	// 			'nLogCode'		=> (int) 8103105,
	// 			'sParam'		=> (string) json_encode($aEditLog),
	// 			'nType0'		=> (int) 0,
	// 			'nCreateTime'	=> (int) NOWTIME,
	// 			'sCreateTime'	=> (string) NOWDATE,
	// 		);
	// 		DoActionLog($aSQL_Array);

	// 		$sMsg	= aUSER['PENDINGSUCCESS'].'<br>';
	// 	}
	// }

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
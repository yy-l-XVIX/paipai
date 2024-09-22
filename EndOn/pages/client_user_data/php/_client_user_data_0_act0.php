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
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0.php']).$aJWT['sBackParam'];
	$oUser = new oClientUser();

	if ($aJWT['a'] == 'INS')
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0_upt0.php']).'&sJWT='.$sBackParamJWT;
		// 欄位檢查 start
		if ($sAccount == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['ACCOUNTEMPTY'].'<br>';
		}
		$nLeng = strlen($sAccount);
		if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sAccount) || $nLeng < 6 || $nLeng > 16)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['ACCOUNTFORMAT'].'<br>';
		}
		else
		{
			$sSQL = '	SELECT 	1
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nOnline != 99
					AND 	sAccount LIKE :sAccount';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false)
			{
				$nErr	= 1;
				$sMsg	.= $sAccount.' '.aERROR['ACCOUNTDUPLICATE'].'<br>';
			}
		}
		$nLeng = strlen($sPassword);
		if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sPassword) || $nLeng < 6 || $nLeng > 16)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['PASSOWORDFORMAT'].'<br>';
		}
		if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword))
		{
			$nErr	= 1;
			$sMsg	.= aERROR['TRANSPASSOWORDFORMAT'].'<br>';
		}
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
		if($sPa == '')
		{
			$nPaId = $aSystem['aParam']['nAgentId']; // 如未填寫，為總代理下級(環境設置)
		}
		else
		{
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nOnline != 99
					AND 	sAccount LIKE :sPa';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sPa', $sPa, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$nErr	= 1;
				$sMsg	.= aERROR['NOPADATA'].'<br>';
			}
			else
			{
				$nPaId = $aRows['nId'];
			}
		}
		if($nPaId != 0)
		{
			$aPaLinkData = $oUser->getLinkData($nPaId);
			if ($aPaLinkData === false)
			{
				$nErr	= 1;
				$sMsg	.= aERROR['NOPALINKDATA'].'<br>';
			}
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
		if($sName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME0EMPTY'].'<br>';
		}
		elseif (mb_strlen($sName0) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME0LENGTH'].'<br>';
		}
		if($sName1 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME1EMPTY'].'<br>';
		}
		elseif (mb_strlen($sName1) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME1LENGTH'].'<br>';
		}
		if ($sKid == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['KINDEMPTY'].'<br>';
		}
		if ($sBirthday == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['BIRTHDAYEMPTY'].'<br>';
		}
		// 欄位檢查 end

/*
		if(!preg_match('/^[A-Za-z0-9]{6,16}$/', $sAccount))
		{
			$nErr	= 1;
			$sMsg	.= aUSER['ACCOUNTERROR'].'<br>';
		}
		else
		{
			$sSQL = '	SELECT 	1
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nOnline != 99
					AND 	sAccount LIKE :sAccount';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false)
			{
				$nErr	= 1;
				$sMsg	.= $sAccount.aUSER['ACCOUNTEXISTED'].'<br>';
			}
		}
		if ($sPhone!= '' && !preg_match('/^09[0-9]{8}$/', $sPhone))
		{
			$nErr	= 1;
			$sMsg	.= aUSER['PHONEERROR'].'<br>';
		}
		if($sName0 == '' || mb_strlen($sName0) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aUSER['NAME0ERROR'].'<br>';
		}
		if($sName1 == '' || mb_strlen($sName1) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aUSER['NAME1ERROR'].'<br>';
		}
		if (!preg_match("/^[A-Z]{1}[12ABCD]{1}[0-9]{8}$/", $sIdNumber))
		{
			$nErr	= 1;
			$sMsg	.= aUSER['IDERROR'].'<br>';
		}
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
		if(!preg_match('/^[A-Za-z0-9]{6,16}$/', $sPassword))
		{
			$nErr	= 1;
			$sMsg	.= aUSER['PASSWORDERROR'].'<br>';
		}
		if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword))
		{
			$nErr	= 1;
			$sMsg	.= aUSER['ACCOUNTERROR'].'<br>';
		}
		if ($sKid == '')
		{
			$nErr	= 1;
			$sMsg	.= aUSER['KINDERROR'].'<br>';
		}
		if($sPa == '')
		{
			$nPaId = 1; // 如未填寫，為mmg001下級
		}
		else
		{
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nOnline != 99
					AND 	sAccount LIKE :sPa';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sPa', $sPa, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$nErr	= 1;
				$sMsg	.= aUSER['PANODATA'].'<br>';
			}
			else
			{
				$nPaId = $aRows['nId'];
			}
		}
		if($nPaId != 0)
		{
			$aPaLinkData = $oUser->getLinkData($nPaId);
			if ($aPaLinkData === false)
			{
				$nErr	= 1;
				$sMsg	.= aUSER['PANOLINKDATA'].'<br>';
			}
		}
*/
		if ($nErr == 0)
		{
			$aRegister = array(
				'sAccount'		=> (string)	$sAccount,
				'sName0'		=> (string)	$sName0,
				'sName1'		=> (string)	$sName1,
				'sPhone'		=> (string)	$sPhone,
				'sWechat'		=> (string)	$sWechat,
				'sEmail'		=> (string)	$sEmail,
				'sPassword'		=> (string)	oCypher::ReHash($sPassword),
				'sTransPassword'	=> (string)	oCypher::ReHash($sTransPassword),
				'aPaLinkData'	=> $aPaLinkData,
				'nStatus'		=> (int)	$nStatus,
				'sKid'		=> (string)	$sKid,
				'sExpired0'		=> (string)	$sExpired0,
				'nExpired0'		=> (int)	strtotime($sExpired0),
				'sExpired1'		=> (string)	$sExpired1,
				'nExpired1'		=> (int)	strtotime($sExpired1),
				'nFrom'		=> (int)	0,	// 0:後台 1:前台
				'nAdmin'		=> (int)	$aAdm['nId'],	// 0:後台 1:前台
				'aDetail'		=> array(
					'sHeight'	=> (string)	$sHeight,
					'sSize'	=> (string)	$sSize,
					'sIdNumber'	=> (string)	$sIdNumber,
					'sBirthday'	=> (string)	$sBirthday,
					'nBirthday'	=> (int)	strtotime($sBirthday.' 00:00:00'),
					'sContent0'	=> (string)	$sContent0,
					'sContent1'	=> (string)	$sContent1,
				),
			);

			$oUser->register($aRegister);

			# 紀錄動作 - 新增

			$sMsg = INSV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0.php']).$aJWT['sBackParam'];
		}
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0_upt0.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;
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
		if($sName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME0EMPTY'].'<br>';
		}
		elseif (mb_strlen($sName0) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME0LENGTH'].'<br>';
		}
		if($sName1 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME1EMPTY'].'<br>';
		}
		elseif (mb_strlen($sName1) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NAME1LENGTH'].'<br>';
		}
		if ($sKid == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['KINDEMPTY'].'<br>';
		}
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
		if($sName0 == '' || mb_strlen($sName0) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aUSER['NAME0ERROR'].'<br>';
		}
		if($sName1 == '' || mb_strlen($sName1) > 20)
		{
			$nErr	= 1;
			$sMsg	.= aUSER['NAME1ERROR'].'<br>';
		}
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
			// 更新會員主表
			$aSQL_Array = array(
				'sKid'		=> (string)	$sKid,
				'nLid'		=> (int)	$nLid,
				'sExpired0'		=> (string)	$sExpired0,
				'nExpired0'		=> (int)	strtotime($sExpired0),
				'sExpired1'		=> (string)	$sExpired1,
				'nExpired1'		=> (int)	strtotime($sExpired1),
				'sName0'		=> (string)	$sName0,
				'sName1'		=> (string)	$sName1,
				'sPhone'		=> (string)	$sPhone,
				'sWechat'		=> (string)	$sWechat,
				'sEmail'		=> (string)	$sEmail,
				'nStatus'		=> (int)	$nStatus,
				'nUpdateTime'	=> (int)	NOWTIME,
				'sUpdateTime'	=> (string)	NOWDATE,
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

			// 更新會員詳細
			$aSQL_Array = array(
				'sHeight'		=> (string)	$sHeight,
				'sWeight'		=> (string)	$sWeight,
				'sIdNumber'		=> (string)	$sIdNumber,
				'sBirthday'		=> (string)	$sBirthday,
				'nBirthday'		=> (int)	strtotime($sBirthday.' 00:00:00'),
				'sSize'		=> (string)	$sSize,
				'sContent0'		=> (string)	$sContent0,
				'sContent1'		=> (string)	$sContent1,
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

			# 變更隱藏會員 (最高管理權限才可更動)
			if ($aAdm['nAdmType'] == 1)
			{
				$sSQL = '	SELECT 	nUid
						FROM 	'.CLIENT_USER_HIDE.'
						WHERE nUid = :nUid
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);
				if ($aRows !== false)
				{
					$aSQL_Array = array(
						'nOnline'		=> (int) $nType,
					);
					$sSQL = '	UPDATE	'.CLIENT_USER_HIDE.'
							SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
							WHERE	nUid = :nUid
							LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);

					$aEditLog[CLIENT_USER_HIDE]['aOld'] = $aRows;
					$aEditLog[CLIENT_USER_HIDE]['aNew'] = $aSQL_Array;
				}
				else
				{
					$aSQL_Array = array(
						'nUid'		=> (int)	$nId,
						'nOnline'		=> (int)	$nType,
						'nCreateTime'	=> (int)	NOWTIME,
						'sCreateTime'	=> (string)	NOWDATE,
					);
					$sSQL = 'INSERT INTO '. CLIENT_USER_HIDE . ' ' . sql_build_array('INSERT', $aSQL_Array );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);

					$aEditLog[CLIENT_USER_HIDE]['aOld'] = array();
					$aEditLog[CLIENT_USER_HIDE]['aNew'] = $aSQL_Array;
				}
			}

			# 紀錄動作 - 更新
			$aSQL_Array = array(
				'nWho'		=> (int)	$aAdm['nId'],
				'nWhom'		=> (int)	$nId,
				'sWhomAccount'	=> (string)	$aOld[CLIENT_USER_DATA]['sAccount'],
				'nKid'		=> (int)	$nId,
				'sIp'			=> (string)	USERIP,
				'nLogCode'		=> (int)	8103102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int)	0,
				'nCreateTime'	=> (int)	NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aSQL_Array);

			$sMsg = UPTV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0.php']).$aJWT['sBackParam'];
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0.php']).$aJWT['sBackParam'];
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

	// 修改密碼
	if ($aJWT['a'] == 'PASSWORD'.$nId)
	{
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0_upt1.php']).'&nId='.$nId.'&sJWT='.$sBackParamJWT;
		$sSQL = '	SELECT 	nId
						sAccount,
						sPassword,
						sTransPassword
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
			$sMsg	= aERROR['NOMEMBER'].'<br>';
		}

		if($sPassword == '' && $sTransPassword == '') // 兩個都空
		{
			$nErr	= 1;
			$sMsg	.= aERROR['PASSWORDUNCHANGED'].'<br>';
		}
		if($sPassword != '' && !preg_match('/^[A-Za-z0-9]{6,16}$/', $sPassword))
		{
			$nErr	= 1;
			$sMsg	.= aERROR['PASSOWORDFORMAT'].'<br>';
		}
		if($sTransPassword != '' && !preg_match('/^[0-9]{6,12}$/', $sTransPassword))
		{
			$nErr	= 1;
			$sMsg	.= aERROR['TRANSPASSOWORDFORMAT'].'<br>';
		}

/*
		// 不能兩個都空
		if($sPassword == '' && $sTransPassword == '')
		{
			$nErr	= 1;
			$sMsg	.= aUSER['PASSWORDUNDEFINE'].'<br>';
		}
		if($sPassword != '' && !preg_match('/^[A-Za-z0-9]{6,16}$/', $sPassword) )
		{
			$nErr	= 1;
			$sMsg	.= aUSER['PASSWORDERROR'].'<br>';
		}
		if($sTransPassword != '' && !preg_match('/^[0-9]{6,12}$/', $sTransPassword) )
		{
			$nErr	= 1;
			$sMsg	.= aUSER['TRANSPASSWORDERROR'].'<br>';
		}
*/
		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nUpdateTime'	=> (int)	NOWTIME,
				'sUpdateTime'	=> (string)	NOWDATE,
			);
			if ($sPassword != '' && oCypher::ReHash($sPassword) != $aOld['sPassword'])
			{
				$aSQL_Array['sPassword'] = (string) oCypher::ReHash($sPassword);
			}
			if ($sTransPassword != '' && oCypher::ReHash($sTransPassword) != $aOld['sTransPassword'])
			{
				$aSQL_Array['sTransPassword'] = (string) oCypher::ReHash($sTransPassword);
			}

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

			# 紀錄動作 - 更新
			$aSQL_Array = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nId,
				'sWhomAccount'	=> (string) $aOld['sAccount'],
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103104,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aSQL_Array);

			$sMsg	= UPTV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_0.php']).$aJWT['sBackParam'];

		}
	}

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
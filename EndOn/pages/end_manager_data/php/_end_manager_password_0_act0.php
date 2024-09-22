<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'. $aSystem['sLang'] .'/end_manager_password.php');

	$sName0		= filter_input_str('sName0',		INPUT_POST,'');
	$sPassword		= filter_input_str('sPassword',	INPUT_POST,'');
	$sNewPassword	= filter_input_str('sNewPassword',	INPUT_POST,'');
	$sConfirmPassword	= filter_input_str('sConfirmPassword',INPUT_POST,'');

	$nErr	= 0;
	$sMsg = '';
	$aActionLog = array();
	$aEditLog = array(
		END_MANAGER_DATA => array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);

	if ($aJWT['a'] == 'UPT')
	{
		$sSQL = '	SELECT 	sAccount,
						sPassword,
						sName0
				FROM 	'.END_MANAGER_DATA.'
				WHERE nId = :nId
				AND 	nOnline != 99
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aAdm['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if (oCypher::ReHash($sPassword) <> $aRows['sPassword'])
		{
			$nErr	= 1;
			$sMsg	.= aERROR['ERRORPASSWORD'].'<br>';
		}
		$aEditLog[END_MANAGER_DATA]['aOld'] = $aRows;

		if ($sNewPassword != '' || $sConfirmPassword != '')
		{
			$nLeng = strlen($sNewPassword);
			if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sNewPassword) || $nLeng < 6 || $nLeng > 16)
			{
				$nErr	= 1;
				$sMsg	.= aERROR['PASSWORDFORMATE'].'<br>';
			}
			if ($sNewPassword <> $sConfirmPassword)
			{
				$nErr	= 1;
				$sMsg	.= aERROR['UNMATCH'].'<br>';
			}
		}
		if (oCypher::ReHash($sNewPassword) == $aAdm['sPassword'] && $aAdm['sName0'] == $sName0)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NODATACHANGE'].'<br>';
		}
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_password_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,.
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			if ($sNewPassword != '' && oCypher::ReHash($sNewPassword) != $aAdm['sPassword'])
			{
				$aSQL_Array['sPassword'] =  oCypher::ReHash($sNewPassword);
			}
			$sSQL = '	UPDATE	'.END_MANAGER_DATA.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND 	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aAdm['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$aEditLog[END_MANAGER_DATA]['aNew'] = $aSQL_Array;

			# 紀錄動作 - 更新
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $aAdm['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aAdm['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8101104,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aJumpMsg['0']['sMsg'] = aPASSWORD['UPDATESUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_payment_online_tunnel.php');
	#require end

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_REQUEST,0);
	$nPid		= filter_input_int('nPid',	INPUT_POST, 0);
	$sKey		= filter_input_str('sKey',	INPUT_POST, '', 20);
	$sValue	= filter_input_str('sValue',	INPUT_POST, '', 20);
	$nMin		= filter_input_int('nMin',	INPUT_POST, 0);
	$nMax		= filter_input_int('nMax',	INPUT_POST, 0);
	$nOnline	= filter_input_int('nOnline',	INPUT_POST, 1);

	#參數結束
	#參數宣告區
	$aData = array();
	$aEditLog = array(
		CLIENT_PAYMENT_TUNNEL	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0.php']);
	$nErr = 0;
	$sMsg = '';
	#宣告結束

	#程式邏輯區

	if ($aJWT['a'] == 'INS')
	{
		$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0_upt0.php']);
		if ($sKey == '')
		{
			$nErr = 1;
			$sMsg .= aERROR['KEY'].'<br>';
		}
		if ($sValue == '')
		{
			$nErr = 1;
			$sMsg .= aERROR['VALUE'].'<br>';
		}
		if ($nMin < 0)
		{
			$nErr = 1;
			$sMsg .= aERROR['MONEY'].'<br>';
		}
		if ($nMin > $nMax)
		{
			$nErr = 1;
			$sMsg .= aERROR['OVERMAX'].'<br>';
		}
		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nPid'			=> (int)	$nPid,
				'sKey'			=> (string)	$sKey,
				'sValue'			=> (string)	$sValue,
				'nMin'			=> (int)	$nMin,
				'nMax'			=> (int)	$nMax,
				'nOnline'			=> (int)	$nOnline,
				'nCreateTime'		=> (int)	NOWTIME,
				'sCreateTime'		=> (string)	NOWDATE,
				'nUpdateTime'		=> (int)	NOWTIME,
				'sUpdateTime'		=> (string)	NOWDATE,
			);

			$sSQL = 'INSERT INTO '. CLIENT_PAYMENT_TUNNEL . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_PAYMENT_TUNNEL]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_PAYMENT_TUNNEL]['aNew']['nId'] = $nLastId;

			//log
			$aActionLog = array(
				'nWho'		=> (int)	$aAdm['nId'],
				'nWhom'		=> (int)	0,
				'sWhomAccount'	=> (string)	'',
				'nKid'		=> (int)	$nLastId,
				'sIp'			=> (string)	USERIP,
				'nLogCode'		=> (int)	8107306,
				'sParam'		=> (string)	json_encode($aEditLog),
				'nType0'		=> (int)	0,
				'nCreateTime'	=> (int)	NOWTIME,
				'sCreateTime'	=> (string)	NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = INSV;
			$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0.php']);
		}
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0_upt0.php']).'&nId='.$nId;
		if ($sKey == '')
		{
			$nErr = 1;
			$sMsg .= aERROR['KEY'].'<br>';
		}
		if ($sValue == '')
		{
			$nErr = 1;
			$sMsg .= aERROR['VALUE'].'<br>';
		}
		if ($nMin < 0)
		{
			$nErr = 1;
			$sMsg .= aERROR['MONEY'].'<br>';
		}
		if ($nMin > $nMax)
		{
			$nErr = 1;
			$sMsg .= aERROR['OVERMAX'].'<br>';
		}

		$oPdo->beginTransaction();
		$sSQL = '	SELECT	nId,
						nPid,
						sKey,
						sValue,
						nMin,
						nMax,
						nOnline,
						sCreateTime,
						sUpdateTime
				FROM	'. CLIENT_PAYMENT_TUNNEL .'
				WHERE	nOnline != 99
				AND	nId = :nId
				LIMIT	1 FOR	UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aData === false)
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = NODATA;
		}
		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nPid'			=> (int)	$nPid,
				'sKey'			=> (string)	$sKey,
				'sValue'			=> (string)	$sValue,
				'nMin'			=> (int)	$nMin,
				'nMax'			=> (int)	$nMax,
				'nOnline'			=> (int)	$nOnline,
				'nUpdateTime'		=> (int)	NOWTIME,
				'sUpdateTime'		=> (string)	NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_PAYMENT_TUNNEL . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			#紀錄動作 - 更新
			$aEditLog[CLIENT_PAYMENT_TUNNEL]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_PAYMENT_TUNNEL]['aNew']['nId'] = $aData['nId'];

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aData['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107307,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();
			$sMsg = UPTV;
			$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_tunnel_0.php']);
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$oPdo->beginTransaction();
		$sSQL = '	SELECT	nId,
						nPid,
						sKey,
						sValue,
						nMin,
						nMax,
						nOnline,
						sCreateTime,
						sUpdateTime
				FROM	'. CLIENT_PAYMENT_TUNNEL .'
				WHERE	nOnline != 99
				AND	nId = :nId
				LIMIT	1 FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aData === false)
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = NODATA;
		}
		else
		{

			$aSQL_Array = array(
				'nOnline'		=> (int) 	99,
				'nUpdateTime'	=> (int)	NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_PAYMENT_TUNNEL . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_PAYMENT_TUNNEL]['aOld'] = $aData;
			$aEditLog[CLIENT_PAYMENT_TUNNEL]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107308,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();
			$sMsg = DELV;
		}
	}
	#程式邏輯結束
	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sChangePage;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
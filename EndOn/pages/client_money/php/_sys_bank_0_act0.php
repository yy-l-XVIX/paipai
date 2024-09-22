<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/sys_bank.php');
	#require end

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_REQUEST,0);
	$nOnline	= filter_input_int('nOnline',	INPUT_POST, 1);
	$nType0	= filter_input_int('nType0',	INPUT_POST, 1);
	$sName0	= filter_input_str('sName0',	INPUT_POST, '',50);
	$sCode	= filter_input_str('sCode',	INPUT_POST, '',30);
	#參數結束

	#參數宣告區
	$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_sys_bank_0.php']).$aJWT['sBackParam'];
	$aData = array();
	$aEditLog = array(
		SYS_BANK	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';
	#宣告結束

	#程式邏輯區
	if ($aJWT['a'] == 'INS')
	{
		$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_sys_bank_0.php']);
		if ($sName0 == '')
		{
			$nErr = 1;
			$sMsg = aERROR['NAME0'];
		}
		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'sCode'		=> (string) $sCode,
				'nOnline'		=> (int) $nOnline,
				'nType0'		=> (int) $nType0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '. SYS_BANK . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			//log ??
			$aEditLog[SYS_BANK]['aNew'] = $aSQL_Array;
			$aEditLog[SYS_BANK]['aNew']['nId'] = $nLastId;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107401,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = INSV;
		}
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						nOnline,
						nType0,
						sCode,
						nUpdateTime,
						sUpdateTime
				FROM 	'. SYS_BANK .'
				WHERE nId = :nId
				LIMIT	1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$nErr = 1;
			$sMsg = NODATA;
		}
		if ($sName0 == '')
		{
			$nErr = 1;
			$sMsg = aERROR['NAME0'];
		}
		if ($nErr == 0)
		{

			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'sCode'		=> (string) $sCode,
				'nOnline'		=> (int) $nOnline,
				'nType0'		=> (int) $nType0,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. SYS_BANK . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[SYS_BANK]['aNew'] = $aSQL_Array;
			$aEditLog[SYS_BANK]['aNew']['nId'] = $aData['nId'];

			#紀錄動作 - 更新
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aData['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107402,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = UPTV;
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						nOnline,
						nType0,
						sCode,
						nUpdateTime,
						sUpdateTime
				FROM 		'. SYS_BANK .'
				WHERE 	nId = :nId
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$nErr = 1;
			$sMsg = NODATA;
		}
		else
		{
			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. SYS_BANK . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[SYS_BANK]['aOld'] = $aData;
			$aEditLog[SYS_BANK]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107403,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = DELV;
		}
	}
	#程式邏輯結束
	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sChangePage;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
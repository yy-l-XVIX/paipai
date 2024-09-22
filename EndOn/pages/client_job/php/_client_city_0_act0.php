<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_city.php');
	#require end

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_REQUEST,0);
	$nOnline	= filter_input_int('nOnline',	INPUT_POST, 1);
	$nLid		= filter_input_int('nLid',	INPUT_POST, 0);
	$sName0	= filter_input_str('sName0',	INPUT_POST, '');
	#參數結束

	#參數宣告區
	$aData = array();
	$aEditLog = array(
		CLIENT_CITY	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_job/php/_client_city_0.php']).'&nLid='.$nLid.'&nOnline='.$nOnline;
	#宣告結束

	#程式邏輯區
	if ($aJWT['a'] == 'INS')
	{
		if ($sName0 == '')
		{
			$nErr = 1;
			$sMsg = aCITY['NAMEERROR'].'<br>';
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'nOnline'		=> (int) $nOnline,
				'nLid'		=> (int) $nLid,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '. CLIENT_CITY . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_CITY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_CITY]['aNew']['nId'] = $nLastId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105101,
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
						nLid,
						nUpdateTime,
						sUpdateTime
				FROM 	'. CLIENT_CITY .'
				WHERE nId = :nId
				AND 	nOnline != 99
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
			$sMsg = aCITY['NAMEERROR'].'<br>';
		}

		if ($nErr == 0)
		{


			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'nLid'		=> (int) $nLid,
				'nOnline'		=> (int) $nOnline,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_CITY . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			#紀錄動作 - 更新
			$aEditLog[CLIENT_CITY]['aOld'] = $aData;
			$aEditLog[CLIENT_CITY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_CITY]['aNew']['nId'] = $nId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105102,
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
						nLid,
						nUpdateTime,
						sUpdateTime
				FROM 	'. CLIENT_CITY .'
				WHERE nId = :nId
				AND 	nOnline != 99
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
		if ($nErr == 0)
		{

			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_CITY . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_CITY]['aOld'] = $aData;
			$aEditLog[CLIENT_CITY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_CITY]['aNew']['nId'] = $nId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105103,
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
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
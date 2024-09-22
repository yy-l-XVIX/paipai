<?php
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_snooze_keywords.php');

	$nId		= filter_input_int('nId',	INPUT_REQUEST,0);
	$sName0	= filter_input_str('sName0',	INPUT_POST,'',50);

	$aEditLog = array(
		CLIENT_SNOOZE_KEYWORDS => array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';

	if ($aJWT['a'] == 'INS') // 8109201
	{
		if ($sName0 == '')
		{
			$nErr = 1;
			$sMsg = aERROR['NAME0'];
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'nOnline'		=> (int) 1,
			);
			$sSQL = 'INSERT INTO '.CLIENT_SNOOZE_KEYWORDS.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_SNOOZE_KEYWORDS]['aOld'] = array();
			$aEditLog[CLIENT_SNOOZE_KEYWORDS]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_SNOOZE_KEYWORDS]['aNew']['nId'] = $nLastId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8109201,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$sMsg = INSV;
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nOnline,
						sName0
				FROM 	'.CLIENT_SNOOZE_KEYWORDS.'
				WHERE nId = :nId
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr = 1;
			$sMsg = NODATA;
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nOnline' => 99,
			);
			$sSQL = '	UPDATE	'.CLIENT_SNOOZE_KEYWORDS.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_SNOOZE_KEYWORDS]['aOld'] = $aRows;
			$aEditLog[CLIENT_SNOOZE_KEYWORDS]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8109202,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$sMsg = DELV;
		}
	}

	# -----------
	$nI = 0;
	$sText = 'var aSNOOZEKEYWORDS = new Array();'.PHP_EOL;
	$sSQL = '	SELECT 	nId,
					sName0
			FROM	'.CLIENT_SNOOZE_KEYWORDS.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$sText .= 'aSNOOZEKEYWORDS['.$nI.'] = \'' . $aRows['sName0'] . '\';'.PHP_EOL;
		$nI++;
	}
	$sText .= PHP_EOL.'// last update '.date('Y-m-d H:i:s');
	$sFileName = dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/ClientTest/plugins/js/SnoozeKeywords.js'; //檔案名稱
	if (file_exists($sFileName))
	{
		  unlink($sFileName);
	}

	$open = fopen("$sFileName","w+"); //開啟檔案，要是沒有檔案將建立一份
	chmod($sFileName, 0644);
	fwrite($open,$sText); //寫入人數
	fclose($open); //關閉檔案

	# -----------

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_developer/php/_client_snooze_keywords_0.php']);
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
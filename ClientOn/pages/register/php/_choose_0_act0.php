<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/choose.php');

	$nLid		= filter_input_int('nLid',	INPUT_REQUEST, 0);

	/**
	 * 回傳陣列 JSON
	 * @var Int nStatus
	 * 	回傳狀態值
	 * 	1 => 正常 其餘待補
	 * @var String sMsg
	 * 	回傳訊息
	 * @var Array aData
	 * 	回傳陣列
	 * @var Int nAlertType
	 * 	回傳訊息提示類型
	 * 	0 => 不需提示框
	 * @var String sUrl
	 * 	回傳後導頁檔案
	 */
	$aReturn = array(
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);

	// 免費試用
	if ($aJWT['a'] == 'FREEUSE')
	{
		// 方案是否開啟免費試用
		$sSQL = '	SELECT 	nType0,
						nFreeDays,
						nFreeStartTime,
						nFreeEndTime
				FROM 	'.CLIENT_USER_KIND.'
				WHERE nLid = :nLid
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
		sql_query($Result);
		$aKind = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aKind === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA;
		}
		else if ($aKind['nType0'] != 1) // 沒有開啟免費試用
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['FREETRY'].'<br>';
		}

		$oPdo->beginTransaction();
		$sSQL = '	SELECT	nId,
						nKid,
						sKid,
						nExpired0,
						nExpired1,
						sExpired0,
						sExpired1
				FROM 	'.CLIENT_USER_DATA.'
				WHERE	nId = :nId
				AND 	nOnline = 1
				LIMIT 1 FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA;
		}
		else if ($nLid == 1 && $aRows['nExpired1'] > 0) // 雇主已買過方案
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['YOUBUY'].'<br>';
		}
		else if ($nLid == 3 && $aRows['nExpired0'] > 0) // 人才已買過方案
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['YOUBUY'].'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
			// 更新會員到期日
			$aSQL_Array = array(
				'nUpdateTime'	=> (int)	NOWTIME,
				'sUpdateTime'	=> (string)	NOWDATE,
			);
			$aRows['aKid'] = explode(',', $aRows['sKid']);
			if (!in_array($nLid, $aRows['aKid']))
			{
				array_push($aRows['aKid'], $nLid);
				$aSQL_Array['sKid'] = implode(',', $aRows['aKid']);
			}
			if ($nLid == 1) #雇主
			{
				$aSQL_Array['nExpired1'] = strtotime(NOWDATE.'+'.$aKind['nFreeDays'].' day');
				$aSQL_Array['sExpired1'] = date('Y-m-d H:i:s',$aSQL_Array['nExpired1']);
			}
			if ($nLid == 3) #人才
			{
				$aSQL_Array['nExpired0'] = strtotime(NOWDATE.'+'.$aKind['nFreeDays'].' day');
				$aSQL_Array['sExpired0'] = date('Y-m-d H:i:s',$aSQL_Array['nExpired0']);
			}

			$sSQL = '	UPDATE '. CLIENT_USER_DATA .'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			// 動作紀錄
			$aActionLog = array(
				'nWho'		=> (int)	$aUser['nId'],
				'nWhom'		=> (int)	0,
				'sWhomAccount'	=> (string)	'',
				'nKid'		=> (int)	$aUser['nId'],
				'sIp'			=> (string)	USERIP,
				'nLogCode'		=> (int)	7100403,
				'sParam'		=> (string)	json_encode($aEditLog),
				'nType0'		=> (int)	0,
				'nCreateTime'	=> (int)	NOWTIME,
				'sCreateTime'	=> (string)	NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = aCHOOSE['UPTV'];
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
		}
		$oPdo->commit();
	}
	// 免費使用 開通方案
	if ($aJWT['a'] == 'OPENKID')
	{
		// 方案是否開啟免費試用
		$sSQL = '	SELECT 	nFreeStartTime,
						nFreeEndTime
				FROM 	'.CLIENT_USER_KIND.'
				WHERE nLid = :nLid
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
		sql_query($Result);
		$aKind = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aKind === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA;
		}
		else if ($aKind['nFreeStartTime'] > NOWTIME || $aKind['nFreeEndTime'] < NOWTIME) // 沒有開啟免費試用
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['FREEUSE'];
		}

		$oPdo->beginTransaction();
		$sSQL = '	SELECT	nId,
						nKid,
						sKid,
						nExpired0,
						nExpired1,
						sExpired0,
						sExpired1
				FROM 	'.CLIENT_USER_DATA.'
				WHERE	nId = :nId
				AND 	nOnline = 1
				LIMIT 1 FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA.'<br>';
		}
		else if (in_array($nLid,$aUser['aKid'])) // 已有此方案
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['YOUBUY'].'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;

			$aSQL_Array = array(
				'nUpdateTime'	=> (int)	NOWTIME,
				'sUpdateTime'	=> (string)	NOWDATE,
			);
			$aRows['aKid'] = explode(',', $aRows['sKid']);
			if (!in_array($nLid, $aRows['aKid']))
			{
				array_push($aRows['aKid'], $nLid);
				$aSQL_Array['sKid'] = implode(',', $aRows['aKid']);
			}

			$sSQL = '	UPDATE '. CLIENT_USER_DATA .'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			// 動作紀錄
			$aActionLog = array(
				'nWho'		=> (int)	$aUser['nId'],
				'nWhom'		=> (int)	0,
				'sWhomAccount'	=> (string)	'',
				'nKid'		=> (int)	$aUser['nId'],
				'sIp'			=> (string)	USERIP,
				'nLogCode'		=> (int)	7100403,
				'sParam'		=> (string)	json_encode($aEditLog),
				'nType0'		=> (int)	0,
				'nCreateTime'	=> (int)	NOWTIME,
				'sCreateTime'	=> (string)	NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = aCHOOSE['UPTV'];
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
		}
		$oPdo->commit();
	}

	echo json_encode($aReturn);
	exit;
?>
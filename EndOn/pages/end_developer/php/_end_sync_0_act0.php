<?php
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_sync.php');

	$aEditLog = array(
		END_MENU_KIND => array(
			'aOld' => array(),
			'aNew' => array(),
		),
		END_MENU_LIST => array(
			'aOld' => array(),
			'aNew' => array(),
		),
		END_LOGCODE => array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';
	$aSetControl = array();
	$sControl = '';
	if ($aJWT['a'] == 'UPTsAll')
	{
		# CURL 公司 同步專案目錄
		$aPostData = array(
			'nPid' => $aSystem['aWebsite']['nId'],
		);
		$sUrl =  COMPANY['URL'].'API/cpy/GetMenu.php';
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');# GET || POST
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aPostData));
		# curl 執行時間
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT,3);
		$result = curl_exec($ch);
		$aReturn = json_decode($result,true);

		$oPdo->beginTransaction();

		if ($aReturn['nStatus'] == 1)
		{
			if (!empty($aReturn['aData']['aKind'])) # 主目錄
			{
				# 清空 Kind
				$sSQL = 'TRUNCATE TABLE '.END_MENU_KIND;
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				foreach ($aReturn['aData']['aKind'] as $LPnId => $LPaKind)
				{
					$sSQL = 'INSERT INTO '.END_MENU_KIND.' ' . sql_build_array('INSERT', $LPaKind );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $LPaKind);
					sql_query($Result);

					$aSetControl[$LPnId] = array();
				}

				$aEditLog[END_MENU_KIND]['New'] = $aReturn['aData']['aKind'];
				$aActionLog = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) 0,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8109101,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);
			}
			if (!empty($aReturn['aData']['aList'])) # 子目錄
			{
				# 清空list
				$sSQL = 'TRUNCATE TABLE '.END_MENU_LIST;
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				foreach ($aReturn['aData']['aList'] as $LPnId => $LPaList)
				{
					$sSQL = 'INSERT INTO '.END_MENU_LIST.' ' . sql_build_array('INSERT', $LPaList );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $LPaList);
					sql_query($Result);

					$aSetControl[$LPaList['nMid']][$LPnId] = $LPnId;
				}

				$aEditLog[END_MENU_KIND]['New'] = $aReturn['aData']['aList'];
				$aActionLog = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) 0,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8109102,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);
			}
			# 修改最高層級權限
			foreach($aSetControl as $LPnMkid => $aMlid)
			{
				$sControl .= $LPnMkid.'_';
				foreach($aMlid as $nMlid)
				{
					$sControl .= $nMlid.',';
				}
				$sControl = substr($sControl,0,-1).'|';
			}
			$sControl = substr($sControl,0,-1); # 1_2,3|4_5,6

			$aSQL_Array = array(
				'sControl'		=> $sControl,
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);
			$sSQL = '	UPDATE	'.END_PERMISSION.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 同時修改 admroot 權限
			if (true)
			{
				$sSQL = 'DELETE	FROM '.END_MENU_CTRL.' WHERE nUid = 1 ';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				foreach ($aSetControl as $LPnMkid => $LPaMlid)
				{
					foreach ($LPaMlid as $LPnMlid)
					{
						$aSQL_Array = array(
							'nUid'		=> 1,
							'nMkid'		=> $LPnMkid,
							'nMlid'		=> $LPnMlid,
							'nCreateTime'	=> NOWTIME,
							'sCreateTime'	=> NOWDATE,
						);

						$sSQL = 'INSERT INTO '.END_MENU_CTRL.' ' . sql_build_array('INSERT', $aSQL_Array );
						$Result = $oPdo->prepare($sSQL);
						sql_build_value($Result, $aSQL_Array);
						sql_query($Result);
					}
				}
			}

			$aJumpMsg['0']['sMsg'] = ' SUCCESS ';
		}
		$oPdo->commit();

		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_developer/php/_end_sync_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	if ($aJWT['a'] == 'UPTsMenuKind')
	{

		# CURL 公司 同步專案目錄
		$aPostData = array(
			'nPid' => $aSystem['aWebsite']['nId'],
		);
		$sUrl =  COMPANY['URL'].'API/cpy/GetMenu.php';
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');# GET || POST
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aPostData));
		# curl 執行時間
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT,3);
		$result = curl_exec($ch);
		$aReturn = json_decode($result,true);

		$oPdo->beginTransaction();

		if ($aReturn['nStatus'] == 1 && !empty($aReturn['aData']['aKind']))
		{
			# 清空list
			$sSQL = 'TRUNCATE TABLE '.END_MENU_KIND;
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);

			foreach ($aReturn['aData']['aKind'] as $LPnId => $LPaKind)
			{
				$sSQL = 'INSERT INTO '.END_MENU_KIND.' ' . sql_build_array('INSERT', $LPaKind );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $LPaKind);
				sql_query($Result);
			}

			$aEditLog[END_MENU_KIND]['New'] = $aReturn['aData']['aKind'];
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) 0,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8109101,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$aJumpMsg['0']['sMsg'] = aSYNC['MANUKINDV'];
		}
		else
		{
			error_log($result);
			$aJumpMsg['0']['sMsg'] = NODATA;
		}

		$oPdo->commit();

		# 紀錄動作 - 刪除
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_developer/php/_end_sync_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	if ($aJWT['a'] == 'UPTsMenuList')
	{
		# CURL 公司 同步專案目錄
		$aPostData = array(
			'nPid' => $aSystem['aWebsite']['nId'],
		);
		$sUrl =  COMPANY['URL'].'API/cpy/GetMenu.php';
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');# GET || POST
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aPostData));
		# curl 執行時間
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT,3);
		$result = curl_exec($ch);
		$aReturn = json_decode($result,true);

		if ($aReturn['nStatus'] == 1 && !empty($aReturn['aData']['aList']))
		{
			$oPdo->beginTransaction();
			# 清空list
			$sSQL = 'TRUNCATE TABLE '.END_MENU_LIST;
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);

			foreach ($aReturn['aData']['aList'] as $LPnId => $LPaList)
			{
				$sSQL = 'INSERT INTO '.END_MENU_LIST.' ' . sql_build_array('INSERT', $LPaList );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $LPaList);
				sql_query($Result);
			}

			$aEditLog[END_MENU_KIND]['New'] = $aReturn['aData']['aList'];
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) 0,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8109102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$oPdo->commit();
			$aJumpMsg['0']['sMsg'] = aSYNC['MANULISTV'];
		}
		else
		{
			error_log($result);
			$aJumpMsg['0']['sMsg'] = NODATA;
		}

		# 紀錄動作 - 刪除
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_developer/php/_end_sync_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	if ($aJWT['a'] == 'UPTsLogCode')
	{

		# CURL 公司 同步專案目錄
		$aPostData = array(
			'nPid' => $aSystem['aWebsite']['nId'],
		);
		$sUrl =  COMPANY['URL'].'API/cpy/GetLogcode.php';
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');# GET || POST
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aPostData));
		# curl 執行時間
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT,3);
		$result = curl_exec($ch);
		$aReturn = json_decode($result,true);

		if ($aReturn['nStatus'] == 1 && !empty($aReturn['aData']))
		{
			$oPdo->beginTransaction();
			# 清空list
			$sSQL = 'TRUNCATE TABLE '.END_LOGCODE.'';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);

			foreach ($aReturn['aData'] as $LPnId => $LPaList)
			{
				$LPaList['nUpdateTime'] = NOWTIME;
				$LPaList['sUpdateTime'] = NOWDATE;

				$sSQL = 'INSERT INTO '.END_LOGCODE.' ' . sql_build_array('INSERT', $LPaList );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $LPaList);
				sql_query($Result);
			}

			$aEditLog[END_LOGCODE]['New'] = $aReturn['aData'];
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) 0,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8109103,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$oPdo->commit();
			$aJumpMsg['0']['sMsg'] = aSYNC['LOGCODEV'];
		}
		else
		{
			error_log($result);
			$aJumpMsg['0']['sMsg'] = NODATA;
		}

		# 紀錄動作 - 刪除
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_developer/php/_end_sync_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
?>
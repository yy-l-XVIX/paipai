<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/bank_add.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');

	$sName0 	= filter_input_str('sName0',	INPUT_POST,'');
	$sName1 	= filter_input_str('sName1',	INPUT_POST,'');
	$sName2 	= filter_input_str('sName2',	INPUT_POST,'');
	$nBid 	= filter_input_int('nBid',	INPUT_POST,0);
	$nId 		= filter_input_int('nId',	INPUT_GET,0);

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
		'nAlertType'	=> 1,
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/center/php/_bank_add_0.php'])
	);
	$nBankCount = 0;
	$aData = array();
	$aPendingField = array_flip(explode(',', $aSystem['aParam']['sPendingField']));	// 需要審核欄位

	if ($aJWT['a'] == 'INS')
	{
		$sSQL = '	SELECT 	nId
				FROM 		'.CLIENT_USER_BANK.'
				WHERE 	nOnline = 1
				AND		nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$nBankCount ++;
		}

		if($nBankCount == $aSystem['aParam']['nCardLimit'])
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERR['BANKLIMIT'].'<br>';
		}

		$sSQL = '	SELECT	nId,
						sName0
				FROM		'.SYS_BANK.'
				WHERE		nOnline = 1
				AND		nId = :nBid
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nBid', $nBid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERR['NOSELBANK'].'<br>';
		}

		if ($sName0 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERR['CARDNUMERR'].'<br>';
		}

		if ($sName1 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERR['CARDNAMEERR'].'<br>';
		}

		if ($sName2 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERR['BANKBRANCHERR'].'<br>';
		}

		if (!isset($_FILES['sFile']) || (isset($_FILES['sFile']) && $_FILES['sFile']['name']==''))
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERR['IMAGEERR'].'<br>';
		}


		if($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			$aSQL_Array = array(
				'nUid'		=> (int) $aUser['nId'],
				'nBid'		=> (int) $nBid,
				'nOnline'		=> (int) 1,
				'sName0'		=> (string) $sName0,
				'sName1'		=> (string) $sName1,
				'sName2'		=> (string) $sName2,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.CLIENT_USER_BANK.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_USER_BANK]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_USER_BANK]['aNew']['nId'] = $nLastId;

			$aData = array(
				'nKid'	=> $nLastId,
				'sTable'	=> CLIENT_USER_BANK,
				'sName0'	=> $sName0,
				'NOWTIME'	=> NOWTIME
			);
			$sKey = cDataEncrypt::update($aData,false);

			$aSQL_Array = array(
				'nKid'		=> (int) $nLastId,
				'sTable'		=> (string) CLIENT_USER_BANK,
				'nEncryptTime'	=> (int) NOWTIME,
				'sEncryptKey'	=> (string) $sKey,
			);

			$sSQL = 'INSERT INTO '. CLIENT_DATA_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100701,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
			{
				$aFile['sTable'] = CLIENT_USER_BANK;
				$aFile['aFile'] = $_FILES['sFile'];
				$aFileInfo = goImage($aFile);

				if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
				{
					$oPdo->rollback();
					$aReturn['nStatus'] = 0;
					$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
					$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_bank_add_0.php']);
					echo json_encode($aReturn);
					exit;
				}
				else
				{
					$aTmp = explode('.',$aFileInfo['sFilename']);
					$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
					$sFname = $aFileInfo['sFilename'];
				}

				$aSQL_Array = array(
					'nKid'		=> (int) $nLastId,
					'sTable'		=> (string) CLIENT_USER_BANK,
					'sFile'		=> (string) $sFname,
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'  	=> (string) NOWDATE,
				);

				$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
				$nImageLastId = $oPdo->lastInsertId();

				#紀錄動作 - 新增
				$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;
				$aActionLog = array(
					'nWho'		=> (int) $aUser['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nLastId,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 7100701,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);
			}

			if ($aUser['nStatus'] == 11)
			{
				$sSQL = '	SELECT 	nId,
								nPendingStatus,
								sPendingStatus
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId = :nId
						LIMIT 1 FOR UPDATE';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);
				$aUserPendingStatus = explode(',', $aRows['sPendingStatus']);		// 資料審核狀態

				# update client_user_data nType3=>1 已上傳
				$aSQL_Array = array(
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);

				if ($aUserPendingStatus[$aPendingField['sBankCard']] == 99) // 被拒絕更正資料
				{
					$aUserPendingStatus[$aPendingField['sBankCard']] = 0; // 改成未審核
				}
				$aSQL_Array['sPendingStatus'] = implode(',', $aUserPendingStatus);
				if (!in_array(99, $aUserPendingStatus)) // 都沒有被拒絕
				{
					$aSQL_Array['nPendingStatus'] = 0; // 審核狀態再次變為未審核
				}

				$sSQL = '	UPDATE 	'.CLIENT_USER_DATA.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE		nId = :nId
						LIMIT 	1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
				$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_USER_DATA]['aNew']['nId'] = $aUser['nId'];

			}

			$oPdo->commit();

			$aReturn['sMsg'] = INSV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_bank_list_0.php']);
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nUid,
						nBid,
						nOnline
				FROM 		'.CLIENT_USER_BANK.'
				WHERE 	nOnline != 99
				AND		nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nId']] = $aRows;
		}

		if (!isset($aData[$nId]))
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= NODATA.'<br>';
		}

		// if (count($aData) == 1)
		// {
		// 	$aReturn['nStatus'] = 0;
		// 	$aReturn['sMsg'] .= aERR['ATLEASTONEBANK'].'<br>';
		// }

		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE 	'.CLIENT_USER_BANK.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE		nId = :nId
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_BANK]['aOld'] = $aData[$nId];
			$aEditLog[CLIENT_USER_BANK]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100703,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = DELV;
			$aReturn['aData']['nId'] = $nId;
		}
	}

	echo json_encode($aReturn);
	exit;
?>
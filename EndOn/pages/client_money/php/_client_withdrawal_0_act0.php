<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_withdrawal.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');
	#require end

	#參數接收區
	$nId			= filter_input_int('nId',			INPUT_REQUEST,0);

	// Excel 用
	$sStartTime		= filter_input_str('sStartTime', 		INPUT_GET,date('Y-m-d 00:00:00'));
	$sEndTime		= filter_input_str('sEndTime', 		INPUT_GET,date('Y-m-d 23:59:59'));
	$nKid			= filter_input_int('nKid', 			INPUT_GET,0);
	$nStatus		= filter_input_int('nStatus', 		INPUT_GET,-1);
	$sAdmin		= filter_input_str('sAdmin', 			INPUT_GET,'');
	$sMemberAccount	= filter_input_str('sMemberAccount', 	INPUT_GET,'');
	$sMemo		= filter_input_str('sMemo', 			INPUT_GET,'');
	#參數結束

	#參數宣告區
	$aData = array();
	$aMemberData = array();
	$aValid = array();
	$aPayment = array();
	$aEditLog = array(
		CLIENT_MONEY	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);

	$sChangePage = sys_web_encode($aMenuToNo['pages/client_money/php/_client_withdrawal_0.php']).$aJWT['sBackParam'];
	$nBefore = 0;
	$nDelta = 0;
	$nAfter = 0;
	$nErr = 0;
	$sMsg = '';

	$aData = array();
	$aSearchId = array();
	$aBankCard = array();
	$sCondition = '';
	$aBindArray = array();
	$aStatus = aWITHDRAWAL['STATUS'];
	$nTotalMoney = 0;
	$nTotalCount = 0;
	#宣告結束

	#程式邏輯區

	if ($aJWT['a'] == 'RISKPASS'.$nId)
	{
		$oPdo->beginTransaction();

		$sSQL = '	SELECT	nId,
						nUid,
						nKid,
						nStatus,
						nAdmin1,
						nUpdateTime,
						sUpdateTime
				FROM	'.CLIENT_MONEY .'
				WHERE	nType0 = 3
				AND	nStatus = 0
				AND	nAdmin1 <= 0
				AND	nId = :nId
				LIMIT	1 FOR	UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if($aPayment === false)
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = NODATA;
		}

		$sSQL = '	SELECT	nId,
						sName0
				FROM	'.	CLIENT_USER_BANK .'
				WHERE		nOnline = 1
				AND		nId = :nId
				LIMIT		1
				FOR		UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$aData['nKid'],PDO::PARAM_INT);
		sql_query($Result);
		$aPayment = $Result->fetch(PDO::FETCH_ASSOC);
		if($aPayment === false)
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = aWITHDRAWAL['ACCOUNTNOTFOUND'];
		}

		$aValid = array(
			'nKid'	=> $aData['nKid'],
			'sTable'	=> CLIENT_USER_BANK,
			'sNameOld'	=> $aPayment['sName0'],
			'NOWTIME'	=> NOWTIME
		);
		if(!cDataEncrypt::check($aValid))
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = aWITHDRAWAL['ACCOUNTERR'];
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nAdmin1'		=> (int) $aAdm['nId'],
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_MONEY]['aOld'] = $aData;
			$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_MONEY]['aNew']['nId'] = $aData['nId'];

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $aData['nUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aData['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107201,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();
			$sMsg = UPTV;
		}
	}

	if ($aJWT['a'] == 'RISKDENY'.$nId)
	{
		$oPdo->beginTransaction();

		$sSQL = '	SELECT	nId,
						nUid,
						nMoney,
						nStatus,
						nFee,
						nKid,
						nAdmin1,
						nUpdateTime,
						sUpdateTime
				FROM	'.CLIENT_MONEY .'
				WHERE	nType0 = 3
				AND	nStatus = 0
				AND	nAdmin1 <= 0
				AND	nId = :nId
				LIMIT	1 FOR	UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if($aData === false)
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = NODATA;
		}
		else
		{
			$sSQL = '	SELECT	nId,
							nUid,
							nMoney,
							nMoneyTime,
							sMoneyKey
					FROM	'.	CLIENT_USER_MONEY .'
					WHERE		nUid = :nUid
					LIMIT		1
					FOR		UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aData['nUid'], PDO::PARAM_INT);
			sql_query($Result);
			$aMemberData = $Result->fetch(PDO::FETCH_ASSOC);

			$nBefore	= $aMemberData['nMoney'];
			$nDelta 	= $aData['nMoney'] + $aData['nFee'];
			$nAfter 	= $nBefore + $nDelta;

			$aNewMoney = array(
				'Money' => (float) $nAfter,
			);

			$aSQL_Array = oTransfer::PointUpdate($aData['nUid'],$aNewMoney,1,true);
			if($aSQL_Array !== false)
			{
				$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nUid = :nUid LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aData['nUid'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_USER_MONEY]['aOld'] = $aMemberData;
				$aEditLog[CLIENT_USER_MONEY]['aNew'] = $aSQL_Array;

				$aAccLog = array(
					'nUid' 		=> (int) $aData['nUid'],
					'nKid' 		=> (int) $aData['nId'],
					'nType0' 		=> (int) 2,
					'nType1' 		=> (int) $aData['nKid'],
					'nType2' 		=> (int) 203,
					'nType3' 		=> (int) 0,
					'nBefore' 		=> (float) $nBefore,
					'nDelta' 		=> (float) $nDelta,
					'nAfter' 		=> (float) $nAfter,
					'sParams' 		=> (string) '',
					'nCreateTime' 	=> (int) NOWTIME,
					'sCreateTime' 	=> (string) NOWDATE,
					'nCreateDay' 	=> (int) strtotime('today'),
				);
				DoLogAcc($aAccLog);

				$aSQL_Array = array(
					'nAdmin1'		=> (int) $aAdm['nId'],
					'nStatus'		=> (int) 99,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	UPDATE '.CLIENT_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_MONEY]['aOld'] = $aData;
				$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_MONEY]['aNew']['nId'] = $aData['nId'];

				$aActionLog = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) $aData['nUid'],
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $aData['nId'],
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8107202,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$oPdo->commit();
				$sMsg = UPTV;
			}
			else
			{
				$oPdo->rollback();
				$sMsg = NODATA;
			}
		}
	}

	if ($aJWT['a'] == 'MONEYPASS'.$nId)
	{
		$oPdo->beginTransaction();

		$sSQL = '	SELECT	nId,
						nUid,
						nKid,
						nStatus,
						nAdmin1,
						nAdmin2,
						nUpdateTime,
						sUpdateTime
				FROM	'.CLIENT_MONEY .'
				WHERE	nType0 = 3
				AND	nStatus = 0
				AND	nAdmin1 > 0
				AND	nAdmin2 <= 0
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

		$sSQL = '	SELECT	nId,
						sName0
				FROM	'.CLIENT_USER_BANK .'
				WHERE	nOnline = 1
				AND	nId = :nId
				LIMIT	1 FOR	UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$aData['nKid'],PDO::PARAM_INT);
		sql_query($Result);
		$aPayment = $Result->fetch(PDO::FETCH_ASSOC);
		if($aPayment === false)
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = NODATA;
		}
		$aValid = array(
			'nKid'	=> $aData['nKid'],
			'sTable'	=> CLIENT_USER_BANK,
			'sNameOld'	=> $aPayment['sName0'],
		);
		if(!cDataEncrypt::check($aValid))
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = aWITHDRAWAL['ACCOUNTERR'];
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nAdmin2'		=> (int) $aAdm['nId'],
				'nStatus'		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '.CLIENT_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_MONEY]['aOld'] = $aData;
			$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_MONEY]['aNew']['nId'] = $aData['nId'];

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $aData['nUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aData['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107203,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();
			$sMsg = UPTV;
		}
	}

	if ($aJWT['a'] == 'MONEYDENY'.$nId)
	{
		$oPdo->beginTransaction();
		$sSQL = '	SELECT	nId,
						nUid,
						nMoney,
						nStatus,
						nKid,
						nFee,
						nAdmin1,
						nUpdateTime,
						sUpdateTime
				FROM	'.CLIENT_MONEY .'
				WHERE	nType0 = 3
				AND	nStatus = 0
				AND	nAdmin1 > 0
				AND	nAdmin2 <= 0
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
		else
		{
			$sSQL = '	SELECT	nUid,
							nMoney,
							nMoneyTime,
							sMoneyKey
					FROM	'.	CLIENT_USER_MONEY .'
					WHERE		nUid = :nUid
					LIMIT		1
					FOR		UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aData['nUid'], PDO::PARAM_INT);
			sql_query($Result);
			$aMemberData = $Result->fetch(PDO::FETCH_ASSOC);

			$nBefore	= $aMemberData['nMoney'];
			$nDelta 	= $aData['nMoney'] + $aData['nFee'];
			$nAfter 	= $nBefore + $nDelta;

			$aNewMoney = array(
				'Money' => (float) $nAfter,
			);

			$aSQL_Array = oTransfer::PointUpdate($aData['nUid'],$aNewMoney,1,true);
			if($aSQL_Array !== false)
			{
				$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nUid = :nUid LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aData['nUid'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_MONEY]['aOld'] = $aMemberData;
				$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;

				$aAccLog = array(
					'nUid' 		=> (int) $aData['nUid'],
					'nKid' 		=> (int) $aData['nId'],
					'nType0' 		=> (int) 2,
					'nType1' 		=> (int) $aData['nKid'],
					'nType2' 		=> (int) 203,
					'nType3' 		=> (int) 0,
					'nBefore' 		=> (float) $nBefore,
					'nDelta' 		=> (float) $nDelta,
					'nAfter' 		=> (float) $nAfter,
					'sParams' 		=> (string) '',
					'nCreateTime' 	=> (int) NOWTIME,
					'sCreateTime' 	=> (string) NOWDATE,
					'nCreateDay' 	=> (int) strtotime('today'),
				);
				DoLogAcc($aAccLog);

				$aSQL_Array = array(
					'nAdmin2'		=> (int) $aAdm['nId'],
					'nStatus'		=> (int) 99,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);

				$sSQL = '	UPDATE '.CLIENT_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_MONEY]['aOld'] = $aData;
				$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_MONEY]['aNew']['nId'] = $aData['nId'];

				$aActionLog = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) $aData['nUid'],
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $aData['nId'],
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8107204,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$oPdo->commit();
				$sMsg = UPTV;
			}
			else
			{
				$oPdo->rollback();
				$sMsg = NODATA;
			}
		}
	}

	if ($aJWT['a'] == 'EXCEL')
	{
		header("Content-type:application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition:filename=". NOWTIME .".xls");

		$sCondition .= ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
		$aBindArray['nStartTime'] = strtotime($sStartTime);
		$aBindArray['nEndTime'] = strtotime($sEndTime);
		if($nKid > 0)
		{
			$sCondition .= ' AND nKid = :nKid';
			$aBindArray['nKid'] = $nKid;
		}
		if($nStatus > -1)
		{
			$sCondition .= ' AND nStatus = :nStatus';
			$aBindArray['nStatus'] = $nStatus;
		}
		if($sAdmin != '')
		{
			$sSQL = '	SELECT 	nId
					FROM 	'.END_MANAGER_DATA.'
					WHERE nOnline = 1
					AND 	sAccount LIKE :sAccount';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', '%'.$sAdmin.'%', PDO::PARAM_STR);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aSearchId[$aRows['nId']] = $aRows['nId'];
			}
			if (!empty($aSearchId))
			{
				$sCondition .= ' AND ( nAdmin1 IN ( '.implode(',', $aSearchId).' ) OR nAdmin2 IN ( '.implode(',', $aSearchId).' ) ) ';
				$aSearchId = array();
			}
		}
		if($sMemberAccount != '')
		{
			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nOnline = 1
					AND 	sAccount LIKE :sAccount';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', '%'.$sMemberAccount.'%', PDO::PARAM_STR);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aSearchId[$aRows['nId']] = $aRows['nId'];
			}
			if (!empty($aSearchId))
			{
				$sCondition .= ' AND nUid IN ( '.implode(',', $aSearchId).' ) ';
				$aSearchId = array();
			}
		}
		if($sMemo != '')
		{
			$sCondition .= ' AND sMemo LIKE :sMemo';
			$aBindArray['sMemo'] = '%'.$sMemo.'%';
		}
		// 取銀行
		$sSQL = '	SELECT	nId,
						sName0,
						sCode
				FROM	'.SYS_BANK .'
				WHERE	nType0 = 1
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aBank[$aRows['nId']] = $aRows;
			$aBank[$aRows['nId']]['sSelect'] = '';
			if($nKid == $aRows['nId'])
			{
				$aBank[$aRows['nId']]['sSelect'] = 'selected';
			}
		}
		$sSQL = '	SELECT 	nId,
						nUid,
						nKid,
						nAdmin1,
						nAdmin2,
						nMoney,
						nStatus,
						nFee,
						sMemo,
						sCreateTime,
						sUpdateTime
				FROM 	'.CLIENT_MONEY.'
				WHERE nType0 = 3
				'.$sCondition.'
				ORDER BY nId DESC';
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result,$aBindArray);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$nTotalMoney += $aRows['nMoney'];
			$nTotalCount ++;

			$aData[$aRows['nId']] = $aRows;


			$aSearchId['aUser'][$aRows['nUid']] = $aRows['nUid'];
			$aSearchId['aAdmin'][$aRows['nAdmin1']] = $aRows['nAdmin1'];
			$aSearchId['aAdmin'][$aRows['nAdmin2']] = $aRows['nAdmin2'];
			$aSearchId['aKid'][$aRows['nKid']] = $aRows['nKid'];
		}

		if (!empty($aSearchId['aAdmin']))
		{
			$aAdminData['-1']['sAccount'] = '';
			$sSQL = '	SELECT 	nId,
							sAccount
					FROM 	'.END_MANAGER_DATA.'
					WHERE nId IN ('.implode(',', $aSearchId['aAdmin']).')';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aAdminData[$aRows['nId']] = $aRows;
			}
		}
		if (!empty($aSearchId['aUser']))
		{
			$sSQL = '	SELECT 	nId,
							sAccount
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nId IN ('.implode(',', $aSearchId['aUser']).')';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aMemberData[$aRows['nId']] = $aRows;
			}
		}
		if (!empty($aSearchId['aKid']))
		{
			# 會員銀行卡
			$sSQL = '	SELECT 	nId,
							sName0,
							sName1,
							sName2,
							nBid
					FROM 	'.CLIENT_USER_BANK.'
					WHERE nId IN ( '.implode(',', $aSearchId['aKid']).' )';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aBankCard[$aRows['nId']] = $aRows;
				$aBankCard[$aRows['nId']]['sBankName'] = $aBank[$aRows['nBid']]['sName0'];
			}
		}

		echo $sStartTime.' ~ '.$sEndTime.'<br><br>';
		echo '<table border=2>';

		echo '<tr>';
		echo '<td>'. aWITHDRAWAL['ACCOUNT'] .'</td>';
		echo '<td>'. aWITHDRAWAL['BANKNAME'] .'</td>';
		echo '<td>'. aWITHDRAWAL['SUBNAME'] .'</td>';
		echo '<td>'. aWITHDRAWAL['USERNAME'] .'</td>';
		echo '<td>'. aWITHDRAWAL['CARDNUMBER'] .'</td>';
		echo '<td>'. aWITHDRAWAL['MONEY'] .'</td>';
		echo '<td>'. aWITHDRAWAL['FEE'] .'</td>';
		echo '<td>'. aWITHDRAWAL['STATUS']['sTitle'] .'</td>';
		echo '<td>'. aWITHDRAWAL['ADMIN1'] .'</td>';
		echo '<td>'. aWITHDRAWAL['ADMIN2'] .'</td>';
		echo '<td>'. aWITHDRAWAL['MEMO'] .'</td>';
		echo '<td>'. CREATETIME .'</td>';
		echo '<td>'. UPDATETIME .'</td>';
		echo '</tr>';

		foreach ($aData as $LPnId => $LPaData)
		{
			echo '<tr>';
			echo '<td>'.$aMemberData[$LPaData['nUid']]['sAccount'].'</td>';

			echo '<td>'.$aBankCard[$LPaData['nKid']]['sBankName'].'</td>';
			echo '<td>'.$aBankCard[$LPaData['nKid']]['sName2'].'</td>';
			echo '<td>'.$aBankCard[$LPaData['nKid']]['sName1'].'</td>';
			echo '<td>'.$aBankCard[$LPaData['nKid']]['sName0'].'</td>';

			echo '<td>'.$LPaData['nMoney'].'</td>';
			echo '<td>'.$LPaData['nFee'].'</td>';
			echo '<td>'.$aStatus[$LPaData['nStatus']]['sText'].'</td>';
			echo '<td>'.$aAdminData[$LPaData['nAdmin1']]['sAccount'].'</td>';
			echo '<td>'.$aAdminData[$LPaData['nAdmin2']]['sAccount'].'</td>';
			echo '<td>'.$LPaData['sMemo'].'</td>';
			echo '<td>'.$LPaData['sCreateTime'].'</td>';
			echo '<td>'.$LPaData['sUpdateTime'].'</td>';
			echo '</tr>';
		}
		echo '</table><br>';
		echo aWITHDRAWAL['TOTALCOUNT'].' : '.$nTotalCount.' '.aWITHDRAWAL['TOTALMONEY'].' : '.$nTotalMoney.'<br>';
		exit;
	}


	#程式邏輯結束
	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sChangePage;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
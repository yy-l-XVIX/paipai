<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_payment_company.php');
	#require end

	#參數接收區
	$nId			= filter_input_int('nId',			INPUT_REQUEST,0);

	$sStartTime		= filter_input_str('sStartTime',		INPUT_GET,'');
	$sEndTime		= filter_input_str('sEndTime',		INPUT_GET,'');
	$sAdmin		= filter_input_str('sAdmin',			INPUT_GET,'');
	$sMemberAccount	= filter_input_str('sMemberAccount',	INPUT_GET,'');
	$sOrder		= filter_input_str('sOrder',			INPUT_GET,'');
	$nKid			= filter_input_int('nKid',			INPUT_GET,0);
	$nStatus		= filter_input_int('nStatus',			INPUT_GET,0);
	#參數結束

	#參數宣告區

	$aData = array();
	$aPayment = array();
	$aMemberData = array();
	# 帳變參數 #
	$aEditLog = array(
		CLIENT_MONEY	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
		CLIENT_PAYMENT	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
		CLIENT_USER_MONEY	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);

	$sSearch = '';
	$nErr = 0;
	$sMsg = '';
	#宣告結束

	#程式邏輯區

	if ($aJWT['a'] == 'PASS'.$nId)
	{
		$oPdo->beginTransaction();

		$sSQL = '	SELECT	nId,
						nUid,
						nMoney,
						nStatus,
						nFee,
						nKid,
						nUkid,
						nAdmin0,
						nUpdateTime,
						sUpdateTime
				FROM	'.	CLIENT_MONEY .'
				WHERE		nType0 = 1
				AND		nStatus = 0
				AND		nId = :nId
				LIMIT		1
				FOR		UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$oPdo->rollback();
			$nErr = 1;
			$sMsg = $aMsg['NOORDER'];
		}
		else
		{
			$aData['sAccount'] = '';
			# 取身分
			$sSQL = '	SELECT	nLid,
							sName0,
							nPrice,
							sPromoteBonus,
							sPromoteBonusTax,
							nType1
					FROM	'.CLIENT_USER_KIND .'
					WHERE	nOnline = 1
					AND	sLang LIKE :sLang
					AND	nLid = :nLid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
			$Result->bindValue(':nLid', $aData['nUkid'], 	PDO::PARAM_INT);
			sql_query($Result);
			$aUserKind = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aUserKind === false)
			{
				$oPdo->rollback();
				$nErr = 1;
				$sMsg .= aERROR['KIND'];
			}
			$aUserKind['aPromoteBonus'] = ($aUserKind['sPromoteBonus'] != '')?explode(',', $aUserKind['sPromoteBonus']):array();
			$aUserKind['aPromoteBonusTax'] = ($aUserKind['sPromoteBonusTax'] != '')?explode(',', $aUserKind['sPromoteBonusTax']):array();


			# 取link
			$sSQL = '	SELECT	nPa,
							sLinkList
					FROM	'.	CLIENT_USER_LINK .'
					WHERE		nUid = :nUid
					AND		nEndTime = 0
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$aData['nUid'],PDO::PARAM_INT);
			sql_query($Result);
			$aLinkData = $Result->fetch(PDO::FETCH_ASSOC);
			$aLinkData['aLinkList'] = explode(',', $aLinkData['sLinkList']);
			array_pop($aLinkData['aLinkList']); // 把自己剔除

			# 統計上級拿多少錢
			foreach ($aUserKind['aPromoteBonus'] as $LPnI => $LPnBonus)
			{
				$LPnUid = (int) array_pop($aLinkData['aLinkList']);
				$LPnTax = 0;
				if (isset($aUserKind['aPromoteBonusTax'][$LPnI]))
				{
					$LPnTax = $aUserKind['aPromoteBonusTax'][$LPnI]; // 須扣除稅金
				}

				if ($aUserKind['nType1'] == 0) # 固定金額
				{
					$aMemberData[$LPnUid]['nDelta'] = ($LPnBonus-$LPnTax);
					$aMemberData[$LPnUid]['nPromoteBonusTax'] = $LPnTax;
				}
				if ($aUserKind['nType1'] == 1) # 百分比 (拿付款金額來處理)
				{
					$aMemberData[$LPnUid]['nDelta'] = ($LPnBonus * $aData['nMoney'] / 100) * ((100 - $LPnTax) / 100);
					// $aMemberData[$LPnUid]['nDelta'] = (($LPnBonus-$LPnTax) * $aUserKind['nPrice'])/100;
					$aMemberData[$LPnUid]['nPromoteBonusTax'] =  ($LPnBonus * $aUserKind['nPrice'] / 100) * ($LPnTax / 100) ;
				}
			}

			# 取需變動會員
			$sSQL = '	SELECT	nId,
							sAccount,
							nStatus,
							nKid,
							sKid,
							nExpired0,
							sExpired0,
							nExpired1,
							sExpired1,
							nUpdateTime,
							sUpdateTime
					FROM	'.CLIENT_USER_DATA .'
					WHERE	nId IN ('.implode(',', array_keys($aMemberData)).','.$aData['nUid'].')
					FOR	UPDATE';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aMemberData[$aRows['nId']]['sAccount']	= $aRows['sAccount'];
				$aMemberData[$aRows['nId']]['nStatus']	= $aRows['nStatus'];
				$aMemberData[$aRows['nId']]['nKid']		= $aRows['nKid'];
				$aMemberData[$aRows['nId']]['sKid']		= $aRows['sKid'];
				$aMemberData[$aRows['nId']]['aKid'] 	= explode(',', $aRows['sKid']);
				$aMemberData[$aRows['nId']]['nExpired0']	= $aRows['nExpired0'];
				$aMemberData[$aRows['nId']]['nExpired1']	= $aRows['nExpired1'];

				if ($aRows['nId'] == $aData['nUid'])
				{
					if ($aRows['nExpired0'] < NOWTIME)
					{
						$aMemberData[$aRows['nId']]['nExpired0']	= NOWTIME;
					}
					if ($aRows['nExpired1'] < NOWTIME)
					{
						$aMemberData[$aRows['nId']]['nExpired1']	= NOWTIME;
					}
					$aData['sAccount'] = $aRows['sAccount'];
				}
			}

			# 更新充值會員到期時間
			$aSQL_Array = array(
				'nUpdateTime'		=> (int)	NOWTIME,
				'sUpdateTime'		=> (string)	NOWDATE,
			);
			switch ($aData['nUkid'])
			{
				case '1':
					# 雇主到期日

					$aSQL_Array['nExpired1'] = $aMemberData[$aData['nUid']]['nExpired1']+($aSystem['aParam']['nPackageDays']*86400);// 86400 = 1day
					$aSQL_Array['sExpired1'] = date('Y-m-d H:i:s',$aSQL_Array['nExpired1']);
					break;

				case '3':
					# 人才到期日

					$aSQL_Array['nExpired0'] = $aMemberData[$aData['nUid']]['nExpired0']+($aSystem['aParam']['nPackageDays']*86400);// 86400 = 1day
					$aSQL_Array['sExpired0'] = date('Y-m-d H:i:s',$aSQL_Array['nExpired0']);
					break;
			}
			if ($aMemberData[$aData['nUid']]['nStatus'] == 11)
			{
				$aSQL_Array['nStatus'] = 0;
			}
			if (!in_array($aData['nUkid'], $aMemberData[$aData['nUid']]['aKid']))
			{
				array_push($aMemberData[$aData['nUid']]['aKid'], $aData['nUkid']);
				$aSQL_Array['sKid'] = implode(',', $aMemberData[$aData['nUid']]['aKid']);
			}

			$sSQL = '	UPDATE '.CLIENT_USER_DATA.'
					SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nUid LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$aData['nUid'],PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aMemberData[$aData['nUid']];
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_USER_DATA]['aNew']['nId'] = $aData['nUid'];
			$aData['sAccount'] = $aMemberData[$aData['nUid']]['sAccount'];
			unset($aMemberData[$aData['nUid']]);

			# 更新上級金額 ($aMemberData 剩下爸爸們)
			foreach($aMemberData as $LPnUid => $LPaDetail)
			{
				if ($LPaDetail['nDelta'] == 0)
				{
					continue;
				}
				#pa到期則反給公司(總代理 nUid)
				if ($LPaDetail['nExpired0'] < NOWTIME && $LPaDetail['nExpired1'] < NOWTIME )
				{
					$LPnUid = $aSystem['aParam']['nAgentId'];
				}
				$sSQL = '	SELECT	nId,
								nUid,
								nMoney,
								nMoneyTime,
								sMoneyKey
						FROM	'. CLIENT_USER_MONEY .'
						WHERE	nUid = :nUid
						LIMIT 1
						FOR	UPDATE';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid',$LPnUid,PDO::PARAM_INT);
				sql_query($Result);
				$LPaMember = $Result->fetch(PDO::FETCH_ASSOC);
				if($LPaMember === false)
				{
					$oPdo->rollback();
					error_log('線上入款審核:219 查無會員 nUid = '.$LPnUid);
					$nErr = 1;
					$sMsg .= '查無會員金額';
					break;
				}

				$nBefore	= $LPaMember['nMoney'];
				$nDelta 	= $LPaDetail['nDelta'];
				$nAfter 	= $nBefore + $nDelta;

				$aNewMoney = array(
					'Money' => (float) $nAfter,
				);

				$aSQL_Array = oTransfer::PointUpdate($LPnUid,$aNewMoney,1,true);
				if($aSQL_Array !== false)
				{
					$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE	nId = :nId LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nId', $LPaMember['nId'], PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);

					$aData['nPromoteBonusTax'] = $LPaDetail['nPromoteBonusTax'];// 將扣除稅金存入log =>sParams

					$aAccLog = array(
						'nUid' 		=> (int)	$LPnUid,
						'nFromUid' 		=> (int)	$aData['nUid'],
						'nKid' 		=> (int)	$aData['nId'],
						'nType0' 		=> (int)	2,
						'nType1' 		=> (int)	$aData['nKid'],
						'nType2' 		=> (int)	201,
						'nType3' 		=> (int)	0,
						'nBefore' 		=> (float)	$nBefore,
						'nDelta' 		=> (float)	$nDelta,
						'nAfter' 		=> (float)	$nAfter,
						'sParams' 		=> (string)	json_encode($aData),
						'nCreateTime' 	=> (int)	NOWTIME,
						'sCreateTime' 	=> (string)	NOWDATE,
						'nCreateDay' 	=> (int)	strtotime('today'),
					);
					DoLogAcc($aAccLog);
				}
				else
				{
					$oPdo->rollback();
					error_log('線上入款審核 :308 :更新會員money失敗 nUid='.$LPnUid);
					$nErr = 1;
					$sMsg .= '更新會員金額失敗';
					break;
				}
			}

			if ($nErr == 0)
			{
				# 更新公司入款累計金額
				$sSQL = '	SELECT	nId,
								nDayMoney,
								nDayTimes,
								nTotalMoney,
								nTotalTimes,
								nUpdateTime,
								sUpdateTime
						FROM	'.CLIENT_PAYMENT .'
						WHERE	nId = :nId
						LIMIT	1
						FOR	UPDATE';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId',$aData['nKid'],PDO::PARAM_INT);
				sql_query($Result);
				$aPayment = $Result->fetch(PDO::FETCH_ASSOC);

				$aSQL_Array = array(
					'nDayMoney'			=> (float) $aPayment['nDayMoney'] + $aData['nMoney'],
					'nDayTimes'			=> (int) $aPayment['nDayTimes'] + 1,
					'nTotalMoney'		=> (float) $aPayment['nTotalMoney'] + $aData['nMoney'] + $aData['nFee'],
					'nTotalTimes'		=> (int) $aPayment['nTotalTimes'] + 1,
					'nUpdateTime'		=> (int) NOWTIME,
					'sUpdateTime'		=> (string) NOWDATE,
				);

				$sSQL = '	UPDATE '.CLIENT_PAYMENT.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aData['nKid'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_PAYMENT]['aOld'] = $aData;
				$aEditLog[CLIENT_PAYMENT]['aNew'] = $aSQL_Array;

				$aSQL_Array = array(
					'nStatus'			=> (int) 1,
					'nAdmin0'			=> (int) $aAdm['nId'],
					'nUpdateTime'		=> (int) NOWTIME,
					'sUpdateTime'		=> (string) NOWDATE,
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
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) $aData['sAccount'],
					'nKid'		=> (int) $aData['nId'],
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8107101,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$sMsg = UPTV;
				$oPdo->commit();
			}
		}
	}

	if ($aJWT['a'] == 'CANCEL'.$nId)
	{
		$oPdo->beginTransaction();

		$sSQL = '	SELECT	nId,
						nUid,
						nMoney,
						nStatus,
						nFee,
						nUkid,
						nAdmin0,
						nUpdateTime,
						sUpdateTime
				FROM	'.	CLIENT_MONEY .'
				WHERE		nType0 = 1
				AND		nStatus = 0
				AND		nId = :nId
				LIMIT		1
				FOR		UPDATE';
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
			$aEditLog[CLIENT_MONEY]['aOld'] = $aData;

			$aSQL_Array = array(
				'nStatus'		=> (int) 99,
				'nAdmin0'			=> (int) $aAdm['nId'],
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_MONEY . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = DELV;

			$oPdo->commit();
		}
	}

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_0.php']).$aJWT['sBackParam'] ;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	#程式邏輯結束
?>
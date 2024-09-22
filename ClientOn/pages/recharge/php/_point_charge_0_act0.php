<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/point_charge.php');

	$nKid		= filter_input_int('nKid',		INPUT_POST, 0);

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
	$aData = array();
	$aReturn = array(
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);
	$aKind = array();
	$aMemberData = array();
	$aEditLog = array(
		CLIENT_MONEY => array(
			'aOld' =>array(),
			'aNew' =>array(),
		),
	);

	if ($aJWT['a'] == 'INS')
	{
		$sSQL = '	SELECT	nLid,
						sName0,
						nPrice,
						sPromoteBonus,
						sPromoteBonusTax,
						nType1
				FROM	'.CLIENT_USER_KIND.'
				WHERE	nOnline = 1
				AND 	nLid = :nLid
				AND	sLang LIKE :sLang';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid', $nKid, PDO::PARAM_INT);
		$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
		sql_query($Result);
		$aKind = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aKind === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['KIND'];
		}
		if ($aUser['nMoney'] < $aKind['nPrice'])
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['NOMONEY'];
		}
		$aKind['aPromoteBonus'] = ($aKind['sPromoteBonus'] != '')?explode(',', $aKind['sPromoteBonus']):array();
		$aKind['aPromoteBonusTax'] = ($aKind['sPromoteBonusTax'] != '')?explode(',', $aKind['sPromoteBonusTax']):array();

		if ($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			# 會員扣款
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
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			$aMember = $Result->fetch(PDO::FETCH_ASSOC);

			$nBefore	= $aMember['nMoney'];
			$nDelta 	= $aKind['nPrice'] * -1;
			$nAfter 	= $nBefore + $nDelta;

			$aNewMoney = array(
				'Money' => (float) $nAfter,
			);
			$aSQL_Array = oTransfer::PointUpdate($aUser['nId'],$aNewMoney,1,true);
			if($aSQL_Array !== false)
			{
				$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aMember['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				// 建立訂單
				$aSQL_Array = array(
					'nUid'		=> (int)	$aUser['nId'],
					'nMoney'		=> (float)	$aKind['nPrice'],
					'nStatus'		=> (int)	1,
					'nKid'		=> (int)	0,
					'nUkid'		=> (int)	$nKid,
					'sOrder'		=> (string)	'',
					'sPaymentName1'	=> (string)	'',
					'sPayType'		=> (string)	'nMoney',
					'nType0'		=> (int)	5,
					'nType1'		=> (int)	3,
					'nType2'		=> (int)	1,
					'nType3'		=> (int)	1,
					'nFee'		=> (float)	0,
					'nAdmin0'		=> (int)	-1,
					'nAdmin1'		=> (int)	-1,
					'nAdmin2'		=> (int)	-1,
					'sMemo'		=> (string)	'',
					'nCreateTime'	=> (int)	NOWTIME,
					'sCreateTime'	=> (string)	NOWDATE,
					'nUpdateTime'	=> (int)	NOWTIME,
					'sUpdateTime'	=> (string)	NOWDATE,
					'nCreateDay'	=> (int)	NOWTIME,
				);
				$sSQL = 'INSERT INTO '.CLIENT_MONEY.' ' . sql_build_array('INSERT', $aSQL_Array );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
				$nLastId = $oPdo->lastInsertId();

				$aData = $aSQL_Array;
				$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_MONEY]['aNew']['nId'] = $nLastId;

				$aAccLog = array(
					'nUid' 		=> (int) $aUser['nId'],
					'nKid' 		=> (int) $nLastId,
					'nType0' 		=> (int) 2,
					'nType1' 		=> (int) 0,
					'nType2' 		=> (int) 200,
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

				# update expiredtime
				$sSQL = '	SELECT 	nId,
								sKid,
								nExpired0,
								sExpired0,
								nExpired1,
								sExpired1,
								nStatus
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId = :nId
						AND 	nOnline = 1
						LIMIT 1 FOR UPDATE';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);
				if ($aRows !== false)
				{
					if ($aRows['nExpired0'] < NOWTIME)
					{
						$aRows['nExpired0']	= NOWTIME;
					}
					if ($aRows['nExpired1'] < NOWTIME)
					{
						$aRows['nExpired1']	= NOWTIME;
					}
					$aRows['aKid'] = explode(',', $aRows['sKid']);
					$aSQL_Array = array(
						'nUpdateTime'		=> (int)	NOWTIME,
						'sUpdateTime'		=> (string)	NOWDATE,
					);
					switch ($nKid)
					{
						case '1':
							# 雇主到期日
							$aSQL_Array['nExpired1'] = $aRows['nExpired1']+($aSystem['aParam']['nPackageDays']*86400);// 86400 = 1day
							$aSQL_Array['sExpired1'] = date('Y-m-d H:i:s',$aSQL_Array['nExpired1']);
							break;

						case '3':
							# 人才到期日
							$aSQL_Array['nExpired0'] = $aRows['nExpired0']+($aSystem['aParam']['nPackageDays']*86400);// 86400 = 1day
							$aSQL_Array['sExpired0'] = date('Y-m-d H:i:s',$aSQL_Array['nExpired0']);
							break;
					}
					if ($aRows['nStatus'] == 11)
					{
						$aSQL_Array['nStatus'] = 0;
					}
					if (!in_array($nKid, $aRows['aKid']))
					{
						array_push($aRows['aKid'], $nKid);
						$aSQL_Array['sKid'] = implode(',', $aRows['aKid']);
					}

					$sSQL = '	UPDATE '.CLIENT_USER_DATA.'
							SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE	nId = :nUid LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);

					$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
					$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;
					$aEditLog[CLIENT_USER_DATA]['aNew']['nId'] = $aUser['nId'];
				}

				#
				$sSQL = '	SELECT	nPa,
								sLinkList
						FROM	'.	CLIENT_USER_LINK .'
						WHERE		nUid = :nUid
						AND		nEndTime = 0
						LIMIT		1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
				sql_query($Result);
				$aLinkData = $Result->fetch(PDO::FETCH_ASSOC);
				$aLinkData['aLinkList'] = explode(',', $aLinkData['sLinkList']);
				array_pop($aLinkData['aLinkList']); // 把自己剔除

				# 統計上級拿多少錢
				foreach ($aKind['aPromoteBonus'] as $LPnI => $LPnBonus)
				{
					$LPnUid = (int) array_pop($aLinkData['aLinkList']);
					$LPnTax = 0;
					if (isset($aKind['aPromoteBonusTax'][$LPnI]))
					{
						$LPnTax = $aKind['aPromoteBonusTax'][$LPnI]; // 須扣除稅金
					}
					if ($aKind['nType1'] == 0) # 固定金額
					{
						$aMemberData[$LPnUid]['nDelta'] = ($LPnBonus-$LPnTax);
						$aMemberData[$LPnUid]['nPromoteBonusTax'] = $LPnTax;
					}
					if ($aKind['nType1'] == 1) # 百分比 (拿付款金額來處理)
					{
						$aMemberData[$LPnUid]['nDelta'] = ($LPnBonus * $aData['nMoney'] / 100) * ((100 - $LPnTax) / 100);
						// $aMemberData[$LPnUid]['nDelta'] = (($LPnBonus-$LPnTax) * $aKind['nPrice'])/100;
						$aMemberData[$LPnUid]['nPromoteBonusTax'] =  ($LPnBonus * $aKind['nPrice'] / 100) * ($LPnTax / 100) ;
					}
				}

				$sSQL = '	SELECT 	nId,
								nExpired0,
								nExpired1
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId IN ('.implode(',', array_keys($aMemberData)).') ';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
				{
					$aMemberData[$aRows['nId']]['nExpired0'] = $aRows['nExpired0'];
					$aMemberData[$aRows['nId']]['nExpired1'] = $aRows['nExpired1'];
				}

				# 更新上級金額 ($aMemberData 存爸爸們)
				foreach ($aMemberData as $LPnUid => $LPaDetail)
				{
					#pa到期則反給公司(總代理 nUid)
					if ( $LPaDetail['nExpired0'] < NOWTIME && $LPaDetail['nExpired1'] < NOWTIME )
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
					if ($LPaMember === false)
					{
						$oPdo->rollBack();
						error_log('查會員money資料');
						exit;
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

						$aEditLog[CLIENT_MONEY]['aNew']['nPromoteBonusTax'] = $LPaDetail['nPromoteBonusTax'];// 將扣除稅金存入log =>sParams

						$aAccLog = array(
							'nUid' 		=> (int) $LPnUid,
							'nFromUid' 		=> (int) $aUser['nId'],
							'nKid' 		=> (int) $nLastId,
							'nType0' 		=> (int) 2,
							'nType1' 		=> (int) 0,
							'nType2' 		=> (int) 201,
							'nType3' 		=> (int) 0,
							'nBefore' 		=> (float) $nBefore,
							'nDelta' 		=> (float) $nDelta,
							'nAfter' 		=> (float) $nAfter,
							'sParams' 		=> (string) json_encode($aEditLog[CLIENT_MONEY]['aNew']),
							'nCreateTime' 	=> (int) NOWTIME,
							'sCreateTime' 	=> (string) NOWDATE,
							'nCreateDay' 	=> (int) strtotime('today'),
						);
						DoLogAcc($aAccLog);
					}
					else
					{
						$oPdo->rollBack();
						error_log('更新會員money失敗');
						exit;
					}
				}

				// #pa到期則反給公司(總代理 nUid)
				// if ($aRows['nExpired0'] < NOWTIME && $aRows['nExpired1'] < NOWTIME )
				// {
				// 	$nPa = $aSystem['aParam']['nAgentId'];
				// }

				// $sSQL = '	SELECT	nUid,
				// 				nMoney,
				// 				nMoneyTime,
				// 				sMoneyKey
				// 		FROM	'.	CLIENT_USER_MONEY .'
				// 		WHERE		nUid = :nUid
				// 		LIMIT		1
				// 		FOR		UPDATE';
				// $Result = $oPdo->prepare($sSQL);
				// $Result->bindValue(':nUid', $nPa, PDO::PARAM_INT);
				// sql_query($Result);
				// $aMemberData = $Result->fetch(PDO::FETCH_ASSOC);

				// $nBefore	= $aMemberData['nMoney'];
				// $nDelta 	= $aKind[$nKid]['nBonus0'];
				// $nAfter 	= $nBefore + $nDelta;

				// $aNewMoney = array(
				// 	'Money' => (float) $nAfter,
				// );

				// $aSQL_Array = oTransfer::PointUpdate($nPa,$aNewMoney,1,true);
				// if($aSQL_Array !== false)
				// {
				// 	$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
				// 			WHERE	nUid = :nUid LIMIT 1';
				// 	$Result = $oPdo->prepare($sSQL);
				// 	$Result->bindValue(':nUid', $nPa, PDO::PARAM_INT);
				// 	sql_build_value($Result, $aSQL_Array);
				// 	sql_query($Result);

				// 	$aAccLog = array(
				// 		'nUid' 		=> (int) $nPa,
				// 		'nFromUid' 		=> (int) $aUser['nId'],
				// 		'nKid' 		=> (int) 0,
				// 		'nType0' 		=> (int) 2,
				// 		'nType1' 		=> (int) 0,
				// 		'nType2' 		=> (int) 201,
				// 		'nType3' 		=> (int) 0,
				// 		'nBefore' 		=> (float) $nBefore,
				// 		'nDelta' 		=> (float) $nDelta,
				// 		'nAfter' 		=> (float) $nAfter,
				// 		'sParams' 		=> (string) '',
				// 		'nCreateTime' 	=> (int) NOWTIME,
				// 		'sCreateTime' 	=> (string) NOWDATE,
				// 		'nCreateDay' 	=> (int) strtotime('today'),
				// 	);
				// 	DoLogAcc($aAccLog);
				// }

				#紀錄動作 - 新增
				$aActionLog = array(
					'nWho'		=> (int) $aUser['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nLastId,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 7100401,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$oPdo->commit();
				$aReturn['sMsg'] = INSV;
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
			}
		}
	}

	echo json_encode($aReturn);
	exit;
?>
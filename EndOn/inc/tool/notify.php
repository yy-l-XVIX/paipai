<?php
	#require
	ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/error_log.txt');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/System.php');
	require_once(dirname(dirname(__FILE__)).'/#Define.php');
	require_once(dirname(dirname(__FILE__)).'/#DefineTable.php');
	require_once(dirname(dirname(__FILE__)).'/#Function.php');
	$aSystem['nConnect'] = 2;
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/ConnectBase.php');
	require_once(dirname(dirname(__FILE__)).'/lang/'.$aSystem['sLang'].'/define.php');
	#require end

	#參數接收區
	$aPost = array();
	$aPost['nUid']		= filter_input_int('nUid',		INPUT_POST,0);
	$aPost['nMoney']		= filter_input_int('nMoney',		INPUT_POST,0);
	$aPost['sOrder']		= filter_input_str('sOrder',		INPUT_POST,'');
	$aPost['sPaymentName1']	= filter_input_str('sPaymentName1',	INPUT_POST,'');
	$sSign			= filter_input_str('sSign',		INPUT_POST,'');
	#參數結束

	if(true)	# true / false
	{
		$file = 'moneyerror.txt';
		$aErr = array(
			'time'	=> date('Y-m-d H:i:s',time()),
			'where'	=> 'start',
			'REQUEST'	=> $_REQUEST,
		);
		$sErr = json_encode($aErr);
		$sMoneyLog = $sErr.PHP_EOL;
		file_put_contents($file, $sMoneyLog, FILE_APPEND | LOCK_EX);
	}

	#參數宣告區
	$aData = array();
	$aPayment = array();
	$aMemberData = array();
	$aReturn = array(
		'nStatus'	=> 0,
		'sMsg'	=> 'error',
	);
	$sSearchIds = '0';

	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	nId,
					sSign
			FROM	'. CLIENT_PAYMENT .'
			WHERE	sName1 LIKE :sName1
			LIMIT	1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sName1',$aPost['sPaymentName1'],PDO::PARAM_STR);
	sql_query($Result);
	$aPayment = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aPayment === false)
	{
		error_log('notify : 1');
		$aReturn['nStatus'] = 10;
		$aReturn['sMsg'] = '查無金流平台';
		echo json_encode($aReturn);
	}

	# 驗證簽名 #
	$sSignCheck = sortASCII($aPost);
	$sSignCheck .= '&sKey=' . $aPayment['sSign'];
	if($sSignCheck !== $sSign)
	{
		error_log(print_r($aPost,true));
		error_log($sSignCheck.' = '.$sSign);
		error_log('notify : 2');
		$aReturn['nStatus'] = 11;
		$aReturn['sMsg'] = '驗證簽名錯誤123';
		echo json_encode($aReturn);
		exit;
	}

	// # 取訂單資料 #
	// $sSQL = '	SELECT 	nId
	// 		FROM		' . CLIENT_MONEY . '
	// 		WHERE		nStatus = 0
	// 		AND		nType0 = 2
	// 		AND		nKid = :nKid
	// 		AND		sOrder LIKE :sOrder
	// 		LIMIT		1';
	// $Result = $oPdo->prepare($sSQL);
	// $Result->bindValue(':nKid',	$nPaymentId,	PDO::PARAM_INT);
	// $Result->bindValue(':sOrder',	$sOrder,		PDO::PARAM_STR);
	// sql_query($Result);
	// $aRows = $Result->fetch(PDO::FETCH_ASSOC);
	// $nId = $aRows['nId'];

	$oPdo->beginTransaction();
	# 取訂單
	$sSQL = '	SELECT 	nId,
					nUid,
					nKid,
					nUkid,
					nMoney,
					nFee,
					sOrder
			FROM	' . CLIENT_MONEY . '
			WHERE	nStatus = 0
			AND	nKid = :nKid
			AND	sOrder LIKE :sOrder
			LIMIT	1
			FOR	UPDATE';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nKid',	$aPayment['nId'],PDO::PARAM_INT);
	$Result->bindValue(':sOrder',	$aPost['sOrder'],	PDO::PARAM_STR);
	sql_query($Result);
	$aData = $Result->fetch(PDO::FETCH_ASSOC);
	if($aData === false)
	{
		$oPdo->rollBack();
		error_log('notify : 3');
		$aReturn['nStatus'] = 12;
		$aReturn['sMsg'] = '查無訂單資料';
		echo json_encode($aReturn);
		exit;
	}
	$aData['nMoney'] = $aPost['nMoney'];

	# 取得購買身分
	$sSQL = '	SELECT	nLid,
					nPrice,
					sPromoteBonus,
					sPromoteBonusTax,
					nType1
			FROM	'.CLIENT_USER_KIND .'
			WHERE	sLang LIKE :sLang
			AND	nLid = :nLid
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	$Result->bindValue(':nLid', $aData['nUkid'], 	PDO::PARAM_INT);
	sql_query($Result);
	$aUserKind = $Result->fetch(PDO::FETCH_ASSOC);
	$aUserKind['aPromoteBonus'] = ($aUserKind['sPromoteBonus'] != '')?explode(',', $aUserKind['sPromoteBonus']):array();
	$aUserKind['aPromoteBonusTax'] = ($aUserKind['sPromoteBonusTax'] != '')?explode(',', $aUserKind['sPromoteBonusTax']):array();

	# 取得充值會員linklist
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
			FOR UPDATE';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
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

			$aSQL_Array['nExpired0'] = $aMemberData[$aData['nUid']]['nExpired0']+($aSystem['aParam']['nPackageDays']*86400);
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
	unset($aMemberData[$aData['nUid']]);

	# 更新上級金額 ($aMemberData 剩下爸爸們)
	foreach ($aMemberData as $LPnUid => $LPaDetail)
	{
		if ($LPaDetail['nDelta'] == 0)
		{
			continue;
		}
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
			error_log('notify : 5');
			$aReturn['nStatus'] = 13;
			$aReturn['sMsg'] = '查會員money資料';
			echo json_encode($aReturn);
			exit;
		}
		// $nType1 = $aData['nKid'];
		// $nType2 = 201;
		// $nLPBonus = $aUserKind['nBonus0'];

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
			$oPdo->rollBack();
			error_log('notify : 6');
			$aReturn['nStatus'] = 14;
			$aReturn['sMsg'] = '更新會員money失敗';
			echo json_encode($aReturn);
			exit;
		}
	}
	# 更新會員金額結束 #

	# 更新金流平台累計 #
	$sSQL = '	SELECT	nId,
					nDayMoney,
					nDayTimes,
					nTotalMoney,
					nTotalTimes,
					nUpdateTime,
					sUpdateTime
			FROM	'. CLIENT_PAYMENT .'
			WHERE	nId = :nId
			LIMIT	1
			FOR	UPDATE';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId',$aData['nKid'],PDO::PARAM_INT);
	sql_query($Result);
	$aPayment = $Result->fetch(PDO::FETCH_ASSOC);

	$aSQL_Array = array(
		'nDayMoney'			=> (float)	$aPayment['nDayMoney'] + $aData['nMoney'],
		'nDayTimes'			=> (int)	$aPayment['nDayTimes'] + 1,
		'nTotalMoney'		=> (float)	$aPayment['nTotalMoney'] + $aData['nMoney'] + $aData['nFee'],
		'nTotalTimes'		=> (int)	$aPayment['nTotalTimes'] + 1,
		'nUpdateTime'		=> (int)	NOWTIME,
		'sUpdateTime'		=> (string)	NOWDATE,
	);

	$sSQL = '	UPDATE '. CLIENT_PAYMENT .' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
			WHERE	nId = :nId LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $aData['nKid'], PDO::PARAM_INT);
	sql_build_value($Result, $aSQL_Array);
	sql_query($Result);
	# 更新金流平台結束 #

	$aEditLog[CLIENT_PAYMENT]['aOld'] = $aPayment;
	$aEditLog[CLIENT_PAYMENT]['aNew'] = $aSQL_Array;

	$aSQL_Array = array(
		'nStatus'			=> (int) 1,
		'nAdmin0'			=> (int) 0,
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
		'nWho'		=> (int)	$aData['nUid'],
		'nWhom'		=> (int)	0,
		'sWhomAccount'	=> (string)	'',
		'nKid'		=> (int)	$aData['nId'],
		'sIp'			=> (string)	USERIP,
		'nLogCode'		=> (int)	5100013,
		'sParam'		=> (string)	json_encode($aEditLog),
		'nType0'		=> (int)	0,
		'nCreateTime'	=> (int)	NOWTIME,
		'sCreateTime'	=> (string)	NOWDATE,
	);
	DoActionLog($aActionLog);

	$oPdo->commit();
	$aReturn['nStatus'] = 1;
	$aReturn['sMsg'] = 'success';
	echo json_encode($aReturn);
	exit;
	#程式邏輯結束
?>
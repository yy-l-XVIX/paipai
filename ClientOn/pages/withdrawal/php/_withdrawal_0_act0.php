<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/withdrawal.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');

	$sTransPassword	= filter_input_str('sTransPassword',INPUT_POST, '', 32);
	$nBid			= filter_input_int('nBid',		INPUT_POST, 0);
	$nMoney		= filter_input_int('nMoney',		INPUT_POST, 0);

	$nDayOrderCount = 0; #當日提領次數
	$nDayOrderMoney = 0; #當日提領金額
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
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/center/php/_transaction_record_0.php']).'&nType2=202',
	);

/*
	檢查交易密碼
	檢查銀行是否存在
	檢查餘額
transaction
	鎖自己檢查餘額
	扣自己錢
	寫自己帳變

	鎖好友檢查餘額
	扣好友錢
	寫好友帳變
commit
*/
	if ($aJWT['a'] == 'INS'.$aUser['nId'])
	{
		if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword))
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg']	.= aERROR['PASSWORDFORMAT'].'<br>';
		}
		$sSQL = '	SELECT 	sTransPassword
				FROM 		'.CLIENT_USER_DATA.'
				WHERE 	nId = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if (oCypher::ReHash($sTransPassword) != $aRows['sTransPassword'])
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg']	.= aERROR['PASSWORD'].'<br>';
		}

		$sSQL = '	SELECT  sName0
				FROM 	'.CLIENT_USER_BANK.'
				WHERE nId = :nBid
				AND 	nUid = :nUid
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nBid', $nBid, PDO::PARAM_INT);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aCard = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aCard === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['NOCARD'].'<br>';
		}
		else
		{
			// 驗證銀行帳號正確性
			$aValid = array(
				'nKid'	=> $nBid,
				'sTable'	=> CLIENT_USER_BANK,
				'sNameOld'	=> $aCard['sName0'],
			);
			if(!cDataEncrypt::check($aValid))
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aERROR['CARDERR'].'<br>';
			}
		}
		$sSQL = '	SELECT	nId,
						nStatus,
						nCreateTime
				FROM	'.CLIENT_MONEY.'
				WHERE	nUid = :nUid
				AND	nType0 = 3
				AND 	nCreateTime >= :nTime
				ORDER BY nCreateTime DESC';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nTime', strtotime('today'), PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			if ( $aRows['nStatus'] == 1 || $aRows['nStatus'] == 0) # 今日提單次數 (含未審核)
			{
				$nDayOrderCount++;
			}
		}
		if ($nDayOrderCount+1 > $aSystem['aParam']['nDayWithdrawal']) # 每日可提領次數
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg'] .= str_replace('[[::nDayWithdrawal::]]', $aSystem['aParam']['nDayWithdrawal'], aERROR['DAYTIMES']).'<br>';
		}
		if ($nMoney <= 0)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['MONEY'].'<br>';
		}
		if ($nMoney < $aSystem['aParam']['nMinWithdrawal']) # 單筆最低提領金額限制
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg'] .= str_replace('[[::nMinWithdrawal::]]', $aSystem['aParam']['nMinWithdrawal'], aERROR['MINMONEY']).'<br>';
		}
		if ($nMoney > $aSystem['aParam']['nMaxWithdrawal']) # 單筆最高提領金額限制
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg'] .= str_replace('[[::nMaxWithdrawal::]]', $aSystem['aParam']['nMaxWithdrawal'], aERROR['MAXMONEY']).'<br>';
		}
		if (($nMoney+$aSystem['aParam']['nWithdrawalFee']) > $aUser['nMoney']) # 檢查會員金額
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['BALANCE'].'<br>';
		}
		if($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			# 新增提領訂單
			$aSQL_Array = array(
				'nUid'		=> (int) $aUser['nId'],
				'nMoney'		=> (float) $nMoney,
				'nStatus'		=> (int) 0,
				'nKid'		=> (int) $nBid,
				'nUkid'		=> (int) $aUser['nKid'],
				'nType0'		=> (int) 3,
				'nType1'		=> (int) 3,
				'nType2'		=> (int) 1,
				'nType3'		=> (int) 2,
				'nFee'		=> (float) $aSystem['aParam']['nWithdrawalFee'],
				'nAdmin0'		=> (int) -1,
				'nAdmin1'		=> (int) -1,
				'nAdmin2'		=> (int) -1,
				'sMemo'		=> (string) '',
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.CLIENT_MONEY.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_MONEY]['aNew']['nId'] = $nLastId;

			# 扣自己錢
			$sSQL = '	SELECT	nId,
							nUid,
							nMoney,
							nMoneyTime,
							sMoneyKey
					FROM	'.CLIENT_USER_MONEY .'
					WHERE	nUid = :nUid
					LIMIT	1 FOR UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			$aMemberData = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aMemberData === false)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg']	.= aERROR['MEMBERMONEY'].'<br>';
			}
			if ($aMemberData['nMoney'] < $nMoney )
			{

				$aReturn['nStatus'] = 0;
				$aReturn['sMsg']	.= aERROR['BALANCE'].'<br>';
			}
			if ($aReturn['nStatus'] == 0)
			{
				$oPdo->rollback();
				echo json_encode($aReturn);
				exit;
			}

			$nBefore	= $aMemberData['nMoney'];
			$nDelta 	= ($nMoney+$aSystem['aParam']['nWithdrawalFee']) * -1;
			$nAfter 	= $nBefore + $nDelta;

			$aNewMoney = array(
				'Money' => (float) $nAfter,
			);

			$aSQL_Array = oTransfer::PointUpdate($aUser['nId'],$aNewMoney,1,true);
			if($aSQL_Array !== false)
			{
				$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nUid = :nUid LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aAccLog = array(
					'nUid' 		=> (int) $aUser['nId'],
					'nKid' 		=> (int) $nLastId,
					'nType0' 		=> (int) 2,
					'nType1' 		=> (int) 0,
					'nType2' 		=> (int) 202,
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

				$aEditLog[CLIENT_USER_MONEY][$aMemberData['nId']]['aOld'] = $aMemberData;
				$aEditLog[CLIENT_USER_MONEY][$aMemberData['nId']]['aNew'] = $aSQL_Array;
			}
			else
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg']	= aERROR['MONEYFAIL'].'<br>';
				$oPdo->rollback();
				echo json_encode($aReturn);
				exit;
			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aUser['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$aReturn['sMsg'] = aWITHDRAWAL['SUCCESS'];

			$oPdo->commit();
		}
	}
	echo json_encode($aReturn);
	exit;
?>
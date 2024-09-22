<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/transfer.php');

	$sAccount		= filter_input_str('sAccount',		INPUT_POST, '', 20);
	$sTransPassword	= filter_input_str('sTransPassword',	INPUT_POST, '', 32);
	$sMemo		= filter_input_str('sMemo',			INPUT_POST, '', 255);
	$nMoney		= filter_input_int('nMoney',			INPUT_POST, 0);

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
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/center/php/_transaction_record_0.php'])
	);
	$nFUid = 0;
	$aParams = array(
		'nPayUid'	=> $aUser['nId'],
		'nTakeUid'	=> $nFUid,
		'nMoney'	=> $nMoney,
		'sMemo'	=> $sMemo,
	);
/*
	檢查交易密碼
	檢查帳號是否存在
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
	if ($aJWT['a'] == 'TRANS'.$aUser['nId'])
	{
		if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword))
		{
			$aReturn['nStatus']	= 0;
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
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['PASSWORD'].'<br>';
		}

		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE sAccount = :sAccount
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount',$sAccount,PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['ACCOUNT'].'<br>';
		}
		else
		{
			$nFUid = $aRows['nId'];
			$aParams['nTakeUid'] = $nFUid;

			$sSQL = '	SELECT 	1
					FROM 	'.CLIENT_USER_FRIEND.'
					WHERE nFUid = :nFUid
					AND 	nUid = :nUid
					AND 	nStatus = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nFUid',$nFUid,PDO::PARAM_STR);
			$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false || $nFUid == $aUser['nId'])#不可轉給自己
			{
				$aReturn['nStatus']	= 0;
				$aReturn['sMsg']	.= aERROR['FRIEND'].'<br>';
			}
		}

		if ($nMoney <= 0)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['MONEY'].'<br>';
		}
		if ($nMoney > $aUser['nMoney'])
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['BALANCE'].'<br>';
		}

		if($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			# 扣自己錢
			$sSQL = '	SELECT	nId,
							nUid,
							nMoney,
							nMoneyTime,
							sMoneyKey
					FROM	'.	CLIENT_USER_MONEY .'
					WHERE	nUid = :nUid
					LIMIT	1
					FOR UPDATE';
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
			$nDelta 	= $nMoney * -1;
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
					'nKid' 		=> (int) $aMemberData['nId'],
					'nType0' 		=> (int) 2,
					'nType1' 		=> (int) 0,
					'nType2' 		=> (int) 211,
					'nType3' 		=> (int) 0,
					'nBefore' 		=> (float) $nBefore,
					'nDelta' 		=> (float) $nDelta,
					'nAfter' 		=> (float) $nAfter,
					'sParams' 		=> (string) json_encode($aParams),
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

			# 加好友錢
			$sSQL = '	SELECT	nId,
							nUid,
							nMoney,
							nMoneyTime,
							sMoneyKey
					FROM	'.	CLIENT_USER_MONEY .'
					WHERE	nUid = :nUid
					LIMIT	1
					FOR UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $nFUid, PDO::PARAM_INT);
			sql_query($Result);
			$aMemberData = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aMemberData === false)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg']	= aERROR['FRIENDMONEY'].'<br>';
			}
			if ($aReturn['nStatus'] == 0)
			{
				$oPdo->rollback();
				echo json_encode($aReturn);
				exit;
			}

			$nBefore	= $aMemberData['nMoney'];
			$nDelta 	= $nMoney;
			$nAfter 	= $nBefore + $nDelta;

			$aNewMoney = array(
				'Money' => (float) $nAfter,
			);

			$aSQL_Array = oTransfer::PointUpdate($nFUid,$aNewMoney,1,true);
			if($aSQL_Array !== false)
			{
				$sSQL = '	UPDATE '.CLIENT_USER_MONEY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nUid = :nUid LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $nFUid, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aAccLog = array(
					'nUid' 		=> (int) $nFUid,
					'nFromUid'		=> (int) $aUser['nId'],
					'nKid' 		=> (int) $aMemberData['nId'],
					'nType0' 		=> (int) 2,
					'nType1' 		=> (int) 0,
					'nType2' 		=> (int) 210,
					'nType3' 		=> (int) 0,
					'nBefore' 		=> (float) $nBefore,
					'nDelta' 		=> (float) $nDelta,
					'nAfter' 		=> (float) $nAfter,
					'sParams' 		=> (string) json_encode($aParams),
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
				'nLogCode'		=> (int) 7101101,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$aReturn['sMsg'] = aTRANSFER['SUCCESS'];

			$oPdo->commit();
		}
	}
	echo json_encode($aReturn);
	exit;
?>
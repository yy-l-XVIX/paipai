<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_payment_company_setting.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');
	#require end

	#參數接收區
	$nId			= filter_input_int('nId',			INPUT_REQUEST,0);
	$sName0		= filter_input_str('sName0',			INPUT_POST, '');
	$sAccount0		= filter_input_str('sAccount0',		INPUT_POST, '');
	$nOnline		= filter_input_int('nOnline',			INPUT_POST, 0);
	$nBid			= filter_input_int('nBid',			INPUT_POST, 0);
	$nMax			= filter_input_int('nMax',			INPUT_POST, 0);
	$nMin			= filter_input_int('nMin',			INPUT_POST, 0);
	$nDayLimitMoney	= filter_input_int('nDayLimitMoney',	INPUT_POST, 0);
	$nDayLimitTimes	= filter_input_int('nDayLimitTimes',	INPUT_POST, 0);
	$nTotalLimitMoney	= filter_input_int('nTotalLimitMoney',	INPUT_POST, 0);
	$nTotalLimitTimes	= filter_input_int('nTotalLimitTimes',	INPUT_POST, 0);
	#參數結束
	#參數宣告區
	$aData = array();
	$aValid = array();
	$aEditLog = array(
		CLIENT_PAYMENT	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$sKey = '';
	#宣告結束

	#程式邏輯區

	if($nTotalLimitMoney <= 0)
	{
		$nTotalLimitMoney = 0;
	}

	if($nTotalLimitMoney >= 10000000)
	{
		$nTotalLimitMoney = 10000000;
	}

	if($nDayLimitMoney <= 0)
	{
		$nDayLimitMoney = 0;
	}

	if($nDayLimitMoney >= 1000000)
	{
		$nDayLimitMoney = 1000000;
	}

	if ($aJWT['a'] == 'INS')
	{
		$oPdo->beginTransaction();
		$aSQL_Array = array(
			'sName0'			=> (string) $sName0,
			'sAccount0'			=> (string) $sAccount0,
			'nOnline'			=> (int) $nOnline,
			'nBid'			=> (int) $nBid,
			'nType0'			=> (int) 1,
			// 'nMax'			=> (float) $nMax,
			// 'nMin'			=> (float) $nMin,
			// 'nDayLimitMoney'		=> (float) $nDayLimitMoney,
			'nDayLimitTimes'		=> (int) $nDayLimitTimes,
			'nTotalLimitMoney'	=> (float) $nTotalLimitMoney,
			'nTotalLimitTimes'	=> (int) $nTotalLimitTimes,
			'nCreateTime'		=> (int) NOWTIME,
			'sCreateTime'		=> (string) NOWDATE,
			'nUpdateTime'		=> (int) NOWTIME,
			'sUpdateTime'		=> (string) NOWDATE,
		);

		$sSQL = 'INSERT INTO '. CLIENT_PAYMENT . ' ' . sql_build_array('INSERT', $aSQL_Array );
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aSQL_Array);
		sql_query($Result);
		$nLastId = $oPdo->lastInsertId();

		$aEditLog[CLIENT_PAYMENT]['aNew'] = $aSQL_Array;
		$aEditLog[CLIENT_PAYMENT]['aNew']['nId'] = $nLastId;

		$aData = array(
			'nKid'	=> $nLastId,
			'sTable'	=> CLIENT_PAYMENT,
			'sName0'	=> $sAccount0,
			'NOWTIME'	=> NOWTIME
		);
		$sKey = cDataEncrypt::update($aData,false);

		$aSQL_Array = array(
			'nKid'		=> (int) $nLastId,
			'sTable'		=> (string) CLIENT_PAYMENT,
			'nEncryptTime'	=> (int) NOWTIME,
			'sEncryptKey'	=> (string) $sKey,
		);

		$sSQL = 'INSERT INTO '. CLIENT_DATA_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aSQL_Array);
		sql_query($Result);

		//log ??
		$aActionLog = array(
			'nWho'		=> (int) $aAdm['nId'],
			'nWhom'		=> (int) 0,
			'sWhomAccount'	=> (string) '',
			'nKid'		=> (int) $nLastId,
			'sIp'			=> (string) USERIP,
			'nLogCode'		=> (int) 8107001,
			'sParam'		=> (string) json_encode($aEditLog),
			'nType0'		=> (int) 0,
			'nCreateTime'	=> (int) NOWTIME,
			'sCreateTime'	=> (string) NOWDATE,
		);
		DoActionLog($aActionLog);

		$oPdo->commit();

		$aJumpMsg['0']['sMsg'] = INSV;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_setting_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$oPdo->beginTransaction();

		$sSQL = '	SELECT	nId,
						sName0,
						sAccount0,
						nOnline,
						nBid,
						nMax,
						nMin,
						nDayLimitMoney,
						nDayLimitTimes,
						nTotalLimitMoney,
						nTotalLimitTimes,
						sCreateTime,
						sUpdateTime
				FROM	'.	CLIENT_PAYMENT .'
				WHERE		nType0 = 1
				AND		nOnline != 99
				AND		nId = :nId
				LIMIT		1
				FOR		UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);

		if (empty($aData))
		{
			$aJumpMsg['0']['sMsg'] = NODATA;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_setting_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			$oPdo->rollback();
		}
		else
		{

			$aValid = array(
				'nKid'	=> $aData['nId'],
				'sTable'	=> CLIENT_PAYMENT,
				'sName0'	=> $sAccount0,
				'sNameOld'	=> $aData['sAccount0'],
				'NOWTIME'	=> NOWTIME
			);
			$sKey = cDataEncrypt::update($aValid);

			if($sKey !== false)
			{
				$aSQL_Array = array(
					'sName0'			=> (string) $sName0,
					'sAccount0'			=> (string) $sAccount0,
					'nOnline'			=> (int) $nOnline,
					'nBid'			=> (int) $nBid,
					'nMax'			=> (float) $nMax,
					'nMin'			=> (float) $nMin,
					'nDayLimitMoney'		=> (float) $nDayLimitMoney,
					'nDayLimitTimes'		=> (int) $nDayLimitTimes,
					'nTotalLimitMoney'	=> (float) $nTotalLimitMoney,
					'nTotalLimitTimes'	=> (int) $nTotalLimitTimes,
					'nUpdateTime'		=> (int) NOWTIME,
					'sUpdateTime'		=> (string) NOWDATE,
				);

				$sSQL = '	UPDATE '. CLIENT_PAYMENT . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aSQL_Array = array(
					'nKid'		=> (int) $aData['nId'],
					'sTable'		=> (string) CLIENT_PAYMENT,
					'nEncryptTime'	=> (int) NOWTIME,
					'sEncryptKey'	=> (string) $sKey,
				);

				$sSQL = '	UPDATE '. CLIENT_DATA_CTRL . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nKid = :nKid AND sTable LIKE :sTable LIMIT 1 ';
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				#紀錄動作 - 更新
				$aEditLog[CLIENT_PAYMENT]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_PAYMENT]['aNew']['nId'] = $aData['nId'];

				$aActionLog = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $aData['nId'],
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8107002,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$aJumpMsg['0']['sMsg'] = UPTV;
				$aJumpMsg['0']['sShow'] = 1;
				$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_setting_0.php']);
				$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
				$oPdo->commit();
			}
			else
			{
				$aJumpMsg['0']['sMsg'] = aPAYMENTCOMPANYSETTING['ACCOUNTERR'];
				$aJumpMsg['0']['sShow'] = 1;
				$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_setting_0.php']);
				$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
				$oPdo->rollback();
			}
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$oPdo->beginTransaction();
		$sSQL = '	SELECT	nId,
						sName0,
						sAccount0,
						nOnline,
						nBid,
						nMax,
						nMin,
						nDayLimitMoney,
						nDayLimitTimes,
						nTotalLimitMoney,
						nTotalLimitTimes,
						sCreateTime,
						sUpdateTime
				FROM	'.	CLIENT_PAYMENT .'
				WHERE		nType0 = 1
				AND		nOnline != 99
				AND		nId = :nId
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);

		if (empty($aData))
		{
			$aJumpMsg['0']['sMsg'] = NODATA;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_setting_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			$oPdo->rollback();
		}
		else
		{
			$aEditLog[CLIENT_PAYMENT]['aOld'] = $aData;
			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_PAYMENT . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_PAYMENT]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8107003,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aJumpMsg['0']['sMsg'] = DELV;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_setting_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			$oPdo->commit();
		}
	}
	#程式邏輯結束
?>
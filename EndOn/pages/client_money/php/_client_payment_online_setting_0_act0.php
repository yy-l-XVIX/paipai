<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_payment_online_setting.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');
	#require end

	#參數接收區
	$nId			= filter_input_int('nId',			INPUT_REQUEST,0);
	$sName0		= filter_input_str('sName0',			INPUT_POST, '');
	$sName1		= filter_input_str('sName1',			INPUT_POST, '');
	$sAccount0		= filter_input_str('sAccount0',		INPUT_POST, '');
	$nType1		= filter_input_int('nType1',			INPUT_POST, 1);
	$nOnline		= filter_input_int('nOnline',			INPUT_POST, 0);
	$nFee			= filter_input_int('nFee',			INPUT_POST, 0);
	$nDayLimitMoney	= filter_input_int('nDayLimitMoney',	INPUT_POST, 0);
	$nDayLimitTimes	= filter_input_int('nDayLimitTimes',	INPUT_POST, 0);
	$nTotalLimitMoney	= filter_input_int('nTotalLimitMoney',	INPUT_POST, 0);
	$nTotalLimitTimes	= filter_input_int('nTotalLimitTimes',	INPUT_POST, 0);
	$nTotalMoney	= filter_input_int('nTotalMoney',		INPUT_POST, 0);
	$nTotalTimes	= filter_input_int('nTotalTimes',		INPUT_POST, 0);
	$nDayMoney		= filter_input_int('nDayMoney',		INPUT_POST, 0);
	$nDayTimes		= filter_input_int('nDayTimes',		INPUT_POST, 0);
	$nMax			= filter_input_int('nMax',			INPUT_POST, 0);
	$nMin			= filter_input_int('nMin',			INPUT_POST, 0);
	$sKey0		= filter_input_str('sKey0',			INPUT_POST, '');
	$sKey1		= filter_input_str('sKey1',			INPUT_POST, '');
	$sKey2		= filter_input_str('sKey2',			INPUT_POST, '');
	$sKey3		= filter_input_str('sKey3',			INPUT_POST, '');
	$sKey4		= filter_input_str('sKey4',			INPUT_POST, '');
	$sKey5		= filter_input_str('sKey5',			INPUT_POST, '');
	$sSign		= filter_input_str('sSign',			INPUT_POST, '');
	
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

	# 每日提單金額上限 #
	if($nDayLimitMoney <= 0)
	{
		$nDayLimitMoney = 0;
	}

	if($nDayLimitMoney >= 1000000)
	{
		$nDayLimitMoney = 1000000;
	}

	# 總金額限制 #
	if($nTotalLimitMoney <= 0)
	{
		$nTotalLimitMoney = 0;
	}

	if($nTotalLimitMoney >= 10000000)
	{
		$nTotalLimitMoney = 10000000;
	}

	# 總累計金額 #
	if($nTotalMoney <= 0)
	{
		$nTotalMoney = 0;
	}

	if($nTotalMoney >= 1000000)
	{
		$nTotalMoney = 1000000;
	}

	# 當日累計金額 #
	if($nDayMoney <= 0)
	{
		$nDayMoney = 0;
	}

	if($nDayMoney >= 1000000)
	{
		$nDayMoney = 1000000;
	}

	if ($aJWT['a'] == 'INS')
	{
		$oPdo->beginTransaction();
		$aSQL_Array = array(
			'sName0'			=> (string)	$sName0,
			'sName1'			=> (string)	$sName1,
			'sAccount0'			=> (string)	$sAccount0,
			'nType0'			=> (int)	2,
			'nType1'			=> (int)	$nType1,
			'nOnline'			=> (int)	$nOnline,
			'nFee'			=> (float)	$nFee,
			'nDayLimitMoney'		=> (float)	$nDayLimitMoney,
			'nDayLimitTimes'		=> (int)	$nDayLimitTimes,
			'nTotalLimitMoney'	=> (float)	$nTotalLimitMoney,
			'nTotalLimitTimes'	=> (int)	$nTotalLimitTimes,
			'nTotalMoney'		=> (float)	$nTotalMoney,
			'nTotalTimes'		=> (int)	$nTotalTimes,
			'nDayMoney'			=> (float)	$nDayMoney,
			'nDayTimes'			=> (int)	$nDayTimes,
			'nMax'			=> (int)	$nMax,
			'nMin'			=> (int)	$nMin,
			'sKey0'			=> (string)	$sKey0,
			'sKey1'			=> (string)	$sKey1,
			'sKey2'			=> (string)	$sKey2,
			'sKey3'			=> (string)	$sKey3,
			'sKey4'			=> (string)	$sKey4,
			'sKey5'			=> (string)	$sKey5,
			'sSign'			=> (string)	$sSign,
			'nCreateTime'		=> (int)	NOWTIME,
			'sCreateTime'		=> (string)	NOWDATE,
			'nUpdateTime'		=> (int)	NOWTIME,
			'sUpdateTime'		=> (string)	NOWDATE,
		);

		$sSQL = 'INSERT INTO '. CLIENT_PAYMENT . ' ' . sql_build_array('INSERT', $aSQL_Array );
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aSQL_Array);
		sql_query($Result);
		$nLastId = $oPdo->lastInsertId();

		$aEditLog[CLIENT_PAYMENT]['aNew'] = $aSQL_Array;
		$aEditLog[CLIENT_PAYMENT]['aNew']['nId'] = $nLastId;

		//log
		$aActionLog = array(
			'nWho'		=> (int) $aAdm['nId'],
			'nWhom'		=> (int) 0,
			'sWhomAccount'	=> (string) '',
			'nKid'		=> (int) $nLastId,
			'sIp'			=> (string) USERIP,
			'nLogCode'		=> (int) 8107301,
			'sParam'		=> (string) json_encode($aEditLog),
			'nType0'		=> (int) 0,
			'nCreateTime'	=> (int) NOWTIME,
			'sCreateTime'	=> (string) NOWDATE,
		);
		DoActionLog($aActionLog);

		$oPdo->commit();


		$aJumpMsg['0']['sMsg'] = INSV;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_setting_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$oPdo->beginTransaction();

		$sSQL = '	SELECT	nId,
						sName0,
						nType1,
						nOnline,
						nFee,
						nDayLimitMoney,
						nDayLimitTimes,
						nTotalLimitMoney,
						nTotalLimitTimes,
						nTotalMoney,
						nTotalTimes,
						nDayMoney,
						nDayTimes,
						nMax,
						nMin,
						sSign,
						sCreateTime,
						sUpdateTime
				FROM		'. CLIENT_PAYMENT .'
				WHERE		nType0 = 2
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
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_setting_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			$oPdo->rollback();
		}
		else
		{
			if($sKey !== false)
			{
				$aSQL_Array = array(
					'sName0'			=> (string)	$sName0,
					'nType1'			=> (int)	$nType1,
					'nOnline'			=> (int)	$nOnline,
					'nFee'			=> (int)	$nFee,
					'nDayLimitMoney'		=> (float)	$nDayLimitMoney,
					'nDayLimitTimes'		=> (int)	$nDayLimitTimes,
					'nTotalLimitMoney'	=> (float)	$nTotalLimitMoney,
					'nTotalLimitTimes'	=> (int)	$nTotalLimitTimes,
					'nTotalMoney'		=> (float)	$nTotalMoney,
					'nTotalTimes'		=> (int)	$nTotalTimes,
					'nDayMoney'			=> (int)	$nDayMoney,
					'nDayTimes'			=> (int)	$nDayTimes,
					'nMax'			=> (int)	$nMax,
					'nMin'			=> (int)	$nMin,
					'sSign'			=> (string)	$sSign,
					'nUpdateTime'		=> (int)	NOWTIME,
					'sUpdateTime'		=> (string)	NOWDATE,
				);

				$sSQL = '	UPDATE '. CLIENT_PAYMENT . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				#紀錄動作 - 更新
				$aEditLog[CLIENT_PAYMENT]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_PAYMENT]['aNew']['nId'] = $aData['nId'];

				$aActionLog = array(
					'nWho'		=> (int)	$aAdm['nId'],
					'nWhom'		=> (int)	0,
					'sWhomAccount'	=> (string)	'',
					'nKid'		=> (int)	$aData['nId'],
					'sIp'			=> (string)	USERIP,
					'nLogCode'		=> (int)	8107302,
					'sParam'		=> (string)	json_encode($aEditLog),
					'nType0'		=> (int)	0,
					'nCreateTime'	=> (int)	NOWTIME,
					'sCreateTime'	=> (string)	NOWDATE,
				);
				DoActionLog($aActionLog);

				$aJumpMsg['0']['sMsg'] = UPTV;
				$aJumpMsg['0']['sShow'] = 1;
				$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_setting_0.php']);
				$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
				$oPdo->commit();
			}
			else
			{

				$aJumpMsg['0']['sMsg'] = aPAYMENTONLINESETTING['ACCOUNTERR'];
				$aJumpMsg['0']['sShow'] = 1;
				$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_setting_0.php']);
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
						sName1,
						sAccount0,
						nOnline,
						sCreateTime,
						sUpdateTime
				FROM	'.	CLIENT_PAYMENT .'
				WHERE		nType0 = 2
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
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_setting_0.php']);
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
				'nWho'		=> (int)	$aAdm['nId'],
				'nWhom'		=> (int)	0,
				'sWhomAccount'	=> (string)	'',
				'nKid'		=> (int)	$nId,
				'sIp'			=> (string)	USERIP,
				'nLogCode'		=> (int)	8107303,
				'sParam'		=> (string)	json_encode($aEditLog),
				'nType0'		=> (int)	0,
				'nCreateTime'	=> (int)	NOWTIME,
				'sCreateTime'	=> (string)	NOWDATE,
			);
			DoActionLog($aActionLog);


			$aJumpMsg['0']['sMsg'] = DELV;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_online_setting_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			$oPdo->commit();
		}
	}
	#程式邏輯結束
?>
<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/recharge.php');

	$nKid		= filter_input_int('nKid',		INPUT_POST, 0);
	$nPid		= filter_input_int('nPid',		INPUT_POST, 0);
	$nTunnel	= filter_input_int('nTunnel',		INPUT_POST, 0);

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
		'nStatus'		=> 0,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);
	$aKind = array();
	$aPayment = array();
	$aTunnel = array();
		$aEditLog = array(
		CLIENT_MONEY => array(
			'aOld' =>array(),
			'aNew' =>array(),
		),
	);

	$nOrder = 0;
	$sPaymentName1 = '';
	$sPayType = '';
	$sOrder = $aSystem['aWebsite']['sTitle0']; # 訂單前綴
	$sNotifyUrl = $aSystem['aWebsite']['sUrl0']; # 回調網址
	$sTime = date('Ymd',NOWTIME);
	$sTime = substr($sTime,1);
	$sOrder .= $sTime;
	$nErr = 0;
	$sMsg = '';

	if ($aJWT['a'] == 'INS')
	{
		$sSQL = '	SELECT	nLid,
						sName0,
						nPrice
				FROM		'.CLIENT_USER_KIND.'
				WHERE		nOnline = 1
				AND		sLang LIKE :sLang';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aKind[$aRows['nLid']] = $aRows;
		}
		if (!isset($aKind[$nKid])) // 檢查方案
		{
			$nErr = 1;
			$sMsg .= aERROR['KIND'].'<br>';
		}

		if ($nPid == 0)
		{
			$nErr = 1;
			$sMsg .= aERROR['PAYMENTSELECT'].'<br>';
		}
		else
		{
			$sSQL = '	SELECT  	sName0,
							sName1,
							sAccount0,
							nTotalLimitMoney,
							nTotalLimitTimes,
							nDayLimitTimes,
							nTotalMoney,
							nTotalTimes,
							nDayTimes,
							sSign
					FROM 		'.CLIENT_PAYMENT.'
					WHERE 	nId = :nPid
					AND 		nOnline = 1
					AND 		nType0 = 2
					LIMIT 	1';

			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nPid', $nPid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$nErr = 1;
				$sMsg .= aERROR['NOPAYMENT'].'<br>';
			}
			else
			{
				$aPayment = $aRows;
				if ($aPayment['nTotalLimitMoney'] > 0 && $aPayment['nTotalLimitMoney'] < ($aPayment['nTotalMoney']+$aKind[$nKid]['nPrice'])) # 總提單金額上限
				{
					$nErr = 1;
					$sMsg .= $aPayment['sName0'].aERROR['MONEYLIMIT'].'<br>';
				}
				if ($aPayment['nTotalLimitTimes'] > 0 && $aPayment['nTotalLimitTimes'] < $aPayment['nTotalTimes']+1) # 總提單次數上限
				{
					$nErr = 1;
					$sMsg .= $aPayment['sName0'].aERROR['MAXLIMIT'].'<br>';
				}
				if ($aPayment['nDayLimitTimes'] > 0 && $aPayment['nDayLimitTimes'] < $aPayment['nDayTimes']+1) # 每日提單次數上限
				{
					$nErr = 1;
					$sMsg .= $aPayment['sName0'].aERROR['DAYMAXLIMIT'].'<br>';
				}
			}
		}

		if ($nTunnel == 0)
		{
			$nErr = 1;
			$sMsg .= aERROR['TUNNELSELECT'].'<br>';
		}
		else
		{
			# 取通道資料 #
			$sSQL = '	SELECT 	nId,
							nPid,
							sKey,
							sValue
					FROM		' . CLIENT_PAYMENT_TUNNEL . '
					WHERE		nId = :nId
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nTunnel, PDO::PARAM_INT);
			sql_query($Result);
			$aTunnel = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aTunnel === false)
			{
				$nErr = 1;
				$sMsg .= aERROR['NOTUNNEL'].'<br>';
			}
		}

		# 5 分鐘內不可重複提單
		$sSQL = '	SELECT	nId,
						nKid,
						nStatus,
						nCreateTime
				FROM		'.CLIENT_MONEY.'
				WHERE		nUid = :nUid
				AND		nType0 = 2
				ORDER BY 	nCreateTime DESC';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			if (($aRows['nCreateTime'] + $aSystem['aParam']['nRechargeTime'] > NOWTIME) && $aRows['nStatus'] == 0 )
			{
				$nErr = 1;
				$sMsg .= str_replace('[[::nTime::]]', ($aSystem['aParam']['nRechargeTime']/60), aERROR['TRYAGAIN']).'<br>';;
				break;
			}
		}

		if ($nErr == 1)
		{
			$aReturn['nStatus'] = $nErr;
			$aReturn['sMsg'] = $sMsg;
			echo json_encode($aReturn);
			exit;
		}
		else
		{
			#建立訂單
			$aSQL_Array = array(
				'nUid'		=> (int)	$aUser['nId'],
				'nMoney'		=> (float)	$aKind[$nKid]['nPrice'],
				'nStatus'		=> (int)	0,
				'nKid'		=> (int)	$nPid,
				'nUkid'		=> (int)	$nKid,
				'sOrder'		=> (string)	$sOrder,
				'sPaymentName1'	=> (string)	$aPayment['sName1'],
				'sPayType'		=> (string)	$aTunnel['sKey'],
				'nType0'		=> (int)	2,
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

			$aEditLog[CLIENT_MONEY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_MONEY]['aNew']['nId'] = $nLastId;

			// 更新sOrder
			$sOrder .= str_pad($nLastId,4,0,STR_PAD_LEFT);
			$aSQL_Array = array(
				'sOrder'	=> (string) $sOrder,
			);
			$sSQL = '	UPDATE	'.CLIENT_MONEY.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nLastId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_MONEY]['aNew']['sOrder'] = $sOrder;

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int)	$aUser['nId'],
				'nWhom'		=> (int)	0,
				'sWhomAccount'	=> (string)	'',
				'nKid'		=> (int)	$nLastId,
				'sIp'			=> (string)	USERIP,
				'nLogCode'		=> (int)	5100001,
				'sParam'		=> (string)	json_encode($aEditLog),
				'nType0'		=> (int)	0,
				'nCreateTime'	=> (int)	NOWTIME,
				'sCreateTime'	=> (string)	NOWDATE,
			);
			DoActionLog($aActionLog);

			# 送往金流機
			$aPostData = array(
				'sAccount0'		=> (string)$aPayment['sAccount0'],
				'sName1'		=> (string)$aPayment['sName1'],
				'nUid'		=> (int)$aUser['nId'],
				'nMoney'		=> (int)$aKind[$nKid]['nPrice'],
				'sNotifyUrl'	=> (string)$sNotifyUrl,
				'sOrder'		=> (string)$sOrder,
				'sPayType'		=> (string)$aTunnel['sKey'],
				// 'sClientBackUrl'	=> (string)$aSystem['aWebsite']['sUrl2'], #前台網域
			);
			$sSign = sortASCII($aPostData);
			$sSign .= '&sKey=' . $aPayment['sSign'];
			$aPostData['sSign'] = md5($sSign);
			$aPostData['sTitle0'] = $aSystem['aWebsite']['sTitle0'];
			// error_log('Payment '.print_r($aPayment,true));
			// error_log('PostData '.print_r($aPostData,true));
			// echo 1;exit;
			$sUrl =  PAY['URL'];
			$ch = curl_init($sUrl);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');# GET || POST
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aPostData));
			# curl 執行時間
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
			curl_setopt($ch, CURLOPT_TIMEOUT,3);
			$result = curl_exec($ch);
			curl_close($ch);
			$aResult = json_decode($result,true);

			if(!empty($aResult) && isset($aResult['sUrl']))
			{
				header('Location:'.$aResult['sUrl']);
				exit;
			}

			$aReturn['aData']['sForm'] = $result;
		}
	}


	echo json_encode($aReturn);
	exit;
?>
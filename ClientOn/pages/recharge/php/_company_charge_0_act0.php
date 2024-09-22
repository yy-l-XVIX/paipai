<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/company_charge.php');

	$nKid		= filter_input_int('nKid',		INPUT_POST, 0);
	$nPid		= filter_input_int('nPid',		INPUT_POST, 0);
	$sMemo	= filter_input_str('sMemo',		INPUT_POST, '');

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

	// $nErr = 0;
	// $sMsg = '';

	if ($aJWT['a'] == 'INS')
	{
		if ($sMemo == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aRECHARGE['MEMOINFO'];
		}
		$sSQL = '	SELECT	nLid,
						sName0,
						nPrice,
						sPromoteBonus,
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

		$sSQL = '	SELECT  	sName0,
						sAccount0,
						nTotalLimitMoney,
						nTotalLimitTimes,
						nDayLimitTimes,
						nTotalMoney,
						nTotalTimes,
						nDayTimes
				FROM 	'.CLIENT_PAYMENT.'
				WHERE nId = :nPid
				AND 	nOnline = 1
				AND 	nType0 = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nPid', $nPid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr = 1;
			$aReturn['sMsg'] = aERROR['ACCOUNT'];
		}
		else
		{
			$aCompany = $aRows;
			if ($aCompany['nTotalLimitMoney'] > 0 && $aCompany['nTotalLimitMoney'] < ($aCompany['nTotalMoney']+$aRankData[$nRank]['nSalePrice'])) # 總提單金額上限
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = $aCompany['sName0'].aERROR['MONEYLIMIT'];
			}
			if ($aCompany['nTotalLimitTimes'] > 0 && $aCompany['nTotalLimitTimes'] < $aCompany['nTotalTimes']+1) # 總提單次數上限
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = $aCompany['sName0'].aERROR['MONEYLIMIT'];
			}
			if ($aCompany['nDayLimitTimes'] > 0 && $aCompany['nDayLimitTimes'] < $aCompany['nDayTimes']+1) # 每日提單次數上限
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = $aCompany['sName0'].aERROR['MONEYLIMIT'];
			}
		}
		// 驗證銀行帳號正確性
		// $aValid = array(
		// 	'nKid'	=> $nPid,
		// 	'sTable'	=> CLIENT_PAYMENT,
		// 	'sNameOld'	=> $aCompany['sAccount0'],
		// );
		// if(!cDataEncrypt::check($aValid))
		// {
		// 	$aReturn['nStatus'] = 0;
		// 	$aReturn['sMsg'] = aERROR['CARDERR'];
		// }

		if ($aReturn['nStatus'] == 1)
		{
			// 建立訂單
			$aSQL_Array = array(
				'nUid'		=> (int)	$aUser['nId'],
				'nMoney'		=> (float)	$aKind['nPrice'],
				'nStatus'		=> (int)	0,
				'nKid'		=> (int)	$nPid,
				'nUkid'		=> (int)	$nKid,
				'nType0'		=> (int)	1,
				'nType1'		=> (int)	3,
				'nType2'		=> (int)	1,
				'nType3'		=> (int)	1,
				'nFee'		=> (float)	$aSystem['aParam']['nRechargeFee'],
				'nAdmin0'		=> (int)	-1,
				'nAdmin1'		=> (int)	-1,
				'nAdmin2'		=> (int)	-1,
				'sMemo'		=> (string)	$sMemo,
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

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100402,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = INSV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);

		}
	}

	echo json_encode($aReturn);
	exit;
?>
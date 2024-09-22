<?php
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/index.php');

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/index/index.js',
	);

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sHtml'	=> 'pages/index/'.$aSystem['sHtml'].$aSystem['nVer'].'/index_0.php', # 本頁html
		'sAjax'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0_ajax0.php']).'&run_page=1',
	);
	#url結束

	#參數宣告區
	#$oSMS = new SMSHttp();
	$aData = array(
		'nTotalUser'	=> 0,
		'nTodayUser'	=> 0,
		'nRechargeCount'	=> 0,
		'nRechargeMoney'	=> 0,
		'nWithdrawalCount'=> 0,
		'nWithdrawalMoney'=> 0,
		'nSmsBalance'	=> 0,
		'sSmsBalanceColor'=> '',
	);

	$aValue = array(
		'a'		=> 'GETCREDIT',
		't'		=> NOWTIME,
	);
	$sAjaxJWT = sys_jwt_encode($aValue);
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	nId,
					sAccount,
					nCreateTime
			FROM	'.	CLIENT_USER_DATA .'
			WHERE	nOnline != 99
			AND 	nId != 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		# today
		if ($aRows['nCreateTime'] >= strtotime('today'))
		{
			$aData['nTodayUser'] ++;
		}
		$aData['nTotalUser'] ++;
	}

	$sSQL = '	SELECT 	nMoney,
					nType0
			FROM 	'.CLIENT_MONEY.'
			WHERE nStatus = 1
			AND 	nType2 = 1
			AND 	nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nStartTime', strtotime('today'), PDO::PARAM_INT);
	$Result->bindValue(':nEndTime', strtotime('tomorrow')-1, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		switch ($aRows['nType0'])
		{
			case '1':
				$aData['nRechargeCount'] ++;
				$aData['nRechargeMoney'] += $aRows['nMoney'];
				break;
			case '3':
				$aData['nWithdrawalCount'] ++;
				$aData['nWithdrawalMoney'] += $aRows['nMoney'];
				break;
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/account_record.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sRechargeList'		=> sys_web_encode($aMenuToNo['pages/center/php/_recharge_list_0.php']),
		'sRewardRecord'		=> sys_web_encode($aMenuToNo['pages/center/php/_reward_record_0.php']),
		'sDownlineList'		=> sys_web_encode($aMenuToNo['pages/center/php/_downline_list_0.php']),
		'sTransfer'			=> sys_web_encode($aMenuToNo['pages/center/php/_transfer_0.php']),
		'sWithdrawal'		=> sys_web_encode($aMenuToNo['pages/withdrawal/php/_withdrawal_0.php']),
		'sTransactionRecord'	=> sys_web_encode($aMenuToNo['pages/center/php/_transaction_record_0.php']),
		'sHtml'			=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/account_record_0.php',
	);
	#url結束

	#參數宣告區
	$aAccountRecord = array(
		'0'	=>	array(
			'sTitle'	=>	aAccountRecord['RECHARGEPLAN'],
			'aList'	=>	array(
				'sRechargeList'	=>	array(
					'sTitle'	=>	aAccountRecord['RECHARGELIST'],
					'sUrl'	=>	$aUrl['sRechargeList'],
				),
			),
		),
		'1'	=>	array(
			'sTitle'	=>	aAccountRecord['MYDOWNLINE'],
			'aList'	=>	array(
				'sRewardRecord'	=>	array(
					'sTitle'	=>	aAccountRecord['REWARDRECORD'],
					'sUrl'	=>	$aUrl['sRewardRecord'],
				),
				'sDownlineList'	=>	array(
					'sTitle'	=>	aAccountRecord['DOWNLINELIST'],
					'sUrl'	=>	$aUrl['sDownlineList'],
				),
			),
		),
		'2'	=>	array(
			'sTitle'	=>	aAccountRecord['WITHDRAWALTRANSFER'],
			'aList'	=>	array(
				'sTransfer'	=>	array(
					'sTitle'	=>	aAccountRecord['TRANSFER'],
					'sUrl'	=>	$aUrl['sTransfer'],
				),
				'sWithdrawal'	=>	array(
					'sTitle'	=>	aAccountRecord['WITHDRAWAL'],
					'sUrl'	=>	$aUrl['sWithdrawal'],
				),
				'sTransactionRecord'	=>	array(
					'sTitle'	=>	aAccountRecord['TRANSACTIONRECORD'],
					'sUrl'	=>	$aUrl['sTransactionRecord'],
				),
			),
		),
	);

	#宣告結束

	#程式邏輯區
	if ($aSystem['aParam']['nTransferSetting'] == 0) #關閉會員轉帳
	{
		unset($aAccountRecord[2]['aList']['sTransfer']);
	}

	#程式邏輯結束

	#輸出json
	#$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
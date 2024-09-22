<?php
	require_once('Connect/BaseCtrl.php');

	# 專案資料
	require_once('Connect/BaseSetting0.php');
	if ($aSystem['nConnect'] != 0)
	{
		# RX RL WX WL AX AL SX (from priority_process.php)
		if ($aSystem['nConnect'] == 1) # 讀
		{
			$aDB = array(
				'dbHost'		=>	'127.0.0.1',
				'dbName' 		=>	'paipaiv00',
				'dbUser'		=>	'root',
				'dbPassword' 	=>	'',
			);
		}

		if ($aSystem['nConnect'] == 2) # 讀寫
		{
			$aDB = array(
				'dbHost'		=>	'127.0.0.1',
				'dbName' 		=>	'paipaiv00',
				'dbUser'		=>	'root',
				'dbPassword' 	=>	'',
			);
		}

		if ($aSystem['nConnect'] == 3) # Ajax
		{
			$aDB = array(
				'dbHost'		=>	'127.0.0.1',
				'dbName' 		=>	'paipaiv00',
				'dbUser'		=>	'root',
				'dbPassword' 	=>	'',
			);
		}
		$oPdo = oPdo_Connection($aDB);
		unset($aDB);

		# 站台連線取資料
		require_once('Connect/BaseSetting1.php');
		# 需要DB連線的共用函式
		require_once('Connect/BaseFunction.php');
		# 前後台通用Function 加密
		require_once('Connect/oCypher.php');
		# 金錢加密
		require_once('Connect/oTransfer.php');
	}
?>
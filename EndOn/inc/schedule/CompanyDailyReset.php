<?php
	ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/error_log.txt');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/System.php');
	require_once(dirname(dirname(__FILE__)).'/#Define.php');
	require_once(dirname(dirname(__FILE__)).'/#DefineTable.php');
	require_once(dirname(dirname(__FILE__)).'/#Function.php');
	$aSystem['nConnect'] = 2;
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/ConnectBase.php');
	require_once(dirname(dirname(__FILE__)).'/lang/'.$aSystem['sLang'].'/define.php');

	$bPass = true;

	if($bPass)
	{
		#清除公司入款累計金額/次數
		$aSQL_Array = array(
			'nDayMoney'	=> (int) 0,
			'nDayTimes'	=> (int) 0
		);
		$sSQL = '	UPDATE ' . CLIENT_PAYMENT .' SET ' .sql_build_array('UPDATE',$aSQL_Array).'
				WHERE	nType0 = 1 AND ( nDayMoney != 0 OR nDayTimes != 0 )';
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result,$aSQL_Array);
		sql_query($Result);

		echo '公司入款當日累計金額/次數 已清除';
	}
?>
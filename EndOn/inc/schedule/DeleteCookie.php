<?php
	ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/error_log.txt');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/System.php');
	require_once(dirname(dirname(__FILE__)).'/#Define.php');
	require_once(dirname(dirname(__FILE__)).'/#DefineTable.php');
	require_once(dirname(dirname(__FILE__)).'/#Function.php');
	$aSystem['nConnect'] = 2;
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/ConnectBase.php');

	$bPass = true;

	if($bPass)
	{
		#清除過期cookie
		$sSQL = 'DELETE FROM ' . END_MANAGER_COOKIE . ' WHERE nUpdateTime < '. COOKIE['CLOSE'];
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);

		#清除過期cookie
		$sSQL = 'DELETE FROM ' . CLIENT_USER_COOKIE . ' WHERE nUpdateTime < '. COOKIE['CLOSE'];
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);

		echo 'cookie 已清除';
	}
?>
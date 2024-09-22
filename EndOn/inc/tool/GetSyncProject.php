<?php
	ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/error_log.txt');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/System.php');

	$sPost = file_get_contents('php://input');
	$aData = json_decode($sPost,true);

	$sText = '<?php '.PHP_EOL.'$aSystem[\'aWebsite\'] = array('.PHP_EOL;
	foreach ($aData as $LPsKey => $LPsValue)
	{
		$sText .= '\''.$LPsKey.'\' => '.'\''.$LPsValue.'\' ,'.PHP_EOL;
	}
	$sText .= ');'.PHP_EOL.'# last update '.date('Y-m-d H:i:s').PHP_EOL;

	$sText .= '$aSystem[\'sTitle\'] = '.'\''.$aData['sName0'].'\';'.PHP_EOL;
	$sText .= '$aSystem[\'sLang\'] = '.'\''.$aData['sLang'].'\';'.PHP_EOL;
	$sText .= '?>';


	$sFileName = dirname(dirname(dirname(dirname(__FILE__)))) .'/System/Connect/BaseSetting0.php'; //檔案名稱
	if (file_exists($sFileName))
	{
		  unlink($sFileName);
	}

	$open = fopen("$sFileName","w+"); //開啟檔案，要是沒有檔案將建立一份
	chmod($sFileName, 0644);
	fwrite($open,$sText); //寫入人數
	fclose($open); //關閉檔案
	exit;
?>
<?php
	require_once('phpqrcode/phpqrcode.php');

	if (!isset($_GET['sUrl']))
	{
		die('No Url');
	}


	$aData = array(
		'Lv'	=> 'L', #預設容錯 L、M、Q、H，Error Correction Level
		'Size'=> (isset($_GET['nSize']) ? $_GET['nSize'] : 4), #預設大小 1~10
		'Url' => urldecode($_GET['sUrl']), #對應qrcode的url
	);

	QRcode::png($aData['Url'], false, $aData['Lv'], $aData['Size'], 2);
?>
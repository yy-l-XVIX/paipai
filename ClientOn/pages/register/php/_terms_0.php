<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/terms.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/register/php/_register_0.php']),
		'sHtml'	=> 'pages/register/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/terms_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'sContent0'	=> '',
	);
	$nKid = 0;
	$sPromoCode = '';
	if (isset($aJWT['nKid']))
	{
		$nKid = $aJWT['nKid'];
	}
	if (isset($aJWT['sPromoCode']))
	{
		$sPromoCode = $aJWT['sPromoCode'];
	}
	$aValue = array(
		'nExp'	=> NOWTIME + JWTWAIT,
		'nKid'	=> $nKid,
		'sPromoCode'=> $sPromoCode,
	);
	$sJWT = sys_jwt_encode($aValue);
	$aUrl['sPage'] .= '&sJWT='.$sJWT;

	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	sContent0
			FROM		'.CLIENT_ANNOUNCE.'
			WHERE		sLang = :sLang
			AND		nKid = 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	$aData = $Result->fetch(PDO::FETCH_ASSOC);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
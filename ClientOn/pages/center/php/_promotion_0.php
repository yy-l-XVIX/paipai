<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/promotion.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/promotion.js',
	);

	#給此頁使用的url
	$aUrl   = array(
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sQrUrl' 		=> sys_web_encode($aMenuToNo['pages/tool/php/_qr_img_0.php']).'&run_page=1',
		'sRegister'		=> $aSystem['aWebsite']['sUrl2'].substr(sys_web_encode($aMenuToNo['pages/register/php/_choose_0.php']), 2).'&sPromoCode='.$aUser['sPromoCode'],
		'sHtml'		=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/promotion_0.php',
	);
	#url結束

	#參數宣告區
	$aValue = array(
		'sPromoCode' => $aUser['sPromoCode'],
	);
	$sJWT = sys_jwt_encode($aValue);

	$aData = array(
		'sHeadImage' => DEFAULTHEADIMG,
		'sQrUrl'	 => '',
		'sPromoUrl'	 => $aUrl['sRegister'].'&sJWT='.$sJWT,
		'sKindName'	 => '',
	);

	#宣告結束

	#程式邏輯區
	$sUrlEncode = urlencode($aUrl['sRegister'].'&sJWT='.$sJWT);
	$aData['sQrUrl'] = $aSystem['aWebsite']['sUrl2'].substr($aUrl['sQrUrl'],2).'&sUrl='.$sUrlEncode;

	// 我的頭
	$sSQL = '	SELECT	nId,
					nKid,
					sFile,
					sTable,
					nCreateTime
			FROM	'.	CLIENT_IMAGE_CTRL .'
			WHERE	nKid = :nKid
			AND 	sTable LIKE :sTable
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nKid', $aUser['nId'], PDO::PARAM_INT);
	$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows!== false)
	{
		$aData['sHeadImage'] =  IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
	}

	$sSQL = '	SELECT 	sName0
			FROM 	'.CLIENT_USER_KIND.'
			WHERE nLid = :nLid
			AND 	sLang LIKE :sLang
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $aUser['nKid'], PDO::PARAM_INT);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows!== false)
	{
		$aData['sKindName'] = $aRows['sName0'];
	}


	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
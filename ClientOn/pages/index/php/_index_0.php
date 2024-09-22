<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/index.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		// '0'	=> 'plugins/js/index/index.js',
	);
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sList'	=> sys_web_encode($aMenuToNo['pages/index/php/_list_0.php']),
		'sOnline'	=> sys_web_encode($aMenuToNo['pages/index/php/_online_0.php']),
		'sHtml'	=> 'pages/index/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/index_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'aKind' 	=> array(),
		'aUser' 	=> array(),
		'aLocation' => array(),
	);

	#宣告結束

	#程式邏輯區

	$sSQL = '	SELECT	nLid,
					sName0
			FROM	'.	CLIENT_USER_KIND .'
			WHERE		nOnline = 1
			AND		sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aKind'][$aRows['nLid']] = array(
			'sName0' => $aRows['sName0'],
			'nCount' => 0,
		);
	}

	$sSQL = '	SELECT 	User_.nId,
					User_.nKid,
					User_.nStatus
			FROM 	'.CLIENT_USER_DATA.' User_,
				'.CLIENT_USER_COOKIE.' Cookie_
			WHERE Cookie_.nUid != 0
			AND 	Cookie_.nUid = User_.nId';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aKind'][$aRows['nKid']]['nCount'] ++;
	}

	$sSQL = '	SELECT	nId,
					nLid,
					sName0
			FROM	'.	CLIENT_LOCATION .'
			WHERE		nOnline = 1
			AND		sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aLocation'][$aRows['nId']] = $aRows;
		$aData['aLocation'][$aRows['nId']]['sImgUrl'] = '';
	}

	// location background pic
	$sSQL = '	SELECT	nId,
					nKid,
					sFile,
					sTable,
					nType0,
					nCreateTime
			FROM	'.	CLIENT_IMAGE_CTRL .'
			WHERE	nKid IN ( '.implode(',', array_keys($aData['aLocation'])).' )
			AND 	sTable LIKE :sTable';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sTable', CLIENT_LOCATION, PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aLocation'][$aRows['nKid']]['sImgUrl'] = 'background-image:linear-gradient(to bottom, rgba(255,255,255,.5) 0%,rgba(255,255,255,.5) 100%),url('.IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'].');';
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
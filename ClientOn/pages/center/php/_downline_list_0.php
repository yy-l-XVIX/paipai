<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/downline_list.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();

	#參數接收區
	$sAccount = filter_input_str('sAccount',	INPUT_POST,'');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_downline_list_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/downline_list_0.php',
	);
	#url結束

	#參數宣告區
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aData = array();
	$aKindData = array();
	$aPage['aVar'] = array(
		'sAccount'	=> $sAccount,
	);
	$aBindArray = array();
	$sCondition = '';
	$nCount = 0;
	#宣告結束

	#程式邏輯區
	if ($sAccount != '')
	{
		$sCondition = ' AND User_.sAccount LIKE :sAccount ';
		$aBindArray['sAccount'] = '%'.$sAccount.'%';
	}

	// 方案
	$sSQL = '	SELECT 	nLid,
					sName0
			FROM 	'.CLIENT_USER_KIND.'
			WHERE nOnline = 1
			AND 	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang',$aSystem['sLang'],PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKindData[$aRows['nLid']] = $aRows;
	}

	$sSQL = '	SELECT	1
			FROM	'.CLIENT_USER_LINK.' Link_,
				'.CLIENT_USER_DATA.' User_
			WHERE	Link_.nPa = :nUid
			AND 	Link_.nEndTime = 0
			AND	User_.nStatus != 99
			AND 	Link_.nUid = User_.nId
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$nCount++;
	}
	$aPage['nDataAmount'] = $nCount;

	$sSQL = '	SELECT	User_.nId,
					User_.sAccount,
					User_.sName0,
					User_.sKid,
					User_.nExpired0,
					User_.nExpired1
			FROM	'.CLIENT_USER_LINK.' Link_,
				'.CLIENT_USER_DATA.' User_
			WHERE	Link_.nPa = :nUid
			AND 	Link_.nEndTime = 0
			AND	User_.nStatus != 99
			AND 	Link_.nUid = User_.nId
			'.$sCondition.sql_limit($nPageStart, $aPage['nPageSize']);;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['aKid'] = explode(',', $aRows['sKid']);
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
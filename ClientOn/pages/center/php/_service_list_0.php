<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/service_list.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0' => 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/center/service_list.js',
	);

	#參數接收區
	$sStartTime = filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$nKid 	= filter_input_int('nKid',		INPUT_REQUEST, 0);
	$sContent	= filter_input_str('sContent',	INPUT_REQUEST, '');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sService'	=> sys_web_encode($aMenuToNo['pages/center/php/_service_0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_service_list_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/service_list_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aKind = array();
	$aBindArray = array();

	$aPage['aVar'] = array(
		'nKid'	=> $nKid,
		'sContent'	=> $sContent,
		'sStartTime'=> $sStartTime,
		'sEndTime'	=> $sEndTime,
	);
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);

	#宣告結束

	#程式邏輯區
	if ($nKid != 0)
	{
		$sCondition .= ' AND nKid = :nKid';
		$aBindArray['nKid'] = $nKid;
	}
	if ($sContent != '')
	{
		$sCondition .= ' AND ( sQuestion LIKE :sContent OR sResponse LIKE :sContent )';
		$aBindArray['sContent'] = '%'.$sContent.'%';
	}

	$sSQL = '	SELECT	sName0,
					nLid
			FROM		'.CLIENT_SERVICE_KIND.'
			WHERE		sLang LIKE :sLang
			AND		nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKind[$aRows['nLid']] = $aRows;
		$aKind[$aRows['nLid']]['sSelect'] = '';
		if ($aRows['nLid'] == $nKid)
		{
			$aKind[$aRows['nLid']]['sSelect'] = 'selected';
		}
	}

	$sSQL = '	SELECT	1
			FROM		'.CLIENT_SERVICE.'
			WHERE		nUid = :nUid
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT	nId,
					nKid,
					nStatus,
					sQuestion,
					sResponse,
					nCreateTime
			FROM		'.CLIENT_SERVICE.'
			WHERE		nUid = :nUid
			'.$sCondition.'
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sCreateDate'] = date('Y-m-d',$aRows['nCreateTime']);
		$aData[$aRows['nId']]['sCreateTime'] = date('H:i:s',$aRows['nCreateTime']);
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
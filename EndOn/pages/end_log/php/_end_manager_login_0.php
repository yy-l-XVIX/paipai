<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_manager_login.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/end_log/end_manager_login.js',
	);
	#js結束

	#參數接收區
	$sAccount 	 = filter_input_str('sAccount',	INPUT_REQUEST, '');
	$sIp		 = filter_input_str('sIp',		INPUT_REQUEST, '');
	$sStartTime	 = filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime	 = filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_log/php/_end_manager_login_0.php']),
		'sHtml'	=> 'pages/end_log/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_manager_login_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBindArray = array();
	$aHideMember = array();
	$aStatus = array(
		'-1' => array(
			'sTitle' => aLOGIN['ALL'],
			'sSelect'=> '',
		),
		'0' => array(
			'sTitle' => aLOGIN['FAIL'],
			'sSelect'=> '',
		),
		'1' => array(
			'sTitle' => aLOGIN['LOGIN'],
			'sSelect'=> '',
		),
		'2' => array(
			'sTitle' => aLOGIN['LOGOUT'],
			'sSelect'=> '',
		),
	);
	$aPage['aVar'] = array(
		'sAccount'	=> $sAccount,
		'sIp'		=> $sIp,
		'sStartTime'=> $sStartTime,
		'sEndTime'	=> $sEndTime,
	);
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	#宣告結束

	#程式邏輯區
	# 篩選隱藏會員
	$sSQL = '	SELECT 	nId,
					sAccount
			FROM 	'.END_MANAGER_DATA.'
			WHERE nOnline != 99
			AND 	nType1 = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aHideMember[$aRows['nId']] = $aRows['sAccount'];
	}

	$sCondition = ' WHERE nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime ';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);
	if ($sAccount != '')
	{
		$sCondition .= ' AND sAccount LIKE :sAccount';
		$aBindArray['sAccount'] = '%'.$sAccount.'%';
	}
	if ($sIp != '')
	{
		$sCondition .= ' AND sIp LIKE :sIp';
		$aBindArray['sIp'] = '%'.$sIp.'%';
	}
	if (!empty($aHideMember) && $aAdm['nAdmType'] != 1)
	{
		$sCondition .= ' AND sAccount NOT IN ( \''.implode('\',\'',$aHideMember).'\' ) ';
	}

	$sSQL = '	SELECT 	1
			FROM 	'.END_MANAGER_LOGIN.'
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = 'SELECT 	nId,
				sAccount,
				nStatus,
				sIp,
				sDevice,
				sBrowser,
				sBrowserVersion,
				sCreateTime
		FROM 	'.END_MANAGER_LOGIN.'
		'.$sCondition.'
		ORDER BY nId DESC
		'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
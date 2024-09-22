<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_job.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/client_job/client_job.js',
	);
	#js結束

	#參數接收區
	$sStartTime = filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$nAid		= filter_input_int('nAid', 		INPUT_REQUEST,0);
	$nStatus	= filter_input_int('nStatus',		INPUT_REQUEST,-1);
	$sName0	= filter_input_str('sName0',		INPUT_REQUEST,'');
	$sSelDay	= filter_input_str('sSelDay',		INPUT_REQUEST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_0_upt0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_0.php']),
		'sJobMsg'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_msg_0.php']),
		'sHtml'	=> 'pages/client_job/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_job_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
	$aCity = array();
	$aArea = array(
		'0'	=> array(
			'sName0' => aJOB['SELECTAREA'],
			'sSelect'=> '',
		),
	);
	$nStartTime = strtotime($sStartTime);
	$nEndTime 	= strtotime($sEndTime);
	$aPage['aVar'] = array(
		'sStartTime'	=> $sStartTime,
		'sEndTime'		=> $sEndTime,
		'sName0'		=> $sName0,
		'nAid'		=> $nAid,
		'nStatus'		=> $nStatus,
		'sSelDay'		=> $sSelDay,
	);
	$aOnline = aONLINE;
	$aDay = aDAY;
	$aStatus = aJOB['aSTATUS'];
	$sSearch = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = ' AND Job_.nCreateTime >= :nStartTime AND Job_.nCreateTime <= :nEndTime';
	$aBind = array(
		'nStartTime'=> $nStartTime,
		'nEndTime' 	=> $nEndTime,
	);

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	foreach ($aPage['aVar'] as $LPsKey => $LPsValue)
	{
		$sBackParam .= '&'.$LPsKey.'='.$LPsValue;
	}
	$aValue = array(
		'sBackParam'=> $sBackParam,
	);
	$aUrl['sIns'] .= '&sJWT='.sys_jwt_encode($aValue);
	$aUrl['sJobMsg'] .= '&sJWT='.sys_jwt_encode($aValue);
	if ($sName0 != '')
	{
		$sCondition .= ' AND Job_.sName0 LIKE :sName0';
		$aBind['sName0'] = '%'.$sName0.'%';
	}
	if ($nStatus > -1 )
	{
		$sCondition .= ' AND Job_.nStatus = :nStatus';
		$aBind['nStatus'] = $nStatus;
		$aStatus[$nStatus]['sSelect'] = 'selected';
	}
	if ($nAid > 0)
	{
		$sCondition .= ' AND Job_.nAid = :nAid';
		$aBind['nAid'] = $nAid;
	}

	# 地區
	$sSQL = '	SELECT 	Area_.nId,
					Area_.sName0 as sArea,
					City_.nId as nCid,
					City_.sName0 as sCity
			FROM 	'.CLIENT_CITY.' City_,
				'.CLIENT_CITY_AREA.' Area_
			WHERE City_.nId = Area_.nCid
			AND 	City_.nOnline = 1
			AND 	Area_.nOnline = 1';

	if ($aAdm['nLid'] != 0) // 有設定管理地區
	{
		$sSQL .= ' AND City_.nLid = '.$aAdm['nLid'];
	}
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCity[$aRows['nCid']]['sName0'] = $aRows['sCity'];
		$aCity[$aRows['nCid']]['sSelect'] = '';
		$aArea[$aRows['nId']]['sName0'] = $aRows['sCity'].' '.$aRows['sArea'];
		$aArea[$aRows['nId']]['sSelect'] = '';

		if ($nAid == $aRows['nId'])
		{
			$aArea[$aRows['nId']]['sSelect'] = 'selected';
		}
	}

	$sSQL = '	SELECT 	Group_.nId
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_
			WHERE Group_.nOnline != 99
			AND 	Group_.nType1 = 1
			AND 	Job_.nAid IN ( '.implode(',', array_keys($aArea)).' )
			AND 	Job_.nGid = Group_.nId
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBind);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT 	Job_.nGid,
					Job_.sName0,
					Job_.sContent0,
					Job_.nStatus,
					Job_.sStartTime,
					Job_.sEndTime,
					Job_.nAid,
					Job_.sCreateTime,
					Job_.sUpdateTime,
					Group_.nUid
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_
			WHERE Group_.nOnline != 99
			AND 	Group_.nType1 = 1
			AND 	Job_.nAid IN ( '.implode(',', array_keys($aArea)).' )
			AND 	Job_.nGid = Group_.nId
			'.$sCondition.'
			ORDER BY Job_.nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nGid']] = $aRows;
		$aData[$aRows['nGid']]['sArea'] = $aArea[$aRows['nAid']]['sName0'];
		$aData[$aRows['nGid']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nGid'].$sSearch;
		$aData[$aRows['nGid']]['sChat'] = $aUrl['sJobMsg'].'&nId='.$aRows['nGid'].$sSearch;
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nGid'],
			't'		=> NOWTIME,
			'sBackParam'=> $sBackParam,
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nGid']]['sDel'] = $aUrl['sDel'].'&nId='.$aRows['nGid'].'&sJWT='.$sJWT;

		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	nId IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
		}
	}
	foreach ($aDay as $LPsText => $LPaDate)
	{
		$aDay[$LPsText]['sSelect'] = '';
		if ($sSelDay == $LPsText)
		{
			$aDay[$LPsText]['sSelect'] = 'active';
		}
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
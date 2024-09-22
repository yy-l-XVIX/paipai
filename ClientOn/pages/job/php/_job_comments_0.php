<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/job_comments.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/job/job_comments.js',
	);
	#js結束

	#參數接收區
	$nId 		= filter_input_int('nId',	INPUT_REQUEST, 0);
	$nFetch 	= filter_input_int('nFetch',	INPUT_REQUEST, 0);
	$nStatus 	= filter_input_int('nStatus',	INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']).'&nStatus=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/job/php/_job_comments_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sMyJob'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']),
		'sPostJob'	=> sys_web_encode($aMenuToNo['pages/job/php/_post_job_0.php']),
		'sHtml'	=> 'pages/job/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/job_comments_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aMemberData = array();
	$aSearchId = array();
	$sCondition = '';
	$sHeadImage = DEFAULTHEADIMG;
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$nErr = 0;
	$sErrMsg = '';
	/**
	 * 回傳陣列 JSON
	 * @var Int nStatus
	 * 	回傳狀態值
	 * 	1 => 正常 其餘待補
	 * @var String sMsg
	 * 	回傳訊息
	 * @var Array aData
	 * 	回傳陣列
	 * @var Int nAlertType
	 * 	回傳訊息提示類型
	 * 	0 => 不需提示框
	 * @var String sUrl
	 * 	回傳後導頁檔案
	 */
	$aReturn = array(
		'nStatus'		=> 0,
		'sMsg'		=> 'Error',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> '',
	);

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
	#宣告結束

	#程式邏輯區

	# 地區
	$sSQL = '	SELECT 	Area_.nId,
					Area_.sName0 as sArea,
					City_.sName0 as sCity
			FROM 	'.CLIENT_CITY.' City_,
				'.CLIENT_CITY_AREA.' Area_
			WHERE City_.nId = Area_.nCid
			AND 	City_.nOnline = 1
			AND 	Area_.nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aArea[$aRows['nId']] = $aRows['sCity'].' '.$aRows['sArea'];
	}

	// 工作資訊
	$sSQL = '	SELECT 	Group_.nId,
					Group_.nUid,
					Job_.sName0,
					Job_.sContent0,
					Job_.nStatus,
					Job_.sStartTime,
					Job_.sEndTime,
					Job_.nAid,
					Job_.sCreateTime
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_
			WHERE Group_.nOnline = 1
			AND 	Group_.nType1 = 1
			AND 	Group_.nUid = :nUid
			AND 	Group_.nId = :nId
			AND 	Job_.nStatus = 1
			AND 	Job_.nGid = Group_.nId
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sContent0'] = convertContent($aRows['sContent0']);
		$aData = $aRows;
		$aData['sArea'] = $aArea[$aRows['nAid']];
		$aData['sDetail'] = $aUrl['sMyJob'].'&nId='.$aRows['nId'];
		$aData['sPostAgain'] = $aUrl['sPostJob'].'&nId='.$aRows['nId'];
		$aData['sImgUrl'] = '';
		$aData['aScore'] = array();

		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
	}
	if (empty($aData))
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	// 已評分紀錄
	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_JOB_SCORE.'
			WHERE nGid = :nGid
			AND 	nStatus = 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	nId,
					nUid,
					nGid,
					sContent0,
					nScore,
					sCreateTime
			FROM 	'.CLIENT_JOB_SCORE.'
			WHERE nGid = :nGid
			AND 	nStatus = 1
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aScore'][$aRows['nId']] = $aRows;
		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
	}

	$sSQL = '	SELECT 	nId,
					nKid,
					sFile,
					sTable,
					nCreateTime
			FROM 	'.CLIENT_IMAGE_CTRL.'
			WHERE sTable LIKE :sTable
			AND 	nKid = :nGid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sTable', CLIENT_JOB, PDO::PARAM_STR);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
	}


	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	nId IN ( '.implode(',', $aSearchId).' )
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
			$aMemberData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
		}

		// 頭
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN ( '.implode(',', $aSearchId).' )
				AND 	sTable LIKE :sTable ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			if ($aRows['nKid'] == $aUser['nId'])
			{
				$aData['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}
			$aMemberData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
	}
	else
	{
		if ($nFetch == 1)
		{
			foreach ($aData['aScore'] as $LPnId => $LPaScore)
			{
				$LPaScore['sScore'] = $LPaScore['nScore'];
				$LPaScore['sName0'] = $aMemberData[$LPaScore['nUid']]['sName0'];
				$LPaScore['sHeadImage'] = $aMemberData[$LPaScore['nUid']]['sHeadImage'];
				$aReturn['aData']['aData'][] = $LPaScore;
			}

			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = 'success'.sizeof($aData);
			$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

			echo json_encode($aReturn);
			exit;
		}
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
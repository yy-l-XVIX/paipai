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
		'0'	=> 'plugins/js/ckeditor/ckeditor.js',
		'1'	=> 'plugins/js/js_date/laydate.js',
		'2'	=> 'plugins/js/client_job/client_job.js',
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId', INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_0.php']).$aJWT['sBackParam'],
		'sHtml'	=> 'pages/client_job/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_job_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'nId'		=> 0,
		'nUid'	=> 0,
		'sName0'	=> '',
		'sContent0'	=> '',
		'nStatus'	=> '',
		'sStartTime'=> date('Y-m-d 00:00:00'),
		'sEndTime'	=> date('Y-m-d 23:59:59'),
		'nAid'	=> '',
		'nEmploye'	=> 0,
		'sEmploye'	=> '',
		'aEmploye'	=> array(),
		'sAccount'	=> '',
		'sImgUrl'	=> '',
	);

	$aMemberData = array();
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		'sBackParam'=> $aJWT['sBackParam'],
		'nExp'	=> NOWTIME+JWTWAIT,
	);
	$sJWT = sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';
	$aOnline = aONLINE;
	$aStatus = aJOB['aSTATUS'];
	unset($aStatus['-1']);
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
		$aArea[$aRows['nId']]['sName0'] = $aRows['sCity'].' '.$aRows['sArea'];
		$aArea[$aRows['nId']]['sSelect'] = '';
	}

	$sSQL = '	SELECT 	Job_.nGid,
					Job_.sName0,
					Job_.sContent0,
					Job_.nStatus,
					Job_.sStartTime,
					Job_.sEndTime,
					Job_.nAid,
					Job_.nEmploye,
					Job_.sEmploye,
					Job_.sCreateTime,
					Job_.sUpdateTime,
					Group_.nUid
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_
			WHERE Group_.nOnline != 99
			AND 	Group_.nId = :nId
			AND 	Group_.nId = Job_.nGid
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sContent0'] = convertContent($aRows['sContent0']);
		$aData = $aRows;
		$aData['aEmploye'] = array();
		if ($aRows['sEmploye'] != '')
		{
			$aData['aEmploye'] = explode(',', $aRows['sEmploye']);
		}
		$aData['sImgUrl'] = '';
		$aData['sAccount'] = '';

		$aStatus[$aData['nStatus']]['sSelect'] = 'selected';
		$aArea[$aData['nAid']]['sSelect'] = 'selected';
		$aMemberData[$aRows['nUid']]['nUid'] = $aRows['nUid'];
	}
	foreach ($aData['aEmploye'] as  $LPsUid)
	{
		$LPnUid = (int) $LPsUid;
		$aMemberData[$LPnUid]['nUid'] = $LPnUid;
	}

	// 工作群組成員
	$sSQL = '	SELECT 	nId,
					nUid
			FROM 	'.CLIENT_USER_GROUP_LIST.'
			WHERE nGid = :nGid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aMemberData[$aRows['nUid']] = $aRows;
	}

	$sSQL = '	SELECT 	nId,
					nKid,
					sFile,
					sTable,
					nCreateTime
			FROM 	'.CLIENT_IMAGE_CTRL.'
			WHERE sTable LIKE :sTable
			AND 	nKid = :nKid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sTable', CLIENT_JOB, PDO::PARAM_STR);
	$Result->bindValue(':nKid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
	}

	if (!empty($aMemberData))
	{
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', array_keys($aMemberData)).' ) ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aData['nUid'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']]['sAccount'] = $aRows['sAccount'];
			$aMemberData[$aRows['nId']]['sJoin'] = '';
			if ($aData['nUid'] == $aRows['nId'])
			{
				$aData['sAccount'] = $aRows['sAccount'];
				unset($aMemberData[$aRows['nId']]);
			}
			if (in_array(str_pad($aRows['nId'],9,0,STR_PAD_LEFT), $aData['aEmploye']))
			{
				$aMemberData[$aRows['nId']]['sJoin'] = aJOB['EMPLOYE'];
			}
		}
	}


	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	else
	{
		$aJumpMsg['0']['sClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
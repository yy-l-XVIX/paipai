<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/my_post_job.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/job/my_post_job.js',
	);
	#js結束

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',	INPUT_REQUEST, 0);
	$nStatus 	= filter_input_int('nStatus',	INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sMyJob'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']),
		'sPostJob'	=> sys_web_encode($aMenuToNo['pages/job/php/_post_job_0.php']),
		'sJobComments'	=> sys_web_encode($aMenuToNo['pages/job/php/_job_comments_0.php']),
		'sHtml'	=> 'pages/job/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/my_post_job_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aType = array();
	$aBindArray = array();
	$aMemberData = array();
	$aStatus = aSTATUS;
	$aSearchId = array(
		'aJid' => array(),
		'aUid' => array(),
	);
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

	#宣告結束

	#程式邏輯區
	if ($sUserCurrentRole != 'boss')
	{
		$nErr = 1;
		$sErrMsg = PARAMSERR;
		$aUrl['sBack'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
	}
	elseif ($aUser['nStatus'] == '11')
	{
		$nErr = 1;
		$sErrMsg = ACCOUNTPENDING;
		$aUrl['sBack'] = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
	}

	$sSQL = '	SELECT	sBirthday,
					sIdNumber
			FROM 	'.CLIENT_USER_DETAIL.'
			WHERE nUid = :nUid
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows['sIdNumber'] == '' || $aRows['sBirthday'] == '')
	{
		$nErr = 1;
		$sErrMsg = aMYPOSTJOB['SETTING'];
		$aUrl['sBack'] = sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']);
	}

	if ($nStatus >= 0)
	{
		$sCondition .= ' AND Job_.nStatus = :nStatus';
		$aBindArray['nStatus'] = $nStatus;
		$aStatus[$nStatus]['sSelect'] = 'active';
	}
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

	# 工作類型
	$sSQL = '	SELECT 	nId,
					sName0
			FROM 	'.CLIENT_JOB_TYPE.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aType[$aRows['nId']]['sName0'] = $aRows['sName0'];
		$aType[$aRows['nId']]['sSelect'] = '';
	}

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
		$sHeadImage = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
	}

	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_
			WHERE Group_.nUid = :nUid
			AND 	Group_.nOnline = 1
			AND 	Group_.nId = Job_.nGid
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	Group_.nId,
					Job_.sName0,
					Job_.sContent0,
					Job_.nStatus,
					Job_.sStartTime,
					Job_.sEndTime,
					Job_.nAid,
					Job_.sType0,
					Job_.sCreateTime
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_
			WHERE Group_.nUid = :nUid
			AND 	Group_.nOnline = 1
			AND 	Group_.nId = Job_.nGid
			'.$sCondition.'
			ORDER BY Group_.nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sContent0'] = convertContent($aRows['sContent0']);

		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sHeadImage'] = $sHeadImage;
		$aData[$aRows['nId']]['sArea'] = $aArea[$aRows['nAid']];
		$aData[$aRows['nId']]['sDetail'] = $aUrl['sMyJob'].'&nId='.$aRows['nId'];
		$aData[$aRows['nId']]['sPostAgain'] = $aUrl['sPostJob'].'&nId='.$aRows['nId'];
		$aData[$aRows['nId']]['sImgUrl'] = '';
		$aData[$aRows['nId']]['aType0'] = array();
		if ($aRows['sType0'] != '')
		{
			$aData[$aRows['nId']]['aType0'] = explode(',', $aRows['sType0']);
		}

		$aSearchId['aJid'][$aRows['nId']] = $aRows['nId'];
	}

	if (!empty($aSearchId['aJid']))
	{
		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', $aSearchId['aJid']).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_JOB, PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nKid']]['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	if (!empty($aSearchId['aUid']))
	{
		$sSQL = '	SELECT 	nId,
						sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	nId IN ( '.implode(',', $aSearchId['aUid']).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
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
		$aRequire['Require'] = $aUrl['sHtml'];
		$aJumpMsg['0']['sMsg'] = '123';
		$aJumpMsg['0']['nClicktoClose'] = 1;
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

		if ($nFetch == 1)
		{
			foreach ($aData as $LPnId => $LPaJob)
			{
				$LPsType0 = '';
				foreach ($LPaJob['aType0'] as $LPsType0)
				{
					$LPnType0 = (int)$LPsType0;
					if (!isset($aType[$LPnType0]))
					{
						continue;
					}
					$LPsType0 .= '<span>'.$aType[$LPnType0]['sName0'].'</span>';
				}
				$LPaJob['sTypeHtml'] = $LPsType0;

				$aReturn['aData']['aData'][] = $LPaJob;
			}

			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = 'success'.sizeof($aData);
			$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

			echo json_encode($aReturn);
			exit;
		}
	}
	#輸出結束
?>
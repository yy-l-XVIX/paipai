<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/job_record.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/job_record.js',
		'1'	=> 'plugins/js/SnoozeKeywords.js',
	);

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',	INPUT_REQUEST,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sFetch'	=> sys_web_encode($aMenuToNo['pages/center/php/_job_record_0.php']).'&run_page=1&nFetch=1',
		'sSaveAct'	=> sys_web_encode($aMenuToNo['pages/index/php/_list_0_act0.php']).'&run_page=1',
		'sAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_job_record_0_act0.php']).'&run_page=1',
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sMyjob'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/job_record_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aArea = array();
	$aType = array();
	$aGroupData = array();
	$aSearchId = array(
		'aUid'=> array(),
		'aJid'=> array(),
	);
	$aValue = array(
		'a'	=> 'INS',
	);
	$sActJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'DEL',
	);
	$sDelJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'sBackUrl'	=> sys_web_encode($aMenuToNo['pages/center/php/_job_record_0.php']),
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
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
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';
	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';

	$aJumpMsg['dataprocessing'] = array(
		'sBoxClass'	=>	'',
		'sShow'	=>	0,	# 是否直接顯示彈窗 0=>隱藏 , 1=>顯示
		'sTitle'	=>	'',	# 標題
		'sIcon'	=>	'',	# 成功=>success,失敗=>error
		'sMsg'	=>	DATAPROCESSING,# 資料處理中
		'sArticle'	=>	'',	# 較長文字
		'aButton'	=>	array(),
		'nClicktoClose'=>	0,	# 是否點擊任意一處即可關閉 0=>否 , 1=>是
	);
	#宣告結束

	#程式邏輯區
	# job place
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
		$aArea[$aRows['nId']] = $aRows;
		$aArea[$aRows['nId']]['sText'] = $aRows['sCity'].' '.$aRows['sArea'];
	}

	# job type
	$sSQL = '	SELECT 	nId,
					sName0
			FROM 	'.CLIENT_JOB_TYPE.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aType[$aRows['nId']] = $aRows;
	}

	// job score
	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_JOB_SCORE.' Score_,
				'.CLIENT_GROUP_CTRL.' Group_
			WHERE Score_.nUid = :nUid
			AND 	Group_.nOnline = 1
			AND 	Group_.nType1 = 1
			AND 	Score_.nGid = Group_.nId';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	Score_.nId,
					Score_.nGid,
					Score_.nScore,
					Score_.nStatus as nIsScored,
					Score_.sContent0 as sScoreContent0,
					Score_.sUpdateTime as sScoreTime,
					Group_.nUid
			FROM 	'.CLIENT_JOB_SCORE.' Score_,
				'.CLIENT_GROUP_CTRL.' Group_
			WHERE Score_.nUid = :nUid
			AND 	Group_.nOnline = 1
			AND 	Group_.nType1 = 1
			AND 	Score_.nGid = Group_.nId
			ORDER BY Score_.nStatus ASC , Group_.nId Desc
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nGid']] = $aRows;
		$aSearchId['aUid'][$aRows['nUid']] = $aRows['nUid'];
		$aSearchId['aGid'][$aRows['nGid']] = $aRows['nGid'];
	}

	if (!empty($aSearchId['aGid']))
	{
		// job data
		$sSQL = '	SELECT	nGid,
						nAid,
						sType0,
						sName0,
						nStatus,
						sContent0,
						sStartTime,
						sEndTime,
						sCreateTime
				FROM	'.CLIENT_JOB.'
				WHERE	nGid IN ( '.implode(',', array_keys($aData)) .' )
				ORDER BY nId DESC';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aRows['sContent0'] = convertContent($aRows['sContent0']);
			foreach ($aRows as $LPsKey => $LPsValue)
			{
				$aData[$aRows['nGid']][$LPsKey] = $LPsValue;
			}
			// $aData[$aRows['nGid']] = $aRows;
			// $aData[$aRows['nGid']]['nScore'] = $aGroupData[$aRows['nGid']]['nScore'];
			// $aData[$aRows['nGid']]['nIsScored'] = $aGroupData[$aRows['nGid']]['nIsScored'];
			// $aData[$aRows['nGid']]['sScoreContent0'] = $aGroupData[$aRows['nGid']]['sScoreContent0'];
			// $aData[$aRows['nGid']]['sScoreTime'] = $aGroupData[$aRows['nGid']]['sScoreTime'];
			// $aData[$aRows['nGid']]['nUid'] = $aGroupData[$aRows['nGid']]['nUid'];
			// $aData[$aRows['nGid']]['sScore'] = $aGroupData[$aRows['nGid']]['nScore'];

			$aData[$aRows['nGid']]['sScore'] = $aData[$aRows['nGid']]['nScore'];
			$aData[$aRows['nGid']]['aType0'] = array();
			$aData[$aRows['nGid']]['sArea'] = '';
			$aData[$aRows['nGid']]['sImgUrl'] = '';
			$aData[$aRows['nGid']]['nFavorite'] = 0;
			$aValue = array(
				'a'	=> 'SCORED'.$aRows['nGid'],
			);
			$LPsJWT = sys_jwt_encode($aValue);
			$aData[$aRows['nGid']]['sScoreUrl'] = $aUrl['sAct'].'&sJWT='.$LPsJWT.'&nJid='.$aRows['nGid'];

			if ($aRows['sType0'] != '')
			{
				$aData[$aRows['nGid']]['aType0'] = explode(',', $aRows['sType0']);
			}
		}
		// job pic
		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', $aSearchId['aGid']).' ) ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_JOB, PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nKid']]['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
		// my save
		$sSQL = '	SELECT	nGid
				FROM		'.CLIENT_USER_JOB_FAVORITE.'
				WHERE		nUid = :nUid
				AND 	nGid IN ( '.implode(',', $aSearchId['aGid']).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nGid']]['nFavorite'] = 1;
		}
	}

	if (!empty($aSearchId['aUid']))
	{
		$sSQL = '	SELECT 	nId,
						nKid,
						sName0
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aSearchId['aUid']).' ) ';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
			$aMemberData[$aRows['nId']]['sInfUrl'] = $aUrl['sInf'].'&nId='.$aRows['nId'];#'javascript:void(0)';
			$aMemberData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			$aMemberData[$aRows['nId']]['sRole'] = 'staff';
			$aMemberData[$aRows['nId']]['nJoin'] = 0;
			if ($aRows['nKid'] == 1)
			{
				$aMemberData[$aRows['nId']]['sRole'] = 'boss';
			}
		}

		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', array_keys($aMemberData)).' ) ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	if ($nFetch == 1)
	{
		foreach ($aData as $LPnId => $LPaData)
		{
			$LPaData['sFavoriteImage'] = '<img src="images/like.png" alt="">';
			$LPsType0 = '';
			foreach ($LPaData['aType0'] as $LPsType)
			{
				$LPnType0 = (int)$LPsType;
				if (!isset($aType[$LPnType0]))
				{
					continue;
				}
				$LPsType0 .= '<span>'.$aType[$LPnType0]['sName0'].'</span> ';
			}

			$LPaData['sTypeHtml'] = $LPsType0;
			$LPaData['sHeadImage'] = $aMemberData[$LPaData['nUid']]['sHeadImage'];
			$LPaData['sInfUrl'] = $aMemberData[$LPaData['nUid']]['sInfUrl'];
			if($LPaData['nFavorite'] == 1 )
			{
				#已收藏工作時呈現
				$LPaData['sFavoriteImage'] = '<img src="images/likeActive.png" alt="">';
			}
			if($LPaData['nIsScored'] == 0)
			{
				$LPaData['sScoreHtml'] = '	<div class="JobListFeedbackContent">
										<input type="hidden" name="sContent0" value="">
										<div class="EmojiContentInput JqChat JqContent0" contenteditable="true"></div>
									</div>
									<div class="JobListFeedbackBtnBox">
										<div class="BtnAct JqSubmit" data-jid="'.$LPaData['nJid'].'">'.aRECORD['FINISH'].'</div>
									</div>';
			}
			else
			{
				$LPaData['sScoreHtml'] = '<div class="JobListFeedbackContent active">
									<div class="JobListFeedbackContentTxt">'.$LPaData['sScoreContent0'].'</div>
								</div>';
			}

			$aReturn['aData']['aData'][] = $LPaData;
		}
		$aReturn['nStatus'] = 1;
		$aReturn['sMsg'] = 'success';
		$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
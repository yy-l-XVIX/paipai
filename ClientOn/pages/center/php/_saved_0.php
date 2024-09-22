<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/saved.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/saved.js',
	);

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',		INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/index/php/_list_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_saved_0.php']),
		'sMyjob'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/saved_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aArea = array();
	$aType = array();
	$aGroupData = array();
	$aHeadImage = array();
	$aSearchId = array();
	$aBlockUid = myBlockUid($aUser['nId']);
	$aValue = array(
		'a'	=> 'INS',
	);
	$sActJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'DEL',
	);
	$sDelJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'JOIN',
	);
	$sJoinJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'OUT',
	);
	$sOutJWT = sys_jwt_encode($aValue);

	$aValue = array(
		'sBackUrl'	=> $aUrl['sPage'],
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);
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
	$sJids = '0';
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = '';

	// 上班提醒
	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['sMsg'] = WORKNOTICE;
	$aJumpMsg['1']['aButton']['0']['sClass'] = 'JqClose JqJoin';
	$aJumpMsg['1']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['1']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['1']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	# 地區
	$sSQL = '	SELECT 	Area_.nId,
					Area_.sName0 as sArea,
					City_.sName0 as sCity,
					City_.nLid
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
	# 工作類型
	$sSQL = '	SELECT 	nId,
					sName0
			FROM 	'.CLIENT_JOB_TYPE.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aType[$aRows['nId']]['sName0'] = $aRows;
	}

	$sSQL = '	SELECT	1
			FROM	'.CLIENT_USER_JOB_FAVORITE.' Favorite_,
				'.CLIENT_GROUP_CTRL.' Group_
			WHERE	Favorite_.nUid = :nUid
			AND 	Group_.nOnline = 1
			AND 	Group_.nType1 = 1
			AND 	Group_.nId = Favorite_.nGid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT	Favorite_.nGid,
					Group_.nUid
			FROM	'.CLIENT_USER_JOB_FAVORITE.' Favorite_,
				'.CLIENT_GROUP_CTRL.' Group_
			WHERE	Favorite_.nUid = :nUid
			AND 	Group_.nOnline = 1
			AND 	Group_.nType1 = 1
			AND 	Group_.nId = Favorite_.nGid
			ORDER BY Favorite_.nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aGroupData[$aRows['nGid']] = $aRows;
		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
	}

	if (!empty($aGroupData))
	{
		$sSQL = '	SELECT	nId,
						nGid,
						sName0,
						sContent0,
						nStatus,
						sStartTime,
						sEndTime,
						nAid,
						sType0,
						sCreateTime
				FROM	'.CLIENT_JOB.'
				WHERE	nGid IN ( '.implode(',', array_keys($aGroupData)) .' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aRows['nUid'] = $aGroupData[$aRows['nGid']]['nUid'];
			if (isset($aBlockUid[$aRows['nUid']]))
			{
				continue;
			}
			$aRows['sContent0'] = convertContent($aRows['sContent0']);
			$aData[$aRows['nGid']] = $aRows;
			$aData[$aRows['nGid']]['sUserInfoUrl'] =  $aUrl['sInf'].'&nId='.$aRows['nUid'];#'javascript:void(0)';
			$aData[$aRows['nGid']]['sDetailUrl'] = 'javascript:void(0)';
			$aData[$aRows['nGid']]['sArea'] = $aArea[$aRows['nAid']]['sText'];
			$aData[$aRows['nGid']]['nFavorite'] = 1;
			$aData[$aRows['nGid']]['nJoin'] = 0;

			# 刊登雇主 & 當地有效人才 才可以進入工作詳情
			if ( $sUserCurrentRole == 'boss' && $aUser['nId'] == $aRows['nUid'] )
			{
				$aData[$aRows['nGid']]['sDetailUrl'] = $aUrl['sMyjob'].'&nId='.$aRows['nGid'];
			}
			if ( $sUserCurrentRole == 'staff' && $aUser['nLid'] == $aArea[$aRows['nAid']]['nLid'] && $aUser['sExpired0'] > NOWDATE )
			{
				$aData[$aRows['nGid']]['sDetailUrl'] = $aUrl['sMyjob'].'&nId='.$aRows['nGid'];
			}
		}
		if (!empty($aData))
		{
			#應徵的工作
			$sSQL = '	SELECT 	nGid
					FROM 	'.CLIENT_USER_GROUP_LIST.'
					WHERE nUid = :nUid
					AND 	nGid IN ( '.implode(',', array_keys($aData)).' )';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aData[$aRows['nGid']]['nJoin'] = 1;
			}
		}
	}

	if (!empty($aSearchId))
	{
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
			$aHeadImage[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	if ($nFetch == 1)
	{
		foreach ($aData as $LPnId => $LPaJobData)
		{
			$LPaJobData['sHeadImage'] = (isset($aHeadImage[$LPaJobData['nUid']]['sHeadImage']))?$aHeadImage[$LPaJobData['nUid']]['sHeadImage']:DEFAULTHEADIMG;
			$LPaJobData['sActBtn'] = '';
			$LPaJobData['sFavoriteImage'] = '<img src="images/like.png" alt="">';

			if($LPaJobData['nStatus'] == 1)
			{
				#已結案時呈現
				$LPaJobData['sActBtn'] = '<div class="JobListInfBtn active">'.aSAVED['CLOSED'].'</div>';
			}
			else
			{
				if($sUserCurrentRole == 'staff')
				{
					#人才時呈現
					if($LPaJobData['nJoin'] == 1)
					{
						#已應徵工作時呈現
						$LPaJobData['sActBtn'] = '<div class="JobListInfBtn active JqOut" data-jid="'.$LPnId.'">'.aSAVED['OUT'].'</div>';
					}
					else
					{
						if ($aUser['nStatus'] == 1)
						{
							#尚未應徵工作時呈現
							$LPaJobData['sActBtn'] = '<div class="JobListInfBtn JqListStupidOut" data-showctrl="1" data-jid="'.$LPnId.'">'.aSAVED['JOIN'].'</div>';
						}
						else
						{
							#尚未應徵工作時呈現
							$LPaJobData['sActBtn'] = '<div class="JobListInfBtn JqJoin" data-jid="'.$LPnId.'">'.aSAVED['JOIN'].'</div>';
						}
					}
				}
				else
				{
					# 未結案時呈現
					$LPaJobData['sActBtn'] = '<a class="JobListInfBtn detail" href="'.$LPaJobData['sDetailUrl'].'">'.aSAVED['DETAIL'].'</a>';
				}
			}
			if($LPaJobData['nFavorite'] == 1 && $sUserCurrentRole == 'staff')
			{
				#已收藏工作時呈現
				$LPaJobData['sFavoriteImage'] = '<img src="images/likeActive.png" alt="">';
			}
			$aReturn['aData']['aData'][] = $LPaJobData;
		}


		$aReturn['nStatus'] = 1;
		$aReturn['sMsg'] = 'success'.sizeof($aData);
		$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
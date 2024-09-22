<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/my_job.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/photon/Photon-Javascript_SDK.js',
		'1'	=> 'plugins/js/photon/Photon_Interface.js',
		'2'	=> 'plugins/js/BaseCmdLogic.js',
		'3'	=> 'plugins/js/Socket.js',
		'4'	=> 'plugins/js/job/my_job.js',
		'5'	=> 'plugins/js/EmojiInsert.js',
		'6'	=> 'plugins/js/SnoozeKeywords.js',
		'7'	=> 'plugins/js/FileWithDelete.js',
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId',		INPUT_GET,0);
	$nFirstJoin	= filter_input_int('nFirstJoin',	INPUT_GET,0); // 我要應徵導頁進來會帶此參數
	$nFetch	= filter_input_int('nFetch',		INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']).'&nId='.$nId,
		'sAct'	=> sys_web_encode($aMenuToNo['pages/index/php/_list_0_act0.php']).'&run_page=1',
		'sPageAct'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_job_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/job/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/my_job_0.php',
	);
	#url結束

	$aData = array(); 	// 放聊天訊息
	$aJobData = array();	// 工作資訊
	$aMemberData = array(); // 會員資訊
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
		'a'	=> 'OUT',
	);
	$sOutJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'CLOSEJOB',
	);
	$sCloseJobJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'KICKOUT',
		'nGid'=> $nId,
	);
	$sKickOutJWT = sys_jwt_encode($aValue);
	$LPaValue = array(
		'a'	=> 'ACCEPT'.$aUser['nId'],
	);
	$sAcceptJob = sys_jwt_encode($LPaValue);
	$LPaValue = array(
		'a'	=> 'REJECT'.$aUser['nId'],
	);
	$sRejectJob = sys_jwt_encode($LPaValue);
	$aValue = array(
		'a'=> 'UPLOADFILE',
	);
	$sImgJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'sBackUrl'=> $aUrl['sPage'],
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

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$nLatestId = 0;
	$sType0 = '';
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 0;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';
	$aJumpMsg['1']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']).'&nId='.$nId;

	// 防呆
	$aJumpMsg['2']= $aJumpMsg['0'];
	$aJumpMsg['2']['sMsg'] = aJOB['SUREDELETE'];
	$aJumpMsg['2']['nClicktoClose'] = 0;
	$aJumpMsg['2']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['2']['aButton']['0']['sClass'] = 'JqKickOut JqReplaceO';
	$aJumpMsg['2']['aButton']['0']['sUrl'] = 'javascript:void(0);';
	$aJumpMsg['2']['aButton']['1']['sText'] = CANCEL;
	$aJumpMsg['2']['aButton']['1']['sClass'] = 'JqClose';

	// 防呆
	$aJumpMsg['3']= $aJumpMsg['2'];
	$aJumpMsg['3']['sMsg'] = aJOB['SURECLOSE'];
	$aJumpMsg['3']['nClicktoClose'] = 0;
	$aJumpMsg['3']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['3']['aButton']['0']['sClass'] = 'JqCloseJob JqReplaceO';

	$aJumpMsg['kindremind'] = $aJumpMsg['0'];
	$aJumpMsg['kindremind']['sTitle'] = aJOB['KINDREMIND'];
	$aJumpMsg['kindremind']['sMsg'] = KINDREMIND;
	$aJumpMsg['kindremind']['nClicktoClose'] = 0;
	$aJumpMsg['kindremind']['aButton']['0']['sClass'] = 'JqInviteO';
	$aJumpMsg['kindremind']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['kindremind']['aButton']['0']['sUrl'] = 'javascript:void(0)';
	$aJumpMsg['kindremind']['aButton']['1']['sClass'] = 'JqClose';
	$aJumpMsg['kindremind']['aButton']['1']['sText'] = CANCEL;

	#程式邏輯區
	if ($sUserCurrentRole == 'staff') // 人才返回 我的工作
	{
		$aUrl['sBack'] = sys_web_encode($aMenuToNo['pages/job/php/_my_job_list_0.php']);
	}

	// 工作資訊
	$sSQL = '	SELECT 	Group_.nId,
					Group_.nUid,
					Job_.sName0,
					Job_.sContent0,
					Job_.nEmploye,
					Job_.sEmploye,
					Job_.sType0,
					Job_.nStatus,
					Job_.sStartTime,
					Job_.sEndTime,
					Job_.nAid,
					Job_.sCreateTime
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_
			WHERE Group_.nOnline = 1
			AND 	Group_.nType1 = 1
			AND 	Group_.nId = :nId
			AND 	Job_.nStatus < 10
			AND 	Job_.nGid = Group_.nId
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aJobData = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aJobData === false)
	{
		$nErr = 1;
		$sErrMsg = aJOB['NOJOBDATA'];
	}
	else if (isset($aBlockUid[$aJobData['nUid']]) && $aJobData['nStatus'] == 0) // 未結案看不到
	{
		$nErr = 1;
		$sErrMsg = aJOB['NOJOBDATA'];
	}
	else
	{
		$aJobData['sContent0'] = convertContent($aJobData['sContent0']);
		$aJobData['aEmploye'] = ($aJobData['sEmploye']!='')?explode(',', $aJobData['sEmploye']):array();
		$aJobData['aType0'] = array();
		$aJobData['nFavorite'] = 0;
		$aJobData['sImgUrl'] = '';

		// 工作地點
		$sSQL = '	SELECT 	Area_.nId,
						Area_.sName0 as sArea,
						City_.sName0 as sCity
				FROM 	'.CLIENT_CITY.' City_,
					'.CLIENT_CITY_AREA.' Area_
				WHERE City_.nId = Area_.nCid
				AND 	City_.nOnline = 1
				AND 	Area_.nOnline = 1
				AND 	Area_.nId = :nId';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aJobData['nAid'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aJobData['sArea'] = $aRows['sCity'].' '.$aRows['sArea'];
		}
		// 工作類型
		if ($aJobData['sType0'] != '')
		{
			$aType0 = explode(',', $aJobData['sType0']);
			foreach ($aType0 as $LPsType)
			{
				$sType0 .= (int)$LPsType.',';
			}
			$sType0 = trim($sType0,',');

			$sSQL = '	SELECT 	nId,
							sName0
					FROM 	'.CLIENT_JOB_TYPE.'
					WHERE nOnline = 1
					AND 	nId IN ('.$sType0.')';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aJobData['aType0'][$aRows['nId']] = $aRows;
			}
		}
		// 收藏
		$sSQL = '	SELECT 	nGid
				FROM 	'.CLIENT_USER_JOB_FAVORITE.'
				WHERE nUid = :nUid
				AND 	nGid = :nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows !== false)
		{
			$aJobData['nFavorite'] = 1;
		}
		// 工作圖片
		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid = :nKid ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_JOB, PDO::PARAM_STR);
		$Result->bindValue(':nKid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aJobData['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
		// 群組內的人
		$sSQL = '	SELECT 	nId,
						nUid,
						nStatus,
						nCreateTime
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid
				AND 	nStatus = 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nUid']] = $aRows;
			$aMemberData[$aRows['nUid']]['nGroupStatus'] = $aRows['nStatus'];
			$aMemberData[$aRows['nUid']]['nInGroup'] = 1;
		}
		//我不再這個群就離開
		if (!isset($aMemberData[$aUser['nId']]['nInGroup']))
		{
			$nErr = 1;
			$sErrMsg = aJOB['NOJOBDATA'];
		}
		else
		{
			// 聊天記錄
			// 只看得到傳給自己或傳給大家的訊息 nTarget = 0, nUid
			// 加入之前的訊息不給看 nCreateTime <= client_group_list.nCreatTime
			$sSQL = '	SELECT 	1
					FROM 	'.CLIENT_GROUP_MSG.'
					WHERE nGid = :nGid
					AND 	nOnline = 1
					AND 	nCreateTime >= :nCreateTime
					AND 	nTargetUid IN (0,'.$aUser['nId'].')';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid',		$nId, PDO::PARAM_INT);
			$Result->bindValue(':nCreateTime',	$aMemberData[$aUser['nId']]['nCreateTime'], PDO::PARAM_INT);
			sql_query($Result);
			$aPage['nDataAmount'] = $Result->rowCount();
			$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
			if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
			{
				$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
			}

			$sSQL = '	SELECT 	nId,
							nUid,
							nTargetUid,
							nStatus0,
							sMsg,
							sCreateTime
					FROM 	'.CLIENT_GROUP_MSG.'
					WHERE nGid = :nGid
					AND 	nOnline = 1
					AND 	nCreateTime >= :nCreateTime
					AND 	nTargetUid IN (0,'.$aUser['nId'].')
					ORDER BY nCreateTime DESC
					'.sql_limit($nPageStart, $aPage['nPageSize']);
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid',		$nId, PDO::PARAM_INT);
			$Result->bindValue(':nCreateTime',	$aMemberData[$aUser['nId']]['nCreateTime'], PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aRows['sMsg'] = convertContent($aRows['sMsg']);
				if ($aRows['sMsg'] == '[:invite job:]')
				{
					if ($aRows['nStatus0'] == '0') // 尚未回覆
					{
						$aRows['sMsg'] = '<div class="serviceListInviteQ">'.aJOB['ASKWORK'].'</div>';
						$aRows['sMsg'] .= '<div class="serviceListInviteBtnBox">
						<div class="serviceListInviteBtn JqInviteBtn JqMyjobBtnAccept" data-href="javascript:void(0)" data-jwt="'.$sAcceptJob.'">'.aJOB['YES'].'</div>
						<div class="serviceListInviteBtn JqInviteBtn JqMyjobBtnDeny" data-href="javascript:void(0)" data-jwt="'.$sRejectJob.'">'.aJOB['NO'].'</div>
						</div>';
					}
					if ($aRows['nStatus0'] == '1')
					{
						$aRows['sMsg'] = '<div class="serviceListInviteQ">'.aJOB['ASKWORK'].'</div>';
						$aRows['sMsg'] .= '<div class="serviceListInviteBtnBox">
						<div class="serviceListInviteBtn  active">'.aJOB['YES'].'</div>
						</div>';
					}
					if ($aRows['nStatus0'] == '99')
					{
						$aRows['sMsg'] = '<div class="serviceListInviteQ">'.aJOB['ASKWORK'].'</div>';
						$aRows['sMsg'] .= '<div class="serviceListInviteBtnBox">
						<div class="serviceListInviteBtn  active">'.aJOB['NO'].'</div>
						</div>';
					}
				}
				$aData[$aRows['nId']] = $aRows;
				$aMemberData[$aRows['nUid']]['nUid'] = $aRows['nUid'];

				if ($nLatestId == 0)
				{
					$nLatestId = $aRows['nId'];
				}
			}
			$aData = array_reverse($aData);

			if (!empty($aMemberData))
			{
				// 成員資訊
				$sSQL = '	SELECT 	nId,
								nKid,
								nStatus,
								sName0,
								sAccount,
								sPassword
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId IN ( '.implode(',', array_keys($aMemberData)).' )
						AND 	nOnline = 1';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);
				while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
				{
					$aMemberData[$aRows['nId']]['nId'] = $aRows['nId'];
					$aMemberData[$aRows['nId']]['sAccount'] = $aRows['sAccount'];
					$aMemberData[$aRows['nId']]['nKid'] = $aRows['nKid'];
					$aMemberData[$aRows['nId']]['sName0'] = $aRows['sName0'];
					$aMemberData[$aRows['nId']]['sInfUrl'] =  $aUrl['sInf'].'&nId='.$aRows['nId'];#'javascript:void(0)';
					$aMemberData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
					$aMemberData[$aRows['nId']]['sRole'] = 'staff';
					$aMemberData[$aRows['nId']]['nJoin'] = 0;

					if ($aRows['nKid'] == 1)
					{
						$aMemberData[$aRows['nId']]['sRole'] = 'boss';
					}
					if (in_array(str_pad($aRows['nId'],9,0,STR_PAD_LEFT), $aJobData['aEmploye']))
					{
						$aMemberData[$aRows['nId']]['nJoin'] = 1;
					}
					if ($aUser['nId'] == $aRows['nId'])
					{
						$aMemberData[$aRows['nId']]['sPassword'] = $aRows['sPassword'];
						$aValue = array(
							'a'		=> 'LOGIN',
							'nGid'	=> $nId,		// 群組id
							'nUid'	=> $aUser['nId'],
							'sAccount' 	=> $aUser['sAccount'],
							'sPassword'	=> $aRows['sPassword'],
						);
						$aMemberData[$aRows['nId']]['sToken'] = sys_jwt_encode($aValue);
					}
				}
				// 成員頭像
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
		$aJumpMsg['0']['aButton']['0']['sClass'] = '';
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
		setcookie('aGroup['.$nId.']',$nLatestId,(NOWTIME + 3600*24*360));
		if ($nFetch == 1)
		{
			foreach ($aData as $LPnId => $LPaMessage)
			{
				$LPaMessage['sHeadImage'] = $aMemberData[$LPaMessage['nUid']]['sHeadImage'];
				$LPaMessage['sName0'] = $aMemberData[$LPaMessage['nUid']]['sName0'];
				$LPaMessage['sInfUrl'] = $aMemberData[$LPaMessage['nUid']]['sInfUrl'];
				$aReturn['aData']['aData'][] = $LPaMessage;
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
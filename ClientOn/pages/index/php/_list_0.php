<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/list.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/index/list.js',
	);
	#js結束

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',		INPUT_GET, 0);
	$nLid 	= filter_input_int('nLid',		INPUT_GET, 0);
	$sName0 	= filter_input_str('sName0',		INPUT_REQUEST, '');
	$sArea 	= filter_input_str('sArea',		INPUT_REQUEST, '');
	$sType 	= filter_input_str('sType',		INPUT_REQUEST, '');
	$nFavorite 	= filter_input_int('nFavorite',	INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sCenter'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/index/php/_list_0_act0.php']).'&run_page=1',
		'sIndex'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']),
		'sPage'	=> sys_web_encode($aMenuToNo['pages/index/php/_list_0.php']).'&nLid='.$nLid,
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sMyjob'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']),
		'sOnline'	=> sys_web_encode($aMenuToNo['pages/index/php/_online_0.php']).'&nLid='.$nLid,
		'sHtml'	=> 'pages/index/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/list_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBlockUid = myBlockUid($aUser['nId']);
	$aOnlineMember = array();
	$aHeadImage = array();
	$aCityArea = array();
	$aArea = array();
	$aTemp = array(
		'aType' => array(),
		'aArea' => array(),
	);
	$aType = array();
	$aSearchId = array();
	$aBindArray = array();
	$sType0 = '';
	$sTemp = '';
	$sPageVar = '';
	$sCondition = '';
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aPage['aVar'] = array(
		'nLid' 	=> $nLid,
		'sName0'	=> $sName0,
		'sArea'	=> $sArea,
		'sType'	=> $sType,
		'nFavorite'	=> $nFavorite,
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
		'a'	=> 'JOIN',
	);
	$sJoinJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'	=> 'OUT',
	);
	$sOutJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'sBackUrl'=> $aUrl['sPage'],
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);
	$aUrl['sOnline'] .= '&sJWT='.sys_jwt_encode($aValue);
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
	$aJumpMsg['0']['nClicktoClose'] = 0;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = '';

	// 上班提醒
	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['sTitle'] = aLIST['KINDREMIND'];
	$aJumpMsg['1']['sMsg'] = ($aUser['nStatus'] == 1) ? aLIST['WORKNOTICE'].KINDREMIND : KINDREMIND;
	$aJumpMsg['1']['aButton']['0']['sClass'] = 'JqClose JqJoin';
	$aJumpMsg['1']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['1']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['1']['aButton']['1']['sText'] = CANCEL;

	$aJumpMsg['Area'] = $aJumpMsg['0'];
	$aJumpMsg['Area']['sTitle'] = aLIST['LOCATION'];
	$aJumpMsg['Area']['sMsg'] = '';
	$aJumpMsg['Area']['sArticle'] = '';
	$aJumpMsg['Area']['aButton']['0']['sClass'] = 'JqSelectBtnClear ';
	$aJumpMsg['Area']['aButton']['0']['sUrl'] = 'javascript:void(0);';
	$aJumpMsg['Area']['aButton']['0']['sText'] = aLIST['CLEAR'];
	$aJumpMsg['Area']['aButton']['1']['sClass'] = 'JqApply JqClose';
	$aJumpMsg['Area']['aButton']['1']['sText'] = aLIST['APPLY'];
	$aJumpMsg['Area']['aButton']['2']['sClass'] = 'JqClose cancel';
	$aJumpMsg['Area']['aButton']['2']['sText'] = CANCEL;

	$aJumpMsg['Type'] = $aJumpMsg['Area'];
	$aJumpMsg['Type']['sTitle'] = aLIST['TYPE'];
	#宣告結束

	#程式邏輯區
	foreach ($aPage['aVar'] as $LPsParam => $LPsValue)
	{
		$sPageVar .= '&'. $LPsParam .'='. $LPsValue;
	}
	if ($sName0 != '')
	{
		$sCondition .= ' AND Job_.sName0 LIKE :sName0 ';
		$aBindArray['sName0'] = '%'.$sName0.'%';
	}
	if ($sArea != '')
	{
		$sCondition .= ' AND Job_.nAid IN ( :nAid )';
		$aBindArray['nAid'] = $sArea;
		$aTemp['aArea'] = explode(',', $sArea);
	}
	if ($sType != '')
	{
		$aTemp['aType'] = explode(',', $sType);
		foreach ($aTemp['aType'] as $LPnType)
		{
			$sType0 .= '%'.str_pad($LPnType,9,0,STR_PAD_LEFT).'%';
			$sTemp .= ' Job_.sType0 LIKE '.'"%'.str_pad($LPnType,9,0,STR_PAD_LEFT).'%" OR';
		}
		$sTemp = trim($sTemp,'OR');
		$sCondition .= ' AND ( '.$sTemp.' )';
	}
	if ($nFavorite == 1)
	{
		$sSQL = '	SELECT 	nGid
				FROM 	'.CLIENT_USER_JOB_FAVORITE.'
				WHERE nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nGid']] = $aRows['nGid'];
		}
		if (!empty($aSearchId))
		{
			$sCondition .= ' AND Job_.nGid IN ( '.implode(',', $aSearchId).' ) ';
			$aSearchId = array();
		}
		else
		{
			$sCondition .= ' AND Job_.nGid = 0 '; // 讓他查不到資料
		}
	}
	if ($sUserCurrentRole == 'staff') // 人才(自己當雇主發的工作不顯示)
	{
		$sCondition .= ' AND Group_.nUid != :nUid ';
		$aBindArray['nUid'] = $aUser['nId'];
	}
	##-----------NEW------------
	$sSQL = '	SELECT 	nId,
					sName0
			FROM 	'.CLIENT_CITY.'
			WHERE nOnline = 1
			AND 	nLid = :nLid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCityArea[$aRows['nId']] = $aRows;
		$aCityArea[$aRows['nId']]['sSelect'] = '';
		$aCityArea[$aRows['nId']]['aArea'] = array();
	}

	$sSQL = '	SELECT 	nId,
					nCid,
					sName0
			FROM 	'.CLIENT_CITY_AREA.'
			WHERE nCid IN ( '.implode(',', array_keys($aCityArea)).' )
			AND 	nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCityArea[$aRows['nCid']]['aArea'][$aRows['nId']]['sText'] = $aCityArea[$aRows['nCid']]['sName0'].$aRows['sName0'];
		$aCityArea[$aRows['nCid']]['aArea'][$aRows['nId']]['sSelect'] = '';
		if (in_array($aRows['nId'], $aTemp['aArea']))
		{
			$aCityArea[$aRows['nCid']]['sSelect'] = 'checked';
			$aCityArea[$aRows['nCid']]['aArea'][$aRows['nId']]['sSelect'] = 'checked';
		}
		$aArea[$aRows['nId']] = $aCityArea[$aRows['nCid']]['sName0'].' '.$aRows['sName0'];
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
		if (in_array($aRows['nId'], $aTemp['aType']))
		{
			$aType[$aRows['nId']]['sSelect'] = 'checked';
		}
	}

	// 工作資訊 (自己發的工作不顯示)
	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_GROUP_CTRL.' Group_,
				'.CLIENT_JOB.' Job_,
				'.CLIENT_USER_DATA.' User_
			WHERE Group_.nOnline = 1
			AND 	User_.nOnline = 1
			AND 	Job_.nStatus < 10
			AND 	Job_.nLid = :nLid
			AND 	Group_.nUid = User_.nId
			AND 	Group_.nId = Job_.nGid
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	Group_.nId,
					Group_.nUid,
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
			WHERE Group_.nOnline = 1
			AND 	Job_.nStatus < 10
			AND 	Job_.nLid = :nLid
			AND 	Group_.nId = Job_.nGid
			'.$sCondition.'
			ORDER BY Job_.nStatus ASC, Job_.nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if (isset($aBlockUid[$aRows['nUid']]))
		{
			continue;
		}
		$aRows['sContent0'] = convertContent($aRows['sContent0']);
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sUserInfoUrl'] = $aUrl['sInf'].'&nId='.$aRows['nUid'];#'javascript:void(0);';
		$aData[$aRows['nId']]['sDetailUrl'] = 'javascript:void(0);';
		$aData[$aRows['nId']]['sArea'] = $aArea[$aRows['nAid']];
		$aData[$aRows['nId']]['sImgUrl'] = '';
		$aData[$aRows['nId']]['nFavorite'] = 0;
		$aData[$aRows['nId']]['nJoin'] = 0;
		$aData[$aRows['nId']]['aType0'] = array();
		if ($aRows['sType0'] != '')
		{
			$aData[$aRows['nId']]['aType0'] = explode(',', $aRows['sType0']);
		}
		# 刊登雇主 & 當地有效人才 才可以進入工作詳情
		if ( $sUserCurrentRole == 'boss' && $aUser['nId'] == $aRows['nUid'] )
		{
			$aData[$aRows['nId']]['sDetailUrl'] = $aUrl['sMyjob'].'&nId='.$aRows['nId'];
		}
		if ( $sUserCurrentRole == 'staff' && $aUser['nLid'] == $nLid && $aUser['sExpired0'] > NOWDATE )
		{
			$aData[$aRows['nId']]['sDetailUrl'] = $aUrl['sMyjob'].'&nId='.$aRows['nId'];
		}

		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
	}

	if (!empty($aData))
	{
		#收藏的工作
		$sSQL = '	SELECT 	nGid
				FROM 	'.CLIENT_USER_JOB_FAVORITE.'
				WHERE nUid = :nUid
				AND 	nGid IN ( '.implode(',', array_keys($aData)).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nGid']]['nFavorite'] = 1;
		}

		#應徵的工作
		$sSQL = '	SELECT 	nGid
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nUid = :nUid
				AND 	nGid IN ( '.implode(',', array_keys($aData)).' )
				AND 	nStatus = 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nGid']]['nJoin'] = 1;
		}
		#工作pic
		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', array_keys($aData)).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_JOB, PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nKid']]['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	// 登入民眾
	$sSQL = '	SELECT 	User_.nId,
					User_.nKid,
					User_.nStatus
			FROM 	'.CLIENT_USER_DATA.' User_,
				'.CLIENT_USER_COOKIE.' Cookie_
			WHERE Cookie_.nUid != 0
			AND 	User_.nLid = :nLid
			AND 	Cookie_.nUid = User_.nId';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if (isset($aBlockUid[$aRows['nId']]))
		{
			continue;
		}

		$aOnlineMember[$aRows['nId']] = $aRows;
		$aOnlineMember[$aRows['nId']]['sUserInfoUrl'] = $aUrl['sInf'].'&nId='.$aRows['nId'];#'javascript:void(0)';
		$aOnlineMember[$aRows['nId']]['sStatusClass'] = '';
		switch ($aRows['nStatus'])
		{
			case '2':
				$aOnlineMember[$aRows['nId']]['sStatusClass'] = 'off';
				break;
			case '1':
				$aOnlineMember[$aRows['nId']]['sStatusClass'] = 'ing';
				break;
		}
		$aSearchId[$aRows['nId']] = $aRows['nId'];
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

	//--------- filter change to jumpmsg 20210304 ------------

	foreach ($aCityArea as $LPnCid => $LPaCity)
	{
		$aJumpMsg['Area']['sArticle'] .= '<div class="WindowSelectItem city JqCityKind" data-cityctrl="'.$LPnCid.'">';
		$aJumpMsg['Area']['sArticle'] .= 	'<div class="WindowSelectItemTxt JqSelectItemTxt">'.$LPaCity['sName0'].'</div>';
		$aJumpMsg['Area']['sArticle'] .= 	'<div class="WindowSelectItemSetting">';
		$aJumpMsg['Area']['sArticle'] .= 	'<input type="checkbox" id="city'. $LPnCid.'" value="'.$LPnCid.'" class="JqCityCheck" '.$LPaCity['sSelect'].'>';
		$aJumpMsg['Area']['sArticle'] .= 		'<label for="city'.$LPnCid.'">';
		$aJumpMsg['Area']['sArticle'] .= 			'<span>'.aLIST['SELECTALL'].'</span>';
		$aJumpMsg['Area']['sArticle'] .= 		'</label>';
		$aJumpMsg['Area']['sArticle'] .= 		'<span class="WindowSelectItemSettingBtnMore JqBtnCityKind">';
		$aJumpMsg['Area']['sArticle'] .= 			'<span class="more">+</span>';
		$aJumpMsg['Area']['sArticle'] .= 			'<span class="less">-</span>';
		$aJumpMsg['Area']['sArticle'] .= 		'</span>';
		$aJumpMsg['Area']['sArticle'] .= 	'</div>';
		$aJumpMsg['Area']['sArticle'] .= '</div>';
		$aJumpMsg['Area']['sArticle'] .= '<div class="WindowSelectItemDetailBox DisplayBlockNone JqCityKindBox" data-city="'.$LPnCid.'">';

			foreach ($LPaCity['aArea'] as $LPnAid => $LPaArea)
			{
				$aJumpMsg['Area']['sArticle'] .= '<div class="WindowSelectItem">';
				$aJumpMsg['Area']['sArticle'] .= 	'<div class="WindowSelectItemTxt JqSelectItemTxt">'.$LPaArea['sText'].'</div>';
				$aJumpMsg['Area']['sArticle'] .= 	'<div class="WindowSelectItemBtn">';
				$aJumpMsg['Area']['sArticle'] .= 		'<label for="area'.$LPnAid.'">';
				$aJumpMsg['Area']['sArticle'] .= 			'<input type="checkbox" id="area'.$LPnAid.'" value="'.$LPnAid.'" '.$LPaArea['sSelect'].' class="JqCityCheckbox JqSearchCheckbox">';
						$aJumpMsg['Area']['sArticle'] .= '</label>';
					$aJumpMsg['Area']['sArticle'] .= '</div>';
				$aJumpMsg['Area']['sArticle'] .= '</div>';

			}
		$aJumpMsg['Area']['sArticle'] .= '</div>';
	}

	foreach ($aType as $LPnId => $LPaType)
	{

		$aJumpMsg['Type']['sArticle'] .= '<div class="WindowSelectItem">';
		$aJumpMsg['Type']['sArticle'] .= '<div class="WindowSelectItemTxt JqSelectItemTxt">'.$LPaType['sName0'].'</div>';
		$aJumpMsg['Type']['sArticle'] .= 	'<div class="WindowSelectItemBtn">';
		$aJumpMsg['Type']['sArticle'] .= 		'<label for="list'.$LPnId.'">';
		$aJumpMsg['Type']['sArticle'] .= 			'<input type="checkbox" id="list'.$LPnId.'" value="'.$LPnId.'" '.$LPaType['sSelect'].' class="JqSearchCheckbox">';
		$aJumpMsg['Type']['sArticle'] .= 		'</label>';
		$aJumpMsg['Type']['sArticle'] .= 	'</div>';
		$aJumpMsg['Type']['sArticle'] .= '</div>';
	}


	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	if ($nFetch == 1)
	{
		foreach ($aData as $LPnId => $LPaJobData)
		{
			$LPsType0 = '';

			foreach ($LPaJobData['aType0'] as $LPsType0)
			{
				$LPnType0 = (int)$LPsType0;
				if (!isset($aType[$LPnType0]))
				{
					continue;
				}
				$LPsType0 .= '<span>'.$aType[$LPnType0]['sName0'].'</span>';
			}
			$LPaJobData['sTypeHtml'] = $LPsType0;
			$LPaJobData['sHeadImage'] = (isset($aHeadImage[$LPaJobData['nUid']]))?$aHeadImage[$LPaJobData['nUid']]['sHeadImage']:DEFAULTHEADIMG;
			$LPaJobData['sActBtn'] = '';
			$LPaJobData['sFavoriteImage'] = '<img src="images/like.png" alt="">';

			if($LPaJobData['nStatus'] == 1)
			{
				#已結案時呈現
				$LPaJobData['sActBtn'] = '<div class="JobListInfBtn active">'.aLIST['CLOSE'].'</div>';
			}
			else
			{
				if($sUserCurrentRole == 'staff')
				{
					#人才時呈現
					if($LPaJobData['nJoin'] == 1)
					{
						#已應徵工作時呈現
						$LPaJobData['sActBtn'] = '<div class="JobListInfBtn active JqOut" data-jid="'.$LPnId.'">'.aLIST['OUT'].'</div>';
					}
					else
					{
						if ($aUser['nStatus'] == 1)
						{
							#尚未應徵工作時呈現
							$LPaJobData['sActBtn'] = '<div class="JobListInfBtn JqListStupidOut" data-showctrl="1" data-jid="'.$LPnId.'">'.aLIST['JOIN'].'</div>';
						}
						else
						{
							#尚未應徵工作時呈現
							$LPaJobData['sActBtn'] = '<div class="JobListInfBtn JqJoin" data-jid="'.$LPnId.'">'.aLIST['JOIN'].'</div>';
						}
					}
				}
				else
				{

					# 未結案時呈現
					$LPaJobData['sActBtn'] = '<a class="JobListInfBtn detail" href="'.$LPaJobData['sDetailUrl'].'">'.aLIST['DETAIL'].'</a>';
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
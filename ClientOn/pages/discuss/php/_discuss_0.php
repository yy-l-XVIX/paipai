<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/discuss.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/discuss/discuss.js',
	);
	#js結束

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',	INPUT_REQUEST, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sPage'		=> sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_0.php']),
		'sAct'		=> sys_web_encode($aMenuToNo['pages/discuss/php/_post_0_act0.php']).'&run_page=1',
		'sDiscussDetail'	=> sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_detail_0.php']),
		'sHtml'		=> 'pages/discuss/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/discuss_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aMemberData = array();
	$aSearchId = array(
		'aDid' => array(),
		'aUid' => array(),
	);
	$aValue = array(
		'a'	=> 'DEL',
		't'	=> NOWTIME,
	);
	$sDelJWT = sys_jwt_encode($aValue);

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

	$aJumpMsg['delete'] = $aJumpMsg['0'];
	$aJumpMsg['delete']['sMsg'] = CDELETE.'?';
	$aJumpMsg['delete']['aButton']['0']['sClass'] = 'JqReplaceO JqDelete';
	$aJumpMsg['delete']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['delete']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['delete']['aButton']['1']['sClass'] = 'JqDiscussClose JqClose cancel';
	$aJumpMsg['delete']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_DISCUSS.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	nId,
					nUid,
					sContent0,
					sCreateTime
			FROM 	'.CLIENT_DISCUSS.'
			WHERE nOnline = 1
			ORDER BY nId DESC '.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sContent0'] = convertContent($aRows['sContent0']);
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['aReply'] = array();
		$aData[$aRows['nId']]['aImgUrl'] = array();

		$aSearchId['aUid'][$aRows['nUid']] = $aRows['nUid'];
	}
	if (!empty($aData))
	{
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN ( '.implode(',', array_keys($aData)).' )
				AND 	sTable LIKE :sTable ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_DISCUSS, PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nKid']]['aImgUrl'][$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	if (!empty($aSearchId['aUid']))
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						nKid
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aSearchId['aUid']).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
			$aMemberData[$aRows['nId']]['sRoleClass'] = '';
			$aMemberData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			if ($aRows['nKid'] == 1)
			{
				$aMemberData[$aRows['nId']]['sRoleClass'] = 'boss';
			}
		}
		// 頭
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN ( '.implode(',', $aSearchId['aUid']).' )
				AND 	sTable LIKE :sTable ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
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
		foreach ($aData as $LPnId => $LPaDiscuss)
		{
			$LPaDiscuss['sName0'] = $aMemberData[$LPaDiscuss['nUid']]['sName0'];
			$LPaDiscuss['sHeadImage'] = $aMemberData[$LPaDiscuss['nUid']]['sHeadImage'];
			$LPaDiscuss['sRoleClass'] = $aMemberData[$LPaDiscuss['nUid']]['sRoleClass'];
			$LPaDiscuss['sDeleteBtn'] = '';
			if ($LPaDiscuss['nUid'] == $aUser['nId'])
			{
				$LPaDiscuss['sDeleteBtn'] = '<div class="discussArticleBtnMore PosRight JqMoreBox">
										<div class="JobListInfBtnMore JqMoreBtn">
											<i class="fas fa-times"></i>
										</div>
										<div class="JobListInfBtnMoreInner DisplayBlockNone JqMoreBlock  JqStupidOut JqReplaceS" data-replace="'.$aUrl['sAct'].'&sJWT='.$sDelJWT.'&nId='.$LPnId.'" data-showctrl="delete">
											<div class="JobListInfBtnMoreInnerAhref">'.aDISCUSS['REMOVE'].'</div>
										</div>
									</div>';
			}
			$aReturn['aData']['aData'][] = $LPaDiscuss;
		}

		$aReturn['nStatus'] = 1;
		$aReturn['sMsg'] = 'success'.sizeof($aData);
		$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
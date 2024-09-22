<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/comments.js',
	);

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',	INPUT_GET, 0);
	$nId 		= filter_input_int('nId',	INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nId,
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_comments_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/comments_0.php',
	);
	#url結束

	#參數宣告區
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aData = array();
	$aSearchId = array(
		'aGid' => array(),
		'aUid' => array(),
	);
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
	$sSQL = '	SELECT	nId
			FROM	'.CLIENT_GROUP_CTRL.'
			WHERE	nUid = :nUid
			AND 	nOnline = 1
			AND 	nType1 = 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$nId,PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aSearchId['aGid'][$aRows['nId']] = $aRows['nId'];
	}

	if (!empty($aSearchId['aGid']))
	{

		$sSQL = '	SELECT	1
				FROM	'.CLIENT_JOB_SCORE.'
				WHERE	nGid IN ( '.implode(',', $aSearchId['aGid']).' )
				AND 	nStatus = 1';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		$aPage['nDataAmount'] = $Result->rowCount();
		$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
		if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
		{
			$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
		}

		$sSQL = '	SELECT	nId,
						nUid,
						sContent0,
						nScore,
						sCreateTime
				FROM	'.CLIENT_JOB_SCORE.'
				WHERE	nGid IN ( '.implode(',', $aSearchId['aGid']).' )
				AND 	nStatus = 1
				ORDER	BY nId DESC
				'.sql_limit($nPageStart, $aPage['nPageSize']);
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nId']] = $aRows;
			$aData[$aRows['nId']]['sScore'] = $aRows['nScore'];
			$aData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			$aSearchId['aUid'][$aRows['nUid']] = $aRows['nUid'];
		}
	}

	if (!empty($aSearchId['aUid']))
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						nKid
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aSearchId['aUid']).' ) ';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
			$aMemberData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			$aMemberData[$aRows['nId']]['sRoleClass'] = '';
			if ($aRows['nKid'] == 1)
			{
				$aMemberData[$aRows['nId']]['sRoleClass'] = 'boss';
			}
		}

		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.CLIENT_IMAGE_CTRL .'
				WHERE	sTable LIKE :sTable
				AND 	nKid IN ( '.implode(',', $aSearchId['aUid']).' ) ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable',CLIENT_USER_DATA ,PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'images/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	foreach ($aData as $LPnId => $LPaData)
	{
		$aData[$LPnId]['sName0'] = $aMemberData[$LPaData['nUid']]['sName0'];
		$aData[$LPnId]['sRoleClass'] = $aMemberData[$LPaData['nUid']]['sRoleClass'];
		if (isset($aMemberData[$LPaData['nUid']]))
		{
			$aData[$LPnId]['sHeadImage'] = $aMemberData[$LPaData['nUid']]['sHeadImage'];
		}
		$aReturn['aData']['aData'][] = $aData[$LPnId];
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	if ($nFetch == 1)
	{
		$aReturn['nStatus'] = 1;
		$aReturn['sMsg'] = 'success'.sizeof($aData);
		$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];
		// $aReturn['aData']['aData'] = $aData;

		echo json_encode($aReturn);
		exit;
	}

	#輸出結束
?>
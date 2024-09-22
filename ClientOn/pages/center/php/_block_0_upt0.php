<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/block.js',
	);

	#參數接收區
	$nFetch = filter_input_int('nFetch',INPUT_REQUEST,0);
	#參數結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
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
	$aValue = array(
		'a'		=> 'DELBLOCK'.$aUser['nId'],
		't'		=> NOWTIME,
	);
	$sJWT =sys_jwt_encode($aValue);
	#宣告結束

	#給此頁使用的url
	$aUrl = array(
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sFetch'	=> sys_web_encode($aMenuToNo['pages/center/php/_block_0_upt0.php']).'&nFetch=1',
		'sAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_block_0_act0.php']).'&run_page=1&sJWT='.$sJWT,
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/block_0_upt0.php',
	);
	#url結束

	#程式邏輯區
	// 封鎖會員
	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_USER_BLOCK.'
			WHERE nUid = :nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	nId,
					nBUid
			FROM 	'.CLIENT_USER_BLOCK.'
			WHERE nUid = :nUid
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aSearchId[$aRows['nBUid']] = $aRows['nBUid'];
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						nKid,
						nStatus
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
			$aMemberData[$aRows['nId']]['sRoleClass'] = '';
			$aMemberData[$aRows['nId']]['sStatusClass'] = '';
			$aMemberData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			$aMemberData[$aRows['nId']]['sUserInfoUrl'] = $aUrl['sInf'].'&nId='.$aRows['nId'];#'javascript:void(0)';

			// 雇主和人才顏色不同
			if($aRows['nKid'] == 1)
			{
				$aMemberData[$aRows['nId']]['sRoleClass'] = 'boss';
			}
			// 上班下班
			if($aRows['nKid'] == 3)
			{
				$sTempClass = '';

				if($aRows['nStatus'] == 2)
				{
					$sTempClass = 'off';
				}
				if($aRows['nStatus'] == 3)
				{
					$sTempClass = 'ing';
				}

				$aMemberData[$aRows['nId']]['sStatusClass'] = '<div class="selfieStatus '.$sTempClass.'"></div>';
			}
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
			$LPaData['sName0']		= $aMemberData[$LPaData['nBUid']]['sName0'];
			$LPaData['sRoleClass']		= $aMemberData[$LPaData['nBUid']]['sRoleClass'];
			$LPaData['sStatusClass']	= $aMemberData[$LPaData['nBUid']]['sStatusClass'];
			$LPaData['sHeadImage']		= $aMemberData[$LPaData['nBUid']]['sHeadImage'];
			$LPaData['sUserInfoUrl']	= $aMemberData[$LPaData['nBUid']]['sUserInfoUrl'];
			$aReturn['aData']['aData'][] = $LPaData;
		}
		$aReturn['nStatus'] = 1;
		$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
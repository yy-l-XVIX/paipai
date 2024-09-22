<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/transfer_choose.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/transfer_choose.js',
	);

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',		INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sPage'		=> sys_web_encode($aMenuToNo['pages/center/php/_transfer_choose_0.php']),
		'sTransfer'		=> sys_web_encode($aMenuToNo['pages/center/php/_transfer_0.php']),
		'sHtml'		=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/transfer_choose_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT 	nFUid
			FROM 	'.CLIENT_USER_FRIEND.'
			WHERE nUid = :nUid
			AND 	nStatus = 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_STR);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}


	$sSQL = '	SELECT 	nFUid
			FROM 	'.CLIENT_USER_FRIEND.'
			WHERE nUid = :nUid
			AND 	nStatus = 1
			'.sql_limit($nPageStart, $aPage['nPageSize']);;
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nFUid']] = $aRows['nFUid'];
	}

	if (!empty($aData))
	{
		$sSQL = '	SELECT 	nId,
						sAccount,
						nKid
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aData).' )
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nId']] = $aRows;
			$aData[$aRows['nId']]['sRoleClass'] = '';
			$aData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			if ($aRows['nKid'] == 1)
			{
				$aData[$aRows['nId']]['sRoleClass'] = 'boss';
			}
		}

		$sSQL = '	SELECT 	nId,
						nKid,
						sFile,
						sTable,
						nType0,
						nCreateTime
				FROM 	'.CLIENT_IMAGE_CTRL.'
				WHERE nKid IN ( '.implode(',', array_keys($aData)).' )
				AND 	sTable LIKE :sTable';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
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
			$aReturn['aData']['aData'][] = $LPaData;
		}

		$aReturn['nStatus'] = 1;
		$aReturn['sMsg'] = 'success'.sizeof($aData);
		$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nLid = filter_input_int('nLid',	INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sHtml'	=> 'pages/index/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/online_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBlockUid = myBlockUid($aUser['nId']);
	$aSearchId = array();
	$nSumData = 0;
	$LPnI = 1;
	$aValue = array(
		'sBackUrl'=> sys_web_encode($aMenuToNo['pages/index/php/_online_0.php']).'&nLid='.$nLid,
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);
	#宣告結束

	#程式邏輯區

	$sSQL = '	SELECT 	User_.nId,
					User_.nKid,
					User_.nStatus,
					User_.sName0
			FROM 	'.CLIENT_USER_DATA.' User_,
				'.CLIENT_USER_COOKIE.' Cookie_
			WHERE Cookie_.nUid != 0
			AND 	User_.nLid = :nLid
			AND 	User_.nOnline = 1
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
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sStatusClass'] = '';
		$aData[$aRows['nId']]['sUserInfoUrl'] = $aUrl['sInf'].'&nId='.$aRows['nId'];#'javascript:void(0)';
		$aData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
		switch ($aRows['nStatus'])
		{
			case '2':
				$aData[$aRows['nId']]['sStatusClass'] = 'off';
				break;
			case '1':
				$aData[$aRows['nId']]['sStatusClass'] = 'ing';
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
			$aData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}
	$nSumData = sizeof($aData);

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
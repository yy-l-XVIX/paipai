<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/member_list.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/member_list.js',
	);

	#參數接收區
	$nFetch 	= filter_input_int('nFetch',	INPUT_REQUEST,0);
	$sSearch	= filter_input_str('sSearch',	INPUT_REQUEST, '');

	#參數結束

	#參數宣告區
	$aData = array();
	$aBlockUid = myBlockUid($aUser['nId']);
	$aValue = array(
		'a'		=> 'CHECKCHAT',
	);
	$sChatJWT =sys_jwt_encode($aValue);
	$aValue = array(
		'sBackUrl'=> sys_web_encode($aMenuToNo['pages/center/php/_member_list_0.php']).'&sSearch='.$sSearch,
	);
	$sInfJWT =sys_jwt_encode($aValue);
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
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

	#給此頁使用的url
	$aUrl = array(
		'sFetch'	=> '',
		'sChat'	=> sys_web_encode($aMenuToNo['pages/center/php/_member_list_0_act0.php']).'&run_page=1&sJWT='.$sChatJWT,
		'sPage'	=> sys_web_encode($aMenuToNo['pages/center/php/_member_list_0.php']),
		'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&sJWT='.$sInfJWT,
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/member_list_0.php',
	);
	#url結束


	#程式邏輯區
	if (!empty($aBlockUid))
	{
		$sCondition = ' AND nId NOT IN ( '.implode(',',$aBlockUid).' ) ';
	}
	if($sSearch != '')
	{
		$aUrl['sFetch'] = $aUrl['sPage'].'&nFetch=1&sSearch='.$sSearch;
		$sSQL = '	SELECT	1
				FROM		'.CLIENT_USER_DATA.'
				WHERE		nOnline = 1
				AND		nId != :nId
				AND		( sAccount LIKE :sSearch
				OR		( sName0 LIKE :sSearch)
				OR		( sPhone LIKE :sSearch AND nType0 = 1)
				OR		( sWechat LIKE :sSearch AND nType1 = 1)
				OR		( sEmail LIKE :sSearch AND nType2 = 1))
				'.$sCondition;
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sSearch','%'.$sSearch.'%',PDO::PARAM_STR);
		$Result->bindValue(':nId',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aPage['nDataAmount'] = $Result->rowCount();
		$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
		if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
		{
			$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
		}

		$sSQL = '	SELECT	nId,
						sName0,
						sKid,
						nKid,
						nStatus as nUserStatus
				FROM	'.CLIENT_USER_DATA.'
				WHERE	nOnline = 1
				AND	nId <> :nId
				AND	( sAccount LIKE :sSearch
				OR	( sName0 LIKE :sSearch)
				OR	( sPhone LIKE :sSearch AND nType0 = 1)
				OR	( sWechat LIKE :sSearch AND nType1 = 1)
				OR	( sEmail LIKE :sSearch AND nType2 = 1))
				'.$sCondition.sql_limit($nPageStart, $aPage['nPageSize']);
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sSearch','%'.$sSearch.'%',PDO::PARAM_STR);
		$Result->bindValue(':nId',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nId']] = $aRows;
			$aData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			$aData[$aRows['nId']]['sUserInfoUrl'] = $aUrl['sInf'].'&nId='.$aRows['nId']; #'javascript:void(0)';
			$aData[$aRows['nId']]['sRoleClass'] = '';
			$aData[$aRows['nId']]['sStatusClass'] = '';

			// 雇主和人才顏色不同
			if($aRows['nKid'] == 1)
			{
				$aData[$aRows['nId']]['sRoleClass'] = 'boss';
			}
			// 上班下班
			if($aRows['nKid'] == 3)
			{
				$sTempClass = '';

				if($aRows['nUserStatus'] == 2)
				{
					$sTempClass = 'off';
				}
				if($aRows['nUserStatus'] == 3)
				{
					$sTempClass = 'ing';
				}

				$aData[$aRows['nId']]['sStatusClass'] = '<div class="selfieStatus '.$sTempClass.'"></div>';
			}
		}

		if (!empty($aData))
		{
			// 頭
			$sSQL = '	SELECT	nId,
							nKid,
							sFile,
							sTable,
							nCreateTime
					FROM	'.	CLIENT_IMAGE_CTRL .'
					WHERE	nKid IN ( '.implode(',', array_keys($aData)).' )
					AND 	sTable LIKE :sTable ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}
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
		$aReturn['aData']['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
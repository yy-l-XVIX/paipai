<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_manager_data.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Plugins/GoogleAuthenticator/googleClass.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/end_manager_data/end_manager_data.js',
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId',		INPUT_GET, 0);
	$nAdmType 	= filter_input_int('nAdmType',	INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0.php']).$aJWT['sBackParam'],
		'sPage'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_upt0.php']),
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_manager_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_manager_data_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$oGg 	= new PHPGangsta_GoogleAuthenticator;
	$aMenuKind = array();
	$aLocation = array();
	$aData = array(
		'nId'			=> $nId,
		'sAccount'		=> '',
		'nOnline'		=> 1,
		'nAdmType'		=> $aAdm['nAdmType'],
		'sName0'		=> '',
		'nType1'		=> 0, #0不隱藏帳號 1隱藏帳號
		'sIp'			=> '',
		'nLid'		=> 0,
		'sUpdateTime'	=> '',
		'nGoogle'		=> 0, #0不啟用 1啟用
		'aControl'		=> array(),
		'sDisable'		=> '',
		'sGoogleQrcode'	=> '',
	);
	$aType1 = array(
		'0' => array(
			'sTitle' => aMANAGER['HIDE0'],
			'sSelect'=> '',
		),
		'1' => array(
			'sTitle' => aMANAGER['HIDE1'],
			'sSelect'=> '',
		),
	);
	$aGoogle = array(
		'0' => array(
			'sTitle' => aMANAGER['GOOGLENOUSE'],
			'sSelect'=> '',
		),
		'1' => array(
			'sTitle' => aMANAGER['GOOGLEUSE'],
			'sSelect'=> '',
		),
	);
	$aOnline = aONLINE;
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		'nExp'	=> NOWTIME + JWTWAIT,
		'nId'		=> $nId,
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sJWTAct = sys_jwt_encode($aValue);
	$aValue = array(
		'a'		=> 'VERIFY',
		'nExp'	=> NOWTIME + JWTWAIT,
		'nId'		=> $nId,
		'nAdmin'	=> $aAdm['nId'],
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sVerifyJWT = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_verify_0_act0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue);
	$aValue = array(
		'sBackParam' => $aJWT['sBackParam'],
	);
	$aUrl['sPage'] .= '&sJWT='.sys_jwt_encode($aValue);
	$nHaveData = 0;
	$nErr = 0;
	$sErrMsg = '';
	$sBackParam = '';
	#宣告結束

	#程式邏輯區

	#sControl 1_1,2,3,6|2_4,5
	$sSQL = 'SELECT 	nId,
				sName0,
				sControl
		FROM 	'.END_PERMISSION.'
		WHERE nId >= :nId
		AND 	nOnline != 99
		ORDER BY nId ASC';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $aAdm['nAdmType'], PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aAdmType[$aRows['nId']] = $aRows;
		$aAdmType[$aRows['nId']]['sSelect'] = '';
		$aAdmType[$aRows['nId']]['aControl'] = array();
		$aTempCtrl = explode('|',$aRows['sControl']);
		foreach ($aTempCtrl as $LPsCtrl)
		{
			$LPaTemp = explode('_',$LPsCtrl);
			$aAdmType[$aRows['nId']]['aControl'][$LPaTemp[0]] = explode(',',$LPaTemp[1]);
		}
	}

	$sSQL = '	SELECT 	nId,
					sAccount,
					nOnline,
					nAdmType,
					sName0,
					sIp,
					nLid,
					nType1,
					sUpdateTime
			FROM 	'.END_MANAGER_DATA.'
			WHERE nId = :nId
			AND 	nOnline != 99
			AND 	nAdmType >= :nAdmType
			LIMIT 1';

	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	$Result->bindValue(':nAdmType', $aAdm['nAdmType'], PDO::PARAM_INT);
	sql_query($Result);
	$nCount = $Result->rowCount();
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData = $aRows;
		$aData['nGoogle']		= 0; #0不啟用 1啟用
		$aData['aControl']	= array();
		$aData['sDisable']	= '';
		$aData['sGoogleQrcode']	= '';
	}
	if ($nCount == 0 && $nId > 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	$sSQL = '	SELECT 	nId,
					sKey,
					nStatus,
					nOnline
			FROM 	'.SYS_GOOGLE_VERIFY.'
			WHERE nUid = :nUid
			AND 	sTable = :sTable
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
	$Result->bindValue(':sTable', END_MANAGER_DATA, PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['nGoogle'] = $aRows['nOnline'];
		if ($aRows['nStatus'] == 0 && $aRows['nOnline'] == 1) # 沒有驗證過
		{
			$aValue = array(
				'a'		=> 'QRCODE',
				'nExpire'	=> NOWTIME + 300,
				'nUid'	=> $nId,
			);

			$sUrlEncode = WEBSITE['ADMURL'].sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_verify_0_act0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue);
			$sUrlEncode = urlencode($sUrlEncode);
			$sCodeUrl = QRCODE['URL'].'qr_img.php?d='.$sUrlEncode;

			$aData['sGoogleQrcode'] = '<img src="'.$sCodeUrl.'">';
		}
	}

	$sSQL = '	SELECT 	nMkid,
					nMlid
			FROM 	'.END_MENU_CTRL.'
			WHERE nUid = :nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aData['nId'], PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['aControl'][$aRows['nMkid']][$aRows['nMlid']] = true;
	}

	// 管理地區
	$sSQL = '	SELECT 	nLid,
					sName0
			FROM 	'.CLIENT_LOCATION.'
			WHERE nOnline = 1
			AND 	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{

		$aLocation[$aRows['nLid']] = $aRows;
		$aLocation[$aRows['nLid']]['sSelect'] = '';
		if ($aRows['nLid'] == $aData['nLid'])
		{
			$aLocation[$aRows['nLid']]['sSelect'] = 'selected';
		}
	}

	if ($nAdmType == 0)
	{
		$nAdmType = $aData['nAdmType'];
	}
	$aType1[$aData['nType1']]['sSelect'] = 'checked';
	$aGoogle[$aData['nGoogle']]['sSelect'] = 'checked';
	$aOnline[$aData['nOnline']]['sSelect'] = 'selected';
	$aAdmType[$nAdmType]['sSelect'] = 'selected';
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	else
	{
		$aJumpMsg['0']['nClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
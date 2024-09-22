<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/setting.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/File.js',
		'1'	=> 'plugins/js/js_date/laydate.js',
		'2'	=> 'plugins/js/center/setting.js',
	);

	#參數接收區

	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0_act0.php']).'&run_page=1',
		'sChangePwd'=> sys_web_encode($aMenuToNo['pages/center/php/_change_pwd_0.php']),
		// 'sChangeTransPwd'=> sys_web_encode($aMenuToNo['pages/center/php/_change_transpwd_0.php']),
		// 'sBankList'	=> sys_web_encode($aMenuToNo['pages/center/php/_bank_list_0.php']),
		// 'sId'		=> sys_web_encode($aMenuToNo['pages/center/php/_id_0.php']),
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sPhoto'	=> sys_web_encode($aMenuToNo['pages/center/php/_photo_0.php']),
		'sVideo'	=> sys_web_encode($aMenuToNo['pages/center/php/_video_0.php']),
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/setting_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aDataPending = array();
	$aPendingField = explode(',', $aSystem['aParam']['sPendingField']);	// 需要審核欄位
	$aLocation = array(
		'0'	=> array(
			'sName0'	=> aSETTING['PLEASESELECT'],
			'sSelect'	=> '',
		),
	);
	$aValue = array(
		'a'	=> 'UPT'.$aUser['nId'],

	);
	$sJWT=sys_jwt_encode($aValue);
	$sSelfieBoxClass = '';
	$bLocationDisabled = false;
	$sPendingMessage = '';	//此帳號尚未通過審核，請確認提供完整用戶資訊
	$aUser['sHeadImage'] = DEFAULTHEADIMG;
	$aUser['sLocationTime'] = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 0;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqGopage';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['sMsg'] = aSETTING['CHANGEPAGECONFIRM'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sText'] = aSETTING['SAVEANDLEAVE'];
	$aJumpMsg['1']['aButton']['0']['sClass'] = 'JqClose JqRedirectLink JqSaveRedirect';

	$aJumpMsg['1']['aButton']['1']['sText'] = aSETTING['NOSAVESTAY'];
	$aJumpMsg['1']['aButton']['1']['sClass'] = 'JqClose';

	// $aJumpMsg['1']['aButton']['2']['sText'] = aSETTING['NOSAVELEAVE'];
	// $aJumpMsg['1']['aButton']['2']['sClass'] = 'JqGopage JqRedirectLink';
	// $aJumpMsg['1']['aButton']['2']['sUrl'] = 'javascript:void(0)';

	$aJumpMsg['dataprocessing'] = array(
		'sBoxClass'	=>	'',
		'sShow'	=>	0,	# 是否直接顯示彈窗 0=>隱藏 , 1=>顯示
		'sTitle'	=>	'',	# 標題
		'sIcon'	=>	'',	# 成功=>success,失敗=>error
		'sMsg'	=>	DATAPROCESSING,# 資料處理中
		'sArticle'	=>	'',	# 較長文字
		'aButton'	=>	array(),
		'nClicktoClose'=>	0,	# 是否點擊任意一處即可關閉 0=>否 , 1=>是
	);

	#宣告結束

	#程式邏輯區

	if($sUserCurrentRole == 'boss') // 是雇主
	{
		$sSelfieBoxClass = 'boss';
	}

	$sSQL = '	SELECT	nLid,
					sName0
			FROM	'.	CLIENT_LOCATION .'
			WHERE		nOnline = 1
			AND		sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aLocation[$aRows['nLid']] = $aRows;
		$aLocation[$aRows['nLid']]['sSelect'] = '';

		if ($aUser['nLid'] == $aRows['nLid'])
		{
			$aLocation[$aRows['nLid']]['sSelect'] = 'selected';
		}
	}

	$sSQL = '	SELECT	sLocationTime,
					nPendingStatus,
					sPendingStatus
			FROM	'.	CLIENT_USER_DATA .'
			WHERE		nId = :nId
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	$aUser['sLocationTime'] = $aRows['sLocationTime'];
	$aUser['nPendingStatus'] = $aRows['nPendingStatus'];
	$aUser['sPendingStatus'] = $aRows['sPendingStatus'];
	if ($aUser['nLid'] != 0 && strtotime($aUser['sLocationTime'].'+30 day') > NOWTIME)
	{
		$bLocationDisabled = true;
	}
	if ($aUser['nStatus'] == 11)
	{
		$sPendingMessage = aSETTING['PENDINGINFO0'];	//此帳號尚未通過審核，請確認提供完整用戶資訊
		if ($aUser['nPendingStatus'] == 0 && $aUser['nType3'] == 1)
		{
			$sPendingMessage = aSETTING['PENDINGINFO1'];	// 請耐心等候系統審核，如有問題請聯絡我們
		}
		if ($aUser['nPendingStatus'] == 1)
		{
			$sPendingMessage = aSETTING['PENDINGINFO2'];	// 系統審核失敗，請更新您的資料
		}
	}
	$aUserPendingStatus = explode(',', $aUser['sPendingStatus']);		// 資料審核狀態
	$aPendingField = explode(',', $aSystem['aParam']['sPendingField']);	// 需要審核欄位
	foreach ($aPendingField as $LPnIndex => $LPsField)
	{
		$aDataPending[$LPsField] = '';
		$LPnStatus = $aUserPendingStatus[$LPnIndex];
		if ($aUser['nStatus'] == 11)
		{
			if ($LPnStatus == 1)
			{
				$aDataPending[$LPsField] = '<span class="FontGreen"><i class="far fa-check-circle"></i></span>';
			}
			if ($LPnStatus == 99)
			{
				$aDataPending[$LPsField] = '<span class="FontRed"><i class="far fa-times-circle"></i></span>';
			}
		}
	}


	$sSQL = '	SELECT	sIdNumber,
					sBirthday
			FROM	'.	CLIENT_USER_DETAIL .'
			WHERE		nUid = :nUid
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	$aUser['sIdNumber'] = $aRows['sIdNumber'];
	$aUser['sBirthday'] = $aRows['sBirthday'];

	// 我的頭
	$sSQL = '	SELECT	nId,
					nKid,
					sFile,
					sTable,
					nCreateTime
			FROM	'.	CLIENT_IMAGE_CTRL .'
			WHERE	nKid = :nKid
			AND 	sTable LIKE :sTable
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nKid', $aUser['nId'], PDO::PARAM_INT);
	$Result->bindValue(':sTable', CLIENT_USER_DATA, PDO::PARAM_STR);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if ($aRows!== false)
	{
		$aUser['sHeadImage'] =  IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_service.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/js_date/laydate.js',
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId', INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_service/php/_client_service_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_service/php/_client_service_0.php']),
		'sHtml'	=> 'pages/client_service/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_service_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'nId'			=> 0,
		'sAccount'		=> '',
		'nKid'		=> 0,
		'nStatus'		=> 0,
		'sQuestion'		=> '',
		'sResponse'		=> '',
		'sCreateTime'	=> '',
		'sUpdateTime'	=> '',
		'sImageUrl'		=> '',
	);

	$aStatus = array(
		'0' => array(
			'sTitle' => aSERVICE['STATUS0'],
			'sChecked'=> '',
		),
		'10' => array(
			'sTitle' => aSERVICE['STATUS10'],
			'sChecked'=> '',
		),
	);

	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		't'		=> NOWTIME,
	);
	$sJWT = sys_jwt_encode($aValue);
	$aOnline = aONLINE;

	$nErr = 0;
	$sMsg = '';
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	Service_.nId,
					Service_.nStatus,
					Service_.sQuestion,
					Service_.sResponse,
					Service_.sCreateTime,
					Service_.sUpdateTime,
					Kind_.sName0,
					User_.sAccount
			FROM		'.CLIENT_SERVICE .' Service_
			JOIN		'.CLIENT_SERVICE_KIND.' Kind_
			ON		Service_.nKid = Kind_.nId
			JOIN		'.CLIENT_USER_DATA.' User_
			ON		Service_.nUid = User_.nId
			WHERE		Service_.nId = :nId
			AND		Kind_.sLang = :sLang
			LIMIT		1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	$nCount = $Result->rowCount();
	if ($nCount == 0 && $nId != 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	else
	{
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData = $aRows;
			$aData['sImageUrl'] = '';
			$aStatus[$aRows['nStatus']]['sChecked'] = 'checked="checked"';
		}
	}


	$sSQL = 'SELECT 	nId,
				nKid,
				sTable,
				sFile,
				nCreateTime
		FROM  	'.CLIENT_IMAGE_CTRL.'
		WHERE 	sTable = :sTable
		AND		nKid = :nKid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sTable',CLIENT_SERVICE,PDO::PARAM_STR);
	$Result->bindValue(':nKid',$aData['nId'],PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		// $aData['sImageUrl'] = date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		$aData['sImageUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];

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
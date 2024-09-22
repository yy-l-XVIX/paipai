<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_broadcast.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nLid		= filter_input_int('nLid', INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_news/php/_client_broadcast_0.php']),
		'sHtml'	=> 'pages/client_news/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_broadcast_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aAnnounceKind = array();
	$aImage = array();
	$nFileCount = 1;
	$aCount = array();
	$aValue = array(
		'a'		=> ($nLid == 0)?'INS':'UPT'.$nLid,
		'nExp'	=> NOWTIME + JWTWAIT,
	);
	$sJWT = sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';
	$nCount = 0;
	$aOnline = aONLINE;
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT	nKid,
					sTable,
					sFile,
					nCreateTime,
					nType0
			FROM	'.	CLIENT_IMAGE_CTRL .'
			WHERE		sTable LIKE \''. CLIENT_BROADCAST .'\'
			ORDER	BY	nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if(!isset($aCount[$aRows['nKid']]))
		{
			$aCount[$aRows['nKid']] = 0;
		}
		else
		{
			$aCount[$aRows['nKid']] ++;
		}
		$aImage[$aRows['nKid']][$aCount[$aRows['nKid']]]['sUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];

		$aValue = array(
			'a'		=> 'DELIMG'.$aRows['nKid'],
			't'		=> NOWTIME,
		);
		$sLPJWT = sys_jwt_encode($aValue);
		$aImage[$aRows['nKid']][$aCount[$aRows['nKid']]]['sDel'] = $aUrl['sAct'].'&nImgKid='.$aRows['nKid'].'&sJWT='.$sLPJWT.'&nLid='.$nLid;
	}

	$sSQL = '	SELECT	nLid,
					sName0
			FROM	'.	CLIENT_BROADCAST_KIND .'
			WHERE		nOnline != 99
			AND		sLang LIKE :sLang
			ORDER	BY	nId DESC';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aAnnounceKind[$aRows['nLid']] = $aRows;
		$aAnnounceKind[$aRows['nLid']]['sSelect'] = '';
	}

	$sSQL = '	SELECT	nId,
					sName0,
					nKid,
					nSync,
					sLang,
					nLid,
					nOnline,
					sCreateTime,
					sUpdateTime
			FROM	'.	CLIENT_BROADCAST .'
			WHERE		nLid = :nLid
			AND		nOnline != 99';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nLid', $nLid, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['nOnline'] = $aRows['nOnline'];
		$aData['nKid'] = $aRows['nKid'];
		$aData['sSyncCheck'] = '';
		if( $aRows['nSync'] == 1 )
		{
			$aData['sSyncCheck'] = 'checked';
		}
		$aData[$aRows['sLang']] = $aRows;
		$nCount++;
	}

	if($nCount == 0 && $nLid != 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	if($nLid != 0)
	{
		$aOnline[$aData['nOnline']]['sSelect'] = 'selected';
		$aData['sKind'] = $aAnnounceKind[$aData['nKid']];
		$aAnnounceKind[$aData['nKid']]['sSelect'] = 'selected';
	}

	foreach(aLANG as $LPsLang => $LPsText)
	{
		if(!isset($aData[$LPsLang]))
		{
			$aData[$LPsLang]['nId'] = 0;
			$aData[$LPsLang]['sName0'] = '';
		}
	}

	$aData['aAnnounceKind'] = $aAnnounceKind;

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
		$aJumpMsg['0']['sClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
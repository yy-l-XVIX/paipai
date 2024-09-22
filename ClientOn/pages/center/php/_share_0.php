<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/share.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/share.js',
	);

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/share_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aImage = array();
	$aVideo = array();
	$sSelfieBoxClass = '';
	$sHeadImage = DEFAULTHEADIMG;
	$nErr = 0;
	$sErrMsg = '';
	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = '';
	#宣告結束

	#程式邏輯區
	if (!isset($aJWT) || $aJWT['nExpireTime'] < NOWTIME) # 網頁過期
	{
		$nErr = 1;
		$sErrMsg = aSHARE['EXPIRED'];
	}

	$nId = $aJWT['nUid'];

	$sSQL = '	SELECT 	Detail_.sHeight,
					Detail_.sSize,
					Detail_.sContent0,
					Detail_.sContent1,
					Detail_.sWeight,
					Detail_.nBirthday,
					Data_.sAccount,
					Data_.sName0,
					Data_.nKid,
					Data_.nLid,
					Data_.nStatus,
					Data_.sPhone,
					Data_.sWechat,
					Data_.sEmail,
					Data_.nType0,
					Data_.nType1,
					Data_.nType2
			FROM 	'.CLIENT_USER_DETAIL.' Detail_,
				'.CLIENT_USER_DATA.' Data_
			WHERE Data_.nId = :nId
			AND 	Data_.nOnline = 1
			AND 	Data_.nId = Detail_.nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData = $aRows;
		$aData['nAge'] = '';
		if ($aRows['nBirthday']>0)
		{
			$aData['nAge'] = date('Y') - date('Y',$aRows['nBirthday']) - 1;
			if (date('n') > date('n',$aRows['nBirthday']) || (date('n') == date('n',$aRows['nBirthday']) && date('j') == date('j',$aRows['nBirthday'])))
			{
				$aData['nAge'] ++;
			}
		}
		$aData['sLocationName0'] = '';
		$aData['sKindName0'] = '';
	}
	if (empty($aData))
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	$sSQL = '	SELECT	nId,
					nKid,
					sFile,
					sTable,
					nCreateTime
			FROM	'.	CLIENT_IMAGE_CTRL .'
			WHERE	nKid = :nKid
			AND 	sTable IN  ("client_user_data","client_user_photo","client_user_video")';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nKid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		if ($aRows['sTable'] == 'client_user_data')
		{
			$sHeadImage = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
		if ($aRows['sTable'] == 'client_user_photo')
		{
			$aImage[$aRows['nId']] =  IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
		if ($aRows['sTable'] == 'client_user_video')
		{
			$aVideo[$aRows['nId']] =  IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	$sSQL = '	SELECT	sName0
			FROM	'.CLIENT_USER_KIND.'
			WHERE	nOnline = 1
			AND	sLang LIKE :sLang
			AND 	nLid = :nLid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang',$aSystem['sLang'],PDO::PARAM_STR);
	$Result->bindValue(':nLid',$aData['nKid'],PDO::PARAM_INT);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['sKindName0'] = $aRows['sName0'];
	}

	$sSQL = '	SELECT	sName0
			FROM	'.	CLIENT_LOCATION .'
			WHERE		nOnline = 1
			AND		sLang LIKE :sLang
			AND 		nLid = :nLid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	$Result->bindValue(':nLid', $aData['nLid'], PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData['sLocationName0'] = $aRows['sName0'];
	}

	#程式邏輯結束

	#輸出json
	if ($nErr == 0)
	{
		$sData = json_encode($aData);
		$aRequire['Require'] = $aUrl['sHtml'];
	}
	else
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['nClicktoClose'] = 0;
		$aJumpMsg['0']['aButton'] = array();
	}

	#輸出結束
?>
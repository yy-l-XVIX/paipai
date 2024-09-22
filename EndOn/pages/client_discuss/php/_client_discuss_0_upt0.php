<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_discuss.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId', INPUT_GET,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0_act0.php']).'&run_page=1',
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0.php']).$aJWT['sBackParam'],
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0_upt0.php']),
		'sHtml'	=> 'pages/client_discuss/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_discuss_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
	$aMemberData = array(
		0 => '',
	);
	$aValue = array(
		'a'		=>'UPT'.$nId,
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$aUrl['sPage'] .= '&sJWT='.sys_jwt_encode($aValue);
	$aPage['aVar'] = array(
		'nId'		=> $nId,

	);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$nErr = 0;
	$sErrMsg = '';
	$nCount = 0;

	#宣告結束

	#程式邏輯區

	$sSQL = '	SELECT	nId,
					nUid,
					sContent0,
					sCreateTime
			FROM	'.	CLIENT_DISCUSS .'
			WHERE		nId = :nId
			AND		nOnline != 99';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sContent0'] = convertContent($aRows['sContent0']);
		$aData = $aRows;
		$aData['aImgUrl'] = array();
		$aData['aReply'] = array();
		$aSearchId[$aRows['nUid']] = $aRows['nUid'];

		$nCount++;
	}
	if($nCount == 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}

	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_DISCUSS_REPLY.'
			WHERE nDid = :nDid
			AND 	nOnline != 99';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nDid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();

	$sSQL = '	SELECT	nId,
					nUid,
					nDid,
					sContent0,
					sCreateTime
			FROM	'.	CLIENT_DISCUSS_REPLY .'
			WHERE		nDid = :nDid
			AND		nOnline != 99
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nDid', $nId, PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aRows['sContent0'] = convertContent($aRows['sContent0']);
		$aData['aReply'][$aRows['nId']] = $aRows;
		$aData['aReply'][$aRows['nId']]['aImgUrl'] = array();
		$aData['aReply'][$aRows['nId']]['sContent0'] = $aRows['sContent0'];

		$aValue = array(
			'a'		=> 'DELREPLY'.$aRows['nId'],
			'nId'		=> $aRows['nDid'],
			'sBackParam'=> $aJWT['sBackParam'],
		);
		$LPsJWT = sys_jwt_encode($aValue);
		$aData['aReply'][$aRows['nId']]['sDelUrl'] = $aUrl['sAct'].'&sJWT='.$LPsJWT.'&nId='.$aRows['nId'];
		$aSearchId[$aRows['nUid']] = $aRows['nUid'];
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	nId IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows['sAccount'];
		}
	}

	if (!empty($aData))
	{
		$sKids =(!empty($aData['aReply']))?implode(',', array_keys($aData['aReply'])).','.$aData['nId'] : $aData['nId'];
		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN ( '.$sKids.' )
				AND 	sTable IN (\''.CLIENT_DISCUSS.'\', \''.CLIENT_DISCUSS_REPLY.'\')';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			if ($aRows['sTable'] == CLIENT_DISCUSS && $aRows['nKid'] == $aData['nId'])
			{
				$aData['aImgUrl'][$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}
			if ($aRows['sTable'] == CLIENT_DISCUSS_REPLY && isset($aData['aReply'][$aRows['nKid']]))
			{
				$aData['aReply'][$aRows['nKid']]['aImgUrl'][$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}
		}
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if($nErr == 1)
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
		$aJumpMsg['0']['aButton']['0']['sClass'] = ($nId == 0) ? 'submit' :'JqReplaceO';
		$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
		$aRequire['Require'] = $aUrl['sHtml'];
	}

	#輸出結束
?>
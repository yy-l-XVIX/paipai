<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_group.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sAccount	= filter_input_str('sAccount',	INPUT_REQUEST, '');
	$sName0	= filter_input_str('sName0',		INPUT_REQUEST, '');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sIns'	=> sys_web_encode($aMenuToNo['pages/client_chat/php/_client_user_group_0_upt0.php']),
		'sDel'	=> sys_web_encode($aMenuToNo['pages/client_chat/php/_client_user_group_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_chat/php/_client_user_group_0.php']),
		'sChat'	=> sys_web_encode($aMenuToNo['pages/client_chat/php/_client_chat_msg_0.php']),
		'sHtml'	=> 'pages/client_chat/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_group_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array(
		'aUid'=>array(),
		'aGid'=>array(),
	);
	$aMember = array();
	$aBind = array();
	$aPage['aVar'] = array(
		'sName0' => $sName0,
		'sAccount' => $sAccount,
	);
	$nCount = 0;
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

	#宣告結束

	#程式邏輯區
	if($sName0 != '')
	{
		$sCondition .= ' AND sName0 LIKE :sName0 ';
		$aBind['sName0'] = '%'.$sName0.'%';
	}
	if ($sAccount != '')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE sAccount LIKE :sAccount
				AND 	nOnline != 99';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sAccount.'%', PDO::PARAM_STR);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId['aUid'][$aRows['nId']] = $aRows['nId'];
		}
		if (!empty($aSearchId['aUid']))
		{
			$sCondition .= ' AND nUid IN ('.implode(',', $aSearchId['aUid']).')';
		}
	}
	$sSQL = '	SELECT	1
			FROM	'.CLIENT_GROUP_CTRL .'
			WHERE nType1 = 0
			AND 	nOnline != 99
			' . $sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$nCount ++;
	}
	$aPage['nDataAmount'] = $nCount;

	$sSQL = '	SELECT	nId,
					sName0,
					nUid,
					nType0,
					sCreateTime,
					sUpdateTime
			FROM	'.CLIENT_GROUP_CTRL .'
			WHERE nType1 = 0
			AND 	nOnline != 99
			' . $sCondition . '
			ORDER	BY	nId DESC '.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBind);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['nCount'] = 0;
		$aData[$aRows['nId']]['sIns'] = $aUrl['sIns'].'&nId='.$aRows['nId'];
		$aValue = array(
			'a'		=> 'DEL'.$aRows['nId'],
			't'		=> NOWTIME,
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$aRows['nId']]['sDel'] = $aUrl['sDel'].'&nId='.$aRows['nId'].'&sJWT='.$sJWT;
		$aData[$aRows['nId']]['sChat'] = $aUrl['sChat'].'&nId='.$aRows['nId'];

		$aSearchId['aGid'][$aRows['nId']] = $aRows['nId'];
		$aSearchId['aUid'][$aRows['nUid']] = $aRows['nUid'];
	}

	if (!empty($aSearchId['aUid']))
	{
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ( '.implode(',', $aSearchId['aUid']).' ) ';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMember[$aRows['nId']] = $aRows['sAccount'];
		}
	}
	if (!empty($aSearchId['aGid']))
	{
		$sSQL = '	SELECT 	nGid,
						nUid
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid IN ( '.implode(',', $aSearchId['aGid']).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nGid']]['nCount'] ++;
		}
	}
	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
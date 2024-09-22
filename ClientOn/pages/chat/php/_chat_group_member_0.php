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
	$nId 		= filter_input_int('nId',	INPUT_REQUEST, 0);
	$sName0 	= filter_input_str('sName0',	INPUT_REQUEST, '');
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_upt_0.php']).'&nGid='.$nId,
		'sPage'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_member_0.php']).'&nId='.$nId,
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sHtml'	=> 'pages/chat/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/chat_group_member_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aSearchId = array();
	$aBindArray = array();
	$nSumData = 0;
	$sCondition = '';
	$aValue = array(
		'sBackUrl'=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_member_0.php']).'&nId='.$nId,
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);
	#宣告結束

	#程式邏輯區
	if ($sName0 != '')
	{
		// 符合暱稱的會員id
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE sName0 LIKE :sName0
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sName0', '%'.$sName0.'%', PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}
		// 符合暱稱的好友uid
		$sSQL = '	SELECT 	nFUid
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE sName0 LIKE :sName0
				AND 	nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sName0', '%'.$sName0.'%', PDO::PARAM_STR);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nFUid']] = $aRows['nFUid'];
		}
		if (!empty($aSearchId))
		{
			$sCondition = ' AND nUid IN ( '.implode(',',$aSearchId).' )';
			$aSearchId = array();
		}
	}

	$sSQL = '	SELECT 	User_.nId,
					User_.nKid,
					User_.nStatus,
					User_.sName0
			FROM 	'.CLIENT_USER_DATA.' User_,
				'.CLIENT_USER_GROUP_LIST.' List_
			WHERE List_.nGid = :nGid
			'.$sCondition.'
			AND	List_.nUid = User_.nId';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sInfUrl'] = $aUrl['sInf'].'&nId='.$aRows['nId'];#'javascript:void(0)';
		$aData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
		$aData[$aRows['nId']]['sRole'] = '';
		if ($aRows['nKid'] == 1)
		{
			$aData[$aRows['nId']]['sRole'] = 'boss';
		}

		$aSearchId[$aRows['nId']] = $aRows['nId'];
	}

	if (!empty($aSearchId))
	{
		// 我的好友(更換為自行設定暱稱)
		$sSQL = '	SELECT	nFUid,
						sName0
				FROM	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nUid
				AND	sName0 != \'\'
				AND	nFUid IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nFUid']]['sName0'] = $aRows['sName0'];
		}

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

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
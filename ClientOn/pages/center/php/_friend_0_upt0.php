<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/center/friend.js',
	);

	#參數接收區
	$nFetch = filter_input_int('nFetch',INPUT_REQUEST,0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sInf'	=> sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
		'sFetch'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0_upt0.php']).'&nFetch=1',
		'sHtml'	=> 'pages/center/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/friend_0_upt0.php',
		'sAct'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0_act0.php']).'&run_page=1',
	);
	#url結束

	#參數宣告區
	// $aPage['nPageSize'] = 4;
	$aData = array();
	$aReturn = array();
	$aSearchId = array();
	$aMemberData = array();
	$aBlock = array(0=>0);
	$aValue = array(
		'a'		=> 'DELFRIEND'.$aUser['nId'],
		't'		=> NOWTIME,
	);
	$sJWT = sys_jwt_encode($aValue);
	$aUrl['sAct'] .= '&sJWT='.$sJWT;
	$aValue = array(
		'sBackUrl'=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0_upt0.php']),
	);
	$aUrl['sInf'] .= '&sJWT='.sys_jwt_encode($aValue);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	#宣告結束

	#程式邏輯區
	// 封鎖名單不顯示
	$sSQL = '	SELECT	nBUid
			FROM		'.CLIENT_USER_BLOCK.'
			WHERE		nUid = :nUid';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aBlock[$aRows['nBUid']] = $aRows['nBUid'];
	}

	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_USER_FRIEND.'
			WHERE nUid = :nUid
			AND 	nStatus = 1
			AND 	nFUid NOT IN ( '.implode(',', $aBlock).' )';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	$aPage['nDataAmount'] = $Result->rowCount();
	$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
	if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
	{
		$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
	}

	$sSQL = '	SELECT 	nId,
					nFUid,
					nStatus
			FROM 	'.CLIENT_USER_FRIEND.'
			WHERE nUid = :nUid
			AND 	nStatus = 1
			AND 	nFUid NOT IN ( '.implode(',', $aBlock).' )
			ORDER BY nStatus ASC, nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aData[$aRows['nId']] = $aRows;
		$aSearchId[$aRows['nFUid']] = $aRows['nFUid'];
	}

	if (!empty($aSearchId))
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						nKid,
						nStatus
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	nId IN ( '.implode(',', $aSearchId).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
			$aMemberData[$aRows['nId']]['sUserInfoUrl'] =  $aUrl['sInf'].'&nId='.$aRows['nId'];#'javascript:void(0)';
			$aMemberData[$aRows['nId']]['sHeadImage'] = DEFAULTHEADIMG;
			$aMemberData[$aRows['nId']]['sRoleClass'] = '';
			$aMemberData[$aRows['nId']]['sStatusClass'] = '';

			# 雇主才可以看會員資料
			// if ($sUserCurrentRole == 'boss')
			// {
			// 	$aMemberData[$aRows['nId']]['sUserInfoUrl'] = $aUrl['sInf'].'&nId='.$aRows['nId'];
			// }
			// 雇主和人才顏色不同
			if($aRows['nKid'] == 1)
			{
				$aMemberData[$aRows['nId']]['sRoleClass'] = 'boss';
			}
			// 上班下班
			if($aRows['nKid'] == 3)
			{
				$sTempClass = '';

				if($aRows['nStatus'] == 2)
				{
					$sTempClass = 'off';
				}
				if($aRows['nStatus'] == 3)
				{
					$sTempClass = 'ing';
				}

				$aMemberData[$aRows['nId']]['sStatusClass'] = '<div class="selfieStatus '.$sTempClass.'"></div>';
			}
		}
		#頭
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
			$aMemberData[$aRows['nKid']]['sHeadImage'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	if ($nFetch == 1)
	{
		foreach ($aData as $LPnId => $LPaFriend)
		{
			$LPaFriend['sName0'] 		= $aMemberData[$LPaFriend['nFUid']]['sName0'];
			$LPaFriend['nKid'] 		= $aMemberData[$LPaFriend['nFUid']]['nKid'];
			$LPaFriend['nUserStatus'] 	= $aMemberData[$LPaFriend['nFUid']]['nStatus'];
			$LPaFriend['sUserInfoUrl'] 	= $aMemberData[$LPaFriend['nFUid']]['sUserInfoUrl'];
			$LPaFriend['sHeadImage'] 	= $aMemberData[$LPaFriend['nFUid']]['sHeadImage'];
			$LPaFriend['sRoleClass'] 	= $aMemberData[$LPaFriend['nFUid']]['sRoleClass'];
			$LPaFriend['sStatusClass'] 	= $aMemberData[$LPaFriend['nFUid']]['sStatusClass'];

			$aReturn['aData'][$LPaFriend['nStatus']][] = $LPaFriend;
		}
		$aReturn['nStatus'] = 1;
		$aReturn['nDataTotal'] = $aPage['nTotal'];

		echo json_encode($aReturn);
		exit;
	}
	#輸出結束
?>
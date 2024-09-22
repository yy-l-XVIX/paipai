<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	#require end

	#參數接收區
	$nId		= filter_input_int('nId',		INPUT_REQUEST,0);
	$nLid		= filter_input_int('nLid',		INPUT_REQUEST,0); # client_user_group_list nId
	$sAccount	= filter_input_int('sAccount',	INPUT_POST, 1);
	$sName0	= filter_input_str('sName0',		INPUT_POST, '',50);
	#參數結束

	#參數宣告區
	$aData = array();
	$aMember = array();
	$aEditLog = array(
		CLIENT_USER_GROUP	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_chat/php/_client_user_group_0.php']);
	#宣告結束

	#程式邏輯區

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nUid,
						sName0,
						nUpdateTime,
						sUpdateTime
				FROM 	'. CLIENT_GROUP_CTRL .'
				WHERE nId = :nId
				AND 	nOnline != 99
				AND 	nType1 = 0
				LIMIT	1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$nErr = 1;
			$sMsg = NODATA;
		}
		if ($sName0 == '')
		{
			$nErr = 1;
			$sMsg = aJOBTYPE['NAMEERROR'].'<br>';
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_GROUP_CTRL . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			#紀錄動作 - 更新
			$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aData;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew']['nId'] = $nId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8106002,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = UPTV;
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT	nId,
						sName0,
						nUid,
						nType0
				FROM	'.	CLIENT_GROUP_CTRL .'
				WHERE		nId = :nId
				AND 	nOnline != 99
				AND 	nType1 = 0
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$nErr = 1;
			$sMsg = NODATA;
		}

		$sSQL = '	SELECT	nId,
						nGid,
						nUid,
						nStatus
				FROM	'.	CLIENT_USER_GROUP_LIST .'
				WHERE		nGid = :nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMember[$aRows['nId']] = $aRows;
		}

		if ($nErr == 0)
		{
			// update client_group_ctrl to 99
			$aSQL_Array = array(
				'nOnline' 		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_GROUP_CTRL.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aData;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;


			$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
					WHERE nGid = :nGid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid', $nId, PDO::PARAM_INT);
			sql_query($Result);


			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = $aMember;
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = array();


			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8106003,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = DELV;
		}
	}

	if ($aJWT['a'] == 'DELMEMBER'.$nLid)
	{
		$sSQL = '	SELECT 	nId,
						nGid,
						nUid,
						nStatus
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nLid,PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$nErr = 1;
			$sMsg = NODATA;
		}

		if ($nErr == 0)
		{

			$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
					WHERE nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nLid, PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = $aData;
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = array();

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $aData['nUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8106004,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = DELV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_chat/php/_client_user_group_0_upt0.php']).'&nId='.$nId;
		}
	}
	#程式邏輯結束

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
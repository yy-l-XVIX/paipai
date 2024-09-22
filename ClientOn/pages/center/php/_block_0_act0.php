<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');

	$nId	= filter_input_int('nId',		INPUT_GET, 0);
	/**
	 * 回傳陣列 JSON
	 * @var Int nStatus
	 * 	回傳狀態值
	 * 	1 => 正常 其餘待補
	 * @var String sMsg
	 * 	回傳訊息
	 * @var Array aData
	 * 	回傳陣列
	 * @var Int nAlertType
	 * 	回傳訊息提示類型
	 * 	0 => 不需提示框
	 * @var String sUrl
	 * 	回傳後導頁檔案
	 */
	$aReturn = array(
		'nStatus'		=> 0,
		'sMsg'		=> 'Error',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);

	// 解封鎖
	if ($aJWT['a'] == 'DELBLOCK'.$aUser['nId'])
	{
		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_block_0_upt0.php']);

		// 查是否已經在封鎖名單
		$sSQL = '	SELECT	nId,
						nUid,
						nBUid
				FROM		'.CLIENT_USER_BLOCK.'
				WHERE		nUid = :nUid
				AND		nId = :nId
				LIMIT		1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['sMsg'] = NODATA;
		}
		else
		{
			$aOld = $aRows;
			$sSQL = '	DELETE FROM	'. CLIENT_USER_BLOCK . '
					WHERE 	nId = :nId
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aOld['nId'], PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_USER_BLOCK]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_BLOCK]['aNew'] = array();

			$sSQL = '	SELECT 	1
					FROM 	'. CLIENT_USER_BLOCK . '
					WHERE (nUid = :nUid AND nBUid = :nBUid)
					OR 	(nUid = :nBUid AND nBUid = :nUid)';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			$Result->bindValue(':nBUid', $aOld['nBUid'], PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false) //(需要雙方都解除封鎖)
			{

				$sSQL = '	SELECT 	nId
						FROM 	'.CLIENT_GROUP_CTRL.'
						WHERE nType0 = 0
						AND	nType1 = 0
						AND 	nOnline > 0 AND nOnline < 99
						AND 	((nUid = :nUid AND nTargetUid = :nBUid)
						OR 	(nUid = :nBUid AND nTargetUid = :nUid))
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				$Result->bindValue(':nBUid', $aOld['nBUid'], PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);


				// 私聊群組 nOnline 2 => 1
				// $sSQL = '	SELECT	Group_.nId
				// 		FROM	'.CLIENT_GROUP_CTRL.' Group_,
				// 			'.CLIENT_USER_GROUP_LIST.' List_
				// 		WHERE	Group_.nType0 = 0
				// 		AND	Group_.nType1 = 0
				// 		AND 	Group_.nOnline = 2
				// 		AND	( List_.nUid = :nUid OR List_.nUid = :nBUid )
				// 		AND	Group_.nId = List_.nGid
				// 		GROUP BY List_.nGid
				// 		HAVING count(List_.nGid) = 2';
				// $Result = $oPdo->prepare($sSQL);
				// $Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
				// $Result->bindValue(':nBUid', $aOld['nBUid'], PDO::PARAM_INT);
				// sql_query($Result);
				// $aRows = $Result->fetch(PDO::FETCH_ASSOC);
				if ($aRows !== false)
				{
					$aSQL_Array = array(
						'nOnline'		=> (int) 1,
						'sUpdateTime'	=> (string) NOWDATE,
						'nUpdateTime'	=> (int) NOWTIME,
					);
					$sSQL = '	UPDATE '.CLIENT_GROUP_CTRL.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE	nId = :nId LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);

					$aEditLog[CLIENT_GROUP_CTRL]['aOld'][$aRows['nId']] = $aRows;
					$aEditLog[CLIENT_GROUP_CTRL]['aNew'][$aRows['nId']] = $aSQL_Array;
				}
			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aOld['nBUid'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100102,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = DELV;
		}
		echo json_encode($aReturn);
		exit;
	}

?>
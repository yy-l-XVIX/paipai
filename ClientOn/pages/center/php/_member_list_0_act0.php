<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');

	$nId	= filter_input_int('nId',	INPUT_GET, 0);
	$nUid	= filter_input_int('nUid',	INPUT_GET, 0);
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
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);

	// 聊天
	if ($aJWT['a'] == 'CHECKCHAT')
	{
		$sSQL = '	SELECT 	1
				FROM 	'.CLIENT_USER_FRIEND.'
				WHERE nUid = :nUid
				AND 	nFUid = :nFUid
				AND 	nStatus != 2';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nFUid', $nUid, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			if ($sUserCurrentRole == 'boss') // 雇主才可以看info
			{
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']).'&nId='.$nUid;
			}
		}

		if ($aReturn['nStatus'] == 1)
		{

			$sSQL = '	SELECT	Group_.nId
					FROM	'.CLIENT_GROUP_CTRL.' Group_,
						'.CLIENT_USER_GROUP_LIST.' List_
					WHERE	Group_.nType0 = 0
					AND	( List_.nUid = :nUid OR List_.nUid = :nFUid )
					AND	Group_.nId = List_.nGid
					GROUP BY List_.nGid
					HAVING count(List_.nGid) = 2';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			$Result->bindValue(':nFUid', $nUid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$sSQL = '	SELECT 	sName0
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId = :nId
						AND 	nOnline = 1
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nUid, PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);

				# 沒聊過 建新群
				$aSQL_Array = array(
					'sName0'		=> (string) $aUser['sName0'].','.$aRows['sName0'],
					'nUid'		=> (int) $aUser['nId'],
					'nOnline'		=> (int) 1,
					'nType0'		=> (int) 0,
					'nType1'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	INSERT INTO '.CLIENT_GROUP_CTRL.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);
				$nGid = $oPdo->lastInsertId();

				$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = array();
				$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_GROUP_CTRL]['aNew']['nId'] = $nGid;

				$aSQL_Array = array(
					'nGid'		=> (int) $nGid,
					'nUid'		=> (int) $aUser['nId'],
					'nStatus'		=> (int) 1,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	INSERT INTO '.CLIENT_USER_GROUP_LIST.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = array();
				$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'][] = $aSQL_Array;

				$aSQL_Array = array(
					'nGid'		=> (int) $nGid,
					'nUid'		=> (int) $nUid,
					'nStatus'		=> (int) 1,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	INSERT INTO '.CLIENT_USER_GROUP_LIST.' '.sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result,$aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'][] = $aSQL_Array;

				$aActionLog = array(
					'nWho'		=> (int) $aUser['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nGid,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 7100006,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

			}
			else
			{
				# 已聊過 進群
				$nGid = $aRows['nId'];
			}

			$aReturn['nStatus'] = 1;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/chat/php/_chat_0.php']).'&nGid='.$nGid;
		}

		echo json_encode($aReturn);
		exit;
	}
?>
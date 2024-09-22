<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');

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
		'sMsg'		=> '',
		'aData'		=> array(
			'aGroup' => array(),
			'aActive'=> array(
				'job'=>0,
				'chat'=>0,
			),
			'nReload'=> 0,
		),
		'nAlertType'	=> 1,
		'sUrl'		=> '',
	);

	$aType1 = array(
		'0'=> 'chat',
		'1'=> 'job',
	);
	$aData = array();
	$aCookie = array();
	$aUpdateGLid = array();
	if ($aJWT['a'] == 'CHECKMESSAGE')
	{
		if (isset($_COOKIE['aGroup']) && !empty($_COOKIE['aGroup']))
		{
			$aCookie = $_COOKIE['aGroup'];
		}

		// 我的群組(聊天群)
		$sSQL = '	SELECT 	Group_.nId,
						Group_.nType1,
						List_.nId as nGLid,
						List_.nStatus,
						List_.nUpdateTime,
						List_.nCreateTime
				FROM 	'.CLIENT_GROUP_CTRL.' Group_,
					'.CLIENT_USER_GROUP_LIST.' List_
				WHERE Group_.nOnline = 1
				AND 	List_.nStatus IN (1,2)
				AND 	List_.nUid = :nUid
				AND 	Group_.nId = List_.nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', 	$aUser['nId'], 	PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData[$aRows['nId']] = $aRows;
		}
		if (!empty($aData))
		{
			// 群組訊息(不是自己講的)
			$sSQL = '	SELECT 	nGid,
							max(nId) as nId,
							max(nCreateTime) as nCreateTime
					FROM 	'.CLIENT_GROUP_MSG.'
					WHERE nTargetUid IN ( 0,'.$aUser['nId'].')
					AND 	nGid IN ( '.implode(',', array_keys($aData)).' )
					AND 	nUid != :nUid
					GROUP BY nGid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', 	$aUser['nId'], 	PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				if ($aRows['nCreateTime'] < $aData[$aRows['nGid']]['nCreateTime']) // 訊息比自己加入時間早 就不提試
				{
					continue;
				}
				if (	$aData[$aRows['nGid']]['nStatus'] == 2 )
				{
					$aUpdateGLid[$aData[$aRows['nGid']]['nGLid']] = $aData[$aRows['nGid']]['nGLid'];
				}
				if (!isset($aCookie[$aRows['nGid']]))
				{
					$aReturn['aData']['aGroup'][$aRows['nGid']] = $aData[$aRows['nGid']];
					$aReturn['aData']['aActive'][$aType1[$aData[$aRows['nGid']]['nType1']]]++;
				}
				elseif ($aCookie[$aRows['nGid']] < $aRows['nId'])
				{
					$aReturn['aData']['aGroup'][$aRows['nGid']] = $aData[$aRows['nGid']];
					$aReturn['aData']['aActive'][$aType1[$aData[$aRows['nGid']]['nType1']]]++;
				}
			}
		}
		// error_log(print_r($aUpdateGLid,true));
		if (!empty($aUpdateGLid))
		{
			// 有接收新訊息 解除封鎖 (nStatus 2 => 1)
			$aSQL_Array = array(
				'nStatus'		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '.CLIENT_USER_GROUP_LIST.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId IN ( '.implode(',',$aUpdateGLid).' ) AND nStatus = 2';
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			if ($aJWT['sThisPage'] == 'chat_group_0') // 群組內偵測到 reload
			{
				$aReturn['aData']['nReload'] = 1;
			}
		}

		$aReturn['nStatus'] = 1;
	}

	echo json_encode($aReturn);
	exit;
?>
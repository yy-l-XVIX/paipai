<?php
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');

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
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);
	$nLogCode = 0;

	if ($aJWT['a'] == 'CHANGEWORK')
	{
		$oPdo->beginTransaction();
		$sSQL = '	SELECT 	nId,
						nStatus
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId
				AND 	nStatus != 11
				AND 	nOnline = 1
				LIMIT 1 FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows !== false)
		{
			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;

			$aSQL_Array = array(
				'nStatus'		=> (int) $aJWT['nStatus'],
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '.CLIENT_USER_DATA.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			switch ($aJWT['nStatus'])
			{
				case '1':
					$aReturn['aData']['sClass'] = 'ing';
					$nLogCode = 7100303;
					break;
				case '2':
					$aReturn['aData']['sClass'] = 'off';
					$nLogCode = 7100304;
					break;
				case '3':
					$aReturn['aData']['sClass'] = '';
					$nLogCode = 7100305;
					break;
				default:
					$aReturn['aData']['sClass'] = '';
					break;
			}

			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aUser['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) $nLogCode,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = UPTV;
		}
		else
		{
			$aReturn['nStatus'] = 1;
			$aReturn['sMsg'] = NODATA;
		}
		$oPdo->commit();
	}

	echo json_encode($aReturn);
	exit;
?>
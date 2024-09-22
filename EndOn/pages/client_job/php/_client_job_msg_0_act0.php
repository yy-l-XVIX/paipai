<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	// require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_group.php');
	#require end

	#參數接收區
	$nId		= filter_input_int('nId',		INPUT_REQUEST,0);
	$nJid		= filter_input_int('nJid',		INPUT_REQUEST,0);# group id

	#參數結束

	#參數宣告區
	$aData = array();
	$aEditLog = array(
		CLIENT_JOB_MSG	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_msg_0.php']).'&nId='.$nJid;
	#宣告結束

	#程式邏輯區

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT	nId,
						nJid,
						nUid,
						sMsg,
						nType0,
						nOnline
				FROM	'.CLIENT_GROUP_MSG.'
				WHERE	nId = :nId
				AND 	nOnline != 99
				LIMIT	1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aData === false)
		{
			$nErr = 1;
			$sMsg = NODATA;
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
			);

			$sSQL = '	UPDATE '. CLIENT_GROUP_MSG . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_GROUP_MSG]['aOld'] = $aData;
			$aEditLog[CLIENT_GROUP_MSG]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_GROUP_MSG]['aNew']['nId'] = $nId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105304,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = DELV;
		}
	}

	#程式邏輯結束

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
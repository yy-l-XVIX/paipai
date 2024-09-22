<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/google.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Plugins/GoogleAuthenticator/googleClass.php');

	$sCode = filter_input_str('sCode', INPUT_GET,'');

	$oGg 	= new PHPGangsta_GoogleAuthenticator;

	$aEditLog = array(
		SYS_GOOGLE_VERIFY => array(
			'aOld' =>array(),
			'aNew' =>array(),
		),
	);
	if ($aJWT['a'] == 'QRCODE')
	{
		# 檢查過期
		if ($aJWT['nExpire'] < NOWTIME)
		{
			echo aGOOGLE['EXPIRED'];
			exit;
		}

		$sSQL = '	SELECT 	Manager_.nId,
						Manager_.sAccount,
						Google_.sKey
				FROM 	'.END_MANAGER_DATA.' Manager_,
					'.SYS_GOOGLE_VERIFY.' Google_
				WHERE Manager_.nId = :nId
				AND 	Google_.sTable LIKE :sTable
				AND 	Manager_.nId = Google_.nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aJWT['nUid'], PDO::PARAM_INT);
		$Result->bindValue(':sTable', END_MANAGER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);

		$name = $aSystem['sTitle'];
		$secret = $aRows['sKey'];
		$title = ACCOUNT.' ['.$aRows['sAccount'].' ] ';
 		$urlencoded = 'otpauth://totp/'.$name.'?secret='.$secret.'&issuer='.$title;

		// $sQrcode = 'otpauth://totp/ ?secret='.$aRows['sKey'].'&issuer='.urlencode($aSystem['sTitle'].' ['.$aRows['sAccount'].' ] ');
		header('Location:'. $urlencoded);
		exit;
	}

	if ($aJWT['a'] == 'VERIFY')
	{
		$sSQL = '	SELECT 	nId,
						nUid,
						nStatus,
						sKey
				FROM 	'.SYS_GOOGLE_VERIFY.'
				WHERE nStatus = 0
				AND 	sTable = :sTable
				AND 	nUid = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aJWT['nId'], PDO::PARAM_INT);
		$Result->bindValue(':sTable', END_MANAGER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);

		if ($aRows === false)
		{
			$aJumpMsg['0']['sMsg'] = NODATA;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$bVerify = $oGg->verifyCode($aRows['sKey'], $sCode);
			if ($bVerify)
			{
				$aEditLog[SYS_GOOGLE_VERIFY]['aOld'] = $aRows;

				$aSQL_Array = array(
					'nStatus'		=> (int) 1,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	UPDATE '.SYS_GOOGLE_VERIFY.'
						SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE nId = :nId
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[SYS_GOOGLE_VERIFY]['aNew'] = $aSQL_Array;
				$aActionLog = array(
					'nWho'		=> (int) $aJWT['nAdmin'],
					'nWhom'		=> (int) $aRows['nUid'],
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $aRows['nId'],
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8101107,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);
				$sMsg = aGOOGLE['VERIFIED'];
			}
			else
			{
				$sMsg = aGOOGLE['FAILD'];
			}

			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_manager_data/php/_end_manager_data_0_upt0.php']).'&nId='.$aJWT['nId'];
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

?>
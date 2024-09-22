<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_discuss.php');
	#require結束

	#參數接收區
	$nId		= filter_input_int('nId',		INPUT_REQUEST,0);
	$nRid		= filter_input_int('nRid',		INPUT_REQUEST,0); # 回覆id
	$sAccount	= filter_input_str('sAccount',	INPUT_POST,'',20);
	$sContent0 	= isset($_POST['sContent0']) ? nl2br($_POST['sContent0']) : '';
	# img 標籤轉換代號 <img src="images/emoji/01.png"> => [:01:] (?)
	$sContent0 = str_replace('<img class="EmojiImgIcon" src="images/emoji/', '[:', $sContent0);
	$sContent0 = str_replace('.png">', ':]', $sContent0);

	#參數結束

	#參數宣告區
	$aData = array();
	$aEditLog = array();
	$nErr = 0;
	$sMsg = '';
	$aValue = array(
		'sBackParam' => $aJWT['sBackParam'],
	);
	$sBackParamJWT = sys_jwt_encode($aValue);
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0_upt0.php']).'&sJWT='.$sBackParamJWT;
	// $sBackUrl = sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0.php']);
	#宣告結束

	#程式邏輯區
	// if ($aJWT['a'] == 'INS')
	// {
	// 	if ($sAccount == '')
	// 	{
	// 		$nErr = 1;
	// 		$sMsg .= aERROR['ACCOUNT'].'<div class="MarginBottom10"></div>';
	// 	}
	// 	else
	// 	{
	// 		$sSQL = '	SELECT 	nId
	// 				FROM 	'.CLIENT_USER_DATA.'
	// 				WHERE nOnline = 1
	// 				AND 	sAccount LIKE :sAccount
	// 				LIMIT 1';
	// 		$Result = $oPdo->prepare($sSQL);
	// 		$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
	// 		sql_query($Result);
	// 		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	// 		if ($aRows === false)
	// 		{
	// 			$nErr = 1;
	// 			$sMsg .= aERROR['NOUSER'].'<div class="MarginBottom10"></div>';
	// 		}
	// 		$nUid = $aRows['nId'];
	// 	}
	// 	if ($sContent0 == '')
	// 	{
	// 		$nErr = 1;
	// 		$sMsg .= aERROR['CONTENT0'].'<div class="MarginBottom10"></div>';
	// 	}

	// 	if ($nErr == 0)
	// 	{
	// 		$aSQL_Array = array(
	// 			'nUid' 		=> (int) $nUid,
	// 			'nOnline' 		=> (int) 1,
	// 			'sContent0' 	=> (string) $sContent0,
	// 			'nCreateTime'	=> (int) NOWTIME,
	// 			'sCreateTime'	=> (string) NOWDATE,
	// 		);

	// 		$sSQL = 'INSERT INTO '.CLIENT_DISCUSS.' ' . sql_build_array('INSERT', $aSQL_Array );
	// 		$Result = $oPdo->prepare($sSQL);
	// 		sql_build_value($Result, $aSQL_Array);
	// 		sql_query($Result);
	// 		$nLastId = $oPdo->lastInsertId();

	// 		$aEditLog[CLIENT_DISCUSS]['aOld'] = array();
	// 		$aEditLog[CLIENT_DISCUSS]['aNew'] = $aSQL_Array;
	// 		$aEditLog[CLIENT_DISCUSS]['aNew']['nId'] = $nLastId;

	// 		$aActionLog = array(
	// 			'nWho'		=> (int) $aAdm['nId'],
	// 			'nWhom'		=> (int) 0,
	// 			'sWhomAccount'	=> (string) '',
	// 			'nKid'		=> (int) $nLastId,
	// 			'sIp'			=> (string) USERIP,
	// 			'nLogCode'		=> (int) 8104001,
	// 			'sParam'		=> (string) json_encode($aEditLog),
	// 			'nType0'		=> (int) 0,
	// 			'nCreateTime'	=> (int) NOWTIME,
	// 			'sCreateTime'	=> (string) NOWDATE,
	// 		);
	// 		DoActionLog($aActionLog);

	// 		$sMsg = INSV;
	// 	}
	// 	else
	// 	{
	// 		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0_upt0.php']);
	// 	}
	// }

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		# 8104002

	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nUid,
						sContent0
				FROM 		'.CLIENT_DISCUSS.'
				WHERE 	nOnline != 99
				AND 		nId = :nId
				LIMIT 1';
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
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_DISCUSS . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId
					AND 	nOnline != 99';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_DISCUSS]['aOld'] = $aData;
			$aEditLog[CLIENT_DISCUSS]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_DISCUSS]['aNew']['nId'] = $nId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8104003,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$sMsg = DELV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0.php']).$aJWT['sBackParam'];
		}
	}

	if ($aJWT['a'] == 'DELREPLY'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nUid,
						sContent0
				FROM 		'.CLIENT_DISCUSS_REPLY.'
				WHERE 	nOnline != 99
				AND 		nId = :nId
				LIMIT 1';
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
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = '	UPDATE '. CLIENT_DISCUSS_REPLY . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId
					AND 	nOnline != 99';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_DISCUSS_REPLY]['aOld'] = $aData;
			$aEditLog[CLIENT_DISCUSS_REPLY]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_DISCUSS_REPLY]['aNew']['nId'] = $nId;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8104004,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$sMsg = DELV;
		}
		# 返回討論區
		$sBackUrl = sys_web_encode($aMenuToNo['pages/client_discuss/php/_client_discuss_0_upt0.php']).'&nId='.$aJWT['nId'].'&sJWT='.$sBackParamJWT;;
	}
	#程式邏輯結束

	# Jumpmsg
	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
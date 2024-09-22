<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/System/Connect/cDataEncrypt.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_review.php');

	$nId		= filter_input_int('nId',		INPUT_REQUEST, 0);
	$nImgKid	= filter_input_int('nImgKid',		INPUT_REQUEST,0);
	$nType3	= filter_input_int('nType3',		INPUT_POST, 0);
	$aPending = isset($_POST['aPending']) ? $_POST['aPending'] : array();

	$sPendingStatus = '';
	$nPendingStatus = 0;
	$aPendingField = explode(',', $aSystem['aParam']['sPendingField']);	// 需要審核欄位
	$nCount = 0;
	$nErr = 0;
	$sMsg = '';
	$aValue = array(
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sBackParamJWT = sys_jwt_encode($aValue);
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_review_0.php']).$aJWT['sBackParam'];

	if ($aJWT['a'] == 'UPT'.$nId) #8103105
	{
		$sSQL = '	SELECT 	nId,
						sAccount,
						nStatus,
						sPendingStatus
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId
				AND	nOnline != 99
				LIMIT 1 ';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr	= 1;
			$sMsg	= NODATA.'<br>';
		}
		elseif ($aRows['nStatus'] != 11)
		{
			$nErr	= 1;
			$sMsg	= aREVIEW['AUDITED'].'<br>';
		}
		if (empty($aPending) || count($aPending) != count($aPendingField))
		{
			$nErr	= 1;
			$sMsg	= aREVIEW['PENDINGITEMS'].'<br>';
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_review_0_upt0.php']).'&sJWT='.$sBackParamJWT.'&nId='.$nId;
		}
		else
		{
			foreach ($aPending as $LPnStatus)
			{
				$LPnStatus = (int)$LPnStatus;
				$sPendingStatus .= $LPnStatus.',';
				if ($LPnStatus == 1)
				{
					$nCount += $LPnStatus;
				}
			}
			$sPendingStatus = trim($sPendingStatus,',');
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'nPendingStatus'	=> (int) 1, // 已審核
				'sPendingStatus'	=> (string) $sPendingStatus,
				'nUpdateTime'	=> (int)NOWTIME,
				'sUpdateTime'	=> (string)NOWDATE,
			);

			if ($nCount == count($aPending)) // 全部都通過就開通會員
			{
				$aSQL_Array['nStatus'] = 0;
			}

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			$sSQL = '	UPDATE	'.CLIENT_USER_DATA.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 更新
			$aSQL_Array = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) $nId,
				'sWhomAccount'	=> (string) $aRows['sAccount'],
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8103105,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aSQL_Array);

			$sMsg	= aREVIEW['PENDINGSUCCESS'].'<br>';
		}
	}

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
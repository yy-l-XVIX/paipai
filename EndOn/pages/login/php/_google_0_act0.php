<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/google.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Plugins/GoogleAuthenticator/googleClass.php');

	$sGoogle	= filter_input_str('sGoogle',		INPUT_POST, '', 16);
	$nExpire 	= filter_input_int('nExpire', 	INPUT_GET,0);
	$nUid 	= filter_input_int('nUid', 		INPUT_GET,0);

	$oGg 	= new PHPGangsta_GoogleAuthenticator;
	#登入驗證
	if ($aJWT['a'] == 'VERIFY')
	{
		$sSQL = '	SELECT 	nStatus,
						sKey
				FROM 	'.SYS_GOOGLE_VERIFY.'
				WHERE nUid = :nUid
				AND 	sTable = :sTable
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aAdm['nId'], PDO::PARAM_INT);
		$Result->bindValue(':sTable', END_MANAGER_DATA, PDO::PARAM_STR);
		sql_query($Result);
		$aVerify = $Result->fetch(PDO::FETCH_ASSOC);

		if ($aVerify['nStatus'] == 0)
		{
			$aJumpMsg['0']['sMsg'] =  aGOOGLE['STATUSERROR'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$bVerify = $oGg->verifyCode($aVerify['sKey'], $sGoogle);
			if ($bVerify)
			{
				$aSQL_Array = array(
					'nGoogle'		=> (int) 1,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	UPDATE	'.END_MANAGER_COOKIE.'
						SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
						WHERE	nUid = :nUid
						AND 	sSid LIKE :sSid
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid', $aAdm['nId'], PDO::PARAM_INT);
				$Result->bindValue(':sSid', $_COOKIE['sSid'], PDO::PARAM_STR);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aJumpMsg['0']['sMsg'] = aGOOGLE['VERIFIED'];
				$aJumpMsg['0']['sShow'] = 1;
				$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
				$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			}
			else
			{
				$aJumpMsg['0']['sMsg'] =  aGOOGLE['FAILD'];
				$aJumpMsg['0']['sShow'] = 1;
				$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
				$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			}
		}
	}

?>
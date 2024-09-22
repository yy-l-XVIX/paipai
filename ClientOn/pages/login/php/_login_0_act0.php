<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/login.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');

	$nKid		= filter_input_int('nKid',		INPUT_POST, 0);
	$sAccount	= filter_input_str('sAccount',	INPUT_POST, '', 16);
	$sPassword	= filter_input_str('sPassword',	INPUT_POST, '', 32);
	$sPassword = trim($sPassword); // 去除空白
	$sPassword = oCypher::ReHash($sPassword);
	$oUser = new oUser();

	$nRemember	= 0;
	$sMsg = '';
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
	if(isset($_POST['nRemember']))
	{
		$nRemember = 1;
	}
	if($aSystem['aParam']['bMaintenance'] == 1 || isset($_COOKIE['nJump']) && $_COOKIE['nJump'] == 1)
	{

		if ($aJWT['a'] == 'LOGIN') #登入
		{
			$nLogin = 0;
			$nUid = 0;
			$sSQL = '	SELECT 	nId,
							nForgotTime
					FROM 	'.CLIENT_USER_DATA.'
					WHERE sKid LIKE :sKid
					AND 	sAccount = :sAccount
					AND	nOnline = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount',	$sAccount, PDO::PARAM_STR);
			$Result->bindValue(':sKid',		'%'.$nKid.'%', PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false)
			{
				if ($aRows['nForgotTime'] > 0 && (($aRows['nForgotTime']+$aSystem['aParam']['nPasswordExpired']) < NOWTIME)) // 臨時密碼已過期
				{
					$aReturn['sMsg'] = aLOGIN['PASSWORDEXPIRED']; //臨時登入密碼已失效，請重新申請
					$aReturn['nStatus'] = $nLogin;
					$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/forgot/php/_forgot_0.php']);
					echo json_encode($aReturn);
					exit;
				}
				$aData = array(
					'sAccount'	=> $sAccount,
					'sPassword'	=> $sPassword,
					'nRemember'	=> $nRemember,
					'nKid'	=> $nKid,
				);

				$nLogin = $oUser->login($aData);
				$nUid = $aRows['nId'];
			}


			if ($nLogin == 1)
			{
				$aSQL_Array = array(
					'nKid' => (int) $nKid,
				);

				$sSQL = '	UPDATE	'.CLIENT_USER_DATA.'
						SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
						WHERE	nId = :nId
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nUid, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aReturn['sMsg'] = aLOGIN['LOGINV'];
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);

				$sSQL = '	SELECT 	nForgotTime,
								nStatus,
								nKid,
								nExpired0,
								nExpired1
						FROM 	'.CLIENT_USER_DATA.'
						WHERE nId = :nId';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId',	$nUid, PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);
				if ($aRows['nForgotTime'] != 0) //臨時密碼登入成功 導頁至修改密碼
				{
					$aReturn['sMsg'] = aLOGIN['TEMPPASSWORD']; // 您目前使用臨時密碼登入, 請更新您的密碼
					$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_change_pwd_0.php']);
				}
				// elseif ($aRows['nStatus'] < 11) // 審核通過
				// {
				// 	$aValue = array(
				// 		'nKid'	=> $nKid,
				// 	);
				// 	if ($nKid == 1 && $aRows['nExpired1'] == 0) // 雇主
				// 	{
				// 		$aReturn['sMsg'] = aLOGIN['GOPAY']; // 會員帳戶審核通過 請前往付款
				// 		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/recharge/php/_recharge_0.php']).'&sJWT='.sys_jwt_encode($aValue);
				// 	}
				// 	if ($nKid == 3 && $aRows['nExpired0'] == 0) //人才
				// 	{
				// 		$aReturn['sMsg'] = aLOGIN['GOPAY']; // 會員帳戶審核通過 請前往付款
				// 		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/recharge/php/_recharge_0.php']).'&sJWT='.sys_jwt_encode($aValue);
				// 	}
				// }
			}
			else
			{
				$aReturn['sMsg'] = aERROR[$nLogin];
			}
			$aReturn['nStatus'] = $nLogin;
			echo json_encode($aReturn);
			exit;
		}
		else if ($aJWT['a'] == 'LOGOUT') #登出
		{
			$aData = array(
				'sAccount'	=> $aJWT['sAccount'],
			);
			$oUser->logout($aData);

			$sMsg = aLOGIN['LOGOUTV'];
			if ($aJWT['nStatus'] == 1)
			{
				$sMsg = aLOGIN['LOGINDISABLE'];
			}

			$aJumpMsg['0']['sMsg'] = $sMsg ;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/login/php/_login_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
	else
	{
		$aJumpMsg['0']['sMsg'] = MAINTENANCE;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
?>
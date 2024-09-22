<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/forgot.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/tool/SMSHttp.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/ClientUserClass.php');

	$sAccount		= filter_input_str('sAccount',		INPUT_POST, '', 16);
	$sPhone		= filter_input_str('sPhone',			INPUT_POST, '', 16);
	$sPassword0		= filter_input_str('sPassword0',		INPUT_POST, '', 32);
	$sPassword1		= filter_input_str('sPassword1',		INPUT_POST, '', 32);
	$nVcode		= filter_input_int('nVcode',			INPUT_POST, 0, 6);
	$oUser = new oClientUser();

	$nErr = 1;
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
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);

	if ($aJWT['a'] == 'UPT')
	{
		if (!preg_match('/^09[0-9]{8}$/', $sAccount))
		{
			$nErr	= 0;
			$sMsg .= aERROR['ACCOUNTFORMATE'].'<br>';
		}
		else
		{
			$sSQL = '	SELECT 	nId,
							sPassword,
							nForgotTime
					FROM 		'.CLIENT_USER_DATA.'
					WHERE 	sAccount = :sAccount
					AND 		nOnline = 1
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$nErr	= 0;
				$sMsg	.= aERROR['PHONENOTFOUND'].'<br>';
			}
			elseif (($aRows['nForgotTime']+$aSystem['aParam']['nPasswordExpired']) > NOWTIME) // 申請忘記密碼還未經過30min
			{
				$nErr	= 0;
				$sMsg	.= str_replace('[[::nPasswordExpired::]]', ($aSystem['aParam']['nPasswordExpired']/60), aERROR['TRYLATER']).'<br>';
			}
			else
			{
				$nUid = $aRows['nId'];
			}
			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aRows;
		}

		if($nErr == 0)
		{
			$aReturn['nStatus'] = $nErr;
			$aReturn['sMsg'] = $sMsg;

			echo json_encode($aReturn);
			exit;
		}
		else
		{
			// 寄送臨時密碼簡訊
			$aResult = $oUser->sendTempPassword($sAccount);
			if ($aResult['nStatus'] == 1)
			{
				$aSQL_Array = array(
					'sPassword'		=> (string) oCypher::ReHash($aResult['sTempPassword']),
					'nForgotTime'	=> (string) NOWTIME,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);

				$sSQL = '	UPDATE '.CLIENT_USER_DATA.' SET '. sql_build_array('UPDATE', $aSQL_Array).'
						WHERE nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nUid,PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aReturn['sMsg'] = aFORGOT['SUCCESS'];
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/login/php/_login_0.php']);

				$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;
				$aActionLog = array(
					'nWho'		=> (int) $nUid,
					'nWhom'		=> (int) $nUid,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nUid,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 7100302,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);
			}
			else
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aFORGOT['FAILED'];

				echo json_encode($aReturn);
				exit;
			}
		}
	}
	echo json_encode($aReturn);
	exit;
?>
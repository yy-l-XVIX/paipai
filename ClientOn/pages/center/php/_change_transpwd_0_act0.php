<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/change_transpwd.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/tool/SMSHttp.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/ClientUserClass.php');

	$sTransPassword	= filter_input_str('sTransPassword',	INPUT_POST, '', 32);
	$sTransPassword0	= filter_input_str('sTransPassword0',	INPUT_POST, '', 32);
	$sTransPassword1	= filter_input_str('sTransPassword1',	INPUT_POST, '', 32);
	$nVcode		= filter_input_int('nVcode',			INPUT_POST, 0);
	$oUser = new oClientUser();
	$sAccount = $aUser['sAccount'];

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
		'nAlertType'	=> 1,
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php'])
	);

	if ($aJWT['a'] == 'GETVCODE')
	{
		// 0:帳號不存在 1:帳號存在
		if ($oUser->checkAccount($sAccount) == 0)
		{
			$aReturn['sMsg'] = 'NotFound';
		}
		else
		{
			$nVcode = $oUser->getVcode($sAccount);
			if($nVcode == 0)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = 'Oncheck';
			}
			else
			{
				$aReturn['nStatus'] = 1;
				$aReturn['sMsg'] = $nVcode;
			}
		}
	}

	if ($aJWT['a'] == 'UPT')
	{

		// if(!preg_match('/^[0-9]{5}$/', $nVcode))
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['CODEFORMATE'].'<br>';
		// }
		// else if($oUser->checkVcode($sAccount,$nVcode) === 0)
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['CODEEXPIRED'].'<br>';
		// }
		$sSQL = '	SELECT 	nId,
						sTransPassword
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);
		if (oCypher::ReHash($sTransPassword) != $aOld['sTransPassword'])
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['PASSWORD'].'<br>';
		}
		if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword0))
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['PASSWORDFORMATE'].'<br>';
		}

		if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword1))
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['CONFIRMFORMATE'].'<br>';
		}

		if($sTransPassword0 != $sTransPassword1)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['DIFFERENT'].'<br>';
		}

		if($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'sTransPassword'		=> (string) oCypher::ReHash($sTransPassword0),
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'UPDATE '.CLIENT_USER_DATA.' SET '. sql_build_array('UPDATE', $aSQL_Array).'
				WHERE nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aUser['nId'],PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aReturn['sMsg'] = UPTV;

			$aEditLog[CLIENT_USER_DATA]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aUser['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100313,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
		}
	}


	echo json_encode($aReturn);
	exit;
?>
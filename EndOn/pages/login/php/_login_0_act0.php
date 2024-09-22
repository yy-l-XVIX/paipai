<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/login.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');

	$nT  		= filter_input_int('nt',		INPUT_REQUEST);
	$sAccount	= filter_input_str('sAccount',	INPUT_POST, '', 16);
	$sPassword	= filter_input_str('sPassword',	INPUT_POST, '', 32);
	$sAccount 	= strtolower($sAccount);
	$sPassword = oCypher::ReHash($sPassword);

	$oAdm = new oUser();
	$nRemember	= 0;
	if(isset($_POST['nRemember']))
	{
		$nRemember = 1;
	}

	#登入
	if ($aJWT['a'] == 'LOGIN')
	{
		$aData = array(
			'sAccount'	=> $sAccount,
			'sPassword'	=> $sPassword,
			'nRemember'	=> $nRemember,
		);
		$nStatus = $oAdm->login($aData);
		if ($nStatus == 1)
		{
			$aJumpMsg['0']['sMsg'] = aLOGIN['LOGINV'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aJumpMsg['0']['sMsg'] = aERROR[$nStatus];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

	#登出
	if ($aJWT['a'] == 'LOGOUT')
	{
		$aData = array(
			'sAccount'	=> $aJWT['sAccount'],
		);
		$oAdm->logout($aData);

		$aJumpMsg['0']['sMsg'] = aLOGIN['LOGOUTV'];
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/login/php/_login_0.php']);
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
?>
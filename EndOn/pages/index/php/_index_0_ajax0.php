<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/index.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/ClientOn/inc/tool/SMSHttp.php');

	$oSMS = new SMSHttp();
	$aReturn = array(
		'nErr'	=> 1,
		'sMsg'	=> 'KeyError'
	);

	if ($aJWT['a'] == 'GETCREDIT')
	{

		if($oSMS->getCredit($aSystem['aParam']['sSMSAcc'], $aSystem['aParam']['sSMSPwd']))
		{
			$aReturn['nErr'] = 0;
			$aReturn['sMsg'] = $oSMS->credit;
		}
		else
		{
			$aReturn['sMsg'] = aINDEX['SEARCHERROR'];
		}
	}

	echo json_encode($aReturn);
	exit;
?>
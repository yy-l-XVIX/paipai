<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/login.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');


	$nT  		= filter_input_int('nt',		INPUT_REQUEST);
	$sAccount	= filter_input_str('sAccount',	INPUT_POST, '', 16);
	$sPassword	= filter_input_str('sPassword',	INPUT_POST, '', 32);
	$sGoogle	= filter_input_str('sGoogle',		INPUT_POST, '', 16);
	$sAccount 	= strtolower($sAccount);
	// $sPassword = oCypher::ReHash($sPassword);

	$oAdm = new oUser();
	$nRemember	= 0;
	if(isset($_POST['nRemember']))
	{
		$nRemember = 1;
	}

	#登入
	if ($aJWT['a'] == 'LOGIN')
	{

		$sPassword = oCypher::ReHash($sPassword,$aSystem['aWebsite']['sCpyKey']);

		$aData = array(
			'sAccount'	=> $sAccount,
			'sPassword'	=> $sPassword,
			'sGoogle'	=> $sGoogle,
		);
		# CURL 公司 同步專案目錄
		$sUrl =  COMPANY['URL'].'API/cpy/EmployeVerify.php';
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');# GET || POST
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
		# curl 執行時間
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT,3);
		$result = curl_exec($ch);
		$aResult = json_decode($result,true);

		if ($aResult['nStatus'] == 0)
		{
			# 登入 admroot
			$sSQL = '	SELECT 	sAccount,
							sPassword
					FROM 	'.END_MANAGER_DATA.'
					WHERE nId = 1';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			$aData = array(
				'sAccount'	=> $aRows['sAccount'],
				'sPassword' => $aRows['sPassword'],
				'nRemember' => $nRemember,
				'sEmploye'	=> $aResult['aData']['sNo'],
			);
			$oAdm->login($aData);

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
			$Result->bindValue(':nUid', 1, PDO::PARAM_INT);
			$Result->bindValue(':sSid', $_COOKIE['sSid'], PDO::PARAM_STR);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aJumpMsg['0']['sMsg'] = aLOGIN['LOGINV'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{

			$aJumpMsg['0']['sMsg'] = $aResult['nStatus'].':'.$aResult['sMsg'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

?>
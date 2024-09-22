<?php
	require_once(dirname(dirname(dirname(dirname(dirname(__file__))))) .'/System/Connect/oCoreOpenssl.php');
	/*
	$sAct => 動作 (註冊register, 驗證verify, 取二維碼qrcode)
	註冊register :
	$aData => array(
		'sWebCode' 	=> 網站代碼,
		'sAccount' 	=> 帳號
	);
	驗證verify :
	$aData => array(
		'sWebCode' 	=> 網站代碼,
		'sAccount' 	=> 帳號
		'sVerifyCode'=> 驗證碼
	);
	取二維碼qrcode :
	$aData => array(
		'sWebCode' 	=> 網站代碼,
		'sAccount' 	=> 帳號
		'sWebTitle'=> $sSysTit
	);
	檢查二為馬有效 chk_expire :
	$aData = array(
		'sWebCode' => 網站代碼
		'sWebTitle' => $sSysTit
		'sAccount' => 帳號
		'nExpire' => NOWTIME+300,
	);

	返回錯誤碼
		0 success
		1001 解密失敗
		1002 執行參數錯誤 (sActType)
		1003 值不可為空
		1004 帳號已註冊
		1005 帳號已註冊, 未驗證
		1006 查無帳號資料
		1007 帳號已認證, 無法取得二維碼
		1008 驗證碼比對錯誤
	取二維碼 -> 成功會返回二維碼圖片網址 (sQrcode)
	*/
	function googleCurl($aData,$sAct='')
	{
		$aVal = array(
			'key'	=> SYS['sGoogleKey'],
			'act' => $sAct,
			'time'=> NOWTIME,
		);
		$aData['sAct'] = md5(implode($aVal));
		$aData['sActType'] = $sAct;

		$aParams = array(
			'nTime' 	=> NOWTIME,
			'sParams' 	=> oCoreOpenssl::AESencrypt(SYS['sGoogleKey'],json_encode($aData)),
		);

		$sUrl = GOOGLE['Url'].'verify.php';
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aParams));

		$sResult = curl_exec($ch);
		curl_close($ch);

		return $sResult;
	}
?>
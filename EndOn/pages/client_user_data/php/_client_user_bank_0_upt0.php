<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_bank.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0.php']).$aJWT['sBackParam'],
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_bank_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_bank_0_upt0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'nId'			=> $nId,
		'sAccount'		=> '',
		'sName0'		=> '',
		'sName1'		=> '',
		'sName2'		=> '',
		'nOnline'		=> 0,
		'nBid'		=> 0,
	);
	$aOnline = aONLINE;
	$aBank = array();
	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$nId,
		'nId'		=> $nId,
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sJWTAct = sys_jwt_encode($aValue);

	$nErr = 0;
	$sErrMsg = '';
	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT 	nId,
					sName0
			FROM 		'.SYS_BANK.'
			WHERE 	nOnline = 1
			ORDER BY 	nId ASC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aBank[$aRows['nId']]['sTitle'] = $aRows['sName0'];
		$aBank[$aRows['nId']]['sSelect'] = '';
	}

	$sSQL = '	SELECT 	User_.sAccount,
					Bank_.nId,
					Bank_.nBid,
					Bank_.nOnline,
					Bank_.sName0,
					Bank_.sName1,
					Bank_.sName2,
					Bank_.sCreateTime,
					Bank_.sUpdateTime
			FROM 	'.CLIENT_USER_BANK.' Bank_,
				'.CLIENT_USER_DATA.' User_
			WHERE Bank_.nId = :nId
			AND 	Bank_.nOnline != 99
			AND 	User_.nId != 99
			AND 	User_.nId = Bank_.nUid
			LIMIT 1';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
	sql_query($Result);
	$nCount = $Result->rowCount();
	if ($nCount == 0 && $nId != 0)
	{
		$nErr = 1;
		$sErrMsg = NODATA;
	}
	else
	{
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData = $aRows;
			$aOnline[$aData['nOnline']]['sSelect'] = 'selected';
			$aBank[$aData['nBid']]['sSelect'] = 'selected';
		}

		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid = :nKid
				AND 	sTable LIKE :sTable
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nKid', $nId, PDO::PARAM_INT);
		$Result->bindValue(':sTable', CLIENT_USER_BANK, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows!== false)
		{
			$aData['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			$aValue = array(
				'a'		=> 'DELIMG'.$aRows['nKid'],
				't'		=> NOWTIME,
			);
			$LPsJWT = sys_jwt_encode($aValue);
			$aData['sDelImgUrl'] = $aUrl['sAct'].'&nImgKid='.$aRows['nKid'].'&sJWT='.$LPsJWT.'&nId='.$nId;
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	}
	else
	{
		$aJumpMsg['0']['nClicktoClose'] = 1;
		$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
		$aJumpMsg['0']['aButton']['0']['sClass'] = 'submit';
		$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
		$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
		$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

		$aRequire['Require'] = $aUrl['sHtml'];
	}
	#輸出結束
?>
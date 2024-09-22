<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_data.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/client_user_data/client_user_data.js',
	);
	#js結束

	#參數接收區
	$nId		= filter_input_int('nId',	INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sBack'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_1.php']).$aJWT['sBackParam'],
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_data_1_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_data_1_upt0.php',

	);
	#url結束

	#參數宣告區
	$aData = array(
		'nId'			=> $nId,
		'nKid'		=> 0,
		'nLid'		=> 0,
		'sName0'		=> '',
		'sName1'		=> '',
		'sAccount'		=> '',
		'sPhone'		=> '',
		'sWechat'		=> '',
		'sEmail'		=> '',
		'nStatus'		=> 0,
		'sHeight'		=> '',
		'sWeight'		=> '',
		'sIdNumber'		=> '',
		'sBirthday'		=> '',
		'nAge'		=> '',
		'sSize'		=> '',
		'sContent0'		=> '',
		'sContent1'		=> '',
		'nAgree'		=> 1,
		'sCreateTime'	=> '',
		'sUpdateTime'	=> '',
		'sExpired0'		=> '',
		'sExpired1'		=> '',
		'sIdImgUrl0'	=> '',
		'sIdImgUrl1'	=> '',
		'aKid'		=> array(),
		'aType'		=> array(
			'1'	=> '',
			'99'	=> '',
		),
		'aTransaction'	=> array(),
	);
	$aUserBank = array();
	$aUserPhoto = array();
	$aUserVideo = array();
	$aStatus = aSTATUS;
	$aMoneyStatus = aMONEYSTATUS;
	$aPayType['money'] = array(
		'sValue'	=> aUSER['POINTCHARGE'],
		'sSelect'	=> '',
	);
	$aPayType['company'] = array(
		'sValue'	=> aUSER['COMPANYCHARGE'],
		'sSelect'	=> '',
	);

	$aKind = array();
	$aLocation = array(
		'0' => array(
			'sTitle' => PLEASESELECT,
			'sSelect'=> '',
		),
	);


	$sSearchId = '0';
	$sJWTAct = '';
	$nErr = 0;
	$sErrMsg = '';
	#宣告結束

	#程式邏輯區
	# 身分
	$sSQL = '	SELECT 	nId,
					nLid,
					sName0
			FROM 		'.CLIENT_USER_KIND.'
			WHERE 	nOnline = 1
			AND		sLang LIKE :sLang
			ORDER BY 	nId ASC';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKind[$aRows['nLid']]['sTitle'] = $aRows['sName0'];
		$aKind[$aRows['nLid']]['sSelect'] = '';
	}
	$sSQL = '	SELECT 	nId,
					nLid,
					sName0
			FROM 		'.CLIENT_LOCATION.'
			WHERE 	nOnline = 1
			AND		sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aLocation[$aRows['nLid']]['sTitle'] = $aRows['sName0'];
		$aLocation[$aRows['nLid']]['sSelect'] = '';
	}

	$sSQL = '	SELECT 	User_.nId,
					User_.sKid,
					User_.nKid,
					User_.nLid,
					User_.sAccount,
					User_.sWechat,
					User_.sEmail,
					User_.nStatus,
					User_.sName0,
					User_.sName1,
					User_.sPhone,
					User_.sExpired0,
					User_.sExpired1,
					User_.sCreateTime,
					User_.sUpdateTime,
					Detail_.sHeight,
					Detail_.sWeight,
					Detail_.sIdNumber,
					Detail_.sBirthday,
					Detail_.nBirthday,
					Detail_.sSize,
					Detail_.sContent0,
					Detail_.sContent1
			FROM 		'.CLIENT_USER_DATA.' User_,
					'.CLIENT_USER_DETAIL.' Detail_
			WHERE 	User_.nId = :nId
			AND 		User_.nOnline != 99
			AND 		User_.nId > 1
			AND 		User_.nId = Detail_.nUid
			LIMIT 	1';
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
			$aData['nAge'] = '';
			if ($aRows['nBirthday']>0)
			{
				$aData['nAge'] = date('Y') - date('Y',$aRows['nBirthday']) - 1;
				if (date('n') > date('n',$aRows['nBirthday']) || (date('n') == date('n',$aRows['nBirthday']) && date('j') == date('j',$aRows['nBirthday'])))
				{
					$aData['nAge'] ++;
				}
			}
			$aData['sIdImgUrl0'] = '';
			$aData['sIdImgUrl1'] = '';
			$aData['aKid'] = explode(',', $aRows['sKid']);
			$aData['aTransaction'] = array();
			$aStatus[$aRows['nStatus']]['sSelect'] = 'selected';
			$aLocation[$aRows['nLid']]['sSelect'] = 'selected';
		}
		foreach ($aData['aKid'] as  $LPnKid)
		{
			$aKind[$LPnKid]['sSelect'] = 'checked';
		}
		$aData['aType'] = array(
			'1'	=> '',
			'99'	=> '',
		);

		$sSQL = '	SELECT 	nUid,
						nOnline
				FROM 	'.CLIENT_USER_HIDE.'
				WHERE nUid = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aData['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows !== false)
		{
			$aData['aType'][$aRows['nOnline']] = 'checked';
		}
		else
		{
			$aData['aType'][99] = 'checked';
		}
		#會員銀行卡
		$sSQL = '	SELECT 	UserBank_.nId,
						UserBank_.sName0,
						UserBank_.sName1,
						UserBank_.sName2,
						Bank_.sName0 as sBankName,
						Bank_.sCode
				FROM 	'.CLIENT_USER_BANK.' UserBank_,
					'.SYS_BANK.' Bank_
				WHERE UserBank_.nUid = :nUid
				AND 	UserBank_.nOnline = 1
				AND 	UserBank_.nBid = Bank_.nId';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $nId, PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aUserBank[$aRows['nId']] = $aRows;
			$aUserBank[$aRows['nId']]['sImgUrl'] = '';
			$sSearchId .= ','.$aRows['nId'];
		}

		$sSQL = '	SELECT	nId,
						nKid,
						sFile,
						sTable,
						nType0,
						nCreateTime
				FROM	'.	CLIENT_IMAGE_CTRL .'
				WHERE	nKid IN ( '.$sSearchId.','.$nId.' )
				AND 	sTable IN ( \'client_user_id\',\'client_user_photo\',\'client_user_video\',\'client_user_bank\' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			if ($aRows['sTable'] == 'client_user_id')
			{
				$aData['sIdImgUrl'.$aRows['nType0']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}
			if ($aRows['sTable'] == 'client_user_bank' && isset($aUserBank[$aRows['nKid']]))
			{
				$aUserBank[$aRows['nKid']]['sImgUrl'] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}
			if ($aRows['sTable'] == 'client_user_photo')
			{
				$aUserPhoto[$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}
			if ($aRows['sTable'] == 'client_user_video')
			{
				$aUserVideo[$aRows['nId']] = IMAGE['URL'].'magic/resize/'.$aFile['sDir'].'/'.date('Y/m/d/',$aRows['nCreateTime']).$aRows['sTable'].'/'.$aRows['sFile'];
			}

		}

		# 付款方式
		$sSQL = '	SELECT 	Tunnel_.sKey,
						Tunnel_.sValue,
						Payment_.sName1
				FROM 	'.CLIENT_PAYMENT_TUNNEL.' Tunnel_,
					'.CLIENT_PAYMENT.' Payment_
				WHERE Tunnel_.nOnline = 1
				AND 	Payment_.nOnline = 1
				AND 	Payment_.nType0 = 2
				AND 	Payment_.nId = Tunnel_.nPid';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aPayType[$aRows['sKey']] = $aRows;
			$aPayType[$aRows['sKey']]['sSelect'] = '';
		}

		$sSQL = '	SELECT	nId,
						nStatus,
						nMoney,
						nUkid,
						sPaymentName1,
						sPayType,
						nType0,
						sUpdateTime
				FROM		'.CLIENT_MONEY.'
				WHERE		nType0 IN (1, 2, 5)
				AND		nUid = :nUid
				ORDER	BY	nId DESC';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid',$nId,PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aTransaction'][$aRows['nId']] = $aRows;

			if ($aRows['nType0'] == 1)
			{
				$aData['aTransaction'][$aRows['nId']]['sPayTypeName'] = aUSER['COMPANYCHARGE'];
			}
			if ($aRows['nType0'] == 2 && isset($aPayType[$aRows['sPayType']]))
			{
				$aData['aTransaction'][$aRows['nId']]['sPayTypeName'] = $aPayType[$aRows['sPayType']]['sValue'];
			}
			if ($aRows['nType0'] == 5)
			{
				$aData['aTransaction'][$aRows['nId']]['sPayTypeName'] = aUSER['POINTCHARGE'];
			}
		}

	}

	$aValue = array(
		'a'		=> ($nId == 0)?'INS':'UPT'.$aData['nId'],
		't'		=> NOWTIME,
		'nId'		=> $aData['nId'],
		'sBackParam'=> $aJWT['sBackParam'],
	);
	$sJWTAct = sys_jwt_encode($aValue);
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
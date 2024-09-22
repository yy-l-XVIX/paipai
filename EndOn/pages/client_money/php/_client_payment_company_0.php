<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_payment_company.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/js_date/laydate.js',
		1	=> 'plugins/js/client_money/client_payment_company.js'
	);
	#js結束

	#參數接收區
	$sStartTime		= filter_input_str('sStartTime',	INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime		= filter_input_str('sEndTime',	INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$nKid			= filter_input_int('nKid',		INPUT_REQUEST, 0);
	$nUKid		= filter_input_int('nUKid',		INPUT_REQUEST, 0);
	$nStatus		= filter_input_int('nStatus',		INPUT_REQUEST, -1);
	$sAdmin		= filter_input_str('sAdmin',		INPUT_REQUEST, '');
	$sMemberAccount	= filter_input_str('sMemberAccount',INPUT_REQUEST, '');
	$sMemo		= filter_input_str('sMemo',		INPUT_REQUEST, '');
	$sSelDay 		= filter_input_str('sSelDay',		INPUT_REQUEST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_payment_company_0.php']),
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_payment_company_0.php',
	);
	#url結束

	#參數宣告區

	$aData = array();
	$aCompany = array();
	$aAdminData = array();
	$aMemberData = array();
	$aSearchId = array();
	$aBindArray = array();
	$aDay = aDAY;
	$aStatus = aPAYMENTCOMPANY['STATUS'];
	unset($aStatus['sTitle']);
	$aCountData = array(
		'nPageCount' => 0,
		'nTotalCount'=> 0,
		'nPageMoney' => 0,
		'nTotalMoney'=> 0,
	);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	$sExcelVar = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];

	$aPage['aVar'] = array(
		'sStartTime'	=> $sStartTime,
		'sEndTime'		=> $sEndTime,
		'nUKid'		=> $nUKid,
		'nKid'		=> $nKid,
		'nStatus'		=> $nStatus,
		'sAdmin'		=> $sAdmin,
		'sMemberAccount'	=> $sMemberAccount,
		'sMemo'		=> $sMemo,
		'sSelDay'		=> $sSelDay,
	);

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

	$sTempDetail = '	<div class="DetailBoxTable Table">
					<div>
						<div>
							<div class="DetailBoxCell1">
								<div>'.aPAYMENTCOMPANY['ACCOUNT'].'</div>
							</div>
							<div class="DetailBoxCell2">
								<div>[[::sAccount::]]</div>
							</div>
						</div>
						<div>
							<div class="DetailBoxCell1">
								<div>'.aPAYMENTCOMPANY['MONEY'].'</div>
							</div>
							<div class="DetailBoxCell2">
								<div>[[::nMoney::]]</div>
							</div>
						</div>
						<div>
							<div class="DetailBoxCell1">
								<div>'.aPAYMENTCOMPANY['FEE'].'</div>
							</div>
							<div class="DetailBoxCell2">
								<div>[[::nFee::]]</div>
							</div>
						</div>
						<div>
							<div class="DetailBoxCell1">
								<div>'.aPAYMENTCOMPANY['RANK'].'</div>
							</div>
							<div class="DetailBoxCell2">
								<div>[[::sKindName::]]</div>
							</div>
						</div>
						<div>
							<div class="DetailBoxCell1">
								<div>'.aPAYMENTCOMPANY['BANKNAME'].'</div>
							</div>
							<div class="DetailBoxCell2">
								<div>[[::sCompany::]]</div>
							</div>
						</div>
						<div>
							<div class="DetailBoxCell1">
								<div>'.aPAYMENTCOMPANY['MEMO'].'</div>
							</div>
							<div class="DetailBoxCell2">
								<div>[[::sMemo::]]</div>
							</div>
						</div>
					</div>
				</div>';
	#宣告結束

	#程式邏輯區
	// xls params
	foreach ($aPage['aVar'] as $LPsKey => $LPsValue)
	{
		$sExcelVar .= '&'.$LPsKey.'='.$LPsValue;
		$sBackParam .= '&'.$LPsKey.'='.$LPsValue;
	}
	$aValue = array(
		'a'		=> 'EXCEL',
		'sBackParam'=> $sBackParam,
	);
	$aUrl['sExcel'] = $aUrl['sAct'] .$sExcelVar. '&sJWT='.sys_jwt_encode($aValue);

	$sCondition = ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);

	if($nUKid > 0) // client_user_kind nLid
	{
		$sCondition .= ' AND nUKid = :nUKid ';
		$aBindArray['nUKid'] = $nUKid;
	}
	if($nKid > 0) // client_user_kind nLid
	{
		$sCondition .= ' AND nKid = :nKid ';
		$aBindArray['nKid'] = $nKid;
	}
	if($nStatus > -1)
	{
		$sCondition .= ' AND nStatus = :nStatus ';
		$aBindArray['nStatus'] = $nStatus;
		$aStatus[$nStatus]['sSelect'] = 'selected';
	}
	if($sAdmin != '')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.END_MANAGER_DATA.'
				WHERE nOnline = 1
				AND 	sAccount LIKE :sAccount';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sAdmin.'%', PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}
		if (!empty($aSearchId))
		{
			$sCondition .= ' AND nAdmin0 IN ( '.implode(',', $aSearchId).' ) ';
			$aSearchId = array();
		}
	}
	if ($sMemberAccount != '')
	{
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nOnline = 1
				AND 	sAccount LIKE :sAccount';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', '%'.$sMemberAccount.'%', PDO::PARAM_STR);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aSearchId[$aRows['nId']] = $aRows['nId'];
		}
		if (!empty($aSearchId))
		{
			$sCondition .= ' AND nUid IN ( '.implode(',', $aSearchId).' ) ';
			$aSearchId = array();
		}
	}

	// 公司入款帳號
	$sSQL = '	SELECT	nId,
					sName0
			FROM	'.	CLIENT_PAYMENT .'
			WHERE		nType0 = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCompany[$aRows['nId']] = $aRows;
		$aCompany[$aRows['nId']]['sSelect'] = '';
		if($nKid == $aRows['nId'])
		{
			$aCompany[$aRows['nId']]['sSelect'] = 'selected';
		}
	}

	// 會員方案
	$sSQL = '	SELECT 	nLid,
					sName0
			FROM 	'.CLIENT_USER_KIND.'
			WHERE nOnline = 1
			AND 	sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aUserKind[$aRows['nLid']] = $aRows;
		$aUserKind[$aRows['nLid']]['sSelect'] = '';
		if($nUKid == $aRows['nLid'])
		{
			$aUserKind[$aRows['nLid']]['sSelect'] = 'selected';
		}
	}

	$sSQL = '	SELECT 	nMoney
			FROM 	'.CLIENT_MONEY.'
			WHERE nType0 = 1
			'.$sCondition;
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCountData['nTotalCount'] ++;
		$aCountData['nTotalMoney'] += $aRows['nMoney'];
	}
	$aPage['nDataAmount'] = $aCountData['nTotalCount'];

	// 取單
	$sSQL = '	SELECT 	nId,
					nUid,
					nAdmin0,
					nMoney,
					nKid,
					nUKid,
					nStatus,
					nFee,
					sMemo,
					sUpdateTime,
					sCreateTime
			FROM 	'.CLIENT_MONEY.'
			WHERE nType0 = 1
			'.$sCondition.'
			ORDER BY nId DESC '.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCountData['nPageCount'] ++;
		$aCountData['nPageMoney'] += $aRows['nMoney'];

		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sCompany'] = $aCompany[$aRows['nKid']]['sName0'];
		$aData[$aRows['nId']]['sKindName'] = $aUserKind[$aRows['nUKid']]['sName0'];
		$LPaValue = array(
			'a'		=> 'PASS'.$aRows['nId'],
			'nExp'	=> NOWTIME + JWTWAIT,
			'sBackParam'=> $sBackParam,
		);
		$aData[$aRows['nId']]['sPassUrl'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue);
		$LPaValue = array(
			'a'		=> 'CANCEL'.$aRows['nId'],
			'nExp'	=> NOWTIME + JWTWAIT,
			'sBackParam'=> $sBackParam,
		);
		$aData[$aRows['nId']]['sCancelUrl'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue);

		$aSearchId['aUser'][$aRows['nUid']] = $aRows['nUid'];
		$aSearchId['aAdmin'][$aRows['nAdmin0']] = $aRows['nAdmin0'];
	}
	if (!empty($aSearchId['aAdmin']))
	{
		$aAdminData['-1']['sAccount'] = '';
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.END_MANAGER_DATA.'
				WHERE nId IN ('.implode(',', $aSearchId['aAdmin']).')';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aAdminData[$aRows['nId']] = $aRows;
		}
	}
	if (!empty($aSearchId['aUser']))
	{
		$sSQL = '	SELECT 	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE nId IN ('.implode(',', $aSearchId['aUser']).')';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aMemberData[$aRows['nId']] = $aRows;
		}
	}

	foreach ($aData as $LPnId => $LPaDetail)
	{
		$LPaTemp = array(
			'[[::sAccount::]]',
			'[[::nMoney::]]',
			'[[::nFee::]]',
			'[[::sKindName::]]',
			'[[::sCompany::]]',
			'[[::sMemo::]]',
		);
		$LPaReplace = array(
			$aMemberData[$LPaDetail['nUid']]['sAccount'],
			$LPaDetail['nMoney'],
			$LPaDetail['nFee'],
			$LPaDetail['sKindName'],
			$LPaDetail['sCompany'],
			$LPaDetail['sMemo'],
		);
		$LPsDetail = str_replace($LPaTemp, $LPaReplace, $sTempDetail);

		$aJumpMsg[$LPnId] = array(
			'sShow'	=>	0,
			'sTitle'	=>	$aMemberData[$LPaDetail['nUid']]['sAccount'].' '.aPAYMENTCOMPANY['DETAIL'],	# 標題
			'sArticle'	=>	$LPsDetail,	# 較長文字
			'aButton'	=>	array(
				'0'	=>	array(
					'sClass'	=>	'',
					'sUrl'	=>	$LPaDetail['sPassUrl'],	# 跳轉之url
					'sText'	=>	CONFIRM,
				),
				'1'	=>	array(
					'sClass'	=>	'cancel',
					'sUrl'	=>	$LPaDetail['sCancelUrl'],# 跳轉之url
					'sText'	=>	DENY,
				),
			),
			'nClicktoClose'	=>	1,
		);

		if($LPaDetail['nStatus'] != 0)
		{
			$aJumpMsg[$LPnId]['aButton'][0]['sClass'] = 'JqClose';
			$aJumpMsg[$LPnId]['aButton'][0]['sText'] = CLOSE;
			$aJumpMsg[$LPnId]['aButton'][0]['sUrl'] = '';
			unset($aJumpMsg[$LPnId]['aButton'][1]);
		}
	}
	foreach ($aDay as $LPsText => $LPaDate)
	{
		$aDay[$LPsText]['sSelect'] = '';
		if ($sSelDay == $LPsText)
		{
			$aDay[$LPsText]['sSelect'] = 'active';
		}
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
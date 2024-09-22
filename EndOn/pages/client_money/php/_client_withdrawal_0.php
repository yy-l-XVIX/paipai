<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_withdrawal.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		0	=> 'plugins/js/js_date/laydate.js',
		1	=> 'plugins/js/client_money/client_withdrawal.js',
	);
	#js結束

	#參數接收區
	$sStartTime		= filter_input_str('sStartTime', 		INPUT_REQUEST, date('Y-m-d 00:00:00'));
	$sEndTime		= filter_input_str('sEndTime', 		INPUT_REQUEST, date('Y-m-d 23:59:59'));
	$nKid			= filter_input_int('nKid', 			INPUT_REQUEST, 0);
	$nStatus		= filter_input_int('nStatus',			INPUT_REQUEST, -1);
	$sAdmin		= filter_input_str('sAdmin',			INPUT_REQUEST, '');
	$sMemberAccount	= filter_input_str('sMemberAccount',	INPUT_REQUEST, '');
	$sMemo		= filter_input_str('sMemo',			INPUT_REQUEST, '');
	$sSelDay 		= filter_input_str('sSelDay',			INPUT_REQUEST, 'TODAY');
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_withdrawal_0_act0.php']).'&run_page=1',
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_money/php/_client_withdrawal_0.php']),
		'sHtml'	=> 'pages/client_money/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_withdrawal_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$aBank = array();
	$aMemberData = array();
	$aAdminData = array();
	$aSearchId = array();
	$aBankCard = array();
	$aBindArray = array();
	$aDay = aDAY;
	$aCountData = array(
		'nPageCount' => 0,
		'nTotalCount'=> 0,
		'nPageMoney' => 0,
		'nTotalMoney'=> 0,
	);
	$aPage['aVar'] = array(
		'sStartTime'	=> $sStartTime,
		'sEndTime'		=> $sEndTime,
		'nKid'		=> $nKid,
		'nStatus'		=> $nStatus,
		'sAdmin'		=> $sAdmin,
		'sMemberAccount'	=> $sMemberAccount,
		'sMemo'		=> $sMemo,
		'sSelDay'		=> $sSelDay,
	);
	$sExcelVar = '';
	$sBackParam = '&nPageNo='.$aPage['nNowNo'];
	$sCondition = '';
	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$aStatus = aWITHDRAWAL['STATUS'];
	unset($aStatus['sTitle']);

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT.'?';
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;

	$sTempDetail = '<div class="DetailBoxTable Table">
				<div>
					<div>
						<div class="DetailBoxCell1">
							<div>'.aWITHDRAWAL['BANKNAME'].'</div>
						</div>
						<div class="DetailBoxCell2 JqCopyMe">
							<div>[[::sBankName::]]([[::sCode::]])</div>
						</div>
						<div class="DetailBoxCell3">
							<div class="JqCopy">['.aWITHDRAWAL['COPY'].']</div>
						</div>
					</div>
					<div>
						<div class="DetailBoxCell1">
							<div>'.aWITHDRAWAL['SUBNAME'].'</div>
						</div>
						<div class="DetailBoxCell2 JqCopyMe">
							<div>[[::sName2::]]</div>
						</div>
						<div class="DetailBoxCell3">
							<div class="JqCopy">['.aWITHDRAWAL['COPY'].']</div>
						</div>
					</div>
					<div>
						<div class="DetailBoxCell1">
							<div>'.aWITHDRAWAL['USERNAME'].'</div>
						</div>
						<div class="DetailBoxCell2 JqCopyMe">
							<div>[[::sName1::]]</div>
						</div>
						<div class="DetailBoxCell3">
							<div class="JqCopy">['.aWITHDRAWAL['COPY'].']</div>
						</div>
					</div>
					<div>
						<div class="DetailBoxCell1">
							<div>'.aWITHDRAWAL['CARDNUMBER'].'</div>
						</div>
						<div class="DetailBoxCell2 JqCopyMe">
							<div>[[::sName0::]]</div>
						</div>
						<div class="DetailBoxCell3">
							<div class="JqCopy">['.aWITHDRAWAL['COPY'].']</div>
						</div>
					</div>
					<div>
						<div class="DetailBoxCell1">
							<div>'.aWITHDRAWAL['MONEY'].'</div>
						</div>
						<div class="DetailBoxCell2 JqCopyMe">
							<div>[[::nMoney::]]</div>
						</div>
						<div class="DetailBoxCell3">
							<div class="JqCopy">['.aWITHDRAWAL['COPY'].']</div>
						</div>
					</div>
					<div>
						<div class="DetailBoxCell1">
							<div>'.aWITHDRAWAL['FEE'].'</div>
						</div>
						<div class="DetailBoxCell2 JqCopyMe">
							<div>[[::nFee::]]</div>
						</div>
						<div class="DetailBoxCell3">
							<div class="JqCopy">['.aWITHDRAWAL['COPY'].']</div>
						</div>
					</div>
				</div>
			</div>';

	#宣告結束

	#程式邏輯區
	$sCondition .= ' AND nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
	$aBindArray['nStartTime'] = strtotime($sStartTime);
	$aBindArray['nEndTime'] = strtotime($sEndTime);

	if($nKid > 0)
	{
		$sCondition .= ' AND nKid = :nKid';
		$aBindArray['nKid'] = $nKid;
	}
	if($nStatus > -1)
	{
		$sCondition .= ' AND nStatus = :nStatus';
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
			$sCondition .= ' AND ( nAdmin1 IN ( '.implode(',', $aSearchId).' ) OR nAdmin2 IN ( '.implode(',', $aSearchId).' ) ) ';
			$aSearchId = array();
		}

	}
	if($sMemberAccount != '')
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
	if($sMemo != '')
	{
		$sCondition .= ' AND sMemo LIKE :sMemo';
		$aBindArray['sMemo'] = '%'.$sMemo.'%';
	}
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


	// 取銀行
	$sSQL = '	SELECT	nId,
					sName0,
					sCode
			FROM	'.SYS_BANK .'
			WHERE	nType0 = 1
			AND 	nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aBank[$aRows['nId']] = $aRows;
		$aBank[$aRows['nId']]['sSelect'] = '';
		if($nKid == $aRows['nId'])
		{
			$aBank[$aRows['nId']]['sSelect'] = 'selected';
		}
	}

	// 取單
	$sSQL = '	SELECT 	nMoney
			FROM 	'.CLIENT_MONEY.'
			WHERE nType0 = 3
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

	$sSQL = '	SELECT 	nId,
					nUid,
					nKid,
					nAdmin1,
					nAdmin2,
					nMoney,
					nStatus,
					nFee,
					sMemo,
					sCreateTime,
					sUpdateTime
			FROM 	'.CLIENT_MONEY.'
			WHERE nType0 = 3
			'.$sCondition.'
			ORDER BY nId DESC
			'.sql_limit($nPageStart, $aPage['nPageSize']);
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result,$aBindArray);
	sql_query($Result);
	while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCountData['nPageCount'] ++;
		$aCountData['nPageMoney'] += $aRows['nMoney'];

		$aData[$aRows['nId']] = $aRows;
		$aData[$aRows['nId']]['sAdmin1'] = '';
		$aData[$aRows['nId']]['sAdmin2'] = '';
		$aData[$aRows['nId']]['sPass'] = '';
		$aData[$aRows['nId']]['sDeny'] = '';

		if ($aRows['nStatus'] == 0 && $aRows['nAdmin1'] <= 0)
		{
			$LPaValue = array(
				'a'		=> 'RISKPASS'.$aRows['nId'],
				'sBackParam'=> $sBackParam,
			);
			$aData[$aRows['nId']]['sPass'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue);

			$LPaValue = array(
				'a'		=> 'RISKDENY'.$aRows['nId'],
				'sBackParam'=> $sBackParam,
			);
			$aData[$aRows['nId']]['sDeny'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue);
		}

		if($aRows['nStatus'] == 0 && $aRows['nAdmin1'] > 0 && $aRows['nAdmin2'] <= 0)
		{
			$LPaValue = array(
				'a'		=> 'MONEYPASS'.$aRows['nId'],
				'sBackParam'=> $sBackParam,
			);
			$aData[$aRows['nId']]['sPass'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue);

			$LPaValue = array(
				'a'		=> 'MONEYDENY'.$aRows['nId'],
				'sBackParam'=> $sBackParam,
			);
			$aData[$aRows['nId']]['sDeny'] = $aUrl['sAct'].'&nId='.$aRows['nId'].'&sJWT='.sys_jwt_encode($LPaValue);
		}

		$aSearchId['aUser'][$aRows['nUid']] = $aRows['nUid'];
		$aSearchId['aAdmin'][$aRows['nAdmin1']] = $aRows['nAdmin1'];
		$aSearchId['aAdmin'][$aRows['nAdmin2']] = $aRows['nAdmin2'];
		$aSearchId['aKid'][$aRows['nKid']] = $aRows['nKid'];
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
	if (!empty($aSearchId['aKid']))
	{
		# 會員銀行卡
		$sSQL = '	SELECT 	nId,
						sName0,
						sName1,
						sName2,
						nBid
				FROM 	'.CLIENT_USER_BANK.'
				WHERE nId IN ( '.implode(',', $aSearchId['aKid']).' )';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aBankCard[$aRows['nId']] = $aRows;
			$aBankCard[$aRows['nId']]['sBankName'] = $aBank[$aRows['nBid']]['sName0'];

			$LPaTemp = array(
				'[[::sBankName::]]',
				'[[::sCode::]]',
				'[[::sName2::]]',
				'[[::sName1::]]',
				'[[::sName0::]]',
			);
			$LPaReplace = array(
				$aBank[$aRows['nBid']]['sName0'],
				$aBank[$aRows['nBid']]['sCode'],
				$aRows['sName2'],
				$aRows['sName1'],
				$aRows['sName0'],
			);
			$aBankCard[$aRows['nId']]['sDetail'] = str_replace($LPaTemp, $LPaReplace, $sTempDetail);
		}
	}

	foreach ($aData as $LPnId => $LPaData)
	{
		$LPaTemp = array(
			'[[::nMoney::]]',
			'[[::nFee::]]',
		);
		$LPaReplace = array(
			$LPaData['nMoney'],
			$LPaData['nFee']
		);
		$LPsDetail = str_replace($LPaTemp, $LPaReplace, $aBankCard[$LPaData['nKid']]['sDetail']);
		$aJumpMsg[$LPnId] = array(
			'sBoxClass'	=>	'',
			'sShow'	=>	0,	# 是否直接顯示彈窗 0=>隱藏 , 1=>顯示
			'sTitle'	=>	$aMemberData[$LPaData['nUid']]['sAccount'].' '.aWITHDRAWAL['DETAIL'],	# 標題
			'sIcon'	=>	'',	# 成功=>success,失敗=>error
			'sMsg'	=>	'',	# 訊息
			'sArticle'	=>	$LPsDetail,	# 較長文字
			'aButton'	=>	array(
				'0'	=>	array(
					'sClass'	=>	'JqClose',	# 若為取消=>cancel,點擊關閉不換頁=>JqClose,送出form=>submit
					'sUrl'	=>	'',	# 跳轉之url
					'sText'	=>	aWITHDRAWAL['CLOSE'],	# 顯示之文字
				),
			),
			'nClicktoClose'	=>	1,	# 是否點擊任意一處即可關閉 0=>否 , 1=>是
		);

		$aData[$LPnId]['sAdmin1'] = $aAdminData[$LPaData['nAdmin1']]['sAccount'];
		$aData[$LPnId]['sAdmin2'] = $aAdminData[$LPaData['nAdmin2']]['sAccount'];
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
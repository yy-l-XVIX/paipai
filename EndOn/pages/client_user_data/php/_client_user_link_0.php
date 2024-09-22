<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_link.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sAccount 	= filter_input_str('sAccount',	INPUT_REQUEST, '', 32);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_link_0.php']),
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_link_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'nAncestor'	=> 0,
		'nSon'	=> 0,
		'aAncestor'	=> array(),
		'aSon'	=> array(),

	);

	$aDefault = array(
		'sAccount'	 	=> '',
		'sRank' 		=> '',
		'sName0' 		=> '',
		'nMoney' 		=> 0,
		'nTeamFirst' 	=> 0,
		'nTeamSecond' 	=> 0,
	);
	$nUid = 0;
	$aTemp = array();
	$aBindArray = array();
	$sCondition = '';
	$aKind = array(
		'0'	=> aLINK['RANK0']
	);

	#宣告結束

	#程式邏輯區
	$sSQL = '	SELECT 	nId,
					nLid,
					sName0
			FROM 		'.CLIENT_USER_KIND.'
			WHERE		sLang LIKE :sLang';
	$Result = $oPdo->prepare($sSQL);
	$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aKind[$aRows['nLid']] = $aRows['sName0'];
	}

	if($sAccount === '')
	{
		$nId = 1;
		$aBindArray['nId'] = $nId;
		$sCondition = 'AND User_.nId = :nId';
	}
	else
	{
		$aBindArray['sAccount'] = $sAccount;
		$sCondition = 'AND User_.sAccount = :sAccount';
	}

	$sSQL = '	SELECT 	User_.nId,
					User_.sAccount,
					Link_.nLevel,
					Link_.nPa,
					Link_.sLinkList
		   	FROM 		'.CLIENT_USER_DATA.' User_
		   	JOIN		'.CLIENT_USER_LINK.' Link_
			ON 		User_.nId = Link_.nUid
		   	WHERE 	User_.nOnline != 99
		   			'.$sCondition.'
		   	LIMIT 	1';
	$Result = $oPdo->prepare($sSQL);
	sql_build_value($Result, $aBindArray);
	sql_query($Result);
	$aRows = $Result->fetch(PDO::FETCH_ASSOC);
	if($aRows !== false)
	{
		$nUid = $aRows['nId'];
		$aData['aAncestor'] = explode(',',$aRows['sLinkList']);
		unset($aData['aAncestor'][$nUid]);
	}

	if($nUid != 0)
	{
		$aTemp = array();
		$aUser = array();
		$sSQL = 'SELECT 	User_.nId,
					User_.sAccount,
					User_.sName0,
					Money_.nMoney,
					Link_.nLevel,
					Link_.nPa
			FROM 		'.CLIENT_USER_DATA.' User_
			JOIN		'.CLIENT_USER_LINK.' as Link_
			ON		User_.nId = Link_.nUid
			JOIN		'.CLIENT_USER_MONEY.' Money_
			ON		User_.nId = Money_.nUid
			WHERE 	User_.nOnline != 99
			ORDER BY Link_.nLevel DESC';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nUid, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			// 避免重覆計算到1.2級會員
			if(isset($aUser[$aRows['nId']]))
			{
				continue;
			}
			$aUser[$aRows['nId']] = 1;
			// print_r('<pre>');
			// print_r($aRows);
			// print_r('</pre>');

			if($aRows['nPa'] == $nUid)
			{
				$aData['aSon'][$aRows['nId']] = $aRows['nId'];
			}
			if(!isset($aTemp[$aRows['nId']])) $aTemp[$aRows['nId']] = $aDefault;
			if(!isset($aTemp[$aRows['nPa']])) $aTemp[$aRows['nPa']] = $aDefault;

			$aTemp[$aRows['nId']]['sAccount'] = $aRows['sAccount'];
			$aTemp[$aRows['nId']]['sName0'] = $aRows['sName0'];

			$aTemp[$aRows['nId']]['nMoney'] = $aRows['nMoney'];
			$aTemp[$aRows['nId']]['sRank'] = '';

			$aTemp[$aRows['nPa']]['nTeamFirst']++;
			$aTemp[$aRows['nPa']]['nTeamSecond'] += $aTemp[$aRows['nId']]['nTeamFirst'];
		}
		// print_r('<pre>');
		// print_r($aTemp);
		// print_r('</pre>');
		// exit;
		ksort($aData['aSon']);

		// 爸爸們(含自己)
		foreach ($aData['aAncestor'] as $LPnId => $LPnPaId)
		{
			$aData['nAncestor']++;
			$nPaId = (int) $LPnPaId;
			$aData['aAncestor'][$LPnId] = $aTemp[$nPaId];

			// 自己紅色
			$aData['aAncestor'][$LPnId]['sClass'] = ($nPaId == $nUid) ? 'FontRed' : '';
		}

		// 兒子們
		foreach ($aData['aSon'] as $LPnId)
		{
			$aData['nSon']++;
			$aData['aSon'][$LPnId] = $aTemp[$LPnId];
		}
	}

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
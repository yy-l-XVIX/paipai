<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_friend.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	$sAccount	= filter_input_str('sAccount',INPUT_REQUEST, '');
	$nType   	= filter_input_int('nType',	INPUT_REQUEST, 1); // 1好友 封鎖
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sPage'	=> sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_friend_0.php']),
		'sHtml'	=> 'pages/client_user_data/'.$aSystem['sHtml'].$aSystem['nVer'].'/client_user_friend_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array();
	$nUid = 0;
	$aBlock = array(0=>0);

	$aPage['aVar'] = array(
		'sAccount'	=> $sAccount,
		'nType'	=> $nType,
	);

	$nPageStart = $aPage['nNowNo'] * $aPage['nPageSize'] - $aPage['nPageSize'];
	$sCondition = '';
	#宣告結束

	#程式邏輯區
	if($sAccount != '' && ($nType == 1 || $nType == 2))
	{
		$sSQL = '	SELECT 	nId
				FROM 		'.CLIENT_USER_DATA.'
				WHERE 	sAccount LIKE :sAccount
				AND 		nOnline != 99';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows !== false)
		{
			$nUid = $aRows['nId'];
		}

		// 好友
		if($nType == 1)
		{
			$sSQL = '	SELECT	nBUid
					FROM		'.CLIENT_USER_BLOCK.'
					WHERE		nUid = :nUid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aBlock[$aRows['nBUid']] = $aRows['nBUid'];
			}

			$sSQL = '	SELECT	1
					FROM		'.CLIENT_USER_FRIEND.' Frined_,
							'.CLIENT_USER_DATA.' User_
					WHERE		Frined_.nFUid = User_.nId
					AND		Frined_.nUid = :nUid
					AND		User_.nOnline = 1
					AND		Frined_.nStatus = 1
					AND		Frined_.nFUid NOT IN ('.implode(',',$aBlock).')';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
			sql_query($Result);
			$aPage['nDataAmount'] = $Result->rowCount();
			$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
			if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
			{
				$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
			}

			$sSQL = '	SELECT	User_.sName0,
							User_.sAccount,
							Frined_.nId,
							Frined_.nFUid,
							Frined_.nStatus,
							Frined_.sCreateTime
					FROM		'.CLIENT_USER_FRIEND.' Frined_,
							'.CLIENT_USER_DATA.' User_
					WHERE		Frined_.nFUid = User_.nId
					AND		Frined_.nUid = :nUid
					AND		User_.nOnline = 1
					AND		Frined_.nStatus = 1
					AND		Frined_.nFUid NOT IN ('.implode(',',$aBlock).')
					ORDER BY	User_.nId DESC '
					.sql_limit($nPageStart, $aPage['nPageSize']);
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aData[$aRows['nId']] = $aRows;

			}
		}

		// 封鎖
		if($nType == 2)
		{

			$sSQL = '	SELECT	1
					FROM		'.CLIENT_USER_BLOCK.' Block_,
							'.CLIENT_USER_DATA.' User_
					WHERE		Block_.nBUid = User_.nId
					AND		Block_.nUid = :nUid
					AND		User_.nOnline = 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
			sql_query($Result);
			$aPage['nDataAmount'] = $Result->rowCount();
			$aPage['nTotal'] = ($aPage['nDataAmount'] / $aPage['nPageSize']);
			if ( ($aPage['nDataAmount'] % $aPage['nPageSize']) > 0 )
			{
				$aPage['nTotal'] = ceil($aPage['nDataAmount'] / $aPage['nPageSize']);
			}


			$sSQL = '	SELECT	User_.sName0,
							User_.sAccount,
							User_.nKid,
							User_.sKid,
							User_.nStatus as nUserStatus,
							Block_.nId,
							Block_.nBUid,
							Block_.sCreateTime
					FROM		'.CLIENT_USER_BLOCK.' Block_,
							'.CLIENT_USER_DATA.' User_
					WHERE		Block_.nBUid = User_.nId
					AND		Block_.nUid = :nUid
					AND		User_.nOnline = 1
					ORDER BY	User_.nId DESC '
					.sql_limit($nPageStart, $aPage['nPageSize']);
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
			sql_query($Result);
			while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aData[$aRows['nId']] = $aRows;
			}
		}
	}

	$aPageList = pageSet($aPage, $aUrl['sPage']);

	// print_r('<pre>');
	// print_r($aData);
	// print_r('</pre>');
	// exit;

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
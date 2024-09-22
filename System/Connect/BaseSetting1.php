<?php
	# 各站需要先取得維護 全局設定 目錄資料 logcode
	# $aSystem['aNav']
	# $aSystem['aLogNums']

	# 目錄 kind / list
	$sSQL = '	SELECT 	nId,
					sMenuName0,
					sMenuTable0
			FROM	end_menu_kind
			WHERE	nOnline = 1
			ORDER BY nSort DESC ,nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aSystem['aNav'][$aRows['nId']] = $aRows;
		$aSystem['aNav'][$aRows['nId']]['aList'] = array();
	}

	$sSQL = '	SELECT 	nId,
					sListName0,
					nMid,
					sListTable0,
					nType0
			FROM	end_menu_list
			WHERE	nOnline = 1
			ORDER BY nSort DESC ,nId DESC';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aSystem['aNav'][$aRows['nMid']]['aList'][$aRows['nId']] = $aRows;
	}
	# 動作代號
	$aSystem['aLogNums'][0] = '';
	$sSQL = '	SELECT 	nType0,
					nCode,
					sName0
			FROM	end_logcode';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aSystem['aLogNums'][$aRows['nType0'].$aRows['nCode']] = $aRows['sName0'];
	}
	# 環境設定
	$sSQL = '	SELECT 	nId,
					sName0,
					sParam
			FROM	sys_param';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aSystem['aParam'][$aRows['sName0']] = $aRows['sParam'];
	}
?>
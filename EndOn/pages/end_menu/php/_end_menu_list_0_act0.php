<?php
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_menu_list.php');

	$nId			= filter_input_int('nId',		INPUT_REQUEST);
	$nOnline		= filter_input_int('nOnline',		INPUT_POST,0);
	$nSort		= filter_input_int('nSort',		INPUT_POST,0);
	$nErr	= 0;
	$sMsg = '';
/*
	if ($aJWT['a'] == 'INS')
	{
		if ($sListName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYNAME'].'<br>';
		}
		if ($sListTable0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYTABLE'].'<br>';
		}
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0_upt0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aSQL_Array = array(
				'sListName0'	=> (string) $sListName0,
				'sListTable0'	=> (string) $sListTable0,
				'nOnline'		=> (int) $nOnline,
				'nSort' 		=> (int) $nSort ,
				'nType0'		=> (int) $nType0,
				'nMid'		=> (int) $nMid,
			);
			$sSQL = 'INSERT INTO '.END_MENU_LIST.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			$rs = sql_query($Result);
			$nLast_id = $oPdo->lastInsertId();

			# 紀錄動作 - 新增
			$aJumpMsg['0']['sMsg'] = aLIST['INSERTSUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
*/
	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						sListName0,
						sListTable0,
						nOnline,
						nMid,
						nSort
				FROM 	'.END_MENU_LIST.'
				WHERE nId = :nId
				AND 	nOnline != 99';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOld === false)
		{
			$nErr	= 1;
			$sMsg	.= aERROR['NODATA'].'<br>';
		}

		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0_upt0.php']).'&nId='.$nId;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aSQL_Array = array(
				'nOnline'		=> (int) $nOnline,
				'nSort' 		=> (int) $nSort,
			);

			$sSQL = '	UPDATE	'.END_MENU_LIST.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND 	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 更新
			$aJumpMsg['0']['sMsg'] = aLIST['UPDATESUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
/*
	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						sListName0,
						sListTable0,
						nOnline,
						nMid,
						nSort
				FROM 	'.END_MENU_LIST.'
				WHERE nId = :nId
				AND 	nOnline != 99';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOld === false)
		{
			$nErr	= 1;
			$sMsg	= aERROR['NODATA'];
		}
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aSQL_Array = array(
				'nOnline'	=> (int) 99,
			);

			$sSQL = '	UPDATE	'.END_MENU_LIST.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND 	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 刪除
			$aJumpMsg['0']['sMsg'] = aLIST['DELETESUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_list_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
*/
?>
<?php
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_menu_kind.php');

	$nId			= filter_input_int('nId',		INPUT_REQUEST);
	$sMenuName0		= filter_input_str('sMenuName0',	INPUT_POST,'');
	$sMenuTable0	= filter_input_str('sMenuTable0',	INPUT_POST,'');
	$nOnline		= filter_input_int('nOnline',		INPUT_POST,0);
	$nSort		= filter_input_int('nSort',		INPUT_POST,0);

	$nErr	= 0;
	$sMsg = '';

	if ($aJWT['a'] == 'INS')
	{
		if ($sMenuName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYNAME'].'<br>';
		}
		if ($sMenuTable0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYTABLE'].'<br>';
		}
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0_upt0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aSQL_Array = array(
				'sMenuName0'	=> (string) $sMenuName0,
				'sMenuTable0'	=> (string) $sMenuTable0,
				'nOnline'		=> (int) $nOnline,
				'nSort' 		=> (int) $nSort ,
			);
			$sSQL = 'INSERT INTO '.END_MENU_KIND.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			$rs = sql_query($Result);
			$nLast_id = $oPdo->lastInsertId();

			# 紀錄動作 - 新增
			$aJumpMsg['0']['sMsg'] = aMENU['INSERTSUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						sMenuName0,
						sMenuTable0,
						nOnline,
						nSort
				FROM 	'.END_MENU_KIND.'
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
		if ($sMenuName0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYNAME'].'<br>';
		}
		if ($sMenuTable0 == '')
		{
			$nErr	= 1;
			$sMsg	.= aERROR['EMPTYTABLE'].'<br>';
		}
		if ($nErr == 1)
		{
			$aJumpMsg['0']['sMsg'] = $sMsg;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0_upt0.php']).'&nId='.$nId;
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aSQL_Array = array(
				'nOnline'		=> (int) $nOnline,
				'nSort' 		=> (int) $nSort ,
			);

			$sSQL = '	UPDATE	'.END_MENU_KIND.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND 	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 更新
			$aJumpMsg['0']['sMsg'] = aMENU['UPDATESUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						sMenuName0,
						sMenuTable0,
						nOnline,
						nSort
				FROM 	'.END_MENU_KIND.'
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
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
		else
		{
			$aSQL_Array = array(
				'nOnline'	=> (int) 99,
			);

			$sSQL = '	UPDATE	'.END_MENU_KIND.'
					SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
					WHERE	nId = :nId
					AND 	nOnline != 99
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# 紀錄動作 - 刪除
			$aJumpMsg['0']['sMsg'] = aMENU['DELETESUCCESS'];
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/end_menu/php/_end_menu_kind_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}
	}
?>
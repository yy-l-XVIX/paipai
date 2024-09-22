<?php
	require_once(dirname(dirname(dirname(__file__))) .'/System/Connect/UserClass.php');
	$nS		= filter_input_int('run_page',INPUT_REQUEST, 0);
	$nDebug 	= filter_input_int('dg',	INPUT_GET, 0);# 1=> 顯示路徑 2=> 顯示路徑 + exit;
	$nId 		= filter_input_int('nId',	INPUT_REQUEST, 0);
	$nLid 	= filter_input_int('nLid',	INPUT_REQUEST, 0);
	$sTempText = INS;
	if ($nId != 0 || $nLid != 0)
	{
		$sTempText = UPT;
	}
	$nAdmId = 0;
	$bFind = true;
	$sSid = isset($_COOKIE['sSid']) ? $_COOKIE['sSid'] : '';
	/*
		$nAdmId
		0 未登入
		> 0 帳號id(登入中)
	*/
	if ($aSystem['nLogin'] == 1) # 需要判斷登入頁面
	{
		$oAdm = new oUser();
		$aAdm = array();
		$aFolder = array();
		$aMenuCtrl = array();
		#判斷有沒有登入
		$nAdmId = $oAdm->checkCookie();
		if ($nAdmId == 0)
		{
			header('Location:'.sys_web_encode($aMenuToNo['pages/login/php/_login_0.php']));
			exit;
		}

		$aValue = array(
			'a'		=> 'UPT',
			't'		=> NOWTIME,
			'sSid'	=> $_COOKIE['sSid'],
		);
		$sJWT = sys_jwt_encode($aValue);
		# 更新cookie => curl update time
		$sUrl = WEBSITE['ADMURL'].sys_web_encode($aMenuToNo['pages/index/php/_index_0_act0.php']).'&run_page=1&sJWT='.$sJWT;
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		# curl 執行時間
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT,3);
		curl_exec($ch);

		$sSQL = '	SELECT 	Manager_.nId,
						Manager_.sAccount,
						Manager_.sPassword,
						Manager_.sIp,
						Manager_.nLid,
						Manager_.sName0,
						Manager_.nAdmType,
						Cookie_.sEmploye,
						Cookie_.nGoogle
				FROM 	'.END_MANAGER_DATA.' Manager_,
					'.END_MANAGER_COOKIE.' Cookie_
				WHERE Manager_.nId = :nId
				AND 	Manager_.nOnline = 1
				AND 	Cookie_.sSid LIKE :sSid
				AND 	Manager_.nId = Cookie_.nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nAdmId, PDO::PARAM_INT);
		$Result->bindValue(':sSid', $_COOKIE['sSid'], PDO::PARAM_STR);
		sql_query($Result);
		$aAdm = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aAdm === false || ($aAdm['sIp'] != '' && strrpos($aAdm['sIp'], USERIP) === false))
		{
			$aValue = array(
				'a'		=> 'LOGOUT',
				'nExp'	=> NOWTIME + JWTWAIT,
				'sAccount'	=> '',
				'nStatus'	=> 1,
			);
			header('Location:'.sys_web_encode($aMenuToNo['pages/login/php/_login_0_act0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue));
		}
		if($aAdm['nGoogle'] == 0)
		{
			$aTempPath = explode('/',$aRequire['Require']);
			$sSQL = '	SELECT 	nStatus
					FROM 	'.SYS_GOOGLE_VERIFY.'
					WHERE nUid = :nUid
					AND 	nOnline = 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $nAdmId, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false && $aTempPath[1] != 'login')
			{
				header('Location:'.sys_web_encode($aMenuToNo['pages/login/php/_google_0.php']));
			}
		}

		# 目錄權限
		# 對應出主標題跟目錄
		$sSQL = '	SELECT 	nId,
						nMkid,
						nMlid
				FROM 	'.END_MENU_CTRL.'
				WHERE nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aAdm['nId'], PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			if(!isset($aSystem['aNav'][$aRows['nMkid']]['aList'][$aRows['nMlid']]))
			{
				continue;
			}
			$sMenuKey = $aSystem['aNav'][$aRows['nMkid']]['aList'][$aRows['nMlid']]['sListTable0'];
			$aMenuCtrl[$aSystem['aNav'][$aRows['nMkid']]['sMenuTable0']][$sMenuKey] = true;
			$aFolder[$sMenuKey]['sListName0'] = aMENULANG['aLIST'][$sMenuKey];
			$aFolder[$sMenuKey]['sMenuTable0'] = $aSystem['aNav'][$aRows['nMkid']]['sMenuTable0'];
			$aFolder[$sMenuKey.'_upt0']['sListName0'] = $sTempText;
		}

		# 判斷權限 #
		$bFind = false;
		$aTemp0 = explode('/',$aRequire['Require']);
		$aTemp1 = explode('.',array_pop($aTemp0));

		if (isset($aMenuCtrl[$aTemp0[1]]))
		{
			foreach ($aMenuCtrl[$aTemp0[1]] as $LPsListTable => $LPbTrue)
			{
				if(stripos($aTemp1[0],$LPsListTable) !== false)
				{
					$bFind = true;
					break;
				}
			}
		}
		if ($aTemp0[1] == 'index' || $aTemp0[1] == 'tool' || $aTemp0[1] == 'login')
		{
			$bFind = true;
		}
		if($bFind === false )
		{
			$nS = 1;
			$aJumpMsg['0']['sMsg'] = ILLEGALVISIT;
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		}

		# 當頁標題
		$sHeadTitle = '';
		if (isset($aFolder[substr($aTemp1[0],1)]))
		{
			$sHeadTitle = $aFolder[substr($aTemp1[0],1)]['sListName0'];
		}
	}

	switch ($nDebug)
	{
		case '1':
			echo $aRequire['Require'].'<br />';
			break;
		case '2':
			echo $aRequire['Require'].'<br />';exit;
			break;
	}

	if (isset($aRequire['Require']) && $bFind == true)
	{
		require_once($aRequire['Require']);
	}
?>
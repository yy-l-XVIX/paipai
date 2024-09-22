<?php
	require_once(dirname(dirname(dirname(__file__))) .'/System/Connect/UserClass.php');
	$nS		= filter_input_int('run_page',	INPUT_REQUEST, 0);
	$nDebug 	= filter_input_int('dg',		INPUT_GET, 0);# 1=> 顯示路徑 2=> 顯示路徑 + exit;
	$nChangeKid = filter_input_str('nChangeKid',	INPUT_GET, '0');# 1=> 雇主 3=> 人才

	$bFind = true;
	$sSid = isset($_COOKIE['sSid']) ? $_COOKIE['sSid'] : '';
	$sCookiePage = isset($_COOKIE['sSavePage']) ? $_COOKIE['sSavePage'] : '';

	$sUserCurrentRole = '';		#會員當前身分
	$sUserCurrentRoleClass = '';
	if (isset($_GET['nJump']) && $_GET['nJump'] == 1)
	{
		setcookie('nJump',1,NOWTIME+7200); // 紀錄兩個小時
		$_COOKIE['nJump'] = 1;
	}
	if(isset($aSystem['aParam']['bMaintenance']) && $aSystem['aParam']['bMaintenance'] == 0 && !isset($_COOKIE['nJump']))
	{
		if ($nS == 1)
		{
			// 登入 忘記密碼
			echo json_encode(array(
				'nStatus'=> 0,
				'sMsg' => MAINTENANCE,
			));
		}
		else
		{
			require_once('#Maintenance.php');
		}

		exit;
	}

	// cookie 檢查跳轉 (2021-04-13 會有問題)
	// if ($nS == 0)
	// {
	// 	$aTempRequire = explode('/',$aRequire['Require']);
	// 	$sSavePage = array_pop($aTempRequire);
	// 	setcookie('sSavePage',$sSavePage, COOKIE['REMEMBER']);

	// 	if ($sSavePage != $sCookiePage)
	// 	{
	// 		echo '<!--'. $sSavePage .' | '.$sCookiePage.'-->';
	// 	}
	// }


	if ($sSid != '' && strpos($aRequire['Require'],'_login_0.php') !== false) // 已登入導去首頁
	{
		header('Location:'.sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']));
		exit;
	}

	if ($aSystem['nLogin'] == 1) # 需要判斷登入頁面
	{
		$oUser = new oUser();
		$aUser = array();
		$aRank = array();
		#判斷有沒有登入
		$nUid = $oUser->checkCookie();
		if ($nUid == 0)
		{
			header('Location:'.sys_web_encode($aMenuToNo['pages/login/php/_login_0.php']));
			exit;
		}

		$sSQL = '	SELECT 	User_.nId,
						User_.sKid,
						User_.nKid,
						User_.nLid,
						User_.sName0,
						User_.sName1,
						User_.sTransPassword,
						User_.sAccount,
						User_.nStatus,
						User_.nType3,
						User_.sExpired0,
						User_.sExpired1,
						User_.sPromoCode,
						User_.nCreateTime,
						Money_.nMoney
				FROM 		'.CLIENT_USER_DATA.' User_
				JOIN		'.CLIENT_USER_MONEY.' Money_
				ON		User_.nId = Money_.nUid
				WHERE 	User_.nId = :nUid
				AND		nOnline != 99';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $nUid, PDO::PARAM_INT);
		sql_query($Result);
		$aUser = $Result->fetch(PDO::FETCH_ASSOC);
		// 禁止登入
		if($aUser === false || $aUser['nStatus'] == 10)
		{
			$aValue = array(
				'a'		=> 'LOGOUT',
				't'		=> NOWTIME,
				'nExp'	=> NOWTIME + JWTWAIT,
				'sAccount'	=> '',
				'nStatus'	=> 1,
			);
			header('Location:'.sys_web_encode($aMenuToNo['pages/login/php/_login_0_act0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue));
		}
		$aUser['aKid'] = explode(',', $aUser['sKid']);


		$aValue = array(
			'a'		=> 'STATUSUPT'.$aUser['nId'],
			't'		=> NOWTIME,
			'sSid' 	=> $_COOKIE['sSid'],
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData = array(
			'sJWT'	=> $sJWT,
			'nId'		=> $aUser['nId'],
		);
		if ($nChangeKid != $aUser['nKid'] && strpos($aUser['sKid'], $nChangeKid) !== false)
		{
			$aData['nKid'] = (int)$nChangeKid;
			$aUser['nKid'] = (int)$nChangeKid;
		}

		# 更新cookie => curl update time 2020.07.22 加更新會員庫存.總覽
		$sUrl = WEBSITE['WEBURL'].sys_web_encode($aMenuToNo['pages/index/php/_index_0_act0.php']).'&run_page=1';
		$ch = curl_init($sUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
		# curl 執行時間
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT,1);
		curl_exec($ch);
		curl_close($ch);

		switch ($aUser['nKid'])
		{
			case '1':
				$sUserCurrentRole = 'boss';
				$sUserCurrentRoleClass = 'Boss';
				break;

			case '3':
				$sUserCurrentRole = 'staff';
				break;
		}

		// check 當前方案是否在免費期間
		$sSQL = '	SELECT 	nFreeStartTime,
						nFreeEndTime
				FROM 	'.CLIENT_USER_KIND.'
				WHERE nLid = :nLid
				AND 	sLang LIKE :sLang
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid', $aUser['nKid'], PDO::PARAM_INT);
		$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		$aUser['nIsBetweenFreeTime'] = 0;
		if ($aRows['nFreeStartTime'] <= NOWTIME && $aRows['nFreeEndTime'] >= NOWTIME)
		{
			$aUser['nIsBetweenFreeTime'] = 1;
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
<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/list.php');

	$nGid 	= filter_input_int('nJid',		INPUT_POST, 0);

	/**
	 * 回傳陣列 JSON
	 * @var Int nStatus
	 * 	回傳狀態值
	 * 	1 => 正常 其餘待補
	 * @var String sMsg
	 * 	回傳訊息
	 * @var Array aData
	 * 	回傳陣列
	 * @var Int nAlertType
	 * 	回傳訊息提示類型
	 * 	0 => 不需提示框
	 * @var String sUrl
	 * 	回傳後導頁檔案
	 */
	$aReturn = array(
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> ''
	);
	$aEditLog = array();
	$aCheck = array();

	// 收藏工作
	if ($aJWT['a'] == 'INS')
	{
		$sSQL = '	SELECT 1
				FROM 	'.CLIENT_USER_JOB_FAVORITE.'
				WHERE nGid = :nGid
				AND 	nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows !== false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['SAVED'].'<br>';
		}

		$sSQL = '	SELECT 	1
				FROM 	'.CLIENT_GROUP_CTRL.' Group_,
					'.CLIENT_JOB.' Job_
				WHERE Group_.nId = :nId
				AND 	Group_.nOnline = 1
				AND 	Group_.nType1 = 1
				AND 	Group_.nId = Job_.nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nUid' 		=> (int) $aUser['nId'],
				'nGid' 		=> (int) $nGid,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '.CLIENT_USER_JOB_FAVORITE.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_USER_JOB_FAVORITE]['aOld'] = array();
			$aEditLog[CLIENT_USER_JOB_FAVORITE]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_USER_JOB_FAVORITE]['aNew']['nId'] = $nLastId;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101001,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = INSV;
		}
	}
	//移除收藏工作
	if ($aJWT['a'] == 'DEL')
	{
		$sSQL = '	SELECT 	nId,
						nGid,
						nUid,
						nCreateTime
				FROM 	'.CLIENT_USER_JOB_FAVORITE.'
				WHERE nUid = :nUid
				AND 	nGid = :nGid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aData === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$sSQL = '	DELETE FROM '.CLIENT_USER_JOB_FAVORITE.'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_query($Result);

			#紀錄動作 - 新增
			$aEditLog[CLIENT_USER_JOB_FAVORITE]['aOld'] = $aData;
			$aEditLog[CLIENT_USER_JOB_FAVORITE]['aNew'] = array();
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aData['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101002,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = UPTV;
		}
	}
	// 我要應徵(加入群組)
	if ($aJWT['a'] == 'JOIN')
	{
		// 檢查工作
		$sSQL = '	SELECT 	Group_.nId,
						Group_.nUid,
						Job_.nLid
				FROM 	'.CLIENT_GROUP_CTRL.' Group_,
					'.CLIENT_JOB.' Job_
				WHERE Group_.nId = :nId
				AND 	Group_.nOnline = 1
				AND 	Group_.nType1 = 1
				AND 	Job_.nStatus = 0
				AND 	Group_.nId = Job_.nGid';

		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$aGroup = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aGroup === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
			$sBackUrl = '';
		}
		// 雇主不可以應徵自己的工作
		if ($aGroup['nUid'] == $aUser['nId'])
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] =  aERROR['CANTJOIN'].'<br>';
			$sBackUrl = '';
		}
		// 雇主不可以按我要應徵
		if ($sUserCurrentRole == 'boss')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['BOSSJOIN'].'<br>';
			$sBackUrl = '';
		}
		# 人才已過期
		elseif ($sUserCurrentRole == 'staff' && strtotime($aUser['sExpired0']) < NOWTIME && $aUser['nIsBetweenFreeTime'] == 0)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['EXPIRED'].'<br>';
			$sBackUrl = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
		}
		# 會員審核
		elseif ($aUser['nStatus'] == 11)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['STATUS'].'<br>';
			$sBackUrl = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
		}
		// # 設定地區 2021-02-24 註解
		// elseif ($aUser['nLid'] == 0)
		// {
		// 	$aReturn['nStatus'] = 0;
		// 	$aReturn['sMsg'] = aERROR['LOCATION'].'<br>';
		// 	$sBackUrl = sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']);
		// }

		# 再次檢查是否上傳影片照片 / 生日身分證
		else
		{
			$sSQL = '	SELECT	sTable
					FROM 	'.CLIENT_IMAGE_CTRL.'
					WHERE nKid = :nKid
					AND 	sTable IN (\'client_user_video\',\'client_user_photo\')';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nKid', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				if (!isset($aCheck[$aRows['sTable']]))
				{
					$aCheck[$aRows['sTable']] = 0;
				}
				$aCheck[$aRows['sTable']]++;
			}
			$aValue = array(
				'sBackUrl' => sys_web_encode($aMenuToNo['pages/index/php/_list_0.php']).'&nLid='.$aGroup['nLid'],
			);
			if (empty($aCheck)) // 都沒上傳
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aERROR['UPLOADDATA'].'<br>';
				$sBackUrl = sys_web_encode($aMenuToNo['pages/center/php/_video_0.php']).'&sJWT='. sys_jwt_encode($aValue);
			}
			elseif (!isset($aCheck['client_user_video']) || $aCheck['client_user_video'] != $aSystem['aParam']['nVideoLimit'])
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aERROR['UPLOADVIDEO'].'<br>';
				$sBackUrl = sys_web_encode($aMenuToNo['pages/center/php/_video_0.php']).'&sJWT='. sys_jwt_encode($aValue);
			}
			elseif (!isset($aCheck['client_user_photo']) || $aCheck['client_user_photo'] != $aSystem['aParam']['nPhotoLimit'])
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aERROR['UPLOADPHOTO'].'<br>';
				$sBackUrl = sys_web_encode($aMenuToNo['pages/center/php/_photo_0.php']).'&sJWT='. sys_jwt_encode($aValue);
			}

			$sSQL = '	SELECT	sBirthday,
							sIdNumber
					FROM 	'.CLIENT_USER_DETAIL.'
					WHERE nUid = :nUid
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			// if ($aRows['sIdNumber'] == '' || $aRows['sBirthday'] == '') 2021-04-26 不使用身分證
			if ($aRows['sBirthday'] == '')
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aERROR['SETTING'].'<br>';
				$sBackUrl = sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']).'&sJWT='. sys_jwt_encode($aValue);
			}
		}

		// if ($aRows['nLid'] != $aUser['nLid']) // 不可以應徵非自己所在地的工作 2021-02-24 註解
		// {
		// 	$aReturn['nStatus'] = 0;
		// 	$aReturn['sMsg'] =  aERROR['DIFFENTLOCATION'].'<br>';
		// 	$sBackUrl = '';
		// }

		// 人才報班後，xx 分鐘內不可重覆報班
		$sSQL = '	SELECT 	List_.nId,
						List_.nCreateTime
				FROM 	'.CLIENT_USER_GROUP_LIST.' List_,
					'.CLIENT_GROUP_CTRL.' Group_
				WHERE List_.nUid = :nUid
				AND 	List_.nStatus = 1
				AND 	Group_.nType1 = 1
				AND 	Group_.nOnline = 1
				AND 	Group_.nId = List_.nGid
				ORDER BY List_.nCreateTime DESC
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows !== false && ($aRows['nCreateTime']+$aSystem['aParam']['nJobGapTime']) > NOWTIME)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = str_replace('[[::nTime::]]', ($aSystem['aParam']['nJobGapTime']/60), aERROR['JOINJOB']).'<br>';
			$sBackUrl = '';
		}

		// 統計人數
		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		sql_query($Result);
		$nMemberCount = $Result->rowCount();

		if ($nMemberCount >= $aSystem['aParam']['nMaxGroupMen']) // 到達人數上限
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['MAXGROUPMEN'];
			$sBackUrl = '';
		}

		$aBlockUid = myBlockUid($aUser['nId']);
		if (isset($aBlockUid[$aGroup['nUid']]))
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>'; // 因為封鎖 無法應徵工作
			$sBackUrl = '';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nUid' 		=> (int) $aUser['nId'],
				'nGid' 		=> (int) $nGid,
				'nStatus' 		=> (int) 1,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.CLIENT_USER_GROUP_LIST.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = array();
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew']['nId'] = $nLastId;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101003,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = INSV; // 應徵成功 加入會員
			$sBackUrl = sys_web_encode($aMenuToNo['pages/job/php/_my_job_0.php']).'&nFirstJoin=1&nId='.$nGid;
		}
		$aReturn['sUrl'] = $sBackUrl;
	}
	// 我要退出
	if ($aJWT['a'] == 'OUT')
	{
		if ($sUserCurrentRole == 'boss')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['CANTOUT'].'<br>';
		}

		$sSQL = '	SELECT 	nId,
						nUid,
						nGid,
						sCreateTime
				FROM 	'.CLIENT_USER_GROUP_LIST.'
				WHERE nGid = :nGid
				AND 	nUid = :nUid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid',$nGid,PDO::PARAM_INT);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aOld = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aOld === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}

		if ($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			// 從群組移除
			$sSQL = '	DELETE FROM '.CLIENT_USER_GROUP_LIST.'
					WHERE nId = :nId
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aOld['nId'], PDO::PARAM_INT);
			sql_query($Result);

			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = $aOld;
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = array();

			// 是否為上工人選
			$sSQL = '	SELECT 	nId,
							nGid,
							nLid,
							sEmploye,
							nStatus
					FROM 	'.CLIENT_JOB.'
					WHERE nGid = :nGid
					LIMIT 1 FOR UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false && strpos($aRows['sEmploye'], str_pad($aUser['nId'],9,0,STR_PAD_LEFT)) !== false && $aRows['nStatus'] == 0) // 未結案
			{
				// 上工人選移除
				$aEmploye = explode(',',$aRows['sEmploye']);
				unset($aEmploye[array_search (str_pad($aUser['nId'],9,0,STR_PAD_LEFT), $aEmploye)]);
				$aSQL_Array = array(
					'sEmploye'		=> (string) implode(',', $aEmploye),
					'sUpdateTime'	=> (string) NOWDATE,
					'nUpdateTime'	=> (int) NOWTIME,
				);
				$sSQL = '	UPDATE '.CLIENT_JOB.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_JOB]['aOld'] = $aRows;
				$aEditLog[CLIENT_JOB]['aNew'] = $aSQL_Array;
			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aOld['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101004,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();

			$aReturn['sMsg'] = aLIST['SUCCESSOUT']; // 退出成功
			$aReturn['aData']['sBtnText'] = aLIST['JOIN'];
			$aReturn['sUrl'] =  sys_web_encode($aMenuToNo['pages/index/php/_list_0.php']).'&nLid='.$aRows[
				'nLid'];
		}
	}

	echo json_encode($aReturn);
	exit;
?>
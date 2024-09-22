<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/post_job.php');

	$nId 		= filter_input_int('nId',		INPUT_POST, 0);
	$nOnline	= filter_input_int('nOnline',		INPUT_POST, 1);
	$sName0 	= filter_input_str('sName0',		INPUT_POST, '');
	$nEmploye 	= filter_input_int('nEmploye',	INPUT_POST, 0);
	$nAid 	= filter_input_int('nAid',		INPUT_POST, 0);
	$nStatus 	= filter_input_int('nStatus',		INPUT_POST, 0);
	$sStartTime = filter_input_str('sStartTime',	INPUT_POST, '');
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_POST, '');
	$sContent0 	= isset($_POST['sContent0']) ? nl2br($_POST['sContent0']) : '';
	$aType0 	= isset($_POST['aJobType']) ? $_POST['aJobType'] : array();
	$sType0 	= '';

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

	# img 標籤轉換代號 <img src="images/emoji/01.png"> => [:01:] (?)
	$sContent0 = str_replace('<img class="EmojiImgIcon" src="images/emoji/', '[:', $sContent0);
	$sContent0 = str_replace('.png">', ':]', $sContent0);

	// 新增 再次發布
	if ($aJWT['a'] == 'INS')
	{
		if ($sName0 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['NAME0'].'<br>';
		}
		if ($sContent0 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['CONTENT0'].'<br>';
		}
		if ($nEmploye <= 0)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['WORKMEN'].'<br>';
		}
		if (strtotime($sStartTime) > strtotime($sEndTime))
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['TIMEERROR'].'<br>';
		}
		if (strtotime($sStartTime) < strtotime('today')-1 || strtotime($sEndTime) < strtotime('today')-1 )
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['TIMEPAST'].'<br>';
		}
		$sSQL = '	SELECT nCid
				FROM 	'.CLIENT_CITY_AREA.'
				WHERE nId = :nAid
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nAid',$nAid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['AREAERROR'].'<br>';
		}
		else
		{
			$sSQL = '	SELECT nLid
					FROM 	'.CLIENT_CITY.'
					WHERE nId = :nId
					AND 	nOnline = 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId',$aRows['nCid'],PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] .= aERROR['AREAERROR'].'<br>';
			}
			$nLid = $aRows['nLid'];
		}
		foreach ($aType0 as $LPnType0)
		{
			$sType0 .=  str_pad($LPnType0,9,0,STR_PAD_LEFT).',';
		}
		$sType0 = trim($sType0,',');

		if ($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			#建群
			$aSQL_Array = array(
				'nUid' 		=> (int) $aUser['nId'],
				'sName0' 		=> (string) $sName0,
				'nOnline' 		=> (int) 1,
				'nType0' 		=> (int) 1,
				'nType1' 		=> (int) 1,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.CLIENT_GROUP_CTRL.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = array();
			$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew']['nId'] = $nLastId;

			// 雇主進入群組
			$aSQL_Array = array(
				'nUid' 		=> (int) $aUser['nId'],
				'nGid' 		=> (int) $nLastId,
				'nStatus' 		=> (int) 1,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.CLIENT_USER_GROUP_LIST.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_USER_GROUP_LIST]['aOld'] = array();
			$aEditLog[CLIENT_USER_GROUP_LIST]['aNew'] = $aSQL_Array;

			#建job詳細資訊
			$aSQL_Array = array(
				'nGid' 		=> (int) $nLastId,
				'sName0' 		=> (string) $sName0,
				'nEmploye' 		=> (int) $nEmploye,
				'sContent0' 	=> (string) $sContent0,
				'sType0'		=> (string) $sType0,
				'nStatus' 		=> (int) $nStatus,
				'sEmploye' 		=> (string) '',
				'nAid' 		=> (int) $nAid,
				'nLid' 		=> (int) $nLid,
				'nStartTime'	=> (int) strtotime($sStartTime),
				'sStartTime'	=> (string) $sStartTime,
				'nEndTime'		=> (int) strtotime($sEndTime),
				'sEndTime'		=> (string) $sEndTime,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '.CLIENT_JOB.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_JOB]['aOld'] = array();
			$aEditLog[CLIENT_JOB]['aNew'] = $aSQL_Array;

			// $aActionLog = array(
			// 	'nWho'		=> (int) $aUser['nId'],
			// 	'nWhom'		=> (int) 0,
			// 	'sWhomAccount'	=> (string) '',
			// 	'nKid'		=> (int) $nLastId,
			// 	'sIp'			=> (string) USERIP,
			// 	'nLogCode'		=> (int) 7100601,
			// 	'sParam'		=> (string) json_encode($aEditLog),
			// 	'nType0'		=> (int) 0,
			// 	'nCreateTime'	=> (int) NOWTIME,
			// 	'sCreateTime'	=> (string) NOWDATE,
			// );
			// DoActionLog($aActionLog);

			// 上圖
			if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
			{
				$aFile['sTable'] = CLIENT_JOB;
				$aFile['aFile'] = $_FILES['sFile'];
				$aFileInfo = goImage($aFile);

				if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
				{
					$oPdo->rollback();
					$aReturn['nStatus'] = 0;
					$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
					$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/job/php/_post_job_0.php']);
					echo json_encode($aReturn);
					exit;
				}
				else
				{
					$aTmp = explode('.',$aFileInfo['sFilename']);
					$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
					$sFname = $aFileInfo['sFilename'];
				}

				$aSQL_Array = array(
					'nKid'		=> (int) $nLastId,
					'sTable'		=> (string) CLIENT_JOB,
					'sFile'		=> (string) $sFname,
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'  	=> (string) NOWDATE,
				);

				$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
				$nImageLastId = $oPdo->lastInsertId();

				#紀錄動作 - 新增
				$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;

			}

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100601,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$oPdo->commit();
			$aReturn['sMsg'] = INSV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']).'&nStatus='.$nStatus;
		}
	}
	//草稿
	if ($aJWT['a'] == 'UPT')
	{
		$sSQL = '	SELECT 	Job_.nId,
						Job_.nStatus,
						Job_.sName0,
						Job_.nEmploye,
						Job_.sStartTime,
						Job_.sEndTime,
						Job_.nAid,
						Job_.sType0,
						Job_.sContent0
				FROM 	'.CLIENT_GROUP_CTRL.' Group_,
					'.CLIENT_JOB.' Job_
				WHERE Group_.nUid = :nUid
				AND 	Group_.nId = :nId
				AND 	Group_.nOnline = 1
				AND 	Group_.nId = Job_.nGid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId',$nId,PDO::PARAM_INT);
		$Result->bindValue(':nUid',$aUser['nId'],PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aData === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA.'<br>';
		}
		if ($sName0 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['NAME0'].'<br>';
		}
		if ($sContent0 == '')
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['CONTENT0'].'<br>';
		}
		if ($nEmploye <= 0)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['WORKMEN'].'<br>';
		}
		if (strtotime($sStartTime) > strtotime($sEndTime))
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['TIMEERROR'].'<br>';
		}
		if (strtotime($sStartTime) < strtotime('today')-1 || strtotime($sEndTime) < strtotime('today')-1 )
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['TIMEPAST'].'<br>';
		}
		$sSQL = '	SELECT nCid
				FROM 	'.CLIENT_CITY_AREA.'
				WHERE nId = :nAid
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nAid',$nAid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] .= aERROR['AREAERROR'].'<br>';
		}
		else
		{
			$sSQL = '	SELECT nLid
					FROM 	'.CLIENT_CITY.'
					WHERE nId = :nId
					AND 	nOnline = 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId',$aRows['nCid'],PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] .= aERROR['AREAERROR'].'<br>';
			}
			$nLid = $aRows['nLid'];
		}

		if ($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nStatus' 		=> (int) $nStatus,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_JOB.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);

			#紀錄動作 - 新增
			$aEditLog[CLIENT_JOB]['aOld'] = $aData;
			$aEditLog[CLIENT_JOB]['aNew'] = $aSQL_Array;
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100602,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = UPTV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']).'&nStatus='.$nStatus;
		}
	}

	if ($aJWT['a'] == 'CHANGECITY')
	{
		$aReturn['aData'][] = array(
			'sName0'	=> aPOSTJOB['SELECTAREA'],
			'nId'		=> 0,
		);

		$sSQL = '	SELECT 	nId
				FROM 	'.CLIENT_CITY.'
				WHERE nOnline = 1
				AND 	nId = :nId
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows !== false)
		{
			$sSQL = '	SELECT 	nId,
							sName0
					FROM 	'.CLIENT_CITY_AREA.'
					WHERE nOnline = 1
					AND 	nCid = :nCid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nCid', $nId, PDO::PARAM_INT);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aReturn['aData'][] = $aRows;
			}
			$aReturn['nStatus'] = 1;
		}
		else
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}
	}

	echo json_encode($aReturn);
	exit;
?>
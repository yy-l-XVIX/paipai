<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_job.php');
	#require end

	#參數接收區
	$nId		= filter_input_int('nId',		INPUT_REQUEST,0);
	$nOnline	= filter_input_int('nOnline',		INPUT_POST, 1);
	$sName0 	= filter_input_str('sName0',		INPUT_POST, '');
	$nAid 	= filter_input_int('nAid',		INPUT_POST, 0);
	$nStatus 	= filter_input_int('nStatus',		INPUT_POST, 0);
	$sStartTime = filter_input_str('sStartTime',	INPUT_POST, '');
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_POST, '');
	$sContent0 	= isset($_POST['sContent0']) ? nl2br($_POST['sContent0']) : '';
	# img 標籤轉換代號 <img src="images/emoji/01.png"> => [:01:] (?)
	$sContent0 = str_replace('<img class="EmojiImgIcon" src="images/emoji/', '[:', $sContent0);
	$sContent0 = str_replace('.png">', ':]', $sContent0);
	#參數結束

	#參數宣告區
	$aData = array();
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
	$aEditLog = array(
		CLIENT_JOB	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	$nErr = 0;
	$sMsg = '';
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_job/php/_client_job_0.php']).$aJWT['sBackParam'];
	#宣告結束

	#程式邏輯區

	if ($aJWT['a'] == 'UPT'.$nId)
	{
		$sSQL = '	SELECT 	Job_.nId,
						Job_.nGid,
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
				WHERE Group_.nId = :nId
				AND 	Group_.nOnline != 99
				AND 	Group_.nId = Job_.nGid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$nErr = 1;
			$sMsg .= NODATA;
		}
		if ($sName0 == '')
		{
			$nErr = 1;
			$sMsg .= aERROR['NAME0'].'<br>';
		}
		if ($sContent0 == '')
		{
			$nErr = 1;
			$sMsg .= aERROR['CONTENT0'].'<br>';
		}
		if (strtotime($sStartTime) > strtotime($sEndTime))
		{
			$nErr = 1;
			$sMsg .=  aERROR['WORKTIME'].'<br>';
		}
		$sSQL = '	SELECT 1
				FROM 	'.CLIENT_CITY_AREA.'
				WHERE nId = :nAid
				AND 	nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nAid',$nAid,PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr = 1;
			$sMsg .=  aERROR['AREA'].'<br>';
		}

		if ($nErr == 0)
		{
			$aSQL_Array = array(
				'sName0'		=> (string) $sName0,
				'nAid'		=> (int) $nAid,
				'sContent0'		=> (string) $sContent0,
				'sStartTime'	=> (string) $sStartTime,
				'sEndTime'		=> (string) $sEndTime,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '. CLIENT_JOB . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			#紀錄動作 - 更新
			$aEditLog[CLIENT_JOB]['aOld'] = $aData;
			$aEditLog[CLIENT_JOB]['aNew'] = $aSQL_Array;

			// 變更標題 群組名稱順便更新
			if ($aData['sName0'] != $sName0)
			{
				$aSQL_Array = array(
					'sName0'		=> (string) $sName0,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);
				$sSQL = '	UPDATE '. CLIENT_GROUP_CTRL . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE	nId = :nId AND nOnline != 99 LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;
			}

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aData['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105302,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = UPTV;
		}
	}

	if ($aJWT['a'] == 'DEL'.$nId)
	{
		$sSQL = '	SELECT 	nId,
						nUid,
						nOnline,
						sName0
				FROM 	'.CLIENT_GROUP_CTRL.'
				WHERE nId = :nId
				AND 	nOnline != 99
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if (empty($aData))
		{
			$nErr = 1;
			$sMsg = NODATA;
		}
		if ($nErr == 0)
		{

			$aSQL_Array = array(
				'nOnline'		=> (int) 99,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '. CLIENT_GROUP_CTRL . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE	nId = :nId AND nOnline != 99 LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aData['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_GROUP_CTRL]['aOld'] = $aData;
			$aEditLog[CLIENT_GROUP_CTRL]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aAdm['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aData['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 8105303,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$sMsg = DELV;
		}
	}

	if ($aJWT['a'] == 'CHANGECITY')
	{
		$aReturn['aData'][] = array(
			'sName0'	=> aJOB['SELECTAREA'],
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

		echo json_encode($aReturn);
		exit;
	}
	#程式邏輯結束

	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
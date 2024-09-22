<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/post_job.php');

	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array(
		'0'	=> 'plugins/js/js_date/laydate.js',
		'1'	=> 'plugins/js/File.js',
		'2'	=> 'plugins/js/job/post_job.js',
		'3'	=> 'plugins/js/EmojiInsert.js',
		'4'	=> 'plugins/js/SnoozeKeywords.js',
	);
	#js結束

	#參數接收區
	$nId 		= filter_input_int('nId',	INPUT_GET, 0);
	#參數結束

	#給此頁使用的url
	$aUrl = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/job/php/_post_job_0_act0.php']),
		'sBack'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']),
		'sHtml'	=> 'pages/job/'.$aSystem['sClientHtml'].$aSystem['nClientVer'].'/post_job_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'sName0'		=> '',
		'nEmploye'		=> '',
		'nStatus'		=> 0,
		'sContent0'		=> '',
		'sType0'		=> '',
		'aType0'		=> array(),
		'nCid'		=> 0,
		'nAid'		=> 0,
		'sStartTime'	=> date('Y-m-d 00:00:00'),
		'sEndTime'		=> date('Y-m-d 23:59:59'),
	);
	$aCity = array(
		'0'	=> array(
			'sName0' => aPOSTJOB['SELECTCITY'],
			'sSelect'=> '',
		),
	);
	$aArea = array(
		'0'	=> array(
			'sName0' => aPOSTJOB['SELECTAREA'],
			'sSelect'=> '',
		),
	);
	$aValue = array(
		'a'		=> 'INS',
	);
	$sJWT = sys_jwt_encode($aValue);
	$aValue = array(
		'a'		=> 'CHANGECITY',
	);
	$sChangeCityJWT = sys_jwt_encode($aValue);
	$nErr = 0;
	$sErrMsg = '';

	$aJumpMsg['0']['sMsg'] = '123';
	$aJumpMsg['0']['nClicktoClose'] = 1;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqClose';

	$aJumpMsg['1'] = $aJumpMsg['0'];
	$aJumpMsg['1']['nClicktoClose'] = 0;
	$aJumpMsg['1']['aButton']['0']['sClass'] = '';

	$aJumpMsg['dataprocessing'] = array(
		'sBoxClass'	=>	'',
		'sShow'	=>	0,
		'sTitle'	=>	'',
		'sIcon'	=>	'',
		'sMsg'	=>	DATAPROCESSING,# 資料處理中
		'sArticle'	=>	'',
		'aButton'	=>	array(),
		'nClicktoClose'=>	0,
	);

	#宣告結束

	#程式邏輯區
	if ($sUserCurrentRole != 'boss')
	{
		$nErr = 1;
		$sErrMsg = PARAMSERR;
		$aUrl['sBack'] = sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
	}
	if ($aUser['nLid'] == 0)
	{
		$nErr = 1;
		$sErrMsg = aPOSTJOB['SETLOCATION'];
		$aUrl['sBack'] = sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']);
	}
	if ($nId != 0)
	{
		$sSQL = '	SELECT 	Group_.nId,
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
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$nErr = 1;
			$sErrMsg = NODATA;
		}
		else
		{
			$aRows['sContent0'] = convertContent($aRows['sContent0']);
			$aData = $aRows;
			$aData['aType0'] = explode(',',$aRows['sType0']);
			if ($aRows['nStatus'] == 10)
			{
				$aValue = array(
					'a'		=> 'UPT',  # 草稿
				);
				$sJWT = sys_jwt_encode($aValue);
			}
			else
			{
				$aValue = array(
					'a'		=> 'INS', # 再次發布
				);
				$sJWT = sys_jwt_encode($aValue);
			}
		}
	}
	foreach ($aData['aType0'] as $LPnI => $LPsType)
	{
		$aData['aType0'][$LPnI] = (int) $LPsType;
	}

	if ($aData['nAid'] != 0)
	{
		$sSQL = '	SELECT 	nId,
						nCid,
						sName0
				FROM 	'.CLIENT_CITY_AREA.'
				WHERE nOnline = 1
				AND 	nCid IN ( SELECT nCid FROM '.CLIENT_CITY_AREA.' WHERE nId = :nId )';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nId', $aData['nAid'], PDO::PARAM_INT);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aArea[$aRows['nId']] = array(
				'sName0' => $aRows['sName0'],
				'sSelect'=> '',
			);

			if ($aRows['nId'] == $aData['nAid'])
			{
				$aData['nCid'] = $aRows['nCid'];
			}
		}
	}

	$sSQL = '	SELECT 	nId,
					sName0
			FROM 	'.CLIENT_JOB_TYPE.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aJobType[$aRows['nId']] = array(
			'sName0' => $aRows['sName0'],
			'sSelect'=> '',
		);

		if (in_array($aRows['nId'], $aData['aType0']))
		{
			$aJobType[$aRows['nId']]['sSelect'] = 'checked';
		}
	}

	$sSQL = '	SELECT 	nId,
					sName0
			FROM 	'.CLIENT_CITY.'
			WHERE nOnline = 1';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
	{
		$aCity[$aRows['nId']] = array(
			'sName0' => $aRows['sName0'],
			'sSelect'=> '',
		);
	}
	$aArea[$aData['nAid']]['sSelect'] = 'selected';
	$aCity[$aData['nCid']]['sSelect'] = 'selected';

	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	if ($nErr == 1)
	{
		$aJumpMsg['0']['sMsg'] = $sErrMsg;
		$aJumpMsg['0']['sShow'] = 1;
		$aJumpMsg['0']['aButton']['0']['sUrl'] = $aUrl['sBack'];
		$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
		$aJumpMsg['0']['aButton']['0']['sClass'] = '';
	}
	else
	{
		$aRequire['Require'] = $aUrl['sHtml'];
	}

	#輸出結束
?>
<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/job_record.php');

	$nGid 		= filter_input_int('nJid',		INPUT_REQUEST,0);
	$nScore 		= filter_input_int('nScore',		INPUT_POST,0);
	$sContent0 		= isset($_POST['sContent0']) ? nl2br($_POST['sContent0']) : '';

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
		'nAlertType'	=> 1,
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/center/php/_job_record_0.php'])
	);
	$nBankCount = 0;

	if ($aJWT['a'] == 'SCORED'.$nGid)
	{
		$sSQL = '	SELECT 	nId,
						nStatus
				FROM 	'.CLIENT_JOB_SCORE.'
				WHERE nGid = :nGid
				AND 	nUid = :nUid
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nGid', $nGid, PDO::PARAM_INT);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = NODATA;
		}
		if ($aRows['nStatus'] == 1)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['FINSIH'];
		}
		if ($nScore <= 0)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aERROR['SCORED'];
		}

		if($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();
			$aSQL_Array = array(
				'sContent0'		=> (string) $sContent0,
				'nScore'		=> (string) $nScore,
				'nStatus'		=> (int) 1,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_JOB_SCORE.' SET '.sql_build_array('UPDATE', $aSQL_Array).'
					WHERE	nId = :nId LIMIT 1 ';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_JOB_SCORE]['aOld'] = $aRows;
			$aEditLog[CLIENT_JOB_SCORE]['aNew'] = $aSQL_Array;

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $aRows['nId'],
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100701,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$oPdo->commit();

			$aReturn['sMsg'] = aRECORD['INSV'];
		}
	}

	echo json_encode($aReturn);
	exit;
?>
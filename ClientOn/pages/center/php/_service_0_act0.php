<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/service.php');


	$nKind	= filter_input_int('nKind',		INPUT_POST, 0);
	$sQuestion	= filter_input_str('sQuestion',	INPUT_POST, '',201);

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
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/center/php/_service_0.php']),
	);

	if ($aJWT['a'] == 'INS')
	{
		$sSQL = '	SELECT	1
				FROM		'.CLIENT_SERVICE_KIND.'
				WHERE		nOnline = 1
				AND		sLang LIKE :sLang
				AND		nLid = :nKind';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
		$Result->bindValue(':nKind', $nKind, PDO::PARAM_INT);
		sql_query($Result);
		$aRows = $Result->fetch(PDO::FETCH_ASSOC);
		if($aRows === false)
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aSERVICE['NOKIND'];
		}
		else
		{
			if ($sQuestion == '')
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aSERVICE['EMPTYTEXT'];
			}

			if(mb_strlen($sQuestion) > 100)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = aSERVICE['OVERWORD'];
			}
		}

		if($aReturn['nStatus'] == 1)
		{
			$aSQL_Array = array(
				'nUid'		=> (int) $aUser['nId'],
				'nKid'		=> (int) $nKind,
				'sQuestion'		=> (string) $sQuestion,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
			);
			$sSQL = 'INSERT INTO '.CLIENT_SERVICE.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_SERVICE]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_SERVICE]['aNew']['nId'] = $nLastId;

			#紀錄動作 - 新增
			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) $aUser['nId'],
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7101201,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = INSV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_service_list_0.php']);

		}
	}
	echo json_encode($aReturn);
	exit;
?>
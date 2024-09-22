<?php
	ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/error_log.txt');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/System.php');
	require_once(dirname(dirname(__FILE__)).'/#Define.php');
	require_once(dirname(dirname(__FILE__)).'/#DefineTable.php');
	require_once(dirname(dirname(__FILE__)).'/#Function.php');
	$aSystem['nConnect'] = 2;
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/ConnectBase.php');
	require_once(dirname(dirname(__FILE__)).'/lang/'.$aSystem['sLang'].'/define.php');

	$sDate = filter_input_str('d',INPUT_REQUEST,date('Y-m-d', time())); #日期基準點
	$nRange = filter_input_int('r',INPUT_REQUEST,30); #區間值
	$sType = filter_input_str('t',INPUT_REQUEST,'day'); #月 天 禮拜

	# end_log / end_log_account / end_manager_login / client_user_login
	if ( $sDate != '' )
	{
		$nNow = time();
		$nDate = strtotime($sDate);
		$sStart = '';
		$sEnd = '';
		$nRange = (int)$nRange;
		switch($sType)
		{
			case 'day':
				if ($nRange < 30) # 2019-09-19 YL 只能搬40天之前注單
				{
					echo $nRange;
					exit;
				}
				$sRange = '-'.$nRange.' day';
				$nStart = strtotime($sRange,$nDate);
				$sStart = date('Y-m-d 00:00:00',$nStart);
				$nEnd = strtotime($sRange,$nDate); # 2019-08-06 YL
				$sEnd = date('Y-m-d 23:59:59',$nEnd);
				break;
			# 指定要處理時間 2019-10-14 YL++
			case 'custom':
				$sStart = date('Y-m-d 00:00:00',$nDate);
				$sEnd = date('Y-m-d 23:59:59',$nDate);
				break;
		}

		$aVal = array(
			'nStart'		=> strtotime($sStart),
			'nEnd'		=> strtotime($sEnd),
			'aMoveData'		=> array(
				END_LOG	=> END_LOG_MOVE,
				END_LOG_ACCOUNT	=> END_LOG_ACCOUNT_MOVE,
				END_MANAGER_LOGIN	=> END_MANAGER_LOGIN_MOVE,
				CLIENT_USER_LOGIN	=> CLIENT_USER_LOGIN_MOVE,
			),
			'sRecordDB'		=> SYS_MOVE_RECORD,
		);

		$oPdo->beginTransaction();
		MoveDataRecord($aVal);
		$oPdo->commit();

		echo date('Y-m-d ', time()).' 已執行過=>'. $sStart;
		exit;
	}

	function MoveDataRecord($aVal)
	{
		global $oPdo;

		$nStart = $aVal['nStart'];
		$nEnd = $aVal['nEnd'];
		$aMoveTable = $aVal['aMoveData'];  #各種 log
		$sRecordDB = $aVal['sRecordDB']; #sys_ctrl_move

		$aType = array();
		$aData = array();
		$aErr = array();

		foreach ($aMoveTable as $LPsTable => $LPbtrue)
		{
			$aData[$LPsTable] = array();
			$sSQL = '	SELECT *
					FROM 	'. $LPsTable .'
					WHERE nCreateTime >= \''. $nStart .'\'
					AND 	nCreateTime <= \''. $nEnd .'\'
					ORDER BY nId ASC';
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aData[$LPsTable][$aRows['nId']] = $aRows;
			}
			if (sizeof($aData[$LPsTable]) > 0)
			{
				$aType[$LPsTable] = array(
					'nRun'	=>0,
					'nRunTime'	=>0,
					'nCount'	=>0,
					'nRerun'	=>1,
				);
			}
		}

		if (!empty($aType))
		{
			$sSQL = '	SELECT nStatus,sTable
					FROM 	'. $sRecordDB .'
					WHERE nMoveTime = '. $nStart .' ';

			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				if ( isset($aType[$aRows['sTable']]) )
				{
					if ($aRows['nStatus'] == 1)
					{
						echo date('Y-m-d H:i:s',$nStart) .'已執行過 「成功」 <br />';
						$aType[$aRows['sTable']]['nRerun'] = 0;
					}
					else
					{
						echo date('Y-m-d H:i:s',$nStart) .'已執行過 「<font color=red>失敗</font>」 <br />';
						$aType[$aRows['sTable']]['nRerun'] = 1;
					}
				}
			}

			foreach ($aType as $LPsTable => $LPaMove)
			{
				$nLastId = 0;
				if ($LPaMove['nRerun'] == 1)
				{
					$aSQL_Array = array(
						'sTable'		=> $LPsTable,
						'nMoveTime'		=> $nStart,
						'sMoveTime'		=> date('Y-m-d H:i:s',$nStart),
						'nCreateTime'	=> NOWTIME,
						'sCreatetime'	=> NOWDATE,
					);
					$sSQL = 'INSERT INTO '. $sRecordDB .' ' . sql_build_array('INSERT', $aSQL_Array );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$nLastId = $oPdo->lastInsertId();
					if ($nLastId > 0)
					{
						$aVal = array(
							'aData'		=> $aData[$LPsTable],
							'nStart'		=> $nStart,
							'nEnd'		=> $nEnd,
							'sOldData'		=> $LPsTable,
							'sNewData'		=> $aMoveTable[$LPsTable],
						);
						# 寫入move
						$aRe = MoveDataClassify($aVal);

						if ($aRe['nStatus'] == 1)
						{
							#更新執行記錄
							$aSQL_Array = array(
								'nStatus'	=> (int) $aRe['nStatus'],
								'nRunTime'	=> (float) $aRe['nRunTime'],
								'nCount'	=> (int) $aRe['nCount'],
							);

							$sSQL = 'UPDATE	'. $sRecordDB .'
								SET	'. sql_build_array('UPDATE', $aSQL_Array) . '
								WHERE	nId = :nId';
							$Result = $oPdo->prepare($sSQL);
							$Result->bindValue(':nId', $nLastId, PDO::PARAM_INT);
							sql_build_value($Result, $aSQL_Array);
							sql_query($Result);
						}
					}
				}
			}
			$sData = "成功! ".PHP_EOL;
			echo $sData;
		}
		else
		{
			echo '未搬動任何資料 '.PHP_EOL;
		}
	}

	function MoveDataClassify($aVal)
	{
		global $oPdo;

		$aRe = array(
			'nStatus'	=> 0, #0 執行失敗 1執行成功
			'nRunTime' 	=> 0,
			'nCount'	=> 0,
		);
		$aDeleteId = array();

		$nRunStart = microtime(true);

		if (!empty($aVal['aData']))
		{
			#分類原lottery_data資料
			$sTable = $aVal['sNewData'];
			$sOldTable = $aVal['sOldData'];

			$nI = 0;
			foreach ($aVal['aData'] as $LPnId => $LPaInsertData)
			{
				if (true)
				{
					$aSQL_Array = $LPaInsertData;
					unset($aSQL_Array['nId']);
					$nLastId = 0;
					$aSQL_Array['nDataYear']	= date('Y', $LPaInsertData['nCreateTime']);
					$aSQL_Array['nDataMonth']	= date('m', $LPaInsertData['nCreateTime']);
					if (true)
					{
						$sSQL = 'INSERT INTO '. $sTable .' ' . sql_build_array('INSERT', $aSQL_Array );
						$Result = $oPdo->prepare($sSQL);
						sql_build_value($Result, $aSQL_Array);
						sql_query($Result);
						$nLastId = $oPdo->lastInsertId();

						if ($nLastId > 0)
						{
							$nI ++;
							$aDeleteId[$LPnId] = $LPnId;
						}
					}
				}
			}

			if ($nI == sizeof($aVal['aData']) && !empty($aDeleteId) )
			{
				#筆數吻合
				$sSQL = 'DELETE FROM '. $sOldTable .'
					WHERE nCreateTime >= '. $aVal['nStart'] .'
					AND 	nCreateTime <= '. $aVal['nEnd'] .'
					AND 	nId IN ( '.implode(',', $aDeleteId).' ) ';
				$Result = $oPdo->prepare($sSQL);
				sql_query($Result);

				$aRe['nCount'] = $nI;
				$aRe['nStatus'] = 1;
			}
		}

		$nRunEnd = microtime(true);
		$aRe['nRunTime'] = (float) ($nRunEnd - $nRunStart);
		return $aRe;
	}
?>
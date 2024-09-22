<?php
	// 20200225 kc 寫帳變記錄
	function DoLogAcc($aData)
	{
		global $oPdo;

		$sSQL = 'INSERT INTO '. END_LOG_ACCOUNT .' ' . sql_build_array('INSERT',$aData);
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aData);
		sql_query($Result);
	}

	function DoActionLog($aData)
	{
		global $oPdo,$aAdm;
		if (isset($aAdm['sEmploye']) && $aAdm['sEmploye'] != '')
		{
			$aData['sEmploye'] = $aAdm['sEmploye'];
		}
		$sSQL = 'INSERT INTO '. END_LOG .' ' . sql_build_array('INSERT',$aData);
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aData);
		sql_query($Result);
	}

	function GetTABLastId($aData)
	{
		global $oPdo;

		$nINSERT = (empty($aData['INSERT']))?0:$aData['INSERT'];
		$sTable = $aData['sTable'];
		$nUpdateTime = NOWTIME;
		$sUpdateTime = date("Y-m-d H:i:s",$nUpdateTime);

		$sSQL = 'SELECT nLastId FROM sys_last_id WHERE sTab LIKE :sTable FOR UPDATE';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':sTable',$sTable, PDO::PARAM_STR);
		sql_query($Result);
		$nLastId = $Result->fetchColumn();

		if($nINSERT > 0)
		{
			$aSQL_Array = array(
				'nLastId' => $nLastId+$nINSERT,
				'nUpdateTime' => $nUpdateTime,
				'sUpdateTime' => $sUpdateTime,
			);
			$sql = 'UPDATE sys_last_id
				SET '. sql_build_array('UPDATE', $aSQL_Array).'
				WHERE sTable LIKE :sTable';
			$Result = $oPdo->prepare($sql);
			$Result->bindValue(':sTable',$sTable, PDO::PARAM_STR);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
		}
		elseif($nLastId == null)
		{
			$sSQL = 'SELECT nId FROM :sTable ORDER BY nId DESC LIMIT 1';
			$Result->bindValue(':sTable',$sTable, PDO::PARAM_STR);
			sql_query($Result);
			$nLastId = $Result->fetchColumn();

			$aSQL_Array = array(
				'sTab' => $sTable,
				'nLastId' => $nLastId+$nINSERT,
				'nUpdateTime' => $nUpdateTime,
				'sUpdateTime' => $sUpdateTime,
			);
			$sSQL = 'INSERT INTO sys_last_id'.sql_build_array('INSERT', $aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
		}

		return $nLastId;
	}
?>
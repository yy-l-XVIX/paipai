<?php
	ignore_user_abort();
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');

	$nUid		= filter_input_int('nId',		INPUT_POST, 0);
	$nKid		= filter_input_int('nKid',		INPUT_POST, 0);

	if ($aJWT['a'] == 'STATUSUPT'.$nUid)
	{
		# 更新會員cookie
		$oAdm = new oUser();
		$oAdm->updateCookie(array('sSid'=>$aJWT['sSid']));

		# 變更身分
		if ($nKid != 0)
		{
			$oPdo->beginTransaction();
			$sSQL = '	SELECT 	nId,
							sKid,
							nKid
					FROM 	'.CLIENT_USER_DATA.'
					WHERE nId = :nId
					AND 	nOnline = 1
					AND 	sKid LIKE :sKid
					LIMIT 1 FOR UPDATE';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nUid, PDO::PARAM_INT);
			$Result->bindValue(':sKid', '%'.$nKid.'%', PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows !== false)
			{
				$aSQL_Array = array(
					'nKid'		=> (int) $nKid,
					'nUpdateTime'	=> (int) NOWTIME,
					'sUpdateTime'	=> (string) NOWDATE,
				);

				$sSQL = '	UPDATE '.CLIENT_USER_DATA.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
						WHERE nId = :nId
						LIMIT 1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nId', $aRows['nId'], PDO::PARAM_INT);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
			}
			$oPdo->commit();
		}
	}
	echo 1;
	exit;
?>
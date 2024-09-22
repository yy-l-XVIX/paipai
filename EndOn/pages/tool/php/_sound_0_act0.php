<?php

	$nMoneyInCount = 0;
	$nMoneyOutCount = 0;
	$nServiceCount = 0;
	$nRingCount = 0;
	$sRingClass = '';
	$bRing = false;
	$nMaxRing = 2;
	$aReturn = array(
		'nErr'	=> 1,
		'sMsg'	=> '',
	);

	if($aJWT['a'] == 'SOUND')
	{
		// 入款
		$sSQL = '	SELECT 	nId
				FROM		'.CLIENT_MONEY.'
				WHERE		nType0 IN ( 1, 2 )
				AND		nStatus = 0';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$nMoneyInCount++;
			$bRing = true;
		}

		if($nMoneyInCount > 0)
		{
			if(isset($_COOKIE['soundRecharge']) && $_COOKIE['soundRecharge'] == 1)
			{
				$nRingCount ++;
				$sRingClass .= 'Recharge,';
			}
		}

		// 出款
		$sSQL = '	SELECT 	nId
				FROM		'.CLIENT_MONEY.'
				WHERE		nType0 = 3
				AND		nStatus = 0';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$nMoneyOutCount++;
			$bRing = true;
		}

		if($nMoneyOutCount > 0)
		{
			if(isset($_COOKIE['soundWithdrawal']) && $_COOKIE['soundWithdrawal'] == 1)
			{
				$nRingCount ++;
				$sRingClass .= 'Withdrawal,';
			}
		}

		if($nRingCount == $nMaxRing)
		{
			$sRingClass = 'all';
		}

		if($nRingCount > 0)
		{
			$aReturn = array(
				'nErr'		=> 0,
				'sRingClass'	=> $sRingClass
			);
		}

		echo json_encode($aReturn);
		exit;
	}
?>
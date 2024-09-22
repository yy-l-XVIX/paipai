<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))) .'/inc/#Unload.php');

	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_withdrawal.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_log_account.php');

	#require end

	#參數接收區

	// Excel 用
	$nSelDay	= filter_input_int('nSelDay',		INPUT_GET, 1);
	$sStartTime = filter_input_str('sStartTime',	INPUT_GET, date('Y-m-d 00:00:00' , NOWTIME));
	$sEndTime 	= filter_input_str('sEndTime',	INPUT_GET, date('Y-m-d 23:59:59' , NOWTIME));
	$sAccount	= filter_input_str('sAccount',	INPUT_GET, '');
	$nType0	= filter_input_int('nType0',		INPUT_GET, -1);
	$nType1	= filter_input_int('nType1',		INPUT_GET, 0); # 來源
	$nType3	= filter_input_int('nType3',		INPUT_GET, -1);
	#參數結束

	#參數宣告區
	$nStartTime = strtotime($sStartTime);
	$nEndTime 	= strtotime($sEndTime);

	$aSearch = array();
	$aHideMember = array();
	$aData = array(
		'aData'	=> array(),
		'aTotal' 	=> array(
			'nBefore' 	=> 0,
			'nDelta'	=> 0,
			'nAfter'	=> 0,
		),
	);
	$aType0 = array(
		'-1'		=> array(
			'sName' 	=> aLOG['ALL'],
			'sSelect' 	=> '',
		),
		'1'	=> array(
			'sName' 	=> aLOG['MISSION'],
			'sSelect' 	=> '',
		),
		'2'	=> array(
			'sName' 	=> aLOG['CASHFLOW'],
			'sSelect' 	=> '',
		),
	);

	$aType3 = array(
		'-1'		=> array(
			'sName' 	=> aLOG['ALL'],
			'sSelect' 	=> '',
		),
		'0'	=> array(
			'sName' 	=> aLOG['TYPEMONEY'],
			'sSelect' 	=> '',
		),

	);
	$aMember[0] = array(
		'sAccount' => '',
		'nPa' => '',
	);

	#宣告結束

	#程式邏輯區

	// 匯出excel
	if ($aJWT['a'] == 'EXCEL')
	{
		header("Content-type:application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition:filename=". NOWTIME .".xls");

		$sCondition = ' WHERE nCreateTime >= :nStartTime AND nCreateTime <= :nEndTime';
		$aBindValue = array(
			'nStartTime'=> $nStartTime,
			'nEndTime' 	=> $nEndTime,
		);

		$sSQL = '	SELECT 	nUid
				FROM 	'.CLIENT_USER_HIDE.'
				WHERE nOnline = 1';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aHideMember[$aRows['nUid']] = $aRows['nUid'];
		}

		if ($sAccount != '')
		{
			$sSQL = '	SELECT 	nId
					FROM 		'.CLIENT_USER_DATA.'
					WHERE 	sAccount LIKE :sAccount
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount',$sAccount,PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);

			$sCondition .= ' AND nUid = :nUid';
			$aBindValue['nUid'] = $aRows['nId'];
		}
		if ($nType0 != -1)
		{
			$sCondition .= ' AND nType0 = :nType0';
			$aBindValue['nType0'] = $nType0;
		}
		if ($nType3 != -1)
		{
			$sCondition .= ' AND nType3 = :nType3';
			$aBindValue['nType3'] = $nType3;
		}
		if ( !empty($aHideMember) && $aAdm['nAdmType'] != 1)
		{
			$sCondition .= ' AND  nUid NOT IN ( '.implode(',', $aHideMember).' ) ';
		}

		$sSQL = '	SELECT	nId,
						sAccount
				FROM 	'.CLIENT_USER_DATA.'
				WHERE	nOnline != 99';
		$Result = $oPdo->prepare($sSQL);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aUserData[$aRows['nId']] = $aRows['sAccount'];
		}

		$sSQL = '	SELECT 	nId,
						nUid,
						nKid,
						nFromUid,
						nType0,
						nType1,
						nType2,
						nType3,
						nBefore,
						nDelta,
						nAfter,
						sParams,
						sCreateTime
				FROM 		'.END_LOG_ACCOUNT.'
				'.$sCondition.'
				AND 	nType3 != 1
				ORDER BY nCreateTime DESC, nId DESC';
		$Result = $oPdo->prepare($sSQL);
		sql_build_value($Result, $aBindValue);
		sql_query($Result);
		while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aData['aTotal']['nBefore'] += $aRows['nBefore'];
			$aData['aTotal']['nDelta'] += $aRows['nDelta'];
			$aData['aTotal']['nAfter'] += $aRows['nAfter'];

			$aData['aData'][$aRows['nId']] = $aRows;
			if ($aRows['nType0'] == 1)
			{
				$aSearch['aMission'][$aRows['nType1']] = $aRows['nType1'];
			}
			if ($aRows['nType0'] == 2)
			{
				$aSearch['aPayment'][$aRows['nType1']] = $aRows['nType1'];
			}
		}
		if (!empty($aSearch['aMission']))
		{
			$sSQL = '	SELECT	nLid,
							sName0
					FROM 	'.CLIENT_MISSIONS.'
					WHERE	nLid IN ( '.implode(',',$aSearch['aMission']).' )
					AND 	sLang LIKE :sLang';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sLang', $aSystem['sLang'], PDO::PARAM_STR);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$aMissionData[$aRows['nLid']] = $aRows['sName0'];
			}
		}
		foreach ($aData['aData'] as $LPnId => $LPaData)
		{
			$aData['aData'][$LPnId]['sAccount'] = $aUserData[$LPaData['nUid']];
			$aData['aData'][$LPnId]['sType0'] =	$aType0[$LPaData['nType0']]['sName'];
			$aData['aData'][$LPnId]['sType1'] = '';
			$aData['aData'][$LPnId]['sType2'] = aTYPE2[$LPaData['nType2']];
			$aData['aData'][$LPnId]['sType3'] =	$aType3[$LPaData['nType3']]['sName'];
			if ($LPaData['nType0'] == 1 && isset($aMissionData[$LPaData['nType1']]))
			{
				$aData['aData'][$LPnId]['sType1'] = $aMissionData[$LPaData['nType1']];
			}
			$aData['aData'][$LPnId]['sFromAccount'] = '';
			if($LPaData['nFromUid'] != 0 && isset($aUserData[$LPaData['nFromUid']]))
			{
				$aData['aData'][$LPnId]['sFromAccount'] = $aUserData[$LPaData['nFromUid']];
			}
		}

		echo $sStartTime.' ~ '.$sEndTime.'<br><br>';
		echo '<table border=2>';

		echo '<tr>';
		echo '<td> No. </td>';
		echo '<td>'. aLOG['TYPE0'] .'</td>';
		echo '<td colspan="3">'. aLOG['TYPE2'] .'</td>';
		echo '<td>'. aLOG['TYPE3'] .'</td>';
		echo '<td>'. aLOG['ACCOUNT'] .'</td>';
		echo '<td>'. aLOG['ID'] .'</td>';
		echo '<td>'. aLOG['BEFORE'] .'</td>';
		echo '<td>'. aLOG['DELTA'] .'</td>';
		echo '<td>'. aLOG['AFTER'] .'</td>';
		echo '<td>'. aLOG['CREATETIME'] .'</td>';
		echo '<td>'. aLOG['PARAM'] .'</td>';
		echo '</tr>';

		foreach ($aData['aData'] as $LPnId => $LPaData)
		{
			echo '<tr>';
			echo '<td>'.$LPnId.'</td>';
			echo '<td>'.$LPaData['sType0'].'</td>';
			echo '<td>'.$LPaData['sType2'].'</td>';
			echo '<td>'.$LPaData['sType1'].'</td>';
			echo '<td>'.$LPaData['sFromAccount'].'</td>';
			echo '<td>'.$LPaData['sType3'].'</td>';
			echo '<td>'.$LPaData['sAccount'].'</td>';
			echo '<td>'.$LPaData['nKid'].'</td>';
			echo '<td>'.$LPaData['nBefore'].'</td>';
			echo '<td>'.$LPaData['nDelta'].'</td>';
			echo '<td>'.$LPaData['nAfter'].'</td>';
			echo '<td>'.$LPaData['sCreateTime'].'</td>';
			echo '<td>'.$LPaData['sParams'].'</td>';

			echo '</tr>';
		}

		echo '<tr>';
		echo '<td colspan="7">'.aLOG['TOTAL'].'</td>';
		echo '<td>'.number_format($aData['aTotal']['nBefore'],3).'</td>';
		echo '<td>'.number_format($aData['aTotal']['nDelta'],3).'</td>';
		echo '<td>'.number_format($aData['aTotal']['nAfter'],3).'</td>';
		echo '<td colspan="2"></td>';
		echo '</tr>';
		echo '</table><br>';
		exit;

	}
	#程式邏輯結束
?>
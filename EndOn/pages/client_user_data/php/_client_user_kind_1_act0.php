<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__file__)))).'/inc/lang/'.$aSystem['sLang'].'/client_user_kind.php');
	#require??

	#參數接收區
	$nLid		= filter_input_int('nLid',		INPUT_REQUEST,0);
	$nPrice	= filter_input_str('nPrice',		INPUT_POST, 0);
	$nType0	= filter_input_int('nType0',		INPUT_POST, 0);
	$nType1	= filter_input_int('nType1',		INPUT_POST, 0);
	$nFreeDays	= filter_input_int('nFreeDays',	INPUT_POST, 0);
	$sPromoteBonus 	= filter_input_str('sPromoteBonus',		INPUT_POST,'',50);
	$sPromoteBonusTax = filter_input_str('sPromoteBonusTax',	INPUT_POST,'',50);
	$sFreeStartTime 	= filter_input_str('sFreeStartTime',	INPUT_POST,'',20);
	$sFreeEndTime 	= filter_input_str('sFreeEndTime',		INPUT_POST,'',20);
	$aName 	= array();
	$aContent 	= array();
	if(isset($_POST['sName0']))
	{
		$aName = $_POST['sName0'];
	}
	if(isset($_POST['sContent0']))
	{
		$aContent = $_POST['sContent0'];
	}
	$sPromoteBonus = trim($sPromoteBonus,',');
	$sPromoteBonusTax = trim($sPromoteBonusTax,',');
	#參數結束

	#參數宣告區
	$nErr	= 0;
	$sMsg = '';
	$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_kind_1_upt0.php']);
	$aId = array();
	$aData = array();
	$aEditLog = array(
		CLIENT_USER_KIND	=> array(
			'aOld' => array(),
			'aNew' => array(),
		),
	);
	#宣告結束

	#程式邏輯區
	if ($aJWT['a'] == 'UPT'.$nLid)
	{
		$sSQL = '	SELECT 	nId,
						sName0,
						sContent0,
						nPrice,
						sPromoteBonus,
						sPromoteBonusTax,
						nType1,
						nOnline,
						nType0,
						nFreeDays,
						sLang
				FROM 		'. CLIENT_USER_KIND .'
				WHERE 	nLid = :nLid';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nLid',$nLid,PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aId[$aRows['sLang']] = $aRows['nId'];
		}
		if (empty($aId))
		{
			$nErr	= 1;
			$sMsg = NODATA;
		}
		if ($nType0 == 1 && $nFreeDays <= 0)
		{
			$nErr	= 1;
			$sMsg = aERROR['FREEDAYS'];
		}
		if ($sPromoteBonus == '')
		{
			$nErr	= 1;
			$sMsg = aERROR['PROMOTEBONUS'];
		}

		if ($nErr == 0)
		{
			foreach(aLANG as $LPsLang => $LPsText)
			{
				$oPdo->beginTransaction();

				$aSQL_Array = array(
					'sName0'		=> (string) $aName[$LPsLang],
					'sContent0'		=> (string) $aContent[$LPsLang],
					'nPrice'		=> (float) $nPrice,
					'sPromoteBonus'	=> (string) $sPromoteBonus,
					'sPromoteBonusTax'=> (string) $sPromoteBonusTax,
					'nType1'		=> (int) $nType1,
					'nType0'		=> (int) $nType0,
					'nFreeDays'		=> (int) $nFreeDays,
					'sFreeStartTime'	=> (string)	$sFreeStartTime,
					'nFreeStartTime'	=> (int)	strtotime($sFreeStartTime),
					'sFreeEndTime'	=> (string)	$sFreeEndTime,
					'nFreeEndTime'	=> (int)	strtotime($sFreeEndTime),
					'nUpdateTime'	=> (int)	NOWTIME,
					'sUpdateTime'	=> (string)	NOWDATE,
				);

				if(isset($aId[$LPsLang]))
				{
					$sSQL = '	UPDATE '. CLIENT_USER_KIND . ' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
							WHERE	nId = :nId LIMIT 1';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nId', $aId[$LPsLang], PDO::PARAM_INT);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
				}
				//沒的話新增
				else
				{
					$aSQL_Array['nCreateTime']	= (int) NOWTIME;
					$aSQL_Array['sCreateTime']	= (string) NOWDATE;
					$aSQL_Array['sLang']		= (string) $LPsLang;
					$aSQL_Array['nLid']		= (int) $nLid;

					$sSQL = 'INSERT INTO '. CLIENT_USER_KIND .' ' . sql_build_array('INSERT', $aSQL_Array );
					$Result = $oPdo->prepare($sSQL);
					sql_build_value($Result, $aSQL_Array);
					sql_query($Result);
					$aId[$LPsLang] = $oPdo->lastInsertId();
				}

				#紀錄動作 - 更新
				$aEditLog[CLIENT_USER_KIND]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_USER_KIND]['aNew']['nId'] = $aId[$LPsLang];
				$aActionLog = array(
					'nWho'		=> (int) $aAdm['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $aId[$LPsLang],
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 8103002,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);
				$oPdo->commit();
			}
			$sMsg = UPTV;
			$sBackUrl = sys_web_encode($aMenuToNo['pages/client_user_data/php/_client_user_kind_1.php']);
		}
	}

	#程式邏輯結束
	$aJumpMsg['0']['sMsg'] = $sMsg;
	$aJumpMsg['0']['sShow'] = 1;
	$aJumpMsg['0']['aButton']['0']['sUrl'] = $sBackUrl;
	$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
?>
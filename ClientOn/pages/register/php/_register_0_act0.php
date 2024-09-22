<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/register.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/tool/SMSHttp.php');
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/ClientUserClass.php');

	$sAccount		= filter_input_str('sAccount',		INPUT_REQUEST, '', 16);
	$sName0		= filter_input_str('sName0',			INPUT_POST, '', 16);
	$sName1		= filter_input_str('sName1',			INPUT_POST, '', 16);
	$sPhone		= filter_input_str('sPhone',			INPUT_POST, '', 16);
	$sWechat		= filter_input_str('sWechat',			INPUT_POST, '', 16);
	$sPassword0		= filter_input_str('sPassword0',		INPUT_POST, '', 32);
	$sPassword1		= filter_input_str('sPassword1',		INPUT_POST, '', 32);
	$sTransPassword0	= filter_input_str('sTransPassword0',	INPUT_POST, '', 32);
	$sTransPassword1	= filter_input_str('sTransPassword1',	INPUT_POST, '', 32);
	$sPromoCode		= filter_input_str('sPromoCode',		INPUT_POST, '', 8);
	$nKid			= filter_input_int('nKid',			INPUT_POST, 0);
	$nVcode		= filter_input_int('nVcode',			INPUT_POST, 0);
	$oUser = new oClientUser();
	$sPassword0 = trim($sPassword0); // 註冊密碼去除空白
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

	if ($aJWT['a'] == 'GETVCODE')
	{
		// echo json_encode($_POST);exit;
		if (!preg_match('/^09[0-9]{8}$/', $sAccount))
		{
			$aReturn['sMsg'] = 'Format';
			$aReturn['aData']['sAccount'] = $sAccount;
		}
		else
		{
			// 0:帳號不存在 存在的話回傳nUid
			if ($oUser->checkAccount($sAccount) !== 0)
			{
				$aReturn['nStatus'] = 0;
				$aReturn['sMsg'] = 'Exist';
			}
			else
			{
				$nVcode = $oUser->getVcode($sAccount);
				if($nVcode == 0)
				{
					$aReturn['nStatus'] = 0;
					$aReturn['sMsg'] = 'Oncheck';
				}
				else
				{
					$aReturn['nStatus']= 1;
					if($aSystem['aParam']['nSMSSetting'] == 0) {
						$aReturn['sMsg'] = $nVcode;
					}

				}
			}
		}
	}


	if ($aJWT['a'] == 'INS')
	{
		$nLeng = strlen($sAccount);
		// if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sAccount) || $nLeng < 6 || $nLeng > 16)
		// 2021-02-24 帳號改手機號碼
		if (!preg_match('/^09[0-9]{8}$/', $sAccount))
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg'] .= aERROR['ACCOUNTFORMATE'].'<br>';
			$aReturn['aData']['sAccount'] = true;
		}
		else if($oUser->checkAccount($sAccount) !== 0)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg'] .= aERROR['ACCOUNTEXIST'].'<br>';
			$aReturn['aData']['sAccount'] = true;
		}
		$nLeng = strlen($sPassword0);
		if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sPassword0) || $nLeng < 6 || $nLeng > 16)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['PASSWORDFORMATE'].'<br>';
			$aReturn['aData']['sPassword0'] = true;
		}
		$nLeng = strlen($sPassword1);
		if(!preg_match('/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i', $sPassword1) || $nLeng < 6 || $nLeng > 16)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['CONFIRMFORMATE'].'<br>';
			$aReturn['aData']['sPassword1'] = true;
		}
		if($sPassword0 != $sPassword1)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['DIFFERENT'].'<br>';
			$aReturn['aData']['sPassword1'] = true;

		}
		// 2021-03-18 YL
		// if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword0))
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['PASSWORDFORMATE'].'<br>';
		// 	$aReturn['aData']['sTransPassword0'] = true;

		// }
		// if(!preg_match('/^[0-9]{6,12}$/', $sTransPassword1))
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['CONFIRMFORMATE'].'<br>';
		// 	$aReturn['aData']['sTransPassword1'] = true;

		// }
		// if($sTransPassword0 != $sTransPassword1)
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['DIFFERENT'].'<br>';
		// 	$aReturn['aData']['sTransPassword1'] = true;

		// }
		if(!preg_match('/^[0-9]{5}$/', $nVcode))
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['CODEFORMATE'].'<br>';
			$aReturn['aData']['nVcode'] = true;
		}
		else if($oUser->checkVcode($sAccount,$nVcode) === 0)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['CODEEXPIRED'].'<br>';
			$aReturn['aData']['nVcode'] = true;
		}
		if($sName0 == '' || mb_strlen($sName0) > 20)
		{
			$aReturn['nStatus']	= 0;
			$aReturn['sMsg']	.= aERROR['NAME0FORMATE'].'<br>';
			$aReturn['aData']['sName0'] = true;

		}
		// 2021-04-26 YL
		// if($sName1 == '' || mb_strlen($sName1) > 20)
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['NAME1FORMATE'].'<br>';
		// 	$aReturn['aData']['sName1'] = true;

		// }
		// if($sWechat == '' || strlen($sWechat) > 20 || !preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $sWechat))
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['WECHATFORMATE'].'<br>';
		// }
		// if (!preg_match('/^09[0-9]{8}$/', $sPhone))
		// {
		// 	$aReturn['nStatus']	= 0;
		// 	$aReturn['sMsg']	.= aERROR['PHONEFORMATE'].'<br>';
		// 	$aReturn['aData']['sPhone'] = true;
		// }

		if($sPromoCode == '')
		{
			$nPaId = $aSystem['aParam']['nAgentId']; // 如未填寫，為總代理下線
		}
		else
		{
			$sSQL = '	SELECT 	nId
					FROM 		'.CLIENT_USER_DATA.'
					WHERE 	nStatus != 99
					AND 		sPromoCode LIKE :sPromoCode';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sPromoCode', $sPromoCode, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				$aReturn['nStatus']	= 0;
				$aReturn['sMsg']	.= aERROR['NOPROMOCODE'].'<br>';
			}

			$nPaId = $aRows['nId'];
		}

		if($nPaId != 0)
		{
			$aPaLinkData = $oUser->getLinkData($nPaId);
			if ($aPaLinkData === false)
			{
				$aReturn['nStatus']	= 0;
				$aReturn['sMsg']	.= aERROR['NOPADATA'].'<br>';
			}
		}
		if($aReturn['nStatus'] == 1)
		{
			$sSQL = '	SELECT 	nType0,
							nFreeDays,
							nFreeStartTime,
							nFreeEndTime
					FROM 	'.CLIENT_USER_KIND.'
					WHERE nLid = :nLid
					AND 	nOnline = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nLid', $nKid, PDO::PARAM_INT);
			sql_query($Result);
			$aKind = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aKind === false)
			{
				$aReturn['nStatus']	= 0;
				$aReturn['sMsg']	.= NODATA.'<br>';
				echo json_encode($aReturn);
				exit;
			}
			$aRegister = array(
				'sAccount'		=> $sAccount,
				'sName0'		=> $sName0,
				'sName1'		=> $sName1,
				'sPhone'		=> $sAccount,
				'sWechat'		=> $sWechat,
				'sEmail'		=> '',
				'sPassword'		=> oCypher::ReHash($sPassword0),
				// 'sTransPassword'	=> oCypher::ReHash($sTransPassword0),
				'aPaLinkData'	=> $aPaLinkData,
				'nStatus'		=> 0,
				'nAgree'		=> 1,
				'nExpired0'		=> 0,
				'sExpired0'		=> '',
				'nExpired1'		=> 0,
				'sExpired1'		=> '',
				'sKid'		=> $nKid,
				'nFrom'		=> 1,	// 0:後台 1:前台
				'nRemember'		=> 0,
				'aDetail'		=> array(
					'sHeight'	=> '',
					'sSize'	=> '',
					'sContent0'	=> '',
					'sContent1'	=> '',
				),
			);

			// if ($aKind['nType0'] == 1) # 開啟免費試用
			// {
			// 	// $aRegister['nStatus'] = 0;
			// 	if ($nKid == 1) #雇主
			// 	{
			// 		$aRegister['nExpired1'] = strtotime(NOWDATE.'+'.$aKind['nFreeDays'].' day');
			// 		$aRegister['sExpired1'] = date('Y-m-d H:i:s',$aRegister['nExpired1']);
			// 	}

			// 	if ($nKid == 3) #人才
			// 	{
			// 		$aRegister['nExpired0'] = strtotime(NOWDATE.'+'.$aKind['nFreeDays'].' day');
			// 		$aRegister['sExpired0'] = date('Y-m-d H:i:s',$aRegister['nExpired0']);
			// 	}
			// }

			$oUser->register($aRegister);
			$oUser->login($aRegister);
			//$aReturn['sMsg']	= aREGISTER['SUCCESS'].'<br>'.aREGISTER['MEMBERREQUEST'];
			//$aReturn['sUrl']	= sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']);
			$aReturn['sMsg']	= aREGISTER['SUCCESS'];
			// $aReturn['sUrl']	= sys_web_encode($aMenuToNo['pages/recharge/php/_recharge_0.php']).'&nKid='.$nKid;

			if ($aKind['nFreeStartTime'] <= NOWTIME && $aKind['nFreeEndTime'] >= NOWTIME) // 免付費期間
			{
				$aReturn['sUrl']	= sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);
			}
			else
			{
				$aValue = array(
					'aUser' 	=> array(
						'aKid'	=> array($nKid),
						'sExpired0'	=> '',
						'sExpired1'	=> '',
					),
				);
				$aReturn['sUrl']	= sys_web_encode($aMenuToNo['pages/register/php/_choose_0.php']).'&sJWT='.sys_jwt_encode($aValue);
			}
		}

	}

	echo json_encode($aReturn);
	exit;
?>
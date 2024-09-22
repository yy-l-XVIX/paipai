<?php
	require_once('UserClass.php');
	class oClientUser extends oUser
	{
		public function register($aData)
		{
			global $oPdo,$aSystem;
			$sPromoCode = '';

			$aSQL_Array = array(
				'sKid'		=> (string) $aData['sKid'],
				'nKid'		=> (int) $aData['sKid'],
				'sAccount'		=> (string) $aData['sAccount'],
				'sPassword'		=> (string) $aData['sPassword'],
				// 'sTransPassword'	=> (string) $aData['sTransPassword'],
				'sName0'		=> (string) $aData['sName0'],
				'sName1'		=> (string) $aData['sName1'],
				'sPhone'		=> (string) $aData['sPhone'],
				'sWechat'		=> (string) $aData['sWechat'],
				'sEmail'		=> (string) $aData['sEmail'],
				'nOnline'		=> (int) 1,
				'nStatus'		=> (int) $aData['nStatus'],
				'sPendingStatus'	=> (string) '',
				'nAgree'		=> (int) $aData['nAgree'],
				'nExpired0'		=> (int) $aData['nExpired0'],
				'sExpired0'		=> (string) $aData['sExpired0'],
				'nExpired1'		=> (int) $aData['nExpired1'],
				'sExpired1'		=> (string) $aData['sExpired1'],
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
				'nUpdateTime'	=> (int) NOWTIME,
				'sUpdateTime'	=> (string) NOWDATE,
				'sPromoCode'	=> (string) ''
			);
			$aPendingFiled = explode(',', $aSystem['aParam']['sPendingField']);
			foreach ($aPendingFiled as $LPsFiled)
			{
				$aSQL_Array['sPendingStatus'] .= '1,';
			}
			$aSQL_Array['sPendingStatus'] = trim($aSQL_Array['sPendingStatus'],',');

			$aEditLog[CLIENT_USER_DATA]['aNew'] = $aSQL_Array;

			$sSQL = 'INSERT INTO '.CLIENT_USER_DATA.' '. sql_build_array('INSERT', $aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nUid = $oPdo->lastInsertId();

			// 產生推薦碼 8碼
			for ($i = 0; $i < (8 - strlen($nUid)); $i++)
			{
				$sTempWords = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$sPromoCode .= $sTempWords[mt_rand(0, 51)];
			}
			$sPromoCode .= $nUid;
			# update sPromoCode
			$aSQL_Array = array(
				'sPromoCode'	=> $sPromoCode,
			);
			$sSQL = 'UPDATE '.CLIENT_USER_DATA.' SET '. sql_build_array('UPDATE', $aSQL_Array).'
				WHERE nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nUid,PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			# detail
			$aSQL_Array = $aData['aDetail'];
			$aSQL_Array['nUid'] = $nUid;

			$sSQL = 'INSERT INTO '.CLIENT_USER_DETAIL.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aNewMoney = array(
				'Money' => (float) 0,
			);
			$aSQL_Array = oTransfer::PointUpdate($nUid,$aNewMoney,1);
			$aSQL_Array['nUid'] = $nUid;

			$sSQL = 'INSERT INTO '.CLIENT_USER_MONEY.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aPaLinkData = $aData['aPaLinkData'];

			$aSQL_Array = array(
				'nUid'		=> (int) $nUid,
				'nLevel'		=> (int) $aPaLinkData['nLevel'] + 1,
				'sLinkList'		=> (string) $aPaLinkData['sLinkList'] .','. str_pad($nUid,9,'0',STR_PAD_LEFT),
				'nPa'			=> (int) $aPaLinkData['nUid'],
				'nGrandPa'		=> (int) 0,
				'nPaLid'		=> (int) $aPaLinkData['nId'],
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			$aEditLog[CLIENT_USER_LINK]['aNew'] = $aSQL_Array;

			$sSQL = 'INSERT INTO '.CLIENT_USER_LINK.' '. sql_build_array('INSERT', $aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aSQL_Array = array(
				'sLidLinkList'	=> (string) $aPaLinkData['sLidLinkList'] .','. str_pad($nLastId,9,'0',STR_PAD_LEFT)
			);
			$aEditLog[CLIENT_USER_LINK]['aNew']['sLidLinkList'] = $aSQL_Array['sLidLinkList'];

			$sSQL = 'UPDATE '.CLIENT_USER_LINK.' SET '. sql_build_array('UPDATE', $aSQL_Array).'
				WHERE nId = :nId LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nLastId,PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			if($aData['nFrom'] == 1)
			{
				// 前台新增
				$nLogCode = 7100301;
				$nWho = $nUid;
			}
			else
			{
				// 後台新增
				$nLogCode = 8103101;
				$nWho = $aData['nAdmin'];
			}
			$aSQL_Array = array(
				'nWho'		=> (int) $nWho,
				'nWhom'		=> (int) $nUid,
				'sWhomAccount'	=> (string) $aData['sAccount'],
				'nKid'		=> (int) $nUid,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) $nLogCode,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aSQL_Array);

		}

		public function checkAccount($sAccount)
		{
			global $oPdo;

			$sSQL = '	SELECT 	nId
					FROM 	'.CLIENT_USER_DATA.'
					WHERE sAccount = :sAccount
					AND 	nOnline != 99
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if($aRows === false)
			{
				return 0;
			}
			else
			{
				return $aRows['nId'];
			}
		}

		public function checkVcode($sAccount,$nVcode)
		{
			global $oPdo;
			global $aSystem;

			// $nExpTime = NOWTIME - 300; // 過期時間(先寫死，到時候讀全局表)
			$nExpTime = NOWTIME - $aSystem['aParam']['nSMSTime']; // 過期時間(先寫死，到時候讀全局表)

			$sSQL = '	SELECT 	nId
					FROM 		'.CLIENT_USER_VERIFY.'
					WHERE 	sPhone LIKE :sAccount
					AND		nVcode = :nVcode
					AND 		nCreateTime > :nCreateTime';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			$Result->bindValue(':nCreateTime', $nExpTime, PDO::PARAM_INT);
			$Result->bindValue(':nVcode', $nVcode, PDO::PARAM_INT);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aRows === false)
			{
				return 0;
			}
			else
			{
				return 1;
			}

			$sSQL = '	SELECT 	nId
					FROM 		'.CLIENT_USER_DATA.'
					WHERE 	sAccount = :sAccount
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if($aRows === false)
			{
				return 0;
			}
			else
			{
				return $aRows['nId'];
			}
		}

		public function getVcode($sAccount)
		{
			global $oPdo;
			global $aSystem;

			// $nExpTime = NOWTIME - 300; // 過期時間(先寫死，到時候讀全局表)
			$nExpTime = NOWTIME - $aSystem['aParam']['nSMSTime']; // 過期時間(先寫死，到時候讀全局表)

			// 刪除過期的驗證碼
			$sSQL = 'DELETE FROM '.CLIENT_USER_VERIFY.' WHERE nCreateTime < :nCreateTime';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nCreateTime', $nExpTime, PDO::PARAM_INT);
			sql_query($Result);

			$sSQL = '	SELECT 	1
					FROM 		'.CLIENT_USER_VERIFY.'
					WHERE 	sPhone = :sAccount
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount', $sAccount, PDO::PARAM_STR);
			sql_query($Result);
			$aRows = $Result->fetch(PDO::FETCH_ASSOC);
			if($aRows === false)
			{
				$nVcode = rand(10000, 99999);
				if($aSystem['aParam']['nSMSSetting'] == 1)
				{
					// 寄簡訊function
					$oSMS = new SMSHttp();

					# 發送帳號 (api.every8d.com 的帳號)
					$sUserID = $aSystem['aParam']['sSMSAcc'];

					# 發送密碼 (api.every8d.com 的密碼)
					$sPassword = $aSystem['aParam']['sSMSPwd'];

					# 簡訊主旨，主旨不會隨著簡訊內容發送出去。用以註記本次發送之用途。可傳入空字串。
					$sSubject = $aSystem['sTitle'];

					# 簡訊內容
					// 內容記得要語系
					$sContent = $sSubject .' - 註冊簡訊驗證碼：';

					# 簡訊預定發送時間。-立即發送：請傳入空字串。-預約發送：請傳入預計發送時間，若傳送時間小於系統接單時間，將不予傳送。格式為YYYYMMDDhhmnss；例如:預約2009/01/31 15:30:00發送，則傳入20090131153000。若傳遞時間已逾現在之時間，將立即發送。
					$sSendTime = '';

					# 取餘額
					if($oSMS->getCredit($sUserID, $sPassword))
					{
						# 傳送簡訊
						if($oSMS->sendSMS($sUserID, $sPassword, urlencode($sSubject), urlencode(($sContent . $nVcode)), $sAccount, $sSendTime))
						{
						}
					}
					else
					{
						error_log('簡訊沒錢ㄌ 請補錢'.PHP_EOL.'帳號 : '.$sUserID);
					}
				}

				$aSQL_Array = array(
					'sPhone'		=> (string) $sAccount,
					'nVcode'		=> (int) $nVcode,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);

				$sSQL = 'INSERT INTO '.CLIENT_USER_VERIFY.' '. sql_build_array('INSERT', $aSQL_Array);
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);

				return $nVcode;
			}
			else
			{
				return 0; // 上一個驗證碼還沒過期
			}
		}

		public function getLinkData($nUid)
		{
			global $oPdo;
			$sSQL = '	SELECT 	nId,
							nUid,
							nLevel,
							sLinkList,
							nPa,
							nPaLid,
							sLidLinkList
					FROM 		client_user_link
					WHERE 	nUid = :nUid
					AND		nEndTime = 0';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nUid', $nUid, PDO::PARAM_INT);
			sql_query($Result);
			$aData = $Result->fetch(PDO::FETCH_ASSOC);

			return $aData;
		}

		public function sendTempPassword($sAccount)
		{
			global $oPdo;
			global $aSystem;

			$aReturn = array(
				'sTempPassword'	=> '',
				'nStatus'		=> 0,
			);
			// 臨時密碼
			$sTempPassword = substr(md5($sAccount.time()),6,6);
			if($aSystem['aParam']['nSMSSetting'] == 1)
			{
				// 寄簡訊function
				$oSMS = new SMSHttp();

				# 發送帳號 (api.every8d.com 的帳號)
				$sUserID = $aSystem['aParam']['sSMSAcc'];

				# 發送密碼 (api.every8d.com 的密碼)
				$sPassword = $aSystem['aParam']['sSMSPwd'];

				# 簡訊主旨，主旨不會隨著簡訊內容發送出去。用以註記本次發送之用途。可傳入空字串。
				$sSubject = $aSystem['sTitle'];

				# 簡訊內容
				// 內容記得要語系
				$sContent = $sSubject .' - 帳號: '.$sAccount.'，簡訊臨時登入密碼： '.$sTempPassword.'。請於'.($aSystem['aParam']['nPasswordExpired']/60).'分鐘內完成登入並修改您的密碼';

				# 簡訊預定發送時間。-立即發送：請傳入空字串。-預約發送：請傳入預計發送時間，若傳送時間小於系統接單時間，將不予傳送。格式為YYYYMMDDhhmnss；例如:預約2009/01/31 15:30:00發送，則傳入20090131153000。若傳遞時間已逾現在之時間，將立即發送。
				$sSendTime = '';

				# 取餘額
				if($oSMS->getCredit($sUserID, $sPassword))
				{
					# 傳送簡訊
					if($oSMS->sendSMS($sUserID, $sPassword, urlencode($sSubject), urlencode(($sContent)), $sAccount, $sSendTime))
					{
						$aReturn['sTempPassword'] = $sTempPassword;
						$aReturn['nStatus'] = 1;
					}
				}
				else
				{
					error_log('簡訊沒錢ㄌ 請補錢'.PHP_EOL.'帳號 : '.$sUserID);
				}
			}


			return $aReturn;
		}

	}
?>
<?php
	class oUser
	{
		/**
		 * Param :
		 * 	$aData => ('sAccount'(string),'sPassword'(string),'nRemember'(int))
		 * Return :
		 */
		public function login($aData) #W
		{
			global $oPdo;

			$nLoginTimes = 0; # 登入次數
			$nStatus = 0;	# -2 五分鐘後再試 -1帳號禁止登入 0 失敗 1 登入成功
			$sSid 	= isset($_COOKIE['sSid'])?$_COOKIE['sSid']:'';
			$sEmploye 	= isset($aData['sEmploye'])?$aData['sEmploye']:'';
			$aLoginCookie = array();
			$aBrowser = checkbrowser();

			# 防止連續登入 (3次阻擋)
			$sSQL = '	SELECT	1
					FROM	'.USER_LOGIN.'
					WHERE	nStatus <= 0
					AND	sAccount LIKE :sAccount
					AND	nCreateTime <= '. NOWTIME .'
					AND	nCreateTime >= '. (NOWTIME - 300) ;
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount',	$aData['sAccount'], PDO::PARAM_STR);
			sql_query($Result);
			while ($aRows = $Result->fetch(PDO::FETCH_ASSOC))
			{
				$nLoginTimes++;
			}
			if ($nLoginTimes >= 3)
			{
				$nStatus = -2;
				return $nStatus;
			}

			$sSQL = '	SELECT 	nId as nUid,
							sAccount,
							nStatus
					FROM 		'.USER_DATA.'
					WHERE 	sAccount = :sAccount
					AND 		sPassword LIKE :sPassword
					AND 		nOnline = 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sAccount',	$aData['sAccount'], PDO::PARAM_STR);
			$Result->bindValue(':sPassword',	$aData['sPassword'], PDO::PARAM_STR);
			sql_query($Result);
			$aLoginData = $Result->fetch(PDO::FETCH_ASSOC);
			if($aLoginData)
			{
				if($aLoginData['nStatus'] == 10)
				{
					$nStatus = -1;
				}
				else
				{
					if ($sSid != '')
					{
						setcookie('sSid', '', COOKIE['CLOSE']);
					}

					$aCookieData = $this->buildCookie();
					$sSid = $aCookieData['sSid'];
					$aLoginData['sSid'] = $aCookieData['sSid'];
					$aLoginData['sEmploye'] = $sEmploye;
					unset($aLoginData['nStatus']);
					$aLoginCookie = $this->updateCookie($aLoginData); # update cookie
					$nStatus = 1;
				}
			}
			if ($nStatus == 1)
			{
				if($aData['nRemember'] == 1)
				{
					setcookie('nRemember', $aData['nRemember'], COOKIE['REMEMBER']);
					setcookie('sAccount', $aData['sAccount'], COOKIE['REMEMBER']);
					if (isset($aData['nKid']))
					{
						setcookie('nKid', $aData['nKid'], COOKIE['REMEMBER']);
					}

				}
				if($aData['nRemember'] == 0)
				{
					setcookie('nRemember', 0, COOKIE['REMEMBER']);
					setcookie('sAccount', '', COOKIE['REMEMBER']);
					setcookie('nKid', '', COOKIE['REMEMBER']);
				}
			}
			if ($aData['sAccount'] == 'admroot')
			{
				$this->deleteCookie();
			}
			else
			{
				// 預設聲音提示全開
				setcookie('soundRecharge', 1, COOKIE['REMEMBER']);
				setcookie('soundWithdrawal', 1, COOKIE['REMEMBER']);
				setcookie('soundService', 1, COOKIE['REMEMBER']);
				setcookie('soundCtrl', 1, COOKIE['REMEMBER']);
				$this->deleteCookie($aLoginCookie);
			}

			# 登入紀錄
			$aSQL_Array = array(
				'sAccount'		=> $aData['sAccount'],
				'sPassword'		=> $aData['sPassword'],
				'nStatus'		=> $nStatus,
				'sBrowser'		=> $aBrowser[0],
				'sBrowserVersion'	=> $aBrowser[1],
				'sDevice'		=> $aBrowser[2],
				'sIp'			=> USERIP,
				'nCreateTime'	=> NOWTIME,
				'sCreateTime'	=> NOWDATE,
			);
			$sSQL = 'INSERT INTO '.USER_LOGIN.' '.sql_build_array('INSERT', $aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			return $nStatus;
		}

		/**
		 * Param :
		 * 	$aData => ('sAccount'(string))
		 * Return :
		 */
		public function logout($aData) #W
		{
			global $oPdo;
			$aBrowser = checkbrowser();
			$sSid = isset($_COOKIE['sSid']) ? $_COOKIE['sSid'] : '';
			$sSQL = '	SELECT 	nId,
							nUid,
							sSid
					FROM		'.USER_COOKIE.'
					WHERE 	sSid LIKE :sSid';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sSid',	$sSid, PDO::PARAM_STR);
			sql_query($Result);
			$aCookieData = $Result->fetch(PDO::FETCH_ASSOC);

			$this->deleteCookie($aCookieData,1);
			setcookie('sSid', '', COOKIE['CLOSE']);

			$aSQL_Array = array(
				'sAccount'		=> $aData['sAccount'],
				'sPassword'		=> '',
				'nStatus'		=> 2,
				'sBrowser'		=> $aBrowser[0],
				'sBrowserVersion'	=> $aBrowser[1],
				'sDevice'		=> $aBrowser[2],
				'sIp'			=> USERIP,
				'nCreateTime'	=> NOWTIME,
				'sCreateTime'	=> NOWDATE,
			);
			$sSQL = 'INSERT INTO '.USER_LOGIN.' '.sql_build_array('INSERT', $aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
		}

		/**
		 * Return :
		 * 	0 	=> 查無cookie
		 * 	>0 	=> nUid (登入中)
		 */
		public function checkCookie()
		{
			global $oPdo;
			$sSid = isset($_COOKIE['sSid'])?$_COOKIE['sSid']:'';

			$sSQL = '	SELECT	Cookie_.nId,
							Cookie_.sSid,
							Cookie_.sIp,
							Cookie_.nUid
					FROM		'.USER_COOKIE.' Cookie_,
							'.USER_DATA.' User_
					WHERE		User_.nStatus != 99
					AND 		Cookie_.sSid LIKE :sSid
					AND		Cookie_.nUpdateTime >= '. COOKIE['CLOSE'] . '
					AND 		User_.nId = Cookie_.nUid
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sSid', $sSid, PDO::PARAM_STR);
			sql_query($Result);
			$aCookieData = $Result->fetch(PDO::FETCH_ASSOC);
			if($aCookieData === false)
			{
				setcookie('sSid', '', COOKIE['CLOSE']);
				return 0;
			}
			return $aCookieData['nUid'];
		}

		/**
		 * Param :
		 * 	$aData => ('nUid'(int),'sAccount'(string),'sSid'(string))
		 */
		public function updateCookie($aData = array())
		{
			global $oPdo;
			$sSid = isset($_COOKIE['sSid'])?$_COOKIE['sSid']:'';
			$aReturn = array();
			$aSQL_Array = array(
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);
			if (!empty($aData))
			{
				$sSid = $aData['sSid'];
				foreach ($aData as $LPsKey => $LPaData)
				{
					$aSQL_Array[$LPsKey] = $LPaData;
				}
				$aReturn = $aData;
			}

			$sSQL = '	UPDATE	'.USER_COOKIE.'
					SET		'. sql_build_array('UPDATE', $aSQL_Array) .'
					WHERE		sSid LIKE :sSid
					LIMIT 	1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sSid', $sSid,	PDO::PARAM_STR);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			return $aReturn;
		}

		private function buildCookie()
		{
			global $oPdo;
			$aSidHash = array(
				'key'	=> SYS['KEY'],
				'act' => rand(10000, 99999),
				'time'=> NOWTIME,
			);
			$sSid = sys_md5($aSidHash);
			setcookie('sSid', $sSid, COOKIE['REMEMBER']);
			$_COOKIE['sSid'] = $sSid;

			$aSQL_Array = array(
				'sSid'		=> $sSid,
				'sIp'			=> USERIP,
				'nCreateTime'	=> NOWTIME,
				'sCreateTime'	=> NOWDATE,
				'nUpdateTime'	=> NOWTIME,
				'sUpdateTime'	=> NOWDATE,
			);
			$sSQL = 'INSERT INTO '.USER_COOKIE.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$aSQL_Array['nId'] = $oPdo->lastInsertId();

			return $aSQL_Array;
		}
		/**
		 * use in $this->login(), $this->logout()
		 * Param : $aData => ('nUid'(int),'sSid'(string))
		 */
		private function deleteCookie($aData = array(),$nLogout = 0)
		{
			global $oPdo;
			# 1 過期刪除
			$sSQL = '	DELETE 	FROM '.USER_COOKIE.'
					WHERE 	nUpdateTime < '. COOKIE['CLOSE'];
			$Result = $oPdo->prepare($sSQL);
			sql_query($Result);

			if(!empty($aData))
			{
				if($nLogout === 1)
				{
					# 2 登出刪除
					$sSQL = '	DELETE 	FROM '.USER_COOKIE.'
							WHERE 	nUid = :nUid
							AND 		sSid LIKE :sSid';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nUid', $aData['nUid'], PDO::PARAM_INT);
					$Result->bindValue(':sSid', $aData['sSid'], PDO::PARAM_STR);
					sql_query($Result);
				}
				else
				{
					# 3 重複登入刪除
					$sSQL = '	DELETE 	FROM '.USER_COOKIE.'
							WHERE 	nUid = :nUid
							AND 		sSid NOT LIKE :sSid';
					$Result = $oPdo->prepare($sSQL);
					$Result->bindValue(':nUid',	$aData['nUid'], PDO::PARAM_INT);
					$Result->bindValue(':sSid',	$aData['sSid'], PDO::PARAM_STR);
					sql_query($Result);
				}
			}
		}

		public function getVsCode($sSid)
		{
			global $oPdo;
			$nVscode = '';
			$sSid = isset($_COOKIE['sSid']) ? $_COOKIE['sSid'] : '';

			$sSQL = '	SELECT 	nVcode
					FROM		'.USER_COOKIE.'
					WHERE		sSid LIKE :sSid
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sSid', $sSid, PDO::PARAM_STR);
			sql_query($Result);
			$sRows = $Result->fetchColumn();

			if ($sRows !== false)
			{
				$nVscode = $aRows['nVcode'];
			}
			return $nVscode;
		}

		public function changeVsCode($sSid)
		{
			global $oPdo;
			$nVscode = rand(10000,99999);
			$sSid = isset($_COOKIE['sSid']) ? $_COOKIE['sSid'] : '';

			$aSQL_Array = array(
				'nVscode'	=> (int) $nVscode,
			);

			$sSQL = '	UPDATE	'.USER_COOKIE.'
					SET		'. sql_build_array('UPDATE', $aSQL_Array) .'
					WHERE		sSid LIKE :sSid
					AND		nUid = 0';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':sSid', $sSid, PDO::PARAM_STR);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			return $nVscode;
		}
	}
?>
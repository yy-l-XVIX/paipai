<?php
	class cDataEncrypt
	{
		static function check($aData)
		{
			global $oPdo;

			$bRes = false;
			$sTable = $aData['sTable'];
			$nKid = $aData['nKid'];
			$sName0 = $aData['sNameOld'];
			$aEncrypt = array();
			$sKey = '';

			$sSQL = '	SELECT	nKid,
							sTable,
							nEncryptTime,
							sEncryptKey
					FROM	'.	CLIENT_DATA_CTRL .'
					WHERE		nKid = :nKid
					AND		sTable LIKE :sTable
					LIMIT		1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nKid', $nKid, PDO::PARAM_INT);
			$Result->bindValue(':sTable', $sTable, PDO::PARAM_STR);
			sql_query($Result);
			$aEncrypt = $Result->fetch(PDO::FETCH_ASSOC);

			if($aEncrypt !== false)
			{
				$aParam = array(
					'nKid'	=> $nKid,
					'sTable'	=> $sTable,
					'sName0'	=> $sName0,
					'NOWTIME'	=> $aEncrypt['nEncryptTime']
				);

				$sKey = self::encrypt($aParam);

				if($sKey === $aEncrypt['sEncryptKey'])
				{
					$bRes = true;
				}
			}

			return $bRes;
		}

		static function update($aData,$bCheck = true)
		{
			global $oPdo;
			$sKey = '';

			if($bCheck)
			{
				if(!self::check($aData))
				{
					return false;
				}
			}

			$sKey = self::encrypt($aData);

			return $sKey;
		}

		static private function encrypt($aData)
		{
			$sKey = '';
			$sTemp = '';
			$sTemp = md5(md5($aData['NOWTIME'].$aData['sTable'].SYS['KEY']).$aData['nKid'].$aData['sName0']);
			$sKey = substr($sTemp,aKEYCTRL['LEFT'],aKEYCTRL['LEFTLEN']).substr($sTemp,aKEYCTRL['RIGHT'],aKEYCTRL['RIGHTLEN']);
			return $sKey;
		}
	}

?>
<?php
	class cJWT
	{
		private $sAlg = 'SHA256';

		public function validToken($sToken)
		{

			if (!$sToken)
			{
				return false;
			}

			$aToken = explode('.', $sToken);
			if (count($aToken) != 3)
			{
				return false;
			}

			$sHeader    = $aToken[0];
			$sContent   = $aToken[1];
			$sSign 	= $aToken[2];

			$sJHeader = $this->base64UrlDecode($sHeader);
			$sJContent = $this->base64UrlDecode($sContent);
			$sSignDecode = $this->base64UrlDecode($sSign);

			$aHeader = json_decode($sJHeader,true);
			$aContent = json_decode($sJContent,true);

			if (!$aHeader)
			{
				return false;
			}

			if (!$aContent)
			{
				return false;
			}

			$this->sAlg = $aHeader['sAlg'];
			//已過期
			if (isset($aContent['nExp']) && $aContent['nExp'] < NOWTIME)
			{
				return false;
			}

			$sSysSign = hash_hmac($this->sAlg, $sHeader . '.' . $sContent, SYS['KEY'], TRUE);

			//簽名錯誤
			if ($sSysSign !== $sSignDecode)
			{
				return false;
			}

			return $aContent;
		}

		public function base64UrlEncode($sData)
		{
			$sData = base64_encode($sData);
			// 將 + 取代為 - ， 將 / 取代為 _
			$sData = strtr($sData, '+/', '-_');
			// 取代全部的 =
			$sData = rtrim($sData,'=');
			return $sData;
		}

		private function base64UrlDecode($sData)
		{
			// 將 - 取代為 + ， _ 取代為 /
			$sData = strtr($sData,'-_','+/');
			$nLen = strlen($sData) % 4;
			$sData = str_pad($sData,$nLen,'=',STR_PAD_RIGHT);
			$sData = base64_decode($sData);
			return $sData;
		}
	}
?>
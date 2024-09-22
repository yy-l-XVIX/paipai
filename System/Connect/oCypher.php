<?php
class oCypher
{
	/**
	 * 雞農HASH 微信登入使用
	 */
	private static $a300_400Prime = array(307,311,313,317,331,337,347,349,353,359,367,373,379,383,389,397);
	public static function salting_key($nId,$nT)
	{
		$aVal = array(
			'key'	=> SYS['sKey'],
			'act' => $nId,
			'time'=> $nT,
		);
		$sMD5 = sys_md5($aVal);
		$sKey = '';
		$aPrime = self::$a300_400Prime;

		for($i=0;$i<16;$i++)
		{
			$sKey .= $sMD5[(($aPrime[$i])%32)];
		}
		return $sKey;
	}

	/**
	 * 阿肥用
	 */
	public static function line_curlKey($sUrl)
	{
		$sCrulKey = 0;
		$sHost = md5('LINE'.$sUrl.'BOT');
		$sCrulKey = substr($sHost,3,7) . substr($sHost,23,29);

		return $sCrulKey;
	}

	/**
	 * API後台用
	 */
	public static function api_curlKey($sUrl)
	{
		$sCrulKey = 0;
		$sHost = md5('API'.$sUrl.'ADM');
		$sCrulKey = substr($sHost,3,7) . substr($sHost,23,29);

		return $sCrulKey;
	}

	/**
	 * 金錢加密
	 */
	static Private $MoneyKey = 'Schlussel';
	static Private $aPrime = array(2,3,5,7,11,13,17,19,23,29,31,32);
	static function MoneyHash($aData)
	{
		if(empty($aData['nMoneyTime'])) return false;
		if(empty($aData['nId'])) return false;
		if(empty($aData['nMoney'])) return false;
		$sLongkey = $aData['nMoneyTime'].$aData['nId'].$aData['nMoney'].self::$MoneyKey;
		$sLongkey = md5($sLongkey);
		for($i=0;$i<12;$i++)
		{
			$aShortkey['Money'] .= $sLongkey[self::$aPrime[$i]-1];
		}
	}

	static function ReHash($sMD5String,$sSalt = SYS['PWDKEY'])
	{
		$sMD5String = md5($sMD5String);
		return md5($sMD5String.$sSalt);
	}
}

?>
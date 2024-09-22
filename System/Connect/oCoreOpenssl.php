<?php
Class oCoreOpenssl
{
	static $nOptions = 0; # 0 => 預設,pkcs5補碼  1=>OPENSSL_RAW_DATA | OPENSSL_NO_PADDING 需要再做pkcs5或pkcs7補碼
	static $sDefaultIV = false; ## ECB默認不需要偏移,CBC需要
	static $sEncryptType = 'AES-128-ECB';
	# AES-128-ECB
	# AES-256-ECB
	# BF-ECB
	# AES-128-CBC
	# AES-256-CBC
	# DES-CBC

	## pkcs5 解密後處理 ##
	private static function pkcs5_unpad($decrypted) {
		$len       = strlen($decrypted);
		if($len === 0) return '';
		$padding   = ord($decrypted[$len-1]);
		$decrypted = substr($decrypted, 0, -$padding);

		return $decrypted;
	}

	## 解密 ##
	public static function AESdecrypt($sKey, $sCipher)
	{
		$sCipher = base64_decode($sCipher);# base64
		if (self::$nOptions === 0)
		{
			$sPlain = openssl_decrypt($sCipher, self::$sEncryptType, $sKey, 0, self::$sDefaultIV);
		}
		else
		{
			$sPlain = openssl_decrypt($sCipher, self::$sEncryptType, $sKey, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, self::$sDefaultIV);
			$sPlain = self::pkcs5_unpad($sPlain);
		}

		return $sPlain;
	}

	##  pkcs5 加密前處理 ###
	private static function pkcs5_pad($text, $blocksize = 16) {
		$pad = $blocksize-(strlen($text)%$blocksize);

		return $text.str_repeat(chr($pad), $pad);
	}

	## 加密 ##
	public static function AESencrypt($key, $sPlain)
	{
		if (self::$nOptions === 0)
		{
			$sCipher = openssl_encrypt($sPlain, self::$sEncryptType, $key, 0, self::$sDefaultIV);
		}
		else
		{
			$sPlain = self::pkcs5_pad($sPlain);
			$sCipher = openssl_encrypt($sPlain, self::$sEncryptType, $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, self::$sDefaultIV);
		}

		$sCipher = base64_encode($sCipher);	# base64
		return $sCipher;
	}
}
?>
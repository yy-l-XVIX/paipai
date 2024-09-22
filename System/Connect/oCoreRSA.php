<?php
/**
 * 私鑰公鑰對偶存在
 * 發信人X產生私鑰A跟公鑰A'
 * 發送人X使用A加密
 * 收信人Y使用A'解密，解密成功則訊息為由X送出
 */
Class oCoreRSA
{
	public static $sPublicKey = '';
	public static $sPrivateKey = '';
	public static $sPublicKeyId = '';# 公鑰文件内容
	public static $sPrivateKeyId = '';# 私鑰文件内容

	/**
	 * 設定RSA公鑰
	 * @return bool 是否成功
	 */
	public function setRsaPublicKey()
	{
		self::$sPublicKeyId = openssl_get_publickey(self::$sPublicKey);
		if (!self::$sPublicKeyId)
		{
			throw new Exception('public key Invalid!!');
		}
		return true;
	}

	/**
	 * 設定RSA私鑰
	 * @return bool 是否成功
	 */
	public function setRsaPrivateKey()
	{
		self::$sPrivateKeyId = openssl_get_privatekey(self::$sPrivateKey);
		if (!self::$sPrivateKeyId)
		{
			throw new Exception('private key Invalid!!');
		}
		return true;
	}

	## 確認一下// AS那段怎麼走
	/**
	 * RSA公鑰加密(需定義公鑰)
	 * @param string $sPlain 要加密的字串
	 * @return string 加密后的内容
	 */
	public function rsaPublicEncrypt($sPlain)
	{
		if (!self::$sPublicKeyId || $sPlain === '')
		{
			throw new Exception('public Key or plain text invalid');
		}
		openssl_public_encrypt($sPlain, $sCrypt, self::$sPublicKeyId);
		openssl_free_key(self::$sPublicKeyId);
		return base64_encode($sCrypt); // 加密后的内容为 binary 透过 base64_encode() 转换为 string 方便传输
	}

	/**
	 * RSA私鑰解密(需定義私鑰)
	 * @param string $sCipher 要解密的字串
	 * @return string 解密后的内容
	 */
	public function rsaPrivateDecrypt($sCipher)
	{
		if (!self::$sPrivateKeyId || $sCipher === '')
		{
			throw new Exception('private Key or encrypted text invalid');
		}
		openssl_private_decrypt(base64_decode($sCipher), $sDecrypted, self::$sPrivateKeyId); // 先将密文做 base64_decode() 解释
		openssl_free_key(self::$sPrivateKeyId);
		return $sDecrypted;
	}
	########################
	private static $sPkcs12 = '';
	#  file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/inc/newjinshun/merchant_cert.pfx') 這東西
	#
	/**
	 * 簽名  生成簽名串  基於sha1withRSA
	 * @param string $sData 簽名前的字符串
	 * @return string 簽名串
	 */
	static function Sign($sData, $sSecret)
	{
		$aCerts = array();
		openssl_pkcs12_read(file_get_contents(self::$sPkcs12, $aCerts, $sSecret)); //其中password为你的证书密码
		if(!$aCerts) return;
		$sSignature = '';
		openssl_sign($sData, $sSignature, $aCerts['pkey']);
		return base64_encode($sSignature);
	}

	/**
	 * 驗證
	 * @param data：原文
	 * @param signature：簽名
	 * @return bool 返回：簽名結果，true為驗簽成功，false為驗簽失敗
	 */
	static function Verity($sData, $signature)
	{
		self::setRsaPublicKey(self::$sPublicKey);
		$bResult = (bool) openssl_verify($sData, base64_decode($signature), self::$sPublicKeyId);
		openssl_free_key(self::$sPublicKeyId);
		return $bResult;
	}
}
?>
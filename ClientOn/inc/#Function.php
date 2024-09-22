<?php
	#----- 網址加/解密 -----#
	function sys_web_encode($sUrl)
	{
		global 	$nWebUrlType,
				$aMenuToUrl;


		switch($nWebUrlType)
		{
			case 1 :
				return sys_enCode1($sUrl);
				break;
			case 2 :
				return sys_enCode2($aMenuToUrl[$sUrl]);
				break;
		}
	}

	function sys_web_decode($aRequire)
	{
		global 	$nWebUrlType;

		switch($nWebUrlType)
		{
			case 1 :
				return sys_deCode1($aRequire);
				break;
			case 2 :
				return sys_deCode2($aRequire['Param']);
				break;
		}
	}

	// method 1
	function sys_enCode1($sUrl)
	{
		if(false)
		{
			return '/?'.$sUrl;
		}
		else
		{
			$sTemp = './?';

			if ($sUrl == '')
			{
				$sTemp = '';
			}
			else
			{
				$sTemp .= base64_encode($sUrl.'_'.substr(md5($sUrl.SYS['KEY']),aCTRL['FIND'],aCTRL['GET']));
			}

			return $sTemp;
		}
	}

	function sys_deCode1($aRequire)
	{
		$sRe = SYS['DEFAULTPAGE'];
		if ($aRequire['Param'] <> '')
		{
			if(false)
			{
				$sRe = $aRequire['Param'];
			}
			else
			{
				$nS = 0;
				$sRe = base64_decode($aRequire['Param']);
				$aTemp = explode('_', $sRe);
				if (sizeof($aTemp) <> 2)
				{
					$nS = 1;
				}
				else
				{
					$sSys_code = substr(md5($aTemp['0'].SYS['KEY']),aCTRL['FIND'],aCTRL['GET']);
					if ($aTemp['1'] <> $sSys_code)
					{
						$nS = 1;
					}
				}

				if (!isset($aRequire['MenuToUrl'][$aTemp['0']]))
				{
					$nS = 1;
				}

				if ($nS == 0)
				{
					$sRe = $aTemp['0'];
				}
			}
		}

		if (isset($aRequire['MenuToUrl'][$sRe]))
		{
			return $sRe;
		}
		else
		{
			exit;
		}
	}

	// method 2
	function sys_enCode2($sUrl)
	{
		global	$aMenuToNo,
				$aSystem;

		$sEnPage = preg_replace("/\\d+/",'', $aMenuToNo[$sUrl]);

		$aToken = explode('/'.$aSystem['sHtml'].$aSystem['nVer'].'/', $sUrl);

		if(count($aToken) != 2)
		{
			$aToken = explode('/php/', $sUrl);
		}
		else
		{
			$sEnPage .= '_'.$aSystem['nVer'];
		}

		$sEnUrl = str_replace('php', $sEnPage, $aToken[1]);

		return './?'.$sEnUrl;
	}

	function sys_deCode2($sUrl)
	{
		global	$aMenuIndex,
				$aSystem,
				$aMenuToNo,
				$aMenuToUrl;

		if($sUrl == '')
		{
			return SYS['DEFAULTPAGE'];
			exit;
		}

		$sDeUrl = 'pages/[FILE]/[TYPE]/[PHP].php';

		$aToken = explode('.', $sUrl);

		if(count($aToken) != 2)
		{
			$aTmp = $aToken;
			unset($aToken);

			$aToken[1] = array_pop($aTmp);
			$aToken[0] = '';

			foreach($aTmp as $LPsToken)
			{
				$aToken[0] .= ($aToken[0] != '') ? '.'.$LPsToken : $LPsToken;
			}
		}

		$aTmp = explode('_', $aToken[1]);

		$sFile = $aMenuIndex[$aTmp[0]];
		$sType = (count($aTmp) == 2) ? $aSystem['sHtml'].$aSystem['nVer'] : 'php';
		$sPhp = $aToken[0];

		$sDeUrl = str_replace('[FILE]',	$sFile,	$sDeUrl);
		$sDeUrl = str_replace('[TYPE]',	$sType,	$sDeUrl);
		$sDeUrl = str_replace('[PHP]',	$sPhp,	$sDeUrl);

		if (isset($aMenuToNo[$sDeUrl]))
		{
			return $aMenuToNo[$sDeUrl];
		}
		else
		{
			return $aMenuToNo['pages/index/php/_index_0.php'];
			exit;
		}
	}

	function base64Pic($sUrlFile)
	{
		$aReturn = array(
			'status' => 1,
			'data' => '',
		);

		$aFileType = @getimagesize($sUrlFile);

		if (isset($aFileType['2']))
		{
			$sImgType = $aFileType['mime'];

			$aReturn['status'] = 0;
			$aReturn['data'] = 'data:'. $sImgType .';base64,'.base64_encode(file_get_contents($sUrlFile));
		}
		return $aReturn['data'];
	}

	function sortASCII($aData)
	{
		ksort($aData);
		$sRequest = '';
		foreach($aData as $k => $v)
		{
			$sRequest .= $k."=".$v."&";
		}
		$sRequest = rtrim($sRequest, '&');
		return $sRequest;
	}

	// 整理content
	function convertContent($sContent)
	{
		if ($sContent == '[:invite job:]') // 邀請上工
		{

			return $sContent;
		}

		// 轉換emoji
		$sContent = str_replace('[:', '<img class="EmojiImgIcon" src="images/emoji/', $sContent);
		$sContent = str_replace(':]', '.png">',  $sContent);


		// 轉換http
		// $sContent = preg_replace('#(http|https)://([0-9a-z\.\-]+)(:?[0-9]*)([0-9a-z\_\/\?\&\=\%\.\;\#\-\~\+]*)#i','<a href="\1://\2\3\4" target="_blank">\1://\2\3\4</a>', $sContent);

		return $sContent;
	}

	//封鎖需要隱藏uid
	function myBlockUid($nUid)
	{
		global $oPdo;
		$aBlockUid = array();
		$sSQL = '	SELECT 	nUid,
						nBUid
				FROM 	'.CLIENT_USER_BLOCK.'
				WHERE nBUid = :nUid
				OR 	nUid = :nUid';

		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $nUid, PDO::PARAM_INT);
		sql_query($Result);
		while($aRows = $Result->fetch(PDO::FETCH_ASSOC))
		{
			$aBlockUid[$aRows['nBUid']] = $aRows['nBUid'];
			$aBlockUid[$aRows['nUid']] = $aRows['nUid'];
		}
		unset($aBlockUid[$nUid]);

		return $aBlockUid;
	}
?>
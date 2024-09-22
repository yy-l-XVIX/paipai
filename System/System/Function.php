<?php
	# 取得使用者 IP
	function getUserIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

			$aIP = explode(',', $ip);

			if (!empty($aIP['0']))
			{
				$ip = $aIP['0'];
			}
			if (!empty($_SERVER['HTTP_TRUE_CLIENT_IP']))
			{
				if ($aIP['0'] == $_SERVER['HTTP_TRUE_CLIENT_IP'])
				{
					$ip = $_SERVER['HTTP_TRUE_CLIENT_IP'];
				}
			}
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	# 判斷目前使用者是使用 手機/電腦在上網
	function isMobile()
	{
		$regex_match =	'/(nokia|iphone|ipad|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|
					htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|
					blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|
					symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|
					jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220
					)/i';

		if(!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT']))
		{
			return false;
		}
		else
		{
			if(preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT'])))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	# 判斷微信
	function is_weixin()
	{
		if(empty($_SERVER['HTTP_USER_AGENT']))
		{
			return true;	## 通常是微信才會雞雞歪歪
		}
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)
		{
			return true;
		}
		return false;
	}

	#檔案上傳至圖片機 ($nFileType 0=>圖片 1=>影片)
	function goImage($aData,$nFileType=0)
	{

		$aReturn = array('sFilename' => '','error' => '');
		$sUrl = $aData['sUrl'];
		$aFile = $aData['aFile'];
		$sCtrl = $aData['sCtrl'];
		$sDir = $aData['sDir'];
		$sTable = $aData['sTable'];

		$nDotPosition = strrpos($aFile["name"],'.');
		$sFileType = substr($aFile["name"],$nDotPosition+1);
		$sFileName = rand(0,9999).date('md'.(date('H')+8).'isY');
		$sFileData = file_get_contents($aFile['tmp_name']);
		$nState = 0;
		## DOUBLE CHECK   sCtrl='png,jpg,jpeg,bmp'
		$nState = checkImage($aFile,$nFileType);

		$aReturn['nState'] = $nState;
		if($nState == 0)
		{
			$sUploadDir = '/tmp/';
			$sFileDirName = $sUploadDir . $sFileName . ".".$sFileType;

			$resFileData = true;
			if ($nFileType == 0)
			{
				$resFileData = imagecreatefromstring($sFileData);
			}
			## 若檔案可以成功解析為圖片 才CURL
			if ($resFileData !== false)
			{
				// Prepare remote upload data
				$aUploadRequest = array(
					'sFileName' 	=> $sFileName,
					'sFileType' 	=> $sFileType,
					'sFileDirName' 	=> basename($sFileDirName),
					'sFileData' 	=> $sFileData,
					'sDir'		=> $sDir,
					'sTable'		=> $sTable
				);
				if ($nFileType == 1) // 影片
				{
					$aUploadRequest['nVideo'] = 1;
				}
				// Execute remote upload

				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $sUrl);
				curl_setopt($curl, CURLOPT_TIMEOUT, 30);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array(
					'Expect:',
				));
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $aUploadRequest);
				$response = curl_exec($curl);
				curl_close($curl);

				// Now delete local temp file
				// unlink($sFileDirName);
			}
			else
			{
				# 權限773
				$sErrorFileDirName = dirname(dirname(dirname(__file__))).'/ErrorFile/'.NOWDATE.'-'.$aFile["name"];
				$sContext = AppendUploadDetail($sFileData);
				file_put_contents($sErrorFileDirName,$sContext);
				trigger_error('上傳圖檔錯誤:'.$aFile["name"]);
				$aReturn['error'] = 'error';
				return $aReturn;
			}

			unset($aReturn['nState']);
		}
		else
		{
			switch ($nState)
			{
				case 1:
					$aReturn['error'] = 'iniSize';
				break;
				case 2:
					$aReturn['error'] = 'formSize';
				break;
				case 3:
					$aReturn['error'] = 'partial';
				break;
				case 4:
					$aReturn['error'] = 'noFile';
				break;
				case 6:
					$aReturn['error'] = 'tmpDir';
				break;
				case 7:
					$aReturn['error'] = 'cantWrite';
				break;
				case 10:
					$aReturn['error'] = 'type';
				break;
				case 11:
					$aReturn['error'] = 'size';
				break;
				default:
					$aReturn['error'] = 'error';
				break;
			}
		}

		$aReturn['sFilename'] = $sFileName.'.'.$sFileType;

		return $aReturn;
	}

	function delImage($aData)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $aData['sUrl']);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $aData);
		if(curl_exec($curl) === false)
		{
			error_log('delimg error: ' . curl_error($curl));
		}
		curl_close($curl);
	}

	function AppendUploadDetail($sContext)
	{
		global $aUser;
		$aWho = 'NONE';
		if(!empty($aUser)) $aWho = $aUser;
		$sAppend = 	'DATE:'.NOWDATE.chr(10).
				'Member:'.$aWho['sAccount'].'(Uid:'.$aWho['nId'].')'.chr(10).
				'#################################'.chr(10).chr(10);
		return $sAppend.$sContext;
	}

	## 圖片檢測 格式限定: bmp,jpg,png 且大小2M以內
	## 參數為  $_FILE['xxx']
	## $nFileType 0=>圖片 1=>影片
	function checkImage($aFile,$nFileType)
	{


		$aAllowedExtsImage = array(
			// "bmp" 	=> true,
			// "BMP" 	=> true,
			"jpg" 	=> true,
			"JPG" 	=> true,
			"jpeg" 	=> true,
			"JPEG" 	=> true,
			"png" 	=> true,
			"PNG" 	=> true,
		);
		$aAllowedExtsVideo = array(
			"mp4"		=> true,
			"MP4"		=> true,
			"mov"		=> true,
			"MOV"		=> true,
			"wmv"		=> true,
			"WMV"		=> true,
			"flv"		=> true,
			"FLV"		=> true,
			"avi"		=> true,
			"AVI"		=> true,
		);


		if($aFile['error'] != 0)
		{
			trigger_error('aFile error :'.$aFile['error']);
			return $aFile['error'];
		}

		$tmp = explode(".", $aFile["name"]);
		$sExtension = strtolower(end($tmp));	## 取出檔案名稱 副檔名

		if ($nFileType == 0)//image
		{
			if ($aFile["size"] > IMAGEMAXSIZE)
			{
				return 11;
			}
			if (!isset($aAllowedExtsImage[$sExtension]))
			{
				return 10;
			}
		}
		if ($nFileType == 1)
		{
			if ($aFile["size"] > VIDEOMAXSIZE)
			{
				return 11;
			}
			if (!isset($aAllowedExtsVideo[$sExtension]))
			{
				return 10;

			}
		}

		return 0;
	}

	#分頁
	#函數listpag(每頁顯示筆數,頁數,總筆數,對應參數)
	function pageSet($aData,$sUrl)
	{
		$aLang = array(
			'FIRST'	=> '第一頁',
			'PREV1'	=> '上一頁',
			'ALONG10'	=> '上十頁',
			'NEXT10'	=> '下十頁',
			'NEXT1'	=> '下一頁',
			'LAST'	=> '最末頁',
			'RECORD'	=> '紀錄',
		);

		$aReturn = array(
			'sHtml'	=> '',
			'sFirst'	=> '',
			'sEnd'	=> '',
			'sAlong10'	=> '',
			'sNext10'	=> '',
			'nAllPage'	=> '',
		);

		// 避免沒資料的時候頁碼會沒有第一頁的顯示
		if($aData['nDataAmount'] == 0)
		{
			$aData['nDataAmount'] = 1;
		}

		#計算總共有多少頁
		if ( ($aData['nDataAmount'] % $aData['nPageSize']) > 0 )
		{
			$aData['nTotal'] = ceil($aData['nDataAmount'] / $aData['nPageSize']);
		}
		else
		{
			$aData['nTotal'] = ($aData['nDataAmount'] / $aData['nPageSize']);
		}

		#計算本頁的起始頁
		if ( strlen($aData['nNowNo']) == 1)
		{
			$aData['nBeginNo'] = 1;
		}
		else
		{
			$aData['nBeginNo'] = substr($aData['nNowNo'],0,(strlen($aData['nNowNo'])-1)) * 10;
		}

		#計算本頁的結束頁
		if ($aData['nTotal'] > 10)
		{
			if (strlen($aData['nNowNo']) == 1)
			{
				$aData['nEndNo'] = strlen($aData['nNowNo']) * 9;
			}
			else
			{
				$aData['nEndNo'] = (substr($aData['nNowNo'],0,(strlen($aData['nNowNo'])-1)) + 1) * 10 - 1;
			}
			if ($aData['nEndNo'] > $aData['nTotal'])
			{
				$aData['nEndNo'] = $aData['nTotal'];
			}
		}
		else
		{
			$aData['nEndNo'] = $aData['nTotal'];
		}

		#串連導頁參數
		$sTemp = '';
		if (!empty($aData['aVar']))
		{
			if(is_array($aData['aVar']))
			{
				foreach($aData['aVar'] as $LPsKey => $LPsVal)
				{
					$sTemp .= '&'. $LPsKey .'='. $LPsVal;
				}
			}
			else
			{
				$sTemp = $aData['aVar'];
			}

		}

		$aLink = array(
			'sFirst'	=> $sUrl.'&nPageNo=1'. $sTemp,
			'sAlong10'	=> $sUrl.'&nPageNo='. ((($aData['nNowNo']-10) == 0) ? 1 : ($aData['nNowNo']-10)) . $sTemp,
			'sNext10'	=> $sUrl.'&nPageNo='. ($aData['nEndNo']+1) . $sTemp,
			'sEnd'	=> $sUrl.'&nPageNo='. $aData['nTotal'] . $sTemp,
			'sPre'	=> ($aData['nNowNo'] == 1)?'javascript:void(0);':$sUrl.'&nPageNo='. ($aData['nNowNo']-1) . $sTemp,
			'sNext'	=> ($aData['nNowNo'] == $aData['nTotal'])?'javascript:void(0);':$sUrl.'&nPageNo='. ($aData['nNowNo']+1) . $sTemp,
		);

		if ($aData['nStyle'] == 2)
		{
			#下拉式

			$aReturn['sHtml'] = '<div class="PageBox '. $aData['sClass'] .'"><div class="PageInner">';

			#上一頁
			if($aData['aButton']['nPrevNext'] == 1)
			{
				if($aData['aButton']['nPrevNextShowStyle'] == 0)
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext prevNextPrev" href="'.$aLink['sPre'].'"><span class="PageBtnStyleTxt prevNextPrevTxt">'. $aLang['PREV1'] .'</span></a>';
				}
				else
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext prevNextPrev" href="'.$aLink['sPre'].'"><span class="PageBtnStyleIcon prevNextPrevIcon"></span></a>';
				}
			}

			#紀錄數量
			if($aData['aButton']['nRecordAmount'] == 1)
			{
				$aReturn['sHtml'] .= '<div class="PageRecord"><span class="PageRecordNum">'. $aData['nDataAmount'] .'</span><span class="PageRecordTxt">'. $aLang['RECORD'] .'</span></div>';
			}

			$aReturn['sHtml'] .=  '<div class="PageSel"><select onchange="location.href=\' '.$sUrl.'\'+this.value">';

			#第一頁
			if($aData['aButton']['nHeadTail'] == 1)
			{
				$aReturn['sHtml'] .= '<option value=\''.$aLink['sFirst'].'\'>'. $aLang['FIRST'] .'</option>';
			}

			#上十頁
			if($aData['aButton']['nPrevNext10'] == 1)
			{
				if ($aData['nNowNo'] >= 10)
				{
					$aReturn['sHtml'] .= '<option value=\''. $aLink['sAlong10'] .'\'>'. $aLang['ALONG10'] .'</option>';
				}
			}

			for ($i=$aData['nBeginNo'] ; $i <= $aData['nEndNo'] ; $i++)
			{
				$sSelected = '';
				if ($aData['nNowNo']==$i)
				{
					$sSelected = 'selected';
				}
				$aReturn['sHtml'] .= '<option value=\''.'&nPageNo='.$i.$sTemp.'\' '. $sSelected .'>'.$i.'</option>';
			}

			#下十頁
			if($aData['aButton']['nPrevNext10'] == 1)
	            {
				if (($aData['nTotal'] > 10) && ($aData['nEndNo'] <> $aData['nTotal']))
				{
					$aReturn['sHtml'] .= '<option value=\''. $aLink['sNext10'] .'\'>'. $aLang['NEXT10'] .'</option>';
				}
			}

			#最末頁
			if($aData['aButton']['nHeadTail'] == 1)
	            {
				$aReturn['sHtml'] .= '<option value=\''.$aLink['sEnd'].'\'>'. $aLang['LAST'] .'</option>';
			}

			$aReturn['sHtml'] .= '</select></div>';

			#下一頁
			if($aData['aButton']['nPrevNext'] == 1)
	            {
				if($aData['aButton']['nPrevNextShowStyle'] == 0)
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext prevNextNext" href="'.$aLink['sNext'].'"><span class="PageBtnStyleTxt prevNextNextTxt">'. $aLang['NEXT1'] .'</span></a>';
				}
				else
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext prevNextNext" href="'.$aLink['sNext'].'"><span class="PageBtnStyleIcon prevNextNextIcon"></span></a>';
				}
			}

			$aReturn['sHtml'] .= '</div></div>';
		}
		else
		{
			#清單式

			$aReturn['sHtml'] = '<div class="PageBox '. $aData['sClass'] .'"><div class="PageInner">';

			#第一頁
			if($aData['aButton']['nHeadTail'] == 1)
			{
				if($aData['aButton']['nHeadTailShowStyle'] == 0)
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther headTail headTailHead" href="'. $aLink['sFirst'] .'"><span class="PageBtnStyleTxt headTailHeadTxt">'. $aLang['FIRST'] .'</span></a>';
				}
				else
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther headTail headTailHead" href="'. $aLink['sFirst'] .'"><span class="PageBtnStyleIcon headTailHeadIcon"></span></a>';
				}
			}

			#上一頁
			if($aData['aButton']['nPrevNext'] == 1)
			{
				if($aData['aButton']['nPrevNextShowStyle'] == 0)
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext prevNextPrev" href="'. $aLink['sPre'] .'"><span class="PageBtnStyleTxt prevNextPrevTxt">'. $aLang['PREV1'] .'</span></a>';
				}
				else
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext prevNextPrev" href="'. $aLink['sPre'] .'"><span class="PageBtnStyleIcon prevNextPrevIcon"></span></a>';
				}
			}

			#上十頁
			if($aData['aButton']['nPrevNext10'] == 1)
			{
				if ($aData['nNowNo'] >= 10)
				{
					if($aData['aButton']['nPrevNext10ShowStyle'] == 0)
					{
						$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext10 prevNext10Prev" href="'. $aLink['sAlong10'] .'"><span class="PageBtnStyleTxt prevNext10PrevTxt">'. $aLang['ALONG10'] .'</span></a>';
					}
					else
					{
						$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext10 prevNext10Prev" href="'. $aLink['sAlong10'] .'"><span class="PageBtnStyleIcon prevNext10PrevIcon"></span></a>';
					}
				}
			}

			#紀錄數量
			if($aData['aButton']['nRecordAmount'] == 1)
			{
				$aReturn['sHtml'] .= '<div class="PageRecord"><span class="PageRecordNum">'. $aData['nDataAmount'] .'</span><span class="PageRecordTxt">紀錄</span></div>';
			}

			$aReturn['sHtml'] .= '<div class="PageListBox">';
			// for ($i=$aData['nBeginNo'] ; $i <= $aData['nEndNo'] ; $i++)
			// {
			// 	$sActive = '';
			// 	if ($aData['nNowNo']==$i)
			// 	{
			// 		$sActive = 'active';
			// 	}
			// 	$aReturn['sHtml'] .= '<div class="PageList '. $sActive .'"><a href="'. $sUrl.'&nPageNo='.$i.$sTemp .'" class="PageListNum">'. $i .'</a></div>';
			// }
			$aReturn['sHtml'] .= '<span class="PageLisNow">'.$aData['nNowNo'].'</span> / <span class="PageListTotal">'.$aData['nTotal'].'</span>';
			$aReturn['sHtml'] .= '</div>';

			#下十頁
			if($aData['aButton']['nPrevNext10'] == 1)
			{
				if (($aData['nTotal'] > 10) && ($aData['nEndNo'] <> $aData['nTotal']))
				{
					if($aData['aButton']['nPrevNext10ShowStyle'] == 0)
					{
						$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext10 prevNext10Next" href="'. $aLink['sNext10'] .'"><span class="PageBtnStyleTxt prevNext10NextTxt">'. $aLang['NEXT10'] .'</span></a>';
					}
					else
					{
						$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext10 prevNext10Next" href="'. $aLink['sNext10'] .'"><span class="PageBtnStyleIcon prevNext10NextIcon"></span></a>';
					}
				}
			}

			#下一頁
			if($aData['aButton']['nPrevNext'] == 1)
			{
				if($aData['aButton']['nPrevNextShowStyle'] == 0)
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext  prevNextNext" href="'. $aLink['sNext'] .'"><span class="PageBtnStyleTxt prevNextNextTxt">'. $aLang['NEXT1'] .'</span></a>';
				}
				else
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther prevNext prevNextNext" href="'. $aLink['sNext'] .'"><span class="PageBtnStyleIcon prevNextNextIcon"></span></a>';
				}
			}

			#最末頁
			if($aData['aButton']['nHeadTail'] == 1)
			{
				if($aData['aButton']['nHeadTailShowStyle'] == 0)
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther headTail headTailTail" href="'. $aLink['sEnd'] .'"><span class="PageBtnStyleTxt headTailTailTxt">'. $aLang['LAST'] .'</span></a>';
				}
				else
				{
					$aReturn['sHtml'] .= '<a class="PageBtnOther headTail headTailTail" href="'. $aLink['sEnd'] .'"><span class="PageBtnStyleIcon headTailTailIcon"></span></a>';
				}
			}

			$aReturn['sHtml'] .= '</div></div>';
		}

		$aReturn['sUrlFirst']		= $aLink['sFirst']; #第一頁連結
		$aReturn['sUrlEnd']		= $aLink['sEnd']; #最末頁連結
		$aReturn['sUrlAlong10']		= ($aData['nNowNo'] >= 10) ? $aLink['sAlong10'] : 'javascript:void(0);'; #前十頁連結
		$aReturn['sUrlNext10']		= (($aData['nTotal'] > 10) && ($aData['nEndNo'] <> $aData['nTotal'])) ? $aLink['sNext10'] : 'javascript:void(0);'; #下十頁連結
		$aReturn['nAllPage']		= $aData['nTotal']; #總頁數
		$aReturn['sUrlPrevPage']	= ($aData['nNowNo'] == 1)?'javascript:void(0);':$aLink['sPre']; #上一頁連結
		$aReturn['sUrlNextPage']	= ($aData['nNowNo'] == $aData['nTotal'])?'javascript:void(0);':$aLink['sNext']; #下一頁連結

		return $aReturn;
	}

	/*發送信件*/
	#'title'
	#'smtp'
	#'from'
	#'to_who'
	#'tmp_file'
	#'html'
	function send_email($send_info)
	{
		$title = $send_info['title'] ;
		$title = "=?UTF-8?B?" . base64_encode($title) . "?=";
		ini_set("SMTP",$send_info['smtp']);
		ini_set("smtp_port",25);
		ini_set("sendmail_from",$send_info['from']);
		$sHeaders = "Content-type: text/html; charset=UTF-8\r\n" ."From: Customer Service<".$send_info['from'].">\r\n";
		$message1 = '';
		if ($send_info['tmp_file'] != '')
		{
			$message = fopen($send_info['tmp_file'],"r");
			if ($message!=0){
				$message1 = fread($message,filesize($send_info['tmp_file']));
			}else{
				$message1 = 'sorry ...';
			}
		}
		else
		{
			$message1 = $send_info['html'];
		}

		$is_Success=mail($send_info['to_who'],$title,$message1,$sHeaders);
		return $is_Success;
	}

	function send_email_mailer($aVal)
	{
		$mail = new PHPMailer();			// 建立新物件

		$mail->IsSMTP();       				// 設定使用SMTP方式寄信
		$mail->SMTPAuth = true;				// 設定SMTP需要驗證
		// $mail->SMTPDebug = 2;			// Debug
		$mail->SMTPSecure = "ssl";			// Gmail的SMTP主機需要使用SSL連線
		$mail->Host = $aVal['smtp'];			// Gmail的SMTP主機
		$mail->Port = $aVal['port'];			// Gmail的SMTP主機的port為465
		$mail->CharSet = "utf-8";			// 設定郵件編碼
		$mail->Encoding = "base64";
		$mail->WordWrap = 50;				// 每50個字元自動斷行

		$mail->Username = 'ds.company465@gmail.com';	// 設定驗證帳號
		$mail->Password = 'ddss1234@@';		// 設定驗證密碼
		$mail->From = 'ds.company465@gmail.com';			// 設定寄件者信箱

		$mail->FromName = $aVal['from_name'];	// 設定寄件者姓名

		$mail->Subject = $aVal['title'];		// 設定郵件標題

		$mail->IsHTML(true);				// 設定郵件內容為HTML

		$mail->AddAddress($aVal['to_who_mail'], $aVal['to_who_name']);	// 收件者郵件及名稱
		$mail->Body = $aVal['html'];			// AddAddress(receiverMail, receiverName)'

		$reVal = $mail->Send();				// 郵件寄出
	}

	// function sys_md5($sAct)
	// {
	// 	$sKey = md5($sAct.EKEY.NOWTIME);
	// 	$sKey = substr($sKey,aKeyCtrl['sLeft'] ,aKeyCtrl['sLeftLen'] ). substr($sKey,aKeyCtrl['sRight'] ,aKeyCtrl['sRightLen'] );
	// 	return $sKey;
	// }

	function sys_jwt_encode($aData)
	{
		// global $oJWT;
		$oJWT = new cJwt();
		$aHeader = array(
			'sAlg'	=> JWTALG,
			'sType'	=> 'JWT',
		);

		$sHeader = $oJWT->base64UrlEncode(json_encode($aHeader));

		$sContent = $oJWT->base64UrlEncode(json_encode($aData));

		$sSign = hash_hmac(JWTALG, $sHeader . '.' . $sContent, SYS['KEY'],TRUE);
		$sSign = $oJWT->base64UrlEncode($sSign);
		$sJWT = $sHeader.'.'.$sContent.'.'.$sSign;
		return $sJWT;
	}

	function checkbrowser()
	{
		##YL 2019-03-04 紀錄會員瀏覽器版本##########
		$aBrowser = array();
		$sBrowser = $_SERVER['HTTP_USER_AGENT'];

		if (stripos($sBrowser, "Firefox/") > 0)
		{
			preg_match("/Firefox\/([^;)]+)+/i", $sBrowser, $sVer);
			$aBrowser[0] = "Firefox";
			$aBrowser[1] = $sVer[1];
		}
		elseif(preg_match('/MicroMessenger\/([^\s]+)/i', $sBrowser, $sVer))
		{
			$aBrowser[0]  = 'weixin';
			$aBrowser[1]   = $sVer[1];
		}
		elseif(preg_match('/QQ\/([^\s]+)/i', $sBrowser, $sVer))
		{
			$aBrowser[0]  = 'QQ';
			$aBrowser[1]   = $sVer[1];
		}
		elseif (stripos($sBrowser, "OPR") > 0)
		{
			preg_match("/OPR\/([\d\.]+)/", $sBrowser, $sVer);
			$aBrowser[0] = "Opera";
			$aBrowser[1] = isset($sVer[1])?$sVer[1]:'';## 2019-9-9 errorlog undefinded
		}
		elseif (stripos($sBrowser, "Edge") > 0)
		{
			preg_match("/Edge\/([\d\.]+)/", $sBrowser, $sVer);
			$aBrowser[0] = "Edge";
			$aBrowser[1] = $sVer[1];
		}
		elseif (stripos($sBrowser, "Chrome") > 0)
		{
			preg_match("/Chrome\/([\d\.]+)/", $sBrowser, $sVer);
			$aBrowser[0] = "Chrome";
			$aBrowser[1] = $sVer[1];
		}
		elseif (stripos($sBrowser,'rv:')>0 && stripos($sBrowser,'Gecko')>0)
		{
			preg_match("/rv:([\d\.]+)/", $sBrowser, $sVer);
			$aBrowser[0] = "IE";
			$aBrowser[1] = $sVer[1];
		}
		elseif (preg_match('/safari\/([^\s]+)/i', $sBrowser, $sVer))
		{
			$aBrowser[0]  = 'Safari';
			$aBrowser[1]   = $sVer[1];
		}
		else
		{
			$aBrowser[0] = 'Other';
			$aBrowser[1] = "";
		}

		$aBrowser[2] = checkdevice($sBrowser);
		return $aBrowser;
	}

	function checkdevice($sBrowser)
	{
		if( stripos($sBrowser, 'windows') !== false ) {
			$sReturn = 'Windows';# PLATFORM_WINDOWS
		}
		else if( stripos($sBrowser, 'iPad') !== false ) {
			$sReturn = 'iPad';	# PLATFORM_IPAD
		}
		else if( stripos($sBrowser, 'iPod') !== false ) {
			$sReturn = 'iPod';	# PLATFORM_IPOD
		}
		else if( stripos($sBrowser, 'iPhone') !== false ) {
			$sReturn = 'iPhone';	# PLATFORM_IPHONE
		}
		elseif( stripos($sBrowser, 'mac') !== false ) {
			$sReturn = 'Apple';	# PLATFORM_APPLE
		}
		elseif( stripos($sBrowser, 'android') !== false ) {
			$sReturn = 'Android';# PLATFORM_ANDROID
		}
		elseif( stripos($sBrowser, 'linux') !== false ) {
			$sReturn = 'Linux';	# PLATFORM_LINUX
		}
		else if( stripos($sBrowser, 'Nokia') !== false ) {
			$sReturn = 'Nokia';	# PLATFORM_NOKIA
		}
		else if( stripos($sBrowser, 'BlackBerry') !== false ) {
			$sReturn = 'BlackBerry';# PLATFORM_BLACKBERRY
		}
		elseif( stripos($sBrowser,'FreeBSD') !== false ) {
			$sReturn = 'FreeBSD';# PLATFORM_FREEBSD
		}
		elseif( stripos($sBrowser,'OpenBSD') !== false ) {
			$sReturn = 'OpenBSD';# PLATFORM_OPENBSD
		}
		elseif( stripos($sBrowser,'NetBSD') !== false ) {
			$sReturn = 'NetBSD';# PLATFORM_NETBSD
		}
		elseif( stripos($sBrowser, 'OpenSolaris') !== false ) {
			$sReturn = 'OpenSolaris';# PLATFORM_OPENSOLARIS
		}
		elseif( stripos($sBrowser, 'SunOS') !== false ) {
			$sReturn ='SunOS';	# PLATFORM_SUNOS
		}
		elseif( stripos($sBrowser, 'OS\/2') !== false ) {
			$sReturn = 'OS/2';	# PLATFORM_OS2
		}
		elseif( stripos($sBrowser, 'BeOS') !== false ) {
			$sReturn = 'BeOS';	# PLATFORM_BEOS
		}
		elseif( stripos($sBrowser, 'win') !== false ) {
			$sReturn = 'Windows';# PLATFORM_WINDOWS
		}
		return $sReturn;
	}

	function Get_IP_Location($sIP)
	{
		$aIP_Data = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$sIP)));
		return $aIP_Data['geoplugin_region'];
	}

	function sys_md5($aVal)
	{
		return md5($aVal['key'].$aVal['act'].$aVal['time']);
	}
?>
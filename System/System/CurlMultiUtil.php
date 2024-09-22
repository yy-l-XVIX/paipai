<?php
#########################
#				#
#		LC		#
#	  2018-09-12	#
#				#
#########################

class CurlMultiUtil
{
	# 根據 URL, PostData 獲取 CURL 請求物件
	private static function GetCurlObject($sURL, $aPostData = array(), $aHeader = array())
	{
		$options = array();
		$options[CURLOPT_URL] = $sURL;
		$options[CURLOPT_TIMEOUT] = 5;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_REFERER] = $sURL;
		$options[CURLOPT_AUTOREFERER] = true;
		$options[CURLOPT_ENCODING] = 'gzip,deflate';
		$options[CURLOPT_CONNECTTIMEOUT] = 5;
		foreach($aHeader as $key => $value)
		{
			$options[$key] = $value;
		}

		if(!empty($aPostData) && is_array($aPostData))
		{
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = http_build_query(array('Data' => $aPostData));
		}

		if(stripos($sURL, 'https') === 0)
		{
			$options[CURLOPT_SSL_VERIFYHOST] = false;
			$options[CURLOPT_SSL_VERIFYPEER] = false;
		}

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		return $ch;
	}

	# 處理
	private static function CURL_Request($aChList)
	{
		$CURL_Mod = curl_multi_init();
		$aName = $aTemp = $aTemp_Name = array();

		# 將待請求物件放入 $CURL_Mod 中
		foreach ($aChList as $k => $ch)
		{
			$aTemp = explode("#", (string) $ch);
			$aName[$aTemp[1]] = $k;
			curl_multi_add_handle($CURL_Mod, $ch);
		}

		# 輪詢
		do
		{
			while (($execrun = curl_multi_exec($CURL_Mod, $running)) == CURLM_CALL_MULTI_PERFORM);
			if ($execrun != CURLM_OK)
			{
				break;
			}
			# 一旦有一個請求完成，找出來處理，因為 CURL 底層是 SELECT 函數，所以最大受限於1024
			while ($done = curl_multi_info_read($CURL_Mod))
			{
				# 從請求中獲取資訊、內容、錯誤
				# $info = curl_getinfo($done['handle']);
				$output = curl_multi_getcontent($done['handle']);
				# $error = curl_error($done['handle']);

				$aTemp_Name = explode("#", (string) $done['handle']);
				$res[$aName[$aTemp_Name[1]]] = $output;
				//$res[$aName[$aTemp_Name[1]]]['error'] = $output;

				# 把請求已經完成的 CURL handle 刪除
				curl_multi_remove_handle($CURL_Mod, $done['handle']);
			}
			# 當沒有資料的時候進行強制睡眠，把 CPU 使用權交出來，避免上面 do 空跑資料無窮迴圈導致 CPU 100%
			if ($running)
			{
				$rel = curl_multi_select($CURL_Mod, 1);
				if($rel == -1)
				{
					usleep(100);
				}
			}
			if($running == false)
			{
				break;
			}
		} while (true);

		curl_multi_close($CURL_Mod);
		return $res;
	}

	# 呼叫 CURL_Multi 多執行續函式
	public static function CURL_Multi($aURL_Data)
	{
		$aData = array();
		if (!empty($aURL_Data))
		{
			$aChList = array();
			foreach ($aURL_Data as $aURL_Data_Key => $aURL_Data_Value)
			{
				if(!isset($aURL_Data_Value['Data']))
				{
					$aURL_Data_Value['Data'] = array();
				}

				$aChList[$aURL_Data_Key] = self::GetCurlObject($aURL_Data_Value['URL'], $aURL_Data_Value['Data']);
			}
			$aData = self::CURL_Request($aChList);
		}
		return $aData;
	}
}
?>
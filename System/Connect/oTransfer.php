<?php
	################2018/8/15 Gino#####################
	# point_check($nUid) 		比對點數與密鑰 比對正確回傳點數陣列 比對錯誤回傳0
	# $nUid	為函式作用對象 為nUid
	# 回傳陣列為 array('Money' => 金錢量, 'Water' => 反水量)
	////////////////
	# point_update($nUid,$type,$nNewPoint,$checkdone,$nPoint)	更新點數
	# $nUid	可填函式作用對象(int)uid 或是作用對象之(array)data
	# $type	可為money或water
	# $delta	為點數變化量
	# $checkdone為是否已完成檢測 若為1則不做額比對 但須使用$nPoint輸入原始點數 ;為0則另做比對 額外呼叫point_check
	# $nPoint	若$checkdone為1 則使用此參數作為點數運算
	////////////////
	# $aPrime	為質數表 抓取12個質數位數進行存檔 存取時結構關係需要先-1
	# MoneyKey	為自定義鑰匙
	# 加密方式為 時間戳+Uid+點數量+自定義密鑰
	#######################################################
	class oTransfer
	{
		static Private $MoneyKey = 'Schlussel';
		static Private $aPrime = array(2,3,5,7,11,13,17,19,23,29,31,32);

		static function PointCheck($nUid)
		{
			global $oPdo;
			$sLongkey 	= '';	#全長鑰匙
			$aShortkey 	= array('Money' => '', 'Water' => '');	#短鑰匙
			$bRes 	= false;  #結果 0為吻合 1為不吻合 預設不吻合 比對吻合才修正

			try
			{
				/*
				2020/03/06 kc water先註解
				$sSQL = '	SELECT 	nMoney,
								nMoneyTime,
								sMoneyKey,
								nWater,
								nWaterTime,
								sWaterKey
						FROM 		web_money_encrypt
						WHERE 	nUid = :nUid
						LIMIT		1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);

				if($aRows['nMoney'] < 0 || $aRows['nWater'] < 0)
				{
					return 48; //不吻合
				}

				$aPoint = array('Money' => $aRows['nMoney'], 'Water' => $aRows['nWater']);

				$sLongkey = $aRows['nMoneyTime'].$nUid.(sprintf('%.5f',$aRows['nMoney']) ).self::$MoneyKey;
				$tmp = $sLongkey;
				$sLongkey = md5($sLongkey);
				for($i=0;$i<12;$i++)
				{
					$aShortkey['Money'] .= $sLongkey[self::$aPrime[$i]-1];
				}

				$sLongkey = $aRows['nWaterTime'].$nUid.(sprintf('%.5f',$aRows['nWater']) ).self::$MoneyKey;
				$sLongkey = md5($sLongkey);
				for($i=0;$i<12;$i++)
				{
					$aShortkey['Water'] .= $sLongkey[self::$aPrime[$i]-1];
				}

				if($aShortkey['Money'] == $aRows['sMoneyKey'] && $aShortkey['Water'] == $aRows['sWaterKey'])
				{
					$bRes = true;
				}
				*/
				$sSQL = '	SELECT 	nMoney,
								nMoneyTime,
								sMoneyKey
						FROM 		'.CLIENT_USER_MONEY.'
						WHERE 	nUid = :nUid
						LIMIT		1';
				$Result = $oPdo->prepare($sSQL);
				$Result->bindValue(':nUid',$nUid,PDO::PARAM_INT);
				sql_query($Result);
				$aRows = $Result->fetch(PDO::FETCH_ASSOC);

				if($aRows['nMoney'] < 0)
				{
					return false; //不吻合
				}

				$aPoint = array('Money' => $aRows['nMoney']);

				$sLongkey = $aRows['nMoneyTime'].$nUid.(sprintf('%.5f',$aRows['nMoney']) ).self::$MoneyKey;
				$tmp = $sLongkey;
				$sLongkey = md5($sLongkey);
				for($i=0;$i<12;$i++)
				{
					$aShortkey['Money'] .= $sLongkey[self::$aPrime[$i]-1];
				}

				if($aShortkey['Money'] == $aRows['sMoneyKey'])
				{
					$bRes = true;
				}

			}
			catch(Exception $e)
			{
				return false;
			}

			if(!$bRes)
			{
				return false; //不吻合
			}
			else
			{
				return $aPoint;
			}
		}

		// $aNewPoint = array(
		// 	'Money' => xxx;
		// 	'Water' => xxx;
		// )
		// $aNewPoint = array(
		// 	'Money' => xxx;
		// )
		static function PointUpdate($nUid, $aNewPoint, $checkdone=0)
		{
			global $oPdo;
			$aLongkey = array();
			$aShortkey = array();


			if($checkdone == 0)
			{
				$aPoint = self::PointCheck($nUid);
				if(!is_array($aPoint))
				{
					return false;// not match
				}
			}

			if(empty($aNewPoint))
			{
				return false;
			}

			$aSQL_Array = array();
			$aSQL_ArrayMoney = array();

			foreach($aNewPoint as $k => $v)
			{
				if($v != -1)
				{
					$aShortkey[$k] = '';
					$aLongkey[$k] = md5(NOWTIME.$nUid.sprintf('%.5f',$v).self::$MoneyKey);
					// echo $k.$v.'|';
					for($i=0;$i<12;$i++)
					{
						$aShortkey[$k] .= $aLongkey[$k][self::$aPrime[$i]-1];
					}
					$aSQL_Array['n'.$k]		= sprintf('%.5f',$v);
					$aSQL_Array['s'.$k.'Key']	= $aShortkey[$k];
					$aSQL_Array['n'.$k.'Time']	= NOWTIME;
				}
			}

			return $aSQL_Array;
		}
	}

?>
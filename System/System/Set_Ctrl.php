<?php
	// PHP_INT_MAX,~PHP_INT_MAX
	// Max => 32bit:2147483647     64bit:9223372036854775807
	// Min => 32bit:-2147483648     64bit:-9223372036854775808
	function filter_input_float($sName, $sType, $fDefult = 0.0, $fbit = 2, $Exponential = false, $fMin = -2147483648, $fMax = PHP_INT_MAX) 
	{
		$fFun_Val = NULL;
		$aInt_Options = array(
			"options"=>
				array(
					"min_range"=> (float) $fMin,
					"max_range"=> (float) $fMax
		));
		// $filter = ($Exponential == true)? 'FILTER_VALIDATE_FLOAT':'FILTER_SANITIZE_STRING';
		// ^^filter_input第三變數無法使用參數...
		//用FILTER_VALIDATE_FLOAT,會把科學記號直接轉成數字
		if($Exponential == true)
		{
			if ( $sType === INPUT_REQUEST ) {
				if ( isset($_POST[$sName]) ) {
					$fFun_Val = filter_input(INPUT_POST, $sName, FILTER_VALIDATE_FLOAT);
				} else {
					if ( isset($_GET[$sName]) ) {
						$fFun_Val = filter_input(INPUT_GET, $sName, FILTER_VALIDATE_FLOAT);
					}
				}
			} else {
				$fFun_Val = filter_input($sType, $sName, FILTER_VALIDATE_FLOAT);
			}
		}
		else
		{
			if ( $sType === INPUT_REQUEST ) {
				if ( isset($_POST[$sName]) ) {
					$fFun_Val = filter_input(INPUT_POST, $sName, FILTER_SANITIZE_STRING);
				} else {
					if ( isset($_GET[$sName]) ) {
						$fFun_Val = filter_input(INPUT_GET, $sName, FILTER_SANITIZE_STRING);
					}
				}
			} else {
				$fFun_Val = filter_input($sType, $sName, FILTER_SANITIZE_STRING);
			}
		}
		if($Exponential == false && !preg_match("/^-?\d+(.\d+)?$/", $fFun_Val))
		{
			return $fDefult;
		}

		if ( ($fMin <> -2147483648) || ($fMax <> PHP_INT_MAX) ) {
			$fFun_Val = filter_var($fFun_Val, $filter, $aInt_Options);
		}

		$fFun_Val = (($fFun_Val === NULL) || ($fFun_Val === false)) ? $fDefult : $fFun_Val;

		// $fFun_Val = sprintf('%.'.$fbit.'f',$fFun_Val);
		$fFun_Val = round($fFun_Val,$fbit); //對浮點數做格式化,小數點後fbit位

		return (float) $fFun_Val;
	}

	function filter_input_int($sName, $sType, $nDefult = 0, $nMin = -2147483648, $nMax = PHP_INT_MAX) 
	{
		$nFun_Val = NULL;
		$aInt_Options = array(
			"options"=>
				array(
					"min_range"=> (int) $nMin,
					"max_range"=> (int) $nMax
		));

		if ( $sType === INPUT_REQUEST ) {
			if ( isset($_POST[$sName]) ) {
				$nFun_Val = filter_input(INPUT_POST, $sName, FILTER_VALIDATE_INT);
			} else {
				if ( isset($_GET[$sName]) ) {
					$nFun_Val = filter_input(INPUT_GET, $sName, FILTER_VALIDATE_INT);
				}
			}
		} else {
			$nFun_Val = filter_input($sType, $sName, FILTER_VALIDATE_INT);
		}

		if ( ($nMin <> -2147483648) || ($nMax <> PHP_INT_MAX) ) {
			$nFun_Val = filter_var($nFun_Val, FILTER_VALIDATE_INT, $aInt_Options);
		}

		$nFun_Val = (($nFun_Val === NULL) || ($nFun_Val === false)) ? $nDefult : $nFun_Val;

		return (int) $nFun_Val;
	}

	function filter_input_str($sName, $sType, $sDefult = '', $nString_Limit = 0) 
	{
		$sFun_Val = NULL;

		if ( $sType === INPUT_REQUEST ) {
			if ( isset($_POST[$sName]) ) {
				$sFun_Val = filter_input(INPUT_POST, $sName, FILTER_SANITIZE_STRING);
			} else {
				if ( isset($_GET[$sName]) ) {
					$sFun_Val = filter_input(INPUT_GET, $sName, FILTER_SANITIZE_STRING);
				}
			}
		} else {
			$sFun_Val = filter_input($sType, $sName, FILTER_SANITIZE_STRING);
		}

		if ( $nString_Limit > 0 ) {
			if ( mb_strlen($sFun_Val) > $nString_Limit ) {
				mb_internal_encoding('UTF-8');
				$sFun_Val = mb_substr($sFun_Val, 0, $nString_Limit);
			}
		}

		$sFun_Val = ($sFun_Val === NULL) ? $sDefult : $sFun_Val;

		return $sFun_Val;
	}

	function filter_input_ary($sName, $sType, $aSetting = array('_Initial'=>'','nMin'=>-2147483648,'nMax'=>PHP_INT_MAX,'nLimit'=>0)) 
	{
		if(empty($aSetting['_Initial'])) $aSetting['nLimit'] = '';
		if(empty($aSetting['nMin'])) $aSetting['nLimit'] = -2147483648;
		if(empty($aSetting['nMax'])) $aSetting['nLimit'] = PHP_INT_MAX;
		if(empty($aSetting['nLimit'])) $aSetting['nLimit'] = 0;
		$aFun_Val = NULL;
		if ( $sType === INPUT_REQUEST ) {
			if ( isset($_POST[$sName]) ) {
				$aFun_Val = filter_input(INPUT_POST, $sName, FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
			} else {
				if ( isset($_GET[$sName]) ) {
				$aFun_Val = filter_input(INPUT_GET, $sName, FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
				}
			}
		} else {
			$aFun_Val = filter_input($sType, $sName, FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
		}

		if($aFun_Val === NULL)
		{
			return array();
		}
		else
		{
			return filter_ary_recursive($aFun_Val,$aSetting);
		}

	}

	function filter_ary_recursive($ary,$aInitialSet = array('_Initial'=>'','nMin'=>-2147483648,'nMax'=>PHP_INT_MAX,'nLimit'=>0))
	{
		foreach($ary as $k => $v)
		{
			if(is_int($v) && $v<PHP_INT_MAX && $v>(-2147483648))
			{
				$v = filter_var($v, FILTER_VALIDATE_INT);
				if ( ($aInitialSet['nMin'] != -2147483648) || ($aInitialSet['nMax'] != PHP_INT_MAX) )
				{
					if($aInitialSet['nMin'] < -2147483648)
					{
						$aInitialSet['nMin'] = -2147483648;
					}
					if($aInitialSet['nMax'] > PHP_INT_MAX)
					{
						$aInitialSet['nMax'] = PHP_INT_MAX;
					}
					if($v < $aInitialSet['nMin'])
					{
						$v = $aInitialSet['nMin'];
					}
					if($v > $aInitialSet['nMax'])
					{
						$v = $aInitialSet['nMax'];
					}
				}
			}
			elseif(is_string($v))
			{
				$v = filter_var($v, FILTER_SANITIZE_STRING);
				if ( $aInitialSet['nLimit'] > 0 )
				{
					if ( mb_strlen($v) > $aInitialSet['nLimit'] )
					{
						mb_internal_encoding('UTF-8');
						$v = mb_substr($v, 0, $aInitialSet['nLimit']);
					}
				}

			}
			elseif(is_array($v))
			{
				$v = filter_ary_recursive($v);
			}
			else
			{
				$v = null;
			}
			$v = ($v === NULL) ? $aInitialSet['_Initial'] : $v;
		}
		return $ary;
	}

?>
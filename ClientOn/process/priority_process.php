<?php
	$aParam = explode('&', $_SERVER['QUERY_STRING']);
	#R => 讀, W => 讀寫, S => 靜態
	#X => 不判斷登入 L =>判斷登入
	#Ix => 目錄,
	#1 => 1 html, 2 php,
	#01 => 檔案 index
	$aMenuToUrl = array();
	/*各項功能追加陣列*/

	// Ix
	require_once('_index.php');
	// Lg
	require_once('_login.php');
	// Rg
	require_once('_register.php');
	// Fg
	require_once('_forgot.php');
	// Cr
	require_once('_center.php');
	// Tl
	require_once('_tool.php');
	// dc
	require_once('_discuss.php');
	// Rc
	require_once('_recharge.php');
	// Jb
	require_once('_job.php');
	// Ct
	require_once('_chat.php');
	// Wd
	require_once('_withdrawal.php');

	/**/

	#反解用
	$aMenuToNo = array();
	foreach ($aMenuToUrl as $k => $v)
	{
		$aMenuToNo[$v] = $k;
	}

	# 反解用 2
	$aMenuIndex = array();
	foreach($aMenuToUrl as $k => $v)
	{
		$sIx = preg_replace("/\\d+/",'', $k);

		if(!isset($aMenuIndex[$sIx]))
		{
			$aTmp = explode('/', $v);
			$aMenuIndex[$sIx] = $aTmp[1];
		}
	}

	// 加密方式 1 原本的 2 彥廷版
	$nWebUrlType = 2;

	$aRequire = array(
		'MenuToNo'	=> $aMenuToNo, #反解檔案目錄位置
		'MenuToUrl'	=> $aMenuToUrl, #檔案目錄位置
		'Param'	=> $aParam['0'], #接收參數
		'Require'	=> '',#迴遞路徑
	);

	$sRe = sys_web_decode($aRequire);
	$aRequire['Require'] = $aRequire['MenuToUrl'][$sRe]; #sRe = RLEm101

	if ($sRe[1] == 'L')
	{
		$aSystem['nLogin'] = 1;
	}

	// jwt check
	$sJWT 	= filter_input_str('sJWT',		INPUT_REQUEST,'');
	$sBackUrl 	= filter_input_str('sBackUrl',	INPUT_REQUEST,sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']));
	$nS		= filter_input_int('run_page',	INPUT_REQUEST, 0);
	$bPass = true;
	$oJWT = new cJwt();
	if($sJWT != '')
	{
		$aJWT = $oJWT->validToken($sJWT);
		if ($aJWT === false)
		{
			$bPass = false;
			if ($nS==1)
			{

				# 返回上一頁
				$aReturn = array(
					'nStatus'		=> 999,
					'sMsg'		=> 'jwt error',
					'aData'		=> array(),
					'nAlertType'	=> 1,
					'sUrl'		=> $sBackUrl
				);
				echo json_encode($aReturn);
				// exit;
			}
			
				header('Location:'.$sBackUrl);
				exit;


			
		}
	}

	if ($bPass)
	{
		// 取得 讀或寫 for Config.php 使用
		if ($sRe[0] == 'R')
		{
			$aSystem['nConnect'] = 1;
		}
		if ($sRe[0] == 'W')
		{
			$aSystem['nConnect'] = 2;
		}
		if ($sRe[0] == 'A')
		{
			$aSystem['nConnect'] = 3;
		}
	}
?>
<?php
	$aParam = explode('&', $_SERVER['QUERY_STRING']);
	#R => 讀, W => 讀寫, S => 靜態
	#X => 不判斷登入 L =>判斷登入
	#Ix => 目錄,
	#1 => 1 html, 2 php,
	#01 => 檔案 index
	$aMenuToUrl = array();
	/*各項功能追加陣列*/

	# Key => Ix
	require_once('_index.php');
	# Key => Tl
	require_once('_tool.php');
	# Key => Lg
	require_once('_login.php');
	# Key => Mg
	require_once('_end_manager_data.php');
	# Key => Ud
	require_once('_client_user_data.php');
	# Key => Ae
	require_once('_client_news.php');
	# Key => Mu
	require_once('_end_menu.php');
	# Key => Lo
	require_once('_end_log.php');
	# Key => My
	require_once('_client_money.php');
	# Key => Rp
	require_once('_end_report.php');
	# Key => Dp
	require_once('_end_developer.php');
	# Key => Dc
	require_once('_client_discuss.php');
	# Key => Jb
	require_once('_client_job.php');
	# Key => Ct
	require_once('_client_chat.php');
	# Key => Sv
	require_once('_client_service.php');

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
	$sJWT 	= filter_input_str('sJWT', INPUT_REQUEST,'');
	$sBackUrl 	= filter_input_str('sBackUrl', INPUT_REQUEST,sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']));
	$bPass = true;
	$oJWT = new cJwt();
	if($sJWT != '')
	{
		$aJWT = $oJWT->validToken($sJWT);
		if ($aJWT === false)
		{
			$bPass = false;
			# 返回上一頁
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
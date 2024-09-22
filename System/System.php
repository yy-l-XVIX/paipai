<?php
	# 環境設定
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	error_reporting(E_ALL);
	date_default_timezone_set('Asia/Taipei');
	header('Content-Type: text/html; charset=UTF-8');

	# 核心載入
	require_once('System/Error_Handler.php');	# 錯誤事件擷取核心
	require_once('System/Set_Ctrl.php');	# 過濾核心
	require_once('System/Function.php');	# 核心共用 Function
	require_once('System/CurlMultiUtil.php');	# 多執行緒 CURL 核心
	require_once('System/Define.php');		# 共用常數 , table define
	require_once('System/Class_Jwt.php');	# jwt

	$aSystem = array(
		'sLang'		=> 'TW', 	# 專案預設語系
		'nConnect'		=> 0,		# 0: 不連線DB 1: 連DB
		'nLogin'		=> 0,		# 0: 不判斷登入 1: 判斷登入
		'bDrive'		=> isMobile(),
		'sTitle'		=> 'test',
		#後台
		'nHtml' 		=> 0,	#0: 手機電腦版同一支檔案 1: 手機電腦版分開
		'sHtml' 		=> 'html_v',
		'nVer' 		=> 1,	#版本號
		#前台
		'nClientHtml' 	=> 0,	#0: 手機電腦版同一支檔案 1: 手機電腦版分開
		'sClientHtml' 	=> 'html_v',
		'nClientVer' 	=> 1,	#版本號
		'aWebsite'		=> array(), #站台資料
		'aLogNums'		=> array(), #動作代號
		'aNav'		=> array(), #目錄
		'aParam'		=> array(), #環境設定
	);

	if($aSystem['nHtml'] != 0)
	{
		if($aSystem['bDrive'])
		{
			$aSystem['sHtml'] = 'Mobile_v';
		}
		else
		{
			$aSystem['sHtml'] = 'Pc_v';
		}
	}
	if($aSystem['nClientHtml'] != 0)
	{
		if($aSystem['bDrive'])
		{
			$aSystem['sClientHtml'] = 'Mobile_v';
		}
		else
		{
			$aSystem['sClientHtml'] = 'Pc_v';
		}
	}

	$aSystem['aBlacklist'] = array(
		'42.51.41.221' => true,
	);

	if(isset($aSystem['aBlacklist'][USERIP]))
	{
		exit;
	}
?>
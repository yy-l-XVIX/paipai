<?php
	define('NOWTIME',			time());
	define('NOWDATE',			date('Y-m-d H:i:s',NOWTIME));
	define('USERIP',			getUserIp());

	define('aLANG', array(
		'TW'		=>	'繁體中文',
		'CN'		=>	'简体中文',
		// 'EN'		=>	'English',
		// 'VN'		=>	'Tiếng việt',
	));
	define('COMPANY', array(
		'URL'	=> 'http://demo801.dtap000s3.com/Project/cpy/',
	));

	define('PAY', array(
		// 'URL'	=> 'http://demo801.dtap000s3.com/Project/pay/API/online/index.php',
		'URL'	=> 'http://payv2.ntt1199.com/API/index.php',
	));

	define('WEBSITE', array(
		'ADMURL'	=> 'http://adm980qwasx.paipaisss.com/',
		'WEBURL'	=> 'https://www.paipaisss.com/',
		// 'SHAREURL'	=> 'http://[[::sRandomText::]].nez001to.com/',
		'SHAREURL'	=> 'http://nez001to.com/',
	));

	define('QRCODE', array(
		'URL'	=> '',
	));

	define('IMAGEMAXSIZE', 1048576*10);## 1048576為1MB
	define('VIDEOMAXSIZE', 1048576*10);## 1048576為1MB

	define('IMAGE', array(
		'URL'	=> 'https://petimg.monopoly168.com/',
	));

	define('GOOGLE', array(
		'URL'	=> 'http://goline.ness9999.com/End/',
	));

	$sSTime = 'Y-m-d 00:00:00';
	$sETime = 'Y-m-d 23:59:59';
	define('aDAY', array(
		#昨日
		'YESTERDAY'	=> array(
			'sStartDay'		=> date($sSTime, strtotime('-1 days')),
			'sEndDay'		=> date($sETime, strtotime('-1 days')),
		),
		#今日
		'TODAY'	=> array(
			'sStartDay'		=> date($sSTime, NOWTIME),
			'sEndDay'		=> date($sETime, NOWTIME),
		),
		#上週
		'LASTWEEK'	=> array(
			'sStartDay'		=> date($sSTime, strtotime('-2 Sunday')),
			'sEndDay'		=> date($sETime, strtotime('Saturday last week')),
		),
		#本週
		'THISWEEK'	=> array(
			'sStartDay'		=> date($sSTime, strtotime('Sunday last week')),
			'sEndDay'		=> date($sETime, strtotime('Saturday this week')),
		),
		#上月
		'LASTMONTH'	=> array(
			'sStartDay'		=> date($sSTime, strtotime('first day of last month')),
			'sEndDay'		=> date($sETime, strtotime('last day of last month')),
		),
		#本月
		'THISMONTH'	=> array(
			'sStartDay'		=> date($sSTime, strtotime('first day of this month')),
			'sEndDay'		=> date($sETime, strtotime('last day of this month')),
		),
	));
?>
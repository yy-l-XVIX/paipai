<?php
	define('aPAYMENTONLINESETTING', array(
		'NAME'		=> '金流名稱',
		'ACCOUNT'		=> '商戶號',
		'CODE'		=> '金流代碼',
		'FEE'			=> '手續費',
		'FEETYPE'		=> array(
			'sTitle'	=> '手續費模式',
			1		=> array(
				'sText'	=> '固定值',
				'sCheck'	=> '',
			),
			2		=> array(
				'sText'	=> '百分比',
				'sCheck'	=> '',
			),
		),
		'sKey0'		=> '商戶KEY',
		'sKey1'		=> '加密KEY',
		'sKey2'		=> '其它KEY1',
		'sKey3'		=> '其它KEY2',
		'sKey4'		=> '其它KEY3',
		'sKey5'		=> '其它KEY4',
		'sSign'		=> 'api密鑰',
		'MAX'			=> '單筆上限',
		'MIN'			=> '單筆下限',
		'DAYLIMITTIMES'	=> '每日提單次數上限',
		'DAYLIMITMONEY'	=> '每日提單金額上限',
	));
?>
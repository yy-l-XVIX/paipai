<?php
	define('aMANUAL', array(
		'ACCOUNT'		=> '會員帳號',
		'MONEY'		=> '金額',
		'TYPE3'		=> array(
			'sTitle'	=> '來源',
			1		=> array(
				'sText'	=> '入款',
				'sSelect'	=> ''
			),
			2		=> array(
				'sText'	=> '出款',
				'sSelect'	=> ''
			),
		),
		'STATUS'		=> array(
			'sTitle'	=> '狀態',
			0		=> array(
				'sText'	=> '未審核',
				'sSelect'	=> '',
				'sClass'	=> 'FontBlue',
			),
			1		=> array(
				'sText'	=> '審核通過',
				'sSelect'	=> '',
				'sClass'	=> 'FontGreen',
			),
			99		=> array(
				'sText'	=> '拒絕',
				'sSelect'	=> '',
				'sClass'	=> 'FontRed',
			),
		),
		'TYPE1'		=> array(
			'sTitle'	=> '類別',
			1		=> array(
				'sText'	=> '測試',
				'sSelect'	=> ''
			),
			2		=> array(
				'sText'	=> '人工',
				'sSelect'	=> ''
			),
			// 3		=> array(
			// 	'sText'	=> '正式',
			// 	'sSelect'	=> ''
			// ),
		),
		'ADMINNAME'			=> '管理員帳號',
		'MEMO'			=> '備註',
		'TOTALINMONEY'		=> '總充值金額',
		'TOTALINCOUNT'		=> '總充值筆數',
		'TOTALOUTMONEY'		=> '總提款金額',
		'TOTALOUTCOUNT'		=> '總提款筆數',
		'PAGETOTALINMONEY'	=> '本頁充值金額',
		'PAGETOTALINCOUNT'	=> '本頁充值筆數',
		'PAGETOTALOUTMONEY'	=> '本頁提款金額',
		'PAGETOTALOUTCOUNT'	=> '本頁提款筆數',
		'MONEYTOOMUCH'		=> '會員金額不足',
		'MONEYBIGGERZERO'		=> '金額需大於0',
		'TITLEUNFILED'		=> '標題未填寫',
	));
?>
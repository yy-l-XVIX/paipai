<?php
	define('aPAYMENTCOMPANY', array(
		'ACCOUNT'		=> '會員帳號',
		'MONEY'		=> '金額',
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
		'RANK'		=> '收費方案',
		'PAYTYPE'		=> '支付方式',
		'TYPE1'		=> array(
			'sTitle'	=> '來源',
			1		=> array(
				'sText'	=> '測試',
				'sSelect'	=> ''
			),
			2		=> array(
				'sText'	=> '人工',
				'sSelect'	=> ''
			),
			3		=> array(
				'sText'	=> '正式',
				'sSelect'	=> ''
			),
		),
		'FEE'			=> '手續費',
		'ADMINNAME'		=> '管理員帳號',
		'MEMO'		=> '備註',
		'BANKNAME'		=> '銀行帳號',
		'CHECK'		=> '查看',
		'DETAIL'		=> '入款資訊',
		'TOTALMONEY'	=> '總金額',
		'PAGETOTALMONEY'	=> '本頁金額',
		'TOTALCOUNT'	=> '總筆數',
		'PAGETOTALCOUNT'	=> '本頁筆數',
	));
?>
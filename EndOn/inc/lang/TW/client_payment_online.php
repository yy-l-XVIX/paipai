<?php
	define('aPAYMENTONLINE', array(
		'ORDER'		=> '訂單編號',
		'USER'		=> '會員名稱',
		'PAYMENT'		=> '金流名稱',
		'TUNNEL'		=> '選用通道',
		'MONEY'		=> '充值金額',
		'FEE'			=> '手續費',
		'ADMINT'		=> '審核員',
		'HANDCONFIRM'	=> '手動通過',
		'PAGETOTALCOUNT'	=> '本頁筆數',
		'PAGETOTALMONEY'	=> '本頁金額',
		'TOTALCOUNT'	=> '總筆數',
		'TOTALMONEY'	=> '總金額',
		'STATUS'		=> array(
			'sTitle'	=> '狀態',
			0		=> array(
				'sText'	=> '未審核',
				'sSelect'	=> '',
				'sClass'	=> 'FontBlue',
			),
			1		=> array(
				'sText'	=> '回調成功',
				'sSelect'	=> '',
				'sClass'	=> 'FontGreen',
			),
			99		=> array(
				'sText'	=> '拒絕',
				'sSelect'	=> '',
				'sClass'	=> 'FontRed',
			),
		),
		'aMsg'		=> array(
			'MANUAL'	=> '確定要手動上分?',
			'NOORDER'	=> '查無訂單',
			'NOMEMBER'	=> '查無該會員',
		),
	));
?>
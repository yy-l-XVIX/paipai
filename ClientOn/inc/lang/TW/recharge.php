<?php
	define('aRECHARGE', array(
		'WAY'		=> '付款方式',
		'ONLINE'	=> '線上入款',
		'POINT'	=> '點數兌換',
		'PAYMENT'	=> '充值平台',
		'TUNNEL'	=> '充值通道',
		'MONEY'	=> '方案價格',
		'FEE'		=> '手續費',
		'TOTAL'	=> '總金額',
		'DEFAULT'	=> '請選擇',
		'GOPAY'	=> '確認付款',
		'METHOD'	=> '方案',
		'INFO'=>'付款完成才能開通會員功能',
		'aPAYMETHOD'	=> array(
			'1'	=> array(
				'sText' => '線上入款',
				'sType' => 'online',
			),
			'2'	=> array(
				'sText' => '公司入款',
				'sType' => 'company',
			),
			'3'	=> array(
				'sText' => '點數兌換',
				'sType' => 'money',
			),
		),
	));

	define('aERROR',array(
		'PAYMENTSELECT'	=> '請選擇充值平台',
		'NOPAYMENT'		=> '查無充值平台',
		'TUNNELSELECT'	=> '請選擇充值通道',
		'NOTUNNEL'		=> '查無平台充值通道',
		'KIND'		=> '查無此方案',
		'MAXLIMIT'		=> '超過總提單次數上限',
		'MONEYLIMIT'	=> '超過總提單金額上限',
		'DAYMAXLIMIT'	=> '超過每日提單次數上限',
		'TRYAGAIN'		=> '[[::nTime::]] 分鐘內不可重複提單，請稍後再嘗試',
	));
?>
<?php
	define('aRECHARGE', array(
		'WAY'		=> '付款方式',
		'BANK'	=> '所屬銀行',
		'NAME'	=> '充值姓名',
		'ACCOUNT'	=> '銀行帳號',
		'METHOD'	=> '會員方案',
		'MONEY'	=> '方案價格',
		'MEMO'	=> '備註',
		'COPY'	=> '複製',
		'FEE'		=> '手續費',
		'TOTAL'	=> '總金額',
		'INFO'	=>'付款完成才能開通會員功能',
		'GOPAY'	=> '確認付款',
		'MEMOINFO'	=> '請填入您的帳戶後五碼，以便核對。',
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
	define('aERROR', array(
		'ACCOUNT'	=> '查無此入款帳號',
		'MAXLIMIT'	=> '超過總提單次數上限',
		'MONEYLIMIT'=> '超過總提單金額上限',
		'DAYMAXLIMIT'=> '超過每日提單次數上限',
		'TRYAGAIN'	=> '[[::nTime::]] 分鐘內不可重複提單，請稍後再嘗試',
		'CARDERR'	=> '銀行帳號異常',
		'NOBANK'	=> '目前無可用充值銀行',
		'PENDING'	=> '您尚有待審核訂單', #您尚有待審核訂單
	));
?>
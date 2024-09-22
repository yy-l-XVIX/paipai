<?php
	define('aRECORD', array(
		'SEARCHACCOUNT'=> '輸入會員帳號',
		'DATE'	=> '日期',
		'MEMO'	=> '內容',
		'MONEY'	=> '點數',
		'BALANCE'	=> '餘額',
		'STATUS'	=> '狀態',
		'TOTAL'	=> '總計',
		'SUBTOTAL'	=> '小計',
		'TO'		=> '至',
		'aTYPE2'	=> array(
			211 => array(
				'sText' => '轉出',
				'sSelect'=> '',
			),
			210 => array(
				'sText' => '轉入',
				'sSelect'=> '',
			),
			202 => array(
				'sText' => '提領',
				'sSelect'=> '',
			),
		),
		'aSTATUS'	=> array(
			0 => array(
				'sText' => '待審核',
				'sSelect'=> '',
			),
			1 => array(
				'sText' => '成功',
				'sSelect'=> '',
			),
			99 => array(
				'sText' => '失敗',
				'sSelect'=> '',
			),
		),
	));

	define('aTYPE2', array(
		'200'	=> '購買方案',
		'201'	=> '推廣獎勵',
		'202'	=> '提領',
		'203'	=> '提領審核失敗返款',
		// '204'	=> '',
		'205'	=> '人工充提補入',
		'206'	=> '人工充提領出',
		'210'	=> '轉帳入款',
		'211'	=> '轉帳扣款',
	));
?>
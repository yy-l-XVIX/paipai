<?php
	define('aUSERKIND', array(
		'NAME'	=> '類別名稱',
		'PRICE'	=> '價格',
		'BONUS0'	=> '推廣獎勵',
		'BONUSINFO' => '(上級代理,上上級代理..)',
		'BONUSTAX'	=> '推廣獎勵稅金',
		'BONUSTAXINFO' => '(上級代理,上上級代理..)',
		'CONTENT0'	=> '描述',
		'FREE'	=> '初次免費試用',
		'FREEDAYS'	=> '免費試用天數',
		'DAYS'	=> '天',
		'MEMBERFREETIME'=> '會員免費使用時間',
		'aTYPE0'	=> array(
			'0'		=> array(
				'sText' 	=> '關閉試用',
				'sSelect'	=> '',
				'sClass'	=> 'FontRed',
			),
			'1'		=> array(
				'sText' 	=> '開啟試用',
				'sSelect'	=> '',
				'sClass'	=> '',
			),

		),
		'aTYPE1'	=> array(
			'0'		=> array(
				'sText' 	=> '固定金額',
				'sSelect'	=> '',
			),
			'1'		=> array(
				'sText' 	=> '百分比',
				'sSelect'	=> '',
			),

		),
	));

	define('aERROR', array(
		'FREEDAYS'	=> '請輸入免費試用天數',
		'PROMOTEBONUS'	=> '請輸入推廣獎勵',
	));
?>
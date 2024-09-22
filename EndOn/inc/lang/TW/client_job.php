<?php
	define('aJOB', array(
		'NAME0'	=> '工作標題',
		'AREA'	=> '工作地區',
		'STATUS'	=> '工作狀態',
		'SELECTAREA'=> '請選擇地區',
		'WORKTIME'	=> '工作時間',
		'WORKMEN'	=> '工作人數',
		'EMPLOYE'	=> '參加人才',
		'CONTENT0'	=> '工作內容',
		'GROUPMEN'	=> '群組人才',
		'CHATHISTORY'=>'聊天紀錄',
		'aSTATUS'	=> array(
			'-1'	=> array(
				'sName0' => '請選擇狀態',
				'sSelect'=> '',
				'sClass' => '',
			),
			'0'	=> array(
				'sName0' => '應徵中',
				'sSelect'=> '',
				'sClass' => '',
			),
			'1'	=> array(
				'sName0' => '結案',
				'sSelect'=> '',
				'sClass' => 'FontRed',
			),
			'10'	=> array(
				'sName0' => '草稿',
				'sSelect'=> '',
				'sClass' => '',
			),
		),

	));

	define('aERROR', array(
		'NAME0'	=> '請輸入工作名稱',
		'CONTENT0'	=> '請輸入工作內容',
		'WORKTIME'	=> '開始時間不可大於結束時間',
		'AREA'	=> '查無此地區',
	));
?>
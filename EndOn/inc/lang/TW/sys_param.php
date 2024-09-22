<?php
	define('aPARAM', array(
		'BMAINTENANCE'	=> '網站維護 0:維護 1:營運',
		'SANDROIDURL'	=> '安卓下載連結',
		'SIOSURL'		=> 'ios下載連結',
		'NAGENTID'		=> '總代理 nUid',
		'NPACKAGEDAYS'	=> '會員方案天數',
		'NJOBGAPTIME'	=> '會員報班間隔時間(秒)',
		'NCARDLIMIT'	=> '銀行帳戶綁定次數',
		'NPHOTOLIMIT'	=> '會員上傳圖片數量',
		'NVIDEOLIMIT'	=> '會員上傳影片數量',
		'NPOSTIMAGE'	=> '單次最高上傳圖片張數',
		'NTRANSFERSETTING'=> '會員轉帳開關 0:關閉 1:開啟',
		'aRECHARGE'	=> array(
			'TITLE'		=> '充值設定',
			'SRECHARGETUNNEL'	=> '開放入款方式(1:線上入款 2:公司入款 3:點數扣款)',
			'NRECHARGEFEE'	=> '入款手續費',

		),
		'aWITHDRAWAL'	=> array(
			'TITLE'		=> '出款設定',
			'NWITHDRAWALFEE'	=> '提領手續費($)',
			'NMINWITHDRAWAL'	=> '單筆最低提領金額($)',
			'NMAXWITHDRAWAL'	=> '單筆最高提領金額($)',
			'NDAYWITHDRAWAL'	=> '每日提領次數',
		),
		'aSMS'	=> array(
			'TITLE'		=> '簡訊設定',
			'NSMSSETTING'	=> '簡訊開關 0:關閉 1:開啟',
			'SSMSACC'		=> '簡訊驗證 API帳號',
			'SSMSPWD'		=> '簡訊驗證 API密碼',
			'NSMSTIME'		=> '簡訊驗證 過期時間(秒)',
		),
	));
	define('PARAMNAME',		'參數名稱');
	define('PARAMS',			'設定值');
	define('LASTUPDATETIME',	'最後修改時間');
	define('NODATACHANGED',		'無資料變更');
	define('NEWNAME',			'新增參數名稱');
	define('NEWPARAM',		'參數值');
	define('ADDPARAMERROR',		'新增參數失敗，重複參數名稱');
?>
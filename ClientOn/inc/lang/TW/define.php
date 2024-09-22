<?php
	define('INS', 				'新增');
	define('UPT', 				'修改');
	define('DEL', 				'刪除');
	define('EDIT', 				'編輯');
	define('SUCCESS', 			'成功');
	define('FAIL', 				'失敗');
	define('FINISHED', 			'完成');
	define('PLEASESELECT', 			'請選擇');
	define('PARAMSERR', 			'參數異常');
	define('EXPORTXLS',			'匯出 Excel'); # 2019-10-24 YL
	define('UNFILLED', 			'未填寫');
	define('FORMATEERR', 			'格式錯誤');
	define('NODATA', 				'查無資料');
	define('NODATAYET', 			'尚無資料');
	define('MAINTENANCE', 			'網站維護中');
	define('TIPMSG', 				'提示訊息');
	define('UPLOADIMG',			'上傳圖片');
	define('CHANGEIMG',			'更改圖片');
	define('WORKNOTICE',			'目前為上班狀態，是否進行報班?');
	define('ACCOUNTPENDING',		'本功能尚未開放<br>此帳號尚未通過審核');
	define('YOUEXPORED',			'當前方案已過期，請重新購買');
	define('DATAPROCESSING', 		'資料處理中<br>請稍後...');
	define('KINDREMIND',	 		'<div>過程中有任何問題請各自聯繫，取消後系統不做提醒告知</div>');
	# 語言
	define('CHOSELANG', 			'選擇語言');
	# 分頁
	define('FIRSTPAGE', 			'第一頁');
	define('PREPAGE', 			'上一頁');
	define('NEXTPAGE', 			'下一頁');
	define('BEFORETEN', 			'前10頁');
	define('NEXTTEN', 			'下10頁');
	define('LASTPAGE', 			'最末頁');

	define('ACCOUNT', 			'帳號');
	define('NAME', 				'名稱');
	define('KIND', 				'分類');
	define('CREATETIME',			'建立時間');
	define('UPDATETIME',			'更新時間');
	define('STARTTIME',			'開始時間');
	define('ENDTIME',				'結束時間');
	define('OPERATE',				'操作');
	define('STATUS', 				'狀態');
	define('SUBMIT', 				'送出');
	define('CONFIRM', 			'確認');
	define('CANCEL', 				'取消');
	define('BACK', 				'返回');
	define('SEARCH', 				'查詢');
	define('CSUBMIT', 			'確認送出');
	define('CDELETE', 			'確認刪除');
	define('CBACK', 				'取消返回');

	define('INSV', 				'新增成功');
	define('UPTV', 				'修改成功');
	define('DELV', 				'刪除成功');

	define('aONLINE',	array(
		1	=> array(
			'sText'	=> '上線',
			'sSelect'	=> '',
		),
		0	=> array(
			'sText'	=> '下線',
			'sSelect'	=> '',
		),
	));

	define('aMENU',	array(
		'CHOOSE'		=> '成為雇主還是人才?',
		'TERMS'		=> '使用規約',
		'REGISTER'		=> '註冊帳號',
		'RECHARGE'		=> '線上入款',
		'POINTCHARGE'	=> '點數兌換',
		'COMPANYCHARGE'	=> '公司入款',
		'FORGOT'		=> '忘記密碼',
		'INDEX'		=> '選擇地區',
		'ONLINE'		=> '上班人才',
		'MYJOBLIST'		=> '我的工作',
		'JOBLIST'		=> '我的工作',
		'MYPOSTJOB'		=> '刊登工作',
		'JOBCOMMENTS'	=> '工作評論',
		'ADDJOB'		=> '新增工作',
		'DISCUSS'		=> '討論區',
		'POST'		=> '發文',
		'DISCUSSDETAIL'	=> '討論區留言',
		'ADDDISCUSS'	=> '新增貼文',
		'EDITINF'		=> '編輯個人檔案',
		'INF'			=> '個人資訊',
		'ID'			=> '身分證照片',
		'BANKLIST'		=> '銀行帳戶',
		'BANKADD'		=> '新增銀行帳戶',
		'SHARE'		=> '分享個人檔案',
		'POINTRECHRD'	=> '推薦點數',
		'FRIEND'		=> '好友名單',
		'FRIENDUPT'		=> '好友名單',
		'BLOCK'		=> '封鎖名單',
		'BLOCKUPT'		=> '封鎖名單',
		'MEMBERLIST'	=> '搜尋會員',
		'SAVED'		=> '收藏工作',
		'PROMOTION'		=> '推廣連結',
		'JOBRECORD'		=> '工作紀錄',
		'ACCOUNTRECORD'	=> '帳戶紀錄',
		'RECHARGELIST'	=> '購買紀錄',
		'REWARDRECORD'	=> '返點紀錄',
		'DOWNLINE'		=> '歸屬下線',
		'TRANSFER'		=> '轉帳',
		'TRANSFERCHOOSE'	=> '轉帳',
		'WITHDRAWAL'	=> '提領',
		'TRANSACTIONRECORD'=> '交易紀錄',
		'COMMENTS'		=> '查看所有評價',
		'CHANGEPASSWORD'	=> '修改密碼',
		'CHANGETRANSPASSWORD'=> '修改交易密碼',
		'SERVICE'		=> '客服',
		'SERVICELIST'	=> '客服',
		'GROUPADD'		=> '新增群組',
		'GROUPUPT'		=> '編輯群組',
		'CHATANNOUNCE'	=> '群組公告',

		// footer
		'SEARCHJOB'		=> '搜尋工作',
		'DISCUSSAREA'	=> '討論區',
		'MEMBER'		=> '會員中心',
		'POSTJOB'		=> '刊登工作',
		'MYJOB'		=> '我的工作',
		'CHAT'		=> '聊天',
	));

	define('aDAYTEXT', array(
		'YESTERDAY'	=> '昨天',
		'TODAY'	=> '今天',
		'LASTWEEK'	=> '上週',
		'THISWEEK'	=> '本週',
		'LASTMONTH'	=> '上月',
		'THISMONTH'	=> '本月',
	));

	# 各種圖片錯誤 #
	define('aIMGERROR',array(
		'ERROR'	=> '上傳失敗，請重新上傳(如重複出現此錯誤，請更換圖片)',
		'TYPE'	=> '格式不符，請重新上傳',
		'SIZE'	=> '大小不符，請重新上傳',
		'INISIZE'	=> '大小超出ini限制，請重新上傳',
		'FORMSIZE'	=> '大小超出表單限制，請重新上傳',
		'PARTIAL'	=> '只有部份被上傳，請重新上傳',
		'NOFILE'	=> '沒有被上傳，請重新上傳',
		'TMPDIR'	=> '找不到臨時資料夾，請重新上傳',
		'CANTWRITE'	=> '文件寫入失敗，請重新上傳',
		'LEASTONE'	=> '請至少上傳一張圖片',
	));

	define('aLOGNUMS', $aSystem['aLogNums']);
?>
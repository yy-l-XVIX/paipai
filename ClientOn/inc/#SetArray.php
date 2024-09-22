<?php
	#沒有Header的頁面
	#(登入,會員中心,分享連結)
	$aNoHeader = array('login_0','center_0');

	#沒有共同框架
	#(登入,選擇方案,使用規約,註冊,線上入款,點數兌換,忘記密碼,會員中心)
	$aNoCommonContainer = array('login_0','choose_0','register_0','terms_0','terms_1','recharge_0','point_charge_0','company_charge_0','forgot_0','center_0');

	#有頁碼
	#(購買紀錄,返點紀錄,歸屬下線,交易紀錄)
	$aHavePage = array('service_list_0','recharge_list_0','reward_record_0','downline_list_0','transaction_record_0');

	#有Footer的頁面(非聊天Footer)
	#(首頁,搜尋工作,會員中心,討論區,刊登工作,聊天群組)
	$aDisplayFooter = array('index_0','list_0','center_0','discuss_0','job_list_0','my_post_job_0','chat_group_0','my_job_list_0');

	#有Footer的頁面(聊天Footer)
	#(聊天,討論區留言)
	$aDisplayChatFooter = array('chat_0','discuss_detail_0');

	# 未通過審核 禁止進入頁面
	$aPendingForbid = array('list_0','recharge_0','point_charge_0','company_charge_0','my_job_0','my_job_list_0','job_list_0','my_post_job_0','job_comments_0','post_job_0','post_0','discuss_0','discuss_detail_0','transfer_0','transfer_choose_0','withdrawal_0','transaction_record_0','chat_0','chat_group_0','chat_group_upt_0','chat_group_add_0');

	# 方案過期 禁止進入頁面
	$aExpiredForbid = array(
		// boss
		'1'	=> array(
			'my_post_job_0','post_job_0','my_job_0',
		),
		// staff
		'3'	=> array(
			'my_job_0',
		),
	);

	$aHeader = array(
		'choose_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['CHOOSE'],
		),
		'terms_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/register/php/_choose_0.php']),
			'sText'	=> aMENU['TERMS'],
		),
		'terms_1'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['TERMS'],
		),
		'register_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/login/php/_login_0.php']),
			'sText'	=> aMENU['REGISTER'],
		),
		'recharge_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['RECHARGE'],
		),
		'point_charge_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['POINTCHARGE'],
		),
		'company_charge_0'=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['COMPANYCHARGE'],
		),
		'forgot_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['FORGOT'],
		),
		'index_0'		=> array(
			'sText'	=> aMENU['INDEX'],
		),
		'online_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']),
			'sText'	=> aMENU['ONLINE'],
		),
		'my_job_0'		=> array(
			'sBack'	=> '',
		),
		'my_job_list_0'	=> array(
			'sText'	=> aMENU['MYJOBLIST'],
		),
		'job_list_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']),
			'sText'	=> aMENU['JOBLIST'],
		),
		'my_post_job_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']),
			'sText'	=> aMENU['MYPOSTJOB'],
			'aButton'	=> array(
				'sClass'	=> 'headerBtn',
				'sUrl'	=> sys_web_encode($aMenuToNo['pages/job/php/_post_job_0.php']),
				'sText'	=> INS,
			),
		),
		'job_comments_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']).'&nStatus=1',
			'sText'	=> aMENU['JOBCOMMENTS'],
		),
		'post_job_0' 	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']),
			'sText'	=> aMENU['ADDJOB'],
		),
		'discuss_0'		=> array(
			'sText'	=> aMENU['DISCUSS'],
			'aButton'	=> array(
				'sClass'	=> 'headerBtn',
				'sUrl'	=> sys_web_encode($aMenuToNo['pages/discuss/php/_post_0.php']),
				'sText'	=> aMENU['POST'],
			),
		),
		'discuss_detail_0'=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_0.php']),
			'sText'	=> aMENU['DISCUSSDETAIL'],
		),
		'post_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_0.php']),
			'sText'	=> aMENU['ADDDISCUSS'],
		),
		'inf_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> (isset( $_GET['nId']) && $_GET['nId']==$aUser['nId'])?aMENU['EDITINF']:aMENU['INF'],
		),
		'setting_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['EDITINF'],
		),
		// 'id_0'		=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']),
		// 	'sText'	=> aMENU['ID'],
		// ),
		// 'bank_list_0'	=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']),
		// 	'sText'	=> aMENU['BANKLIST'],
		// 	'aButton'	=> array(
		// 		'sClass'	=> 'headerBtn',
		// 		'sUrl'	=> sys_web_encode($aMenuToNo['pages/center/php/_bank_add_0.php']),
		// 		'sText'	=> INS,
		// 	),
		// ),
		// 'bank_add_0'	=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_bank_list_0.php']),
		// 	'sText'	=> aMENU['BANKADD'],
		// ),
		'photo_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=>(isset( $_GET['nId']) && $_GET['nId']==$aUser['nId'])?aMENU['EDITINF']:aMENU['INF'],
		),
		'video_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=>(isset( $_GET['nId']) && $_GET['nId']==$aUser['nId'])?aMENU['EDITINF']:aMENU['INF'],
		),
		'share_0'		=> array(
			'sText'	=> aMENU['SHARE'],
		),
		// 'point_record_0'	=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		// 	'sText'	=> aMENU['POINTRECHRD'],
		// ),
		'friend_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['FRIEND'],
			'aButton'	=> array(
				'sClass'	=> 'headerBtn',
				'sUrl'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0_upt0.php']),
				'sText'	=> EDIT,
			),
		),
		'friend_0_upt0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']),
			'sText'	=> aMENU['FRIENDUPT'],
			'aButton'	=> array(
				'sClass'	=> 'headerBtn2',
				'sUrl'	=> sys_web_encode($aMenuToNo['pages/center/php/_friend_0.php']),
				'sText'	=> FINISHED,
			),
		),
		'block_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['BLOCK'],
			'aButton'	=> array(
				'sClass'	=> 'headerBtn',
				'sUrl'	=> sys_web_encode($aMenuToNo['pages/center/php/_block_0_upt0.php']),
				'sText'	=> EDIT,
			),
		),
		'block_0_upt0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_block_0.php']),
			'sText'	=> aMENU['BLOCKUPT'],
			'aButton'	=> array(
				'sClass'	=> 'headerBtn2',
				'sUrl'	=> sys_web_encode($aMenuToNo['pages/center/php/_block_0.php']),
				'sText'	=> FINISHED,
			),
		),
		'member_list_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['MEMBERLIST'],
		),
		'saved_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['SAVED'],
		),
		'promotion_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['PROMOTION'],
		),
		'job_record_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['JOBRECORD'],
		),
		// 'account_record_0'=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
		// 	'sText'	=> aMENU['ACCOUNTRECORD'],
		// ),
		'recharge_list_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['RECHARGELIST'],
		),
		// 'reward_record_0'	=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_account_record_0.php']),
		// 	'sText'	=> aMENU['REWARDRECORD'],
		// ),
		// 'downline_list_0'	=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_account_record_0.php']),
		// 	'sText'	=> aMENU['DOWNLINE'],
		// ),
		// 'transfer_0'	=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_account_record_0.php']),
		// 	'sText'	=> aMENU['TRANSFER'],
		// ),
		// 'transfer_choose_0'=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_transfer_0.php']),
		// 	'sText'	=> aMENU['TRANSFERCHOOSE'],
		// 	'aButton'	=> array(
		// 		'sClass'	=> 'headerBtn',
		// 		'sText'	=> FINISHED,
		// 	),
		// ),
		// 'withdrawal_0'	=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_account_record_0.php']),
		// 	'sText'	=> aMENU['WITHDRAWAL'],
		// ),
		// 'transaction_record_0'=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_account_record_0.php']),
		// 	'sText'	=> aMENU['TRANSACTIONRECORD'],
		// ),
		'comments_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['COMMENTS'],
		),
		'change_pwd_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']),
			'sText'	=> aMENU['CHANGEPASSWORD'],
		),
		// 'change_transpwd_0'=> array(
		// 	'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_setting_0.php']),
		// 	'sText'	=> aMENU['CHANGETRANSPASSWORD'],
		// ),
		'service_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_service_list_0.php']),
			'sText'	=> aMENU['SERVICE'],
		),
		'service_list_0'	=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sText'	=> aMENU['SERVICELIST'],
		),
		'chat_0'		=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']),
			'sText'	=> '',
			'aButton'	=> array(
				'sClass'	=> 'chatBtnEdit',
				'sUrl'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_upt_0.php']),
				'sText'	=> '<i class="fas fa-ellipsis-h"></i>',
			),
		),
		'chat_group_upt_0'=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']),
			'sText'	=> '',
		),
		'chat_group_announce_0'=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']),
			'sText'	=> aMENU['CHATANNOUNCE'],
		),
		'chat_group_add_0'=> array(
			'sBack'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']),
			'sText'	=> !isset($_GET['nGroupEdit'])?aMENU['GROUPADD']:aMENU['GROUPUPT'],
			'aButton'	=> array(
				'sClass'	=> 'headerBtn2',
				'sText'	=> FINISHED,
			),
		),
	);

	# Footer按鈕
	$aFooter = array(
		'index'	=> array(
			'sUrl'	=> sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']),
			'sIcon'	=> '<i class="fas fa-compass"></i>',
			'sText'	=> aMENU['SEARCHJOB'],
		),
		'discuss'	=> array(
			'sUrl'	=> sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_0.php']),
			'sIcon'	=> '<i class="fas fa-comments"></i>',
			'sText'	=> aMENU['DISCUSSAREA'],
		),
		'center'	=> array(
			'sUrl'	=> sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']),
			'sIcon'	=> '<i class="fas fa-user"></i>',
			'sText'	=> aMENU['MEMBER'],
		),
		'job'		=> array(
			'sUrl'	=> $sUserCurrentRole=='boss'?sys_web_encode($aMenuToNo['pages/job/php/_my_post_job_0.php']):sys_web_encode($aMenuToNo['pages/job/php/_my_job_list_0.php']),
			'sIcon'	=> '<i class="fas fa-briefcase"></i>',
			'sText'	=> $sUserCurrentRole=='boss'?aMENU['POSTJOB']:aMENU['MYJOB'],
		),
		'chat'	=> array(
			'sUrl'	=> sys_web_encode($aMenuToNo['pages/chat/php/_chat_group_0.php']),
			'sIcon'	=> '<i class="fas fa-comment-dots"></i>',
			'sText'	=> aMENU['CHAT'],
		),
	);

	if (true)
	{
		$aButton = array(
			'sClass' 	=> 'headerBtn',
			'sUrl'	=>  sys_web_encode($aMenuToNo['pages/center/php/_inf_0.php']),
			'sText'	=> '人才', #不可編輯時顯示
			#若為編輯時畫面,右下角按鈕呈現"刪除",並擁有"刪除"功能
		);
		$aHeader['inf_0']['aButton'] = $aButton;
		$aHeader['photo_0']['aButton'] = $aButton;
		$aHeader['video_0']['aButton'] = $aButton;
	}

	// fetch 偵測新訊息
	$aValue = array(
		'a'	=> 'CHECKMESSAGE',
		'sThisPage'=> $sPage,
	);
	$sCheckMessageFetch = sys_web_encode($aMenuToNo['pages/tool/php/_ajax_CheckMessage_0.php']).'&run_page=1&sJWT='.sys_jwt_encode($aValue);
?>
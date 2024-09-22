<?php
	ignore_user_abort(true);
	ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/error_log.txt');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/System.php');
	$aSystem['nConnect'] = 2;
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/ConnectBase.php');

	$aMsg = $_POST;
	if (!empty($aMsg['chat']))
	{
		foreach($aMsg['chat'] as $LPnIndex => $LPaDetail)
		{
			$oPdo->beginTransaction();
			$LPaDetail['sMsg'] = str_replace('<img class="EmojiImgIcon" src="images/emoji/', '[:', $LPaDetail['sMsg']);
			$LPaDetail['sMsg'] = str_replace('.png">', ':]', $LPaDetail['sMsg']);
			$aSQL_Array = array(
				'nGid'		=> $LPaDetail['nGroupId'],
				'nType0'		=> 0,
				'nUid'		=> $LPaDetail['nUid'],
				'sMsg'		=> $LPaDetail['sMsg'],
				'nCreateTime'	=> strtotime($LPaDetail['sCreateTime']),
				'sCreateTime'	=> $LPaDetail['sCreateTime'],
			);

			$sSQL = 'INSERT INTO client_chat_msg '.sql_build_array('INSERT',$aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);
			$oPdo->commit();
		}
	}
	if (!empty($aMsg['job']))
	{
		foreach($aMsg['job'] as $LPnIndex => $LPaDetail)
		{
			$oPdo->beginTransaction();
			$LPaDetail['sMsg'] = str_replace('<img class="EmojiImgIcon" src="images/emoji/', '[:', $LPaDetail['sMsg']);
			$LPaDetail['sMsg'] = str_replace('.png">', ':]', $LPaDetail['sMsg']);
			$aSQL_Array = array(
				'nJid'		=> $LPaDetail['nGroupId'],
				'nType0'		=> 0,
				'nUid'		=> $LPaDetail['nUid'],
				'nTargetUid'	=> $LPaDetail['nTargetUid'],
				'sMsg'		=> $LPaDetail['sMsg'],
				'nCreateTime'	=> strtotime($LPaDetail['sCreateTime']),
				'sCreateTime'	=> $LPaDetail['sCreateTime'],
			);

			$sSQL = 'INSERT INTO client_job_msg '.sql_build_array('INSERT',$aSQL_Array);
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result,$aSQL_Array);
			sql_query($Result);
			$oPdo->commit();
		}
	}
?>
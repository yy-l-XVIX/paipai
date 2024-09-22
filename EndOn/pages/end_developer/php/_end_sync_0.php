<?php
	#require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/end_sync.php');
	#require結束

	#給此頁運用的css，按先後順序排列陣列
	$aCss = array();
	#css 結束

	#給此頁運用的js，按先後順序排列陣列
	$aJs = array();
	#js結束

	#參數接收區
	#參數結束

	#給此頁使用的url
	$aUrl   = array(
		'sAct'	=> sys_web_encode($aMenuToNo['pages/end_developer/php/_end_sync_0_act0.php']).'&run_page=1',
		'sHtml'	=> 'pages/end_developer/'.$aSystem['sHtml'].$aSystem['nVer'].'/end_sync_0.php',
	);
	#url結束

	#參數宣告區
	$aData = array(
		'sAll' => $aUrl['sAct'],
		'sMenuKind' => $aUrl['sAct'],
		'sMenuList' => $aUrl['sAct'],
		'sLogCode' => $aUrl['sAct'],
	);

	$aJumpMsg['0']['sClicktoClose'] = 1;
	$aJumpMsg['0']['sMsg'] = CSUBMIT;
	$aJumpMsg['0']['aButton']['0']['sClass'] = 'JqReplaceO';
	$aJumpMsg['0']['aButton']['0']['sUrl'] = '';
	$aJumpMsg['0']['aButton']['0']['sText'] = SUBMIT;
	$aJumpMsg['0']['aButton']['1']['sClass'] = 'JqClose cancel';
	$aJumpMsg['0']['aButton']['1']['sText'] = CANCEL;
	#宣告結束

	#程式邏輯區
	foreach ($aData as $LPsKey => $LPsUrl)
	{
		$aValue = array(
			'a'		=> 'UPT'.$LPsKey,
			't'		=> NOWTIME
		);
		$sJWT = sys_jwt_encode($aValue);
		$aData[$LPsKey] .= '&sJWT='. $sJWT.'&nt='. NOWTIME;
	}
	#程式邏輯結束

	#輸出json
	$sData = json_encode($aData);
	$aRequire['Require'] = $aUrl['sHtml'];
	#輸出結束
?>
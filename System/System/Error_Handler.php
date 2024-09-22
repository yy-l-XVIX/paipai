<?php
	# 2019-4-26 gino (2019-08-02 PAUL Edit.)
	set_error_handler(function($nCode, $sMsg, $sFile, $nLine)
	{
		global	$sDB_Name,
				$aAdm,
				$aUser,
				$aRequire, #$aRequire['Require']
				$sUser_IP,
				$aErrorLog;

		# 宣告
		$sWho = $sWhere = '[ ]';

		$aErrMaps = array(
			E_ERROR	=> '錯誤',		#1
			E_WARNING	=> '警告',		#2
			E_PARSE	=> '語法錯誤',	#4
			E_NOTICE	=> '提醒',		#8
		);

		$sErrType = $aErrMaps[$nCode] ?? '錯誤['. $nCode .']';

		if(empty($sUser_IP)) { $sUser_IP = ''; }
		if(empty($sDB_Name)) { $sDB_Name = 'none'; }
		if(!empty($aAdm)) { $sWho = '['. $aAdm['sAccount'] .']'; } else if (!empty($aUser)) { $sWho = '['. $aUser['sAccount'] .']'; }
		if(!empty($aRequire['Require'])) { $sWhere = '['. $aRequire['Require'] .']'; }

		# [{站別}]{錯誤類型}:  {錯誤訊息} in {錯誤路徑} on line {錯誤行數}  {錯誤者}{錯誤者IP}{錯誤頁面}
		# [26-Apr-2019 15:29:51 Asia/Taipei] [M_GameCity002]提醒:  Use of undefined constant qwer - assumed 'qwer' in /home/newsysweb/WebSite/GameCity/web/inc/tool/UserClass.php on line 317  [mmg001](118.170.39.207)[pages/proxy/html/user_management.php]
		$text = sprintf('[%s]%s:  %s in %s on line %s  %s%s%s',
			$sDB_Name, $sErrType, $sMsg, $sFile, $nLine, $sWho, '('. $sUser_IP .')', $sWhere);

		error_log($text, 0);

		// $sError = file_get_contents(dirname(dirname(__FILE__)) .'/Logs/Error_Log.txt');
		// $aErrorLog = json_decode($sError, true);

		// if(!is_array($aErrorLog)) { $aErrorLog = array(); }

		// $aErrorLog[$sMsg .' in '. $sFile .' line '. $nLine] = $sDB_Name;
		// $sErr = '{'. chr(10);
		// $i = 0;

		// foreach($aErrorLog as $k => $v)
		// {
		// 	if($i > 0)
		// 	{
		// 		$sErr .= ','. chr(10) . chr(9) . sprintf('%-120s: "%s"', '"'. $k .'"', $v);
		// 	}
		// 	else
		// 	{
		// 		$sErr .= chr(9) . sprintf('%-120s: "%s"', '"'. $k .'"', $v);
		// 		$i++;
		// 	}
		// }

		// $sErr .= chr(10) .'}';

		// file_put_contents(dirname(dirname(__FILE__)) .'/Logs/Error_Log.txt', $sErr);
	});

	set_exception_handler(function($oException)
	{
		global	$sDB_Name,
				$aAdm,
				$aUser,
				$aRequire, #$aRequire['Require']
				$sUser_IP,
				$aErrorLog;

		# 宣告
		$sWho = $sWhere = '[ ]';

		$nCode = $oException->getCode();
		$sMsg = $oException->getMessage();
		$sFile = $oException->getFile();
		$nLine = $oException->getLine();
		$sTrace = $oException->getTraceAsString();

		$aErrMaps = array(
			E_ERROR	=> '錯誤',		#1
			E_WARNING	=> '警告',		#2
			E_PARSE	=> '語法錯誤',	#4
			E_NOTICE	=> '提醒',		#8
		);

		$sErrType = $aErrMaps[$nCode] ?? '錯誤['. $nCode .']';

		if(empty($sUser_IP)) { $sUser_IP = ''; }
		if(empty($sDB_Name)) { $sDB_Name = 'none'; }
		if(!empty($aAdm)) { $sWho = '['. $aAdm['sAccount'] .']'; } else if (!empty($aUser)) { $sWho = '['. $aUser['sAccount'] .']'; }
		if(!empty($aRequire['Require'])) { $sWhere = '['. $aRequire['Require'] .']'; }

		# [{站別}]{錯誤類型}:  {錯誤訊息} in {錯誤路徑} on line {錯誤行數}  {錯誤者}{錯誤者IP}{錯誤頁面} (換行) Stack trace:{Trace}
		# [26-Apr-2019 16:38:22 Asia/Taipei] [M_GameCity]例外錯誤[0]:  Call to undefined function sutst() in C:\xampp\htdocs\8591_\Admin\tmp\adm\index.php on line 11  (::1)[pages/login/html/login.php] \r\n Stack trace::#0 {main}
		$text = sprintf('[%s]%s:  %s in %s on line %s  %s%s%s '. PHP_EOL .'Stack trace:%s',
			$sDB_Name, $sErrType, $sMsg, $sFile, $nLine, $sWho, '('. $sUser_IP .')', $sWhere, $sTrace);

		error_log($text, 0);

		// $sError = file_get_contents(dirname(dirname(__FILE__)) .'/Logs/Error_Log.txt');
		// $aErrorLog = json_decode($sError, true);

		// if(!is_array($aErrorLog)) $aErrorLog = array();
		// $aErrorLog[$sMsg .' in '. $sFile .' line '. $nLine] = $sDB_Name;
		// $sErr = '{'. chr(10);
		// $i = 0;

		// foreach($aErrorLog as $k => $v)
		// {
		// 	if($i > 0)
		// 	{
		// 		$sErr .= ','. chr(10) . chr(9) . sprintf('%-120s: "%s"', '"'. $k .'"', $v);
		// 	}
		// 	else
		// 	{
		// 		$sErr .= chr(9) . sprintf('%-120s: "%s"', '"'. $k .'"', $v);
		// 		$i++;
		// 	}
		// }
		// $sErr .= chr(10) .'}';

		// file_put_contents(dirname(dirname(__FILE__)) .'/Logs/Error_Log.txt', $sErr);
	});
?>
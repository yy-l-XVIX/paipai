<?php
	$aPageRequire = explode('/',$aRequire['Require']);
	$sPage = str_replace('.php','',$aPageRequire[3]);
	// 分享連結不可去其他頁面
	if (strpos($_SERVER['HTTP_HOST'],'nez001to.com') !== false && $sPage != 'share_0')
	{
		require_once(dirname(dirname(__file__)) .'/404.html');
		exit;
	}
	// 前台不可去分享頁面
	if (strpos(WEBSITE['WEBURL'],$_SERVER['HTTP_HOST']) !== false && $sPage == 'share_0')
	{
		require_once(dirname(dirname(__file__)) .'/404.html');
		exit;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
		<meta name="format-detection" content="telephone=no" />
		<link rel="apple-touch-icon-precomposed" href="images/favicon.ico">
		<link rel="shortcut icon" href="images/favicon.ico">
		<link rel="bookmark" href="images/favicon.ico"/>
		<title><?php echo $sPage=='share_0'?'分享連結':$aSystem['sTitle'];?></title>
		<?php
		require_once('#Css.php');
		if (isset($aCss))
		{
			foreach ($aCss as $LPsUrl)
			{
				echo '<link href=\''. $LPsUrl .'?t='.VTIME.'\'  media=\'all\' rel=\'stylesheet\' type=\'text/css\' />';
			}
		}
		?>
	</head>
	<body class="<?php echo $sUserCurrentRoleClass; ?> JqBody">
		<?php
		require_once('inc/#SetArray.php');

		// 尚未通過審核進入
		if (in_array($sPage,$aPendingForbid) && isset($aUser['nStatus']) && $aUser['nStatus'] == '11')
		{
			$aJumpMsg['0']['sMsg'] = ACCOUNTPENDING; #本功能尚未開放 此帳號尚未通過審核
			$aJumpMsg['0']['sShow'] = 1;
			$aJumpMsg['0']['nClicktoClose'] = 0;
			$aJumpMsg['0']['aButton']['0']['sClass'] = '';
			$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
			$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;
			unset($aJumpMsg['0']['aButton']['1']);

			$nS = 1; // 不讓她載入頁面
		}
		$aExpiredKind = array(
			'1' => 'sExpired1',
			'3' => 'sExpired0'
		);

		if (isset($aUser) && $aUser['nIsBetweenFreeTime'] == 0) // 目前不是免費使用期間
		{
			if ($aUser[$aExpiredKind[$aUser['nKid']]] < NOWDATE && in_array($sPage,$aExpiredForbid[$aUser['nKid']]))
			{
				$aJumpMsg['0']['sMsg'] = YOUEXPORED; #本功能尚未開放 此帳號尚未通過審核
				$aJumpMsg['0']['sShow'] = 1;
				$aJumpMsg['0']['nClicktoClose'] = 0;
				$aJumpMsg['0']['aButton']['0']['sClass'] = '';
				$aJumpMsg['0']['aButton']['0']['sUrl'] = sys_web_encode($aMenuToNo['pages/center/php/_center_0.php']);
				$aJumpMsg['0']['aButton']['0']['sText'] = CONFIRM;

				$nS = 1; // 不讓她載入頁面
			}
		}

		require_once('inc/#JumpMsg.php');
		if ($nS != 1) # not run_page=1
		{
			if (!in_array($sPage,$aNoHeader))
			{
				require_once('#Header.php');
			}

			if (isset($aRequire['Require']))
			{
				if (!in_array($sPage,$aNoCommonContainer))
				{
					$sHavePageClass = '';
					if(in_array($sPage,$aHavePage))
					{
						$sHavePageClass = 'havePage';
					}
					if (in_array($sPage,$aDisplayFooter)) # 有Footer的頁面(非聊天Footer)
					{
						echo '<div class="MainBox JqMainBox '.$sHavePageClass.'">';
					}
					else if (in_array($sPage,$aDisplayChatFooter)) # 有Footer的頁面(聊天Footer)
					{
						echo '<div class="MainBox JqMainBox chatFooter '.$sHavePageClass.'">';
					}
					else
					{
						echo '<div class="MainBox JqMainBox noFooter '.$sHavePageClass.'">';
					}
				}

				require_once($aRequire['Require']);

				if (!in_array($sPage,$aNoCommonContainer))
				{
					echo '</div>';
				}
			}

			if((in_array($sPage,$aDisplayFooter)))
			{
				require_once('inc/#Footer.php');
			}
		}
		?>

		<?php
		require_once('#Js.php');
		if (isset($aJs))
		{
			foreach ($aJs as $LPsUrl)
			{
				echo '<script src=\''. $LPsUrl.'?t='.VTIME.'\' type=\'text/javascript\'></script>';
			}
		}
		?>
	</body>
</html>
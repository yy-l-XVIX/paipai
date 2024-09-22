<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $aSystem['sTitle'];?></title>
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

	<body>
		<?php
		require_once('inc/#JumpMsg.php');

		if ($nS != 1) # not run_page
		{
			$aTempPath=explode('/', $aRequire['Require']);
			if ((isset($nAdmId) && $nAdmId > 0) && $aTempPath[1] != 'login')
			{
				require_once('#Header.php');
				require_once('#Nav.php');
				echo '<div class="ContentContainer JqNavContentContainer">';
			}

			if (isset($aRequire['Require']))
			{
				require_once($aRequire['Require']);
			}

			if ((isset($nAdmId) && $nAdmId > 0))
			{
				echo '</div>';
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
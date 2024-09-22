<header>
	<div class="headerContainer">
		<?php
		if(isset($aHeader[$sPage]['sBack']))
		{
			if (isset($aJWT['sBackUrl']))
			{
				$aHeader[$sPage]['sBack'] = $aJWT['sBackUrl'];
			}
		?>
			<a href="<?php echo isset($aHeader[$sPage])?$aHeader[$sPage]['sBack']:'javascript:history.go(-1);';?>" class="headerIcon headerLeft JqHeaderLeft">
				<i class="fas fa-arrow-left"></i>
			</a>
		<?php
		}
		?>
		<?php
		if(isset($aHeader[$sPage]['sText']))
		{
			echo '<div class="headerTit JqHeaderTit">'.$aHeader[$sPage]['sText'].'</div>';
		}
		?>
		<?php
		if(isset($aHeader[$sPage]['aButton']))
		{
			$sUrl = '';
			$sText = '';
			$sClass = 'headerBtn';
			if(isset($aHeader[$sPage]['aButton']['sUrl']))
			{
				$sUrl = $aHeader[$sPage]['aButton']['sUrl'];
				if (isset($aJWT['sBackUrl']))
				{
					$sUrl = $aJWT['sBackUrl'];
				}
			}
			if(isset($aHeader[$sPage]['aButton']['sText']))
			{
				$sText = $aHeader[$sPage]['aButton']['sText'];
			}
			if(isset($aHeader[$sPage]['aButton']['sClass']))
			{
				$sClass = $aHeader[$sPage]['aButton']['sClass'];
			}
			if($sUrl == '')
			{
				echo '<div class="'.$sClass.' headerRight0 JqHeaderBtn">'.$sText.'</div>';
			}
			else
			{
				if (($sPage == 'bank_list_0' && $aUser['nStatus'] == 11) || $sPage != 'bank_list_0') // 銀行帳戶 審核通過就不給新增
				{
					echo '<a href="'.$sUrl.'" class="'.$sClass.' headerRight0 JqHeaderLink">'.$sText.'</a>';
				}
			}
		}
		?>
	</div>
</header>
<?php $aData = json_decode($sData,true);?>
<!-- 使用規約 -->
<div class="termsBox">
	<div class="termsBlock">
		<?php
		if (empty($aData))
		{
			echo '<div class="NoData">'.NODATAYET.'</div>';
		}
		else
		{
			echo $aData['sContent0'];
		}
		?>
	</div>
</div>
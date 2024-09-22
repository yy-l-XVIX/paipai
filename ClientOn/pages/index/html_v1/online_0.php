<?php $aData = json_decode($sData,true);?>
<!-- 上班人才 -->
<div class="workBox">

	<table class="workTable">
		<tbody>
			<?php
			if (empty($aData))
			{
				echo '<div class="NoData">'.NODATAYET.'</div>';
			}
			foreach ($aData as $LPnUid => $LPaData)
			{

				if($LPnI%5==1)
				{
					echo '<tr>';
				}
			?>
					<td>
						<a href="<?php echo $LPaData['sUserInfoUrl'];?>">
							<div class="workUserPic selfieBox BG" style="background-image: url('<?php echo $LPaData['sHeadImage']; ?>');">
								<?php
								if($LPaData['nKid'] == 3)
								{
									?>
									<!-- 若為下班selfieStatus + off , 若為工作中 selfieStatus + ing -->
									<div class="selfieStatus <?php echo $LPaData['sStatusClass'];?>"></div>
									<?php
								}
								?>
							</div>
							<div class="workUserName"><?php echo $LPaData['sName0'];?></div>
						</a>
					</td>
			<?php
				if($LPnI%5==0)
				{
					echo '</tr>';
				}
				$LPnI++;
			}
			if($nSumData%5 != 0)
			{
				for($nAdd=1;$nAdd<=(5-($nSumData%5));$nAdd++)
				{
					echo '<td></td>';
				}
			}
			?>
		</tbody>
	</table>
</div>

<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
?>
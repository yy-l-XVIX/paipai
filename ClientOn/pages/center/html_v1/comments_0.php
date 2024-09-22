<?php $aData = json_decode($sData,true);?>
<!-- 評價 -->
<input type="hidden" name="sBack" value="<?php echo $aUrl['sBack'];?>">
<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1;?>">
<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1&nId='.$nId;?>">
<div class="commentsBox JqAppend">
	<?php
	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}
	foreach ($aData as $LPnId => $LPaData)
	{
	?>
		<div class="commentsBlock">
			<table class="commentsTable">
				<tbody>
					<tr>
						<td>
							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="commentsImg selfieBox <?php echo $LPaData['sRoleClass'];?>">
								<img src="<?php echo $LPaData['sHeadImage'];?>">
							</div>
							<div class="commentsNick"><?php echo $LPaData['sName0'];?></div>
						</td>
						<td>
							<div class="commentsScore">
								<?php
								for ($i=0; $i < 5; $i++)
								{
									$LPsImg = 'scoreActive';
									if ($LPaData['nScore'] <= $i)
									{
										$LPsImg = 'score';
									}
									?>
									<!-- 要有顏色就用這個 -->
									<div class="commentsScoreImg">
										<img src="images/<?php echo $LPsImg;?>.png" alt="">
									</div>
									<?php
								}
								?>
							</div>
							<div class="commentsTxt"><?php echo $LPaData['sContent0'];?></div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="commentsDate"><?php echo $LPaData['sCreateTime'];?></div>
		</div>
	<?php
	}
	?>

	<?php
		#卷軸到底後,Loading時出現, class + active
		require_once('inc/#Loading.php');
	?>
</div>
<div class="DisplayNone JqCopy">
	<div class=" commentsBlock">
		<table class="commentsTable">
			<tbody>
				<tr>
					<td>
						<!-- 若此人身份為雇主,selfieBox + boss -->
						<div class="commentsImg selfieBox [[::sRoleClass::]]">
							[[::sHeadImage::]]
						</div>
						<div class="commentsNick">[[::sName0::]]</div>
					</td>
					<td>
						<div class="commentsScore">
							[[::sScore::]]
						</div>
						<div class="commentsTxt">[[::sContent0::]]</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="commentsDate">[[::sCreateTime::]]</div>
	</div>
</div>

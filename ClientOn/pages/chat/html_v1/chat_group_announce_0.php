<?php $aData = json_decode($sData,true);?>
<!-- 新增群組 -->
<div class="discussBox">
	<form id="JqPostForm" method="POST" action="<?php echo $aData['sActUrl'];?>" class="MarginBottom20">
		<input type="hidden" class="JqBackUrl" value="<?php echo $aUrl['sBack'];?>">
		<input type="hidden" name="nId" value="<?php echo $nId;?>">
		<div class="discussBlock JqBlock" data-id="77">
			<div class="discussArticleBox">
				<table class="discussArticleTable">
					<tbody>
						<tr>
							<td class="discussArticlePic">
								<!-- 若此人身份為雇主,selfieBox + boss -->
								<div class="selfieBox  BG" style="background-image: url('<?php echo $aMemberData['sImgUrl'];?>');"></div>
							</td>
							<td class="discussArticleData">
								<div class="discussArticleTop">
									<div class="discussArticleName"><?php echo $aMemberData['sName0'];?></div>
									<div class="discussArticleContent"><?php echo $aData['sUpdateTime'];?></div>
								</div>
								<?php
								if ($nUid == $aUser['nId'])
								{
									?>

									<div class="Textarea">
										<textarea name="sContent0"><?php echo strip_tags($aData['sContent0']);?></textarea>
									</div>

									<?php
								}
								else
								{
									?>
									<div class="discussArticleContent"><?php echo $aData['sContent0'];?></div>
									<?php
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		if ($nUid == $aUser['nId'])
		{
			?>
			<div class="TextAlignCenter">
				<input type="submit" class="BtnAny JqFinish" value="完成">
			</div>
			<?php
		}
		?>
	</form>
</div>
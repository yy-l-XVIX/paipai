<!-- 封鎖名單-編輯 -->
<div class="friendUptBox JqAppend">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sFetch'];?>">
	<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
	<input type="hidden" name="nPageNo" value="<?php echo ($aPage['nNowNo']+1);?>">
	<?php
	foreach ($aData as $LPnId => $LPaData)
	{
		?>
		<div class="friendUptList">
			<table class="friendUptTable">
				<tbody>
					<tr>
						<td class="friendUptTdIcon JqAct " data-jqid="<?php echo $LPnId;?>">
							<i class="fas fa-times-circle"></i>
						</td>
						<td class="friendUptTdPic">
							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="selfieBox <?php echo $aMemberData[$LPaData['nBUid']]['sRoleClass'];?> BG" style="background-image: url('<?php echo $aMemberData[$LPaData['nBUid']]['sHeadImage'];?>');">
								<?php echo $aMemberData[$LPaData['nBUid']]['sStatusClass'];?>
							</div>
						</td>
						<td class="friendUptTdName">
							<div><?php echo $aMemberData[$LPaData['nBUid']]['sName0'];?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
	?>


	<div class="DisplayNone JqCopy">
		<div class="friendUptList">
			<table class="friendUptTable">
				<tbody>
					<tr>
						<td class="friendUptTdIcon JqAct " data-jqid="[[::nId::]]">
							<i class="fas fa-times-circle"></i>
						</td>
						<td class="friendUptTdPic">
							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="selfieBox [[::sRoleClass::]] BG" style="background-image: url('[[::sHeadImage::]]');">
								[[::sStatusClass::]]
							</div>
						</td>
						<td class="friendUptTdName">
							<div> [[::sName0::]]</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
?>
<?php $aData = json_decode($sData,true);?>
<!-- 封鎖名單 -->
<div class="friendBox JqAppend">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sFetch'];?>">
	<input type="hidden" name="nPageNo" value="<?php echo ($aPage['nNowNo']+1);?>">
	<?php
	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}
	foreach ($aData as $LPnId => $LPaData)
	{
		?>
		<a class="friendList" href="<?php echo $aMemberData[$LPaData['nBUid']]['sUserInfoUrl'];?>">
			<table class="friendTable">
				<tbody>
					<tr>
						<td class="friendTdPic">
							<div class="selfieBox <?php echo $aMemberData[$LPaData['nBUid']]['sRoleClass'];?> BG" style="background-image: url('<?php echo $aMemberData[$LPaData['nBUid']]['sHeadImage'];?>');">
								<?php echo $aMemberData[$LPaData['nBUid']]['sStatusClass'];?>
							</div>

						</td>
						<td class="friendTdName">
							<div><?php echo $aMemberData[$LPaData['nBUid']]['sName0'];?></div>
						</td>
						<td class="friendTdDecro">
							<i class="fas fa-chevron-right"></i>
						</td>
					</tr>
				</tbody>
			</table>
		</a>
		<?php
	}
	?>

	<div class="DisplayNone JqCopy">
		<a class="friendList" href="[[::sUserInfoUrl::]]">
			<table class="friendTable">
				<tbody>
					<tr>
						<td class="friendTdPic">
							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="selfieBox [[::sRoleClass::]] BG" style="background-image: url('[[::sHeadImage::]]');">
								[[::sStatusClass::]]
							</div>
						</td>
						<td class="friendTdName">
							<div>[[::sName0::]]</div>
						</td>
						<td class="friendTdDecro">
							<i class="fas fa-chevron-right"></i>
						</td>
					</tr>
				</tbody>
			</table>
		</a>
	</div>
</div>

<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
?>
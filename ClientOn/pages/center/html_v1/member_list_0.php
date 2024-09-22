<!-- 搜尋會員 -->

<header>
	<form action="<?php echo $aUrl['sPage'];?>" method="post">
		<div class="headerContainer TextAlignLeft">
			<a href="<?php echo $aUrl['sBack'];?>" class="headerIcon headerLeft">
				<i class="fas fa-arrow-left"></i>
			</a>
			<div class="headerFindIpt Ipt">
				<input type="text"  name="sSearch" value="<?php echo $sSearch;?>" placeholder="<?php echo aLIST['PLACEHOLDER'];?>">
			</div>
			<div class="headerFindBtn headerRight0">
				<input type="submit">
				<i class="fas fa-search"></i>
			</div>
		</div>
	</form>
</header>

<div class="findBox JqAppend">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sFetch'];?>">
	<input type="hidden" name="sChat" value="<?php echo $aUrl['sChat'];?>">
	<input type="hidden" name="nPageNo" value="<?php echo ($aPage['nNowNo']+1);?>">
	<?php
	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}
	foreach($aData as $LPnId => $LPaData)
	{
	?>
		<a class="findList" href="<?php echo $LPaData['sUserInfoUrl'];?>">
			<table class="findTable">
				<tbody>
					<tr>
						<td class="findTdPic">
							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="selfieBox <?php echo $LPaData['sRoleClass'];?> BG" style="background-image: url('<?php echo $LPaData['sHeadImage'];?>');">
								<?php echo $LPaData['sStatusClass'];?>
							</div>
						</td>
						<td class="findTdName">
							<div><?php echo $LPaData['sName0'];?></div>
						</td>
						<td class="findTdDecro">
							<a href="javascript:void(0)" data-id="<?php echo $LPnId;?>" class="JqGoChat">
								<i class="fas fa-chevron-right"></i>
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		</a>
	<?php
	}
	?>
</div>

<div class="DisplayNone JqCopy">
	<a class="findList" href="[[::sUserInfoUrl::]]">
		<table class="findTable">
			<tbody>
				<tr>
					<td class="findTdPic">

						<!-- 若此人身份為雇主,selfieBox + boss -->
						<div class="selfieBox [[::sRoleClass::]] BG" style="background-image: url('[[::sHeadImage::]]');">
							[[::sStatusClass::]]
						</div>
					</td>
					<td class="findTdName">
						<div>[[::sName0::]]</div>
					</td>
					<td class="findTdDecro">
						<a href="javascript:void(0)" data-id="[[::nId::]]" class="JqGoChat">
							<i class="fas fa-chevron-right"></i>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</a>
</div>

<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
?>
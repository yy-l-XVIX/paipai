<?php $aData = json_decode($sData,true);?>
<!-- 好友名單 -->
<div class="rechargeRecordBox">
	<div class="rechargeRecordSearchBox">
		<form action="<?php echo $aUrl['sPage'];?>" method="POST">
			<table class="FormSearchTable">
				<tbody>
					<tr>
						<td style="width:100%;">
							<div class="Ipt">
								<input type="text" name="sSearch" value="<?php echo $sSearch;?>" placeholder="<?php echo '請輸入';?>">
							</div>
						</td>
						<td>
							<div class="FormSearchBtn">
								<input type="submit">
								<div class="FormSearchBtnTxt"><i class="fas fa-search"></i></div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<div class="friendBox">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sFetch'];?>">
	<input type="hidden" name="sChat" value="<?php echo $aUrl['sChat'];?>">
	<input type="hidden" name="nPageNo" value="<?php echo ($aPage['nNowNo']+1);?>">
	<?php
	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}
	foreach($aData as $LPnType => $LPaFriend)
	{
		echo '<div class="friendKindBox">';
		if($LPnType == 0)
		{
			echo '<div class="friendKindTitle">'.aFRIEND['CONFIRM'].'</div>';
		}
		else
		{
			echo '<div class="friendKindTitle">'.aFRIEND['MYFRIEND'].'</div>';
		}

		echo '<div class="friendListBox JqAppend'.$LPnType.'">';
		foreach($LPaFriend as $LPnId => $LPaData)
		{
			?>
			<div class="friendList">
				<table class="friendTable">
					<tbody>
						<tr>
							<td class="friendTdPic">
								<!-- 若此人身份為雇主,selfieBox + boss -->

								<a class="selfieBox <?php echo $aMemberData[$LPaData['nFUid']]['sRoleClass'];?> BG" href="<?php echo $aMemberData[$LPaData['nFUid']]['sUserInfoUrl'];?>" style="background-image: url('<?php echo $aMemberData[$LPaData['nFUid']]['sHeadImage'];?>');">
									<?php echo $aMemberData[$LPaData['nFUid']]['sStatusClass'];?>
								</a>
							</td>
							<td class="friendTdName">
								<div class="JqNameText DisplayBlockNone active">
									<?php echo $aMemberData[$LPaData['nFUid']]['sName0'];?>
								</div>
								<?php
								if ($LPnType == 1)
								{
									?>
									<div class="JqNameUpt DisplayInlineBlockNone ">
										<div class="Ipt">
											<input class="JqNameIpt" type="text" name="sName0" value="<?php echo $aMemberData[$LPaData['nFUid']]['sName0'];?>" data-act="<?php echo $LPaData['sUptName'];?>">
										</div>
									</div>
									<?php
								}
								?>
							</td>
							<td class="friendTdBtnBox">
								<?php
								if($LPnType == 0)
								{
									// 確認好友
									?>
									<div class="friendTdBtn Bg_17BB0C JqAct" data-jqurl="<?php echo $aUrl['sAgree'].'&nId='.$LPnId;?>">
										<i class="fas fa-check"></i>
									</div>
									<div class="friendTdBtn Bg_EB4545 JqAct" data-jqurl="<?php echo $aUrl['sDeny'].'&nId='.$LPnId;?>">
										<i class="fas fa-times"></i>
									</div>
									<?php
								}
								else
								{
									?>
									 <a href="javascript:void(0)" data-id="<?php echo $LPaData['nFUid'];?>" class="friendTdBtn Bg_EA76AC JqGoChat">
										<i class="fas fa-comment-dots"></i>
									</a>
									<?php
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php
		}
		?>
		<div class="DisplayNone JqCopy<?php echo $LPnType;?>">
				<div class="friendList">
					<table class="friendTable">
						<tbody>
							<tr>
								<td class="friendTdPic">

									<!-- 若此人身份為雇主,selfieBox + boss -->
									<a class="selfieBox [[::sRoleClass::]] BG" href="<?php echo $aUrl['sInf'].'&nId=[[::nFUid::]]';?>" style="background-image: url('[[::sHeadImage::]]');">
										[[::sStatusClass::]]
									</a>
								</td>
								<td class="friendTdName">
									<div>[[::sName0::]]</div>
								</td>
								<td class="friendTdBtnBox">
									<?php
									if($LPnType == 0)
									{
										?>
										<div class="friendTdBtn Bg_17BB0C JqAct" data-jqurl="<?php echo $aUrl['sAgree'].'&nId=[[::nId::]]';?>">
											<i class="fas fa-check"></i>
										</div>
										<div class="friendTdBtn Bg_EB4545 JqAct" data-jqurl="<?php echo $aUrl['sDeny'].'&nId=[[::nId::]]';?>">
											<i class="fas fa-times"></i>
										</div>
										<?php
									}
									else
									{
										?>
										<a href="javascript:void(0)" data-id="[[::nFUid::]]" class="friendTdBtn Bg_EA76AC JqGoChat">
											<i class="fas fa-comment-dots"></i>
										</a>
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
		echo '</div></div>';
	}
	?>
</div>

<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
?>
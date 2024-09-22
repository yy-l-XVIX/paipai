<?php $aData = json_decode($sData,true);?>
<!-- 收藏工作 -->
<div class="findListBox JqAppend">
	<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1';?>">
	<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
	<input type="hidden" name="sActJWT" value="<?php echo $sActJWT;?>">
	<input type="hidden" name="sDelJWT" value="<?php echo $sDelJWT;?>">
	<input type="hidden" name="sJoinJWT" value="<?php echo $sJoinJWT;?>">
	<input type="hidden" name="sOutJWT" value="<?php echo $sOutJWT;?>">
	<?php

	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}
	foreach($aData as $LPnId => $LPaData)
	{
		?>
		<div class="JobListBlock">
			<table class="JobListInf">
				<tbody>
					<tr>
						<td class="JobListInfPic">
							<a class="selfieBox boss BG" href="<?php echo $LPaData['sUserInfoUrl'];?>" style="background-image: url('<?php echo isset($aHeadImage[$LPaData['nUid']]) ? $aHeadImage[$LPaData['nUid']]['sHeadImage'] : DEFAULTHEADIMG;?>');"></a>
						</td>
						<td class="JobListInfTit"><?php echo $LPaData['sName0'];?></td>
						<?php
						if($sUserCurrentRole == 'staff')
						{
							?>
							<td class="JobListInfLike">
								<div class="LikeIconImg JqFavorite" data-jid="<?php echo $LPnId;?>" data-favorite="<?php echo $LPaData['nFavorite'];?>">
									<?php
									if($LPaData['nFavorite'] == 1)
									{
										#已收藏工作時呈現
										echo '<img src="images/likeActive.png" alt="">';
									}
									else
									{
										#尚未收藏工作時呈現
										echo '<img src="images/like.png" alt="">';
									}
									?>
								</div>
							</td>
							<?php
						}
						?>
						<td class="JobListInfBtnTd">
							<?php
							if($LPaData['nStatus'] == 1)
							{
								?>
								<div class="JobListInfBtn active"><?php echo aSAVED['CLOSED'];?></div>
								<?php
							}
							else
							{
								if($sUserCurrentRole == 'staff')
								{
									#人才時呈現
									if($LPaData['nJoin'] == 1)
									{
										?>
										<!-- 已應徵工作時呈現 -->
										<div class="JobListInfBtn active JqOut" data-jid="<?php echo $LPnId;?>"><?php echo aSAVED['OUT'];?></div>
										<?php
									}
									else
									{

										if ($aUser['nStatus'] == 1)
										{
											?>
											<!-- 尚未應徵工作時呈現 -->
											<div class="JobListInfBtn JqListStupidOut" data-showctrl="1" data-jid="<?php echo $LPnId;?>">
												<?php echo aSAVED['JOIN'];?>
											</div>
											<?php
										}
										else
										{
											?>
											<!-- 尚未應徵工作時呈現 -->
											<div class="JobListInfBtn JqJoin" data-jid="<?php echo $LPnId;?>"><?php echo aSAVED['JOIN'];?></div>
											<?php
										}
									}
								}
								else
								{
									#雇主時呈現
									?>
									<a class="JobListInfBtn detail" href="<?php echo $LPaData['sDetailUrl'];?>"><?php echo aSAVED['DETAIL'];?></a>
									<?php
								}
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="JobListContent">
				<div><?php echo aSAVED['WORKTIME'];?></div>
				<div>
					<span><?php echo $LPaData['sStartTime'];?></span>
					<span>~</span>
					<span><?php echo $LPaData['sEndTime'];?></span>
				</div>
			</div>
			<div class="JobListContent">
				<div><?php echo aSAVED['WORKPLACE'];?></div>
				<div><?php echo $LPaData['sArea'];?></div>
			</div>
			<div class="JobListContent"><?php echo $LPaData['sContent0'];?></div>
			<div class="JobListDate"><?php echo $LPaData['sCreateTime'];?></div>
		</div>
		<?php
	}
	?>
</div>
<div class="DisplayNone JqCopy">
	<div class="JobListBlock">
		<table class="JobListInf">
			<tbody>
				<tr>
					<td class="JobListInfPic">
						<a class="selfieBox boss BG" href="[[::sUserInfoUrl::]]" style="background-image: url('[[::sHeadImage::]]');"></a>
					</td>
					<td class="JobListInfTit">[[::sName0::]]</td>
					<?php
					if($sUserCurrentRole == 'staff')
					{
					?>
						<td class="JobListInfLike">
							<div class="LikeIconImg JqFavorite" data-jid="[[::nId::]]" data-favorite="[[::nFavorite::]]">
								[[::sFavoriteImage::]]
							</div>
						</td>
					<?php
					}
					?>
					<td class="JobListInfBtnTd">
						<!-- # 已應徵/結案active 工作詳情detail -->
						[[::sActBtn::]]
					</td>
				</tr>
			</tbody>
		</table>
		<div class="JobListContent">
			<div><?php echo aSAVED['WORKTIME'];?></div>
			<div>
				<span>[[::sStartTime::]]</span>
				<span>~</span>
				<span>[[::sEndTime::]]</span>
			</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aSAVED['WORKPLACE'];?></div>
			<div>[[::sArea::]]</div>
		</div>
		<div class="JobListContent">[[::sContent0::]]</div>
		<div class="JobListDate">[[::sCreateTime::]]</div>
	</div>
</div>
<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
	require_once('inc/#Top.php');
?>
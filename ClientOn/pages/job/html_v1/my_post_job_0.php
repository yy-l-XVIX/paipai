<?php $aData = json_decode($sData,true);?>
<!-- 刊登工作 -->
<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1&nStatus='.$nStatus;?>">
<div class="mypostJob">
	<div class="mypostJobMenuBox">
		<!-- 當前分類class + active -->
		<?php
		foreach ($aStatus as $LPnStatus => $LPaStatus)
		{
			?>
			<a class="mypostJobMenuBtn <?php echo $LPaStatus['sSelect'];?>" href="<?php echo $aUrl['sPage'].'&nStatus='.$LPnStatus;?>">
				<?php echo $LPaStatus['sText'];?>
			</a>
			<?php
		}
		?>
	</div>
	<div class="mypostJobBox JqAppend">
		<?php
		if (empty($aData))
		{
			echo '<div class="NoData">'.NODATAYET.'</div>';
		}
		foreach ($aData as $LPnId => $LPaJob)
		{
			?>
			<div class="JobListBlock">
				<table class="JobListInf">
					<tbody>
						<tr>
							<td class="JobListInfPic">
								<a class="selfieBox boss BG" href="<?php echo $aUrl['sInf'].'&nId='.$aUser['nId'];?>" style="background-image: url('<?php echo $LPaJob['sHeadImage'];?>');"></a>
							</td>
							<td class="JobListInfTit"><?php echo $LPaJob['sName0'];?></td>
							<td class="JobListInfBtnTd">
								<?php
								if($LPaJob['nStatus'] < 10)
								{
									#單種功能
									?>
									<a class="JobListInfBtn detail" href="<?php echo $LPaJob['sDetail'];?>"><?php echo aMYPOSTJOB['DETAIL'];?></a>
									<div class="JobListInfBtnNotify DisplayBlockNone JqCheckGroupMessage" data-gid="<?php echo $LPnId;?>">N</div>
									<?php
								}
								/* 2021-02-24 要求改成按鈕分開顯示
								else
								{
									#多種功能
									?>
									<div class="JobListInfBtnMoreBox JqMoreBox">
										<div class="JobListInfBtnMore JqMoreBtn">
											<i class="fas fa-ellipsis-h"></i>
										</div>
										<div class="JobListInfBtnMoreInner DisplayBlockNone JqMoreBlock">
											<a href="<?php echo $LPaJob['sDetail'];?>" class="JobListInfBtnMoreInnerAhref"><?php echo aMYPOSTJOB['DETAIL'];?></a>
											<a href="<?php echo $LPaJob['sPostAgain'];?>" class="JobListInfBtnMoreInnerAhref"><?php echo aMYPOSTJOB['REPOST'];?></a>
										</div>
									</div>
									<?php
								}
								*/
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="JobListContent">
					<div><?php echo aMYPOSTJOB['WORKTIME'];?></div>
					<div>
						<span><?php echo $LPaJob['sStartTime'];?></span>
						<span>~</span>
						<span><?php echo $LPaJob['sEndTime'];?></span>
					</div>
				</div>
				<div class="JobListContent">
					<div><?php echo aMYPOSTJOB['WORKPLACE'];?></div>
					<div><?php echo $LPaJob['sArea'];?></div>
				</div>
				<div class="JobListContent">
					<div><?php echo aMYPOSTJOB['WORKTYPE'];?></div>
					<div>
						<?php
						foreach ($LPaJob['aType0'] as $LPsType0)
						{
							$LPnType0 = (int)$LPsType0;
							if (!isset($aType[$LPnType0]))
							{
								continue;
							}
							?>
							<span><?php echo $aType[$LPnType0]['sName0'];?></span>
							<?php
						}
						?>
					</div>
				</div>
				<div class="JobListContent">
					<?php echo $LPaJob['sContent0'];?>
					<div class="JobListContentImg">
						<?php
						if ($LPaJob['sImgUrl'] != '')
						{
							?>
							<img src="<?php echo $LPaJob['sImgUrl'];?>" alt="">
							<?php
						}
						?>
					</div>
				</div>
				<div class="JobListDate"><?php echo $LPaJob['sCreateTime'];?></div>

				<div class="JobListBtnViewBox">
					<a class="JobListBtnView" href="<?php echo $LPaJob['sPostAgain'];?>">
						<div class=""><?php echo ($LPaJob['nStatus'] == 10)? aMYPOSTJOB['EDITDRAFT']:aMYPOSTJOB['REPOST'];?></div>
					</a>
					<?php
					if($LPaJob['nStatus'] == 1)
					{
						#狀態為"已完成"時才顯示
						?>
						<a class="JobListBtnView" href="<?php echo $aUrl['sJobComments'].'&nId='.$LPaJob['nId']; ?>">
							<div class=""><?php echo aMYPOSTJOB['COMMENT'];?></div>
						</a>
						<?php
					}
					?>
				</div>
				<?php
				/*
					<a href="<?php echo $LPaJob['sPostAgain'];?>" class="JobListBtnViewBox">
						<div class="JobListBtnView"><?php echo aMYPOSTJOB['REPOST'];?></div>
					</a>
					<?php
					if($LPaJob['nStatus'] == 1)
					{
						#狀態為"已完成"時才顯示
						?>
						<a class="JobListBtnViewBox" href="<?php echo $aUrl['sJobComments'].'&nId='.$LPaJob['nId']; ?>">
							<div class="JobListBtnView"><?php echo aMYPOSTJOB['COMMENT'];?></div>
						</a>
						<?php
					}
					?>
				*/
				?>
			</div>
			<?php
		}
		?>
	</div>
</div>
<div class="DisplayNone JqCopy">
	<div class="JobListBlock">
		<table class="JobListInf">
			<tbody>
				<tr>
					<td class="JobListInfPic">
						<a class="selfieBox boss BG" href="<?php echo $aUrl['sInf'].'&nId='.$aUser['nId'];?>" style="background-image: url('<?php echo $sHeadImage;?>');"></a>
					</td>
					<td class="JobListInfTit">[[::sName0::]]</td>
					<td class="JobListInfBtnTd">
						<?php
						if($nStatus < 10)
						{
							#單種功能
							?>
							<a class="JobListInfBtn detail" href="[[::sDetail::]]"><?php echo aMYPOSTJOB['DETAIL'];?></a>
							<div class="JobListInfBtnNotify DisplayBlockNone JqCheckGroupMessage" data-gid="[[::nId::]]">N</div>
							<?php
						}
						?>
						<?php
						/*
						<div class="JobListInfBtnMoreBox JqMoreBox">
							<div class="JobListInfBtnMore JqMoreBtn">
								<i class="fas fa-ellipsis-h"></i>
							</div>
							<div class="JobListInfBtnMoreInner DisplayBlockNone JqMoreBlock">
								<a href="[[::sDetail::]]" class="JobListInfBtnMoreInnerAhref"><?php echo aMYPOSTJOB['DETAIL'];?></a>
								<a href="[[::sPostAgain::]]" class="JobListInfBtnMoreInnerAhref"><?php echo aMYPOSTJOB['REPOST'];?></a>
							</div>
						</div>
						*/
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="JobListContent">
			<div><?php echo aMYPOSTJOB['WORKTIME'];?></div>
			<div>
				<span>[[::sStartTime::]]</span>
				<span>~</span>
				<span>[[::sEndTime::]]</span>
			</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aMYPOSTJOB['WORKPLACE'];?></div>
			<div>[[::sArea::]]</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aMYPOSTJOB['WORKTYPE'];?></div>
			<div>[[::sTypeHtml::]]</div>
		</div>
		<div class="JobListContent">
			[[::sContent0::]]
			<div class="JobListContentImg">
				[[::sImgUrl::]]
			</div>
		</div>
		<div class="JobListDate">[[::sCreateTime::]]</div>
		<div class="JobListBtnViewBox">
			<a class="JobListBtnView" href="[[::sPostAgain::]]">
				<div class=""><?php echo ($nStatus == 10)? aMYPOSTJOB['EDITDRAFT']:aMYPOSTJOB['REPOST'];?></div>
			</a>
			<?php
			if($nStatus == 1)
			{
				#狀態為"已完成"時才顯示
				?>
				<a class="JobListBtnView" href="<?php echo $aUrl['sJobComments'].'&nId=[[::nId::]]'; ?>">
					<div class=""><?php echo aMYPOSTJOB['COMMENT'];?></div>
				</a>
				<?php
			}
			?>
		</div>
		<?php
		/*
		if ($nStatus == 1) // 已完成才可以看評論
		{
			?>
			<a class="JobListBtnViewBox JqCommentLink" href="<?php echo $aUrl['sJobComments'].'&nId=[[::nId::]]';?>">
				<div class="JobListBtnView"><?php echo aMYPOSTJOB['COMMENT'];?></div>
			</a>
			<?php
		}
		*/
		?>
	</div>
</div>
<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');

	require_once('inc/#Top.php');
?>

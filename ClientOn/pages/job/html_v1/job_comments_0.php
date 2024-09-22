<?php $aData = json_decode($sData,true);?>
<!-- 查看工作評論 -->
<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1&nId='.$nId;?>">
<div class="jobComments">
	<div class="mypostJobBox">
		<div class="JobListBlock">
			<table class="JobListInf">
				<tbody>
					<tr>
						<td class="JobListInfPic">
							<a class="selfieBox boss BG" href="<?php echo $aUrl['sInf'].'&nId='.$aUser['nId'];?>" style="background-image: url('<?php echo $aData['sHeadImage'];?>');"></a>
						</td>
						<td class="JobListInfTit"><?php echo $aData['sName0'];?></td>
						<td class="JobListInfBtnTd">
							<?php
							if(false)
							{
								#單種功能
								?>
								<a class="JobListInfBtn detail" href="?sFolder=job&sPage=myjob"><?php echo aCOMMENTS['DETAIL']?></a>
								<div class="JobListInfBtnNotify">19</div>
								<?php
							}
							else
							{
								#多種功能
								?>
								<div class="JobListInfBtnMoreBox JqMoreBox">
									<div class="JobListInfBtnMore JqMoreBtn">
										<i class="fas fa-ellipsis-h"></i>
									</div>
									<div class="JobListInfBtnMoreInner DisplayBlockNone JqMoreBlock">
										<a href="<?php echo $aData['sDetail'];?>" class="JobListInfBtnMoreInnerAhref"><?php echo aCOMMENTS['DETAIL']?></a>
										<a href="<?php echo $aData['sPostAgain'];?>" class="JobListInfBtnMoreInnerAhref"><?php echo aCOMMENTS['REPOST'];?></a>
									</div>
								</div>
								<?php
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="JobListContent">
				<div><?php echo aCOMMENTS['WORKTIME']?></div>
				<div>
					<span><?php echo $aData['sStartTime'];?></span>
					<span>~</span>
					<span><?php echo $aData['sEndTime'];?></span>
				</div>
			</div>
			<div class="JobListContent">
				<div><?php echo aCOMMENTS['WORKPLACE']?></div>
				<div><?php echo $aData['sArea'];?></div>
			</div>
			<div class="JobListContent">
				<?php echo $aData['sContent0'];?>
				<div class="JobListContentImg">
					<?php
					if ($aData['sImgUrl'] != '')
					{
						?>
						<img src="<?php echo $aData['sImgUrl'];?>" alt="">
						<?php
					}
					?>
				</div>
			</div>
			<div class="JobListDate"><?php echo $aData['sCreateTime'];?></div>
			<div class="JobListCommentsBox <?php echo (!empty($aData['aScore']))?'active':''?> JqAppend">
				<?php
				foreach ($aData['aScore'] as $LPnSid => $LPaScore)
				{
					?>
					<div class="JobListCommentsList">
						<table class="JobListCommentsTable">
							<tbody>
								<tr>
									<td class="JobListCommentsTdPic">

										<a href="<?php echo $aUrl['sInf'].'&nId='.$LPaScore['nUid'];?>" class="JobListCommentsTdInf">
											<div class="selfieBox BG" style="background-image: url('<?php echo $aMemberData[$LPaScore['nUid']]['sHeadImage'];?>');"></div>
											<div class="JobListCommentsTdName"><?php echo $aMemberData[$LPaScore['nUid']]['sName0'];?></div>
										</a>
									</td>
									<td class="JobListCommentsTdData">
										<div class="JobListCommentsScore">
											<?php
											for ($i=1; $i <=5 ; $i++)
											{
												$LPsPic = 'score.png';
												if ($LPaScore['nScore'] >= $i)
												{
													$LPsPic = 'scoreActive.png';
												}
												?>
												<div class="JobListCommentsScoreCell">
													<img src="images/<?php echo $LPsPic;?>" alt="">
												</div>
												<?php
											}
											?>
										</div>
										<div class="JobListCommentsContent"><?php echo $LPaScore['sContent0'];?></div>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="JobListCommentsDate"><?php echo $LPaScore['sCreateTime'];?></div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<div class="DisplayNone JqCopy">
	<div class="JobListCommentsList">
		<table class="JobListCommentsTable">
			<tbody>
				<tr>
					<td class="JobListCommentsTdPic">
						<a href="<?php echo $aUrl['sInf'].'&nId=[[::nUid::]]';?>" class="JobListCommentsTdInf">
							<div class="selfieBox BG" style="background-image: url('[[::sHeadImage::]]');"></div>
							<div class="JobListCommentsTdName">[[::sName0::]]</div>
						</a>
					</td>
					<td class="JobListCommentsTdData">
						<div class="JobListCommentsScore">
							[[::sScore::]]
						</div>
						<div class="JobListCommentsContent">[[::sContent0::]]</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="JobListCommentsDate">[[::sCreateTime::]]</div>
	</div>
</div>
<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
	require_once('inc/#Top.php');
?>
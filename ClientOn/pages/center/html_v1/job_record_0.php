<?php $aData = json_decode($sData,true);?>
<!-- 工作紀錄 -->
<div class="jodRecordBox JqAppend">
	<input type="hidden" name="sSaveAct" value="<?php echo $aUrl['sSaveAct'];?>">
	<input type="hidden" name="sActJWT" value="<?php echo $sActJWT;?>">
	<input type="hidden" name="sDelJWT" value="<?php echo $sDelJWT;?>">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sFetch'];?>">
	<input type="hidden" name="nPageNo" value="<?php echo ($aPage['nNowNo']+1);?>">
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
							<a class="selfieBox boss BG" href="<?php echo $aMemberData[$LPaData['nUid']]['sInfUrl'];?>" style="background-image: url('<?php echo $aMemberData[$LPaData['nUid']]['sHeadImage'];?>');"></a>
						</td>
						<td class="JobListInfTit"><?php echo $LPaData['sName0'];?></td>
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
						<td class="JobListInfBtnTd">
							<a class="JobListInfBtn detail" href="<?php echo $aUrl['sMyjob'].'&nId='.$LPnId;?>"><?php echo aRECORD['DETAIL'];?></a>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="JobListContent">
				<div><?php echo aRECORD['WORKTIME'];?></div>
				<div>
					<span><?php echo $LPaData['sStartTime'];?></span>
					<span>~</span>
					<span><?php echo $LPaData['sEndTime'];?></span>
				</div>
			</div>
			<div class="JobListContent">
				<div><?php echo aRECORD['WORKPLACE'];?></div>
				<div><?php echo $aArea[$LPaData['nAid']]['sText'];?></div>
			</div>
			<div class="JobListContent">
				<div><?php echo aRECORD['WORKTYPE'];?></div>
				<div>
					<?php
					foreach ($LPaData['aType0'] as $LPsType0)
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
					;?>
				</div>
			</div>
			<div class="JobListContent">
				<?php echo $LPaData['sContent0']?>
				<div class="JobListContentImg">
					<?php
					if ($LPaData['sImgUrl'] != '')
					{
						?>
						<img src="<?php echo $LPaData['sImgUrl'];?>" alt="">
						<?php
					}
					?>
				</div>
			</div>
			<div class="JobListDate"><?php echo $LPaData['sCreateTime']?></div>
			<div class="JobListFeedbackBox">
				<form action="<?php echo $LPaData['sScoreUrl']?>" method="POST" data-jid="<?php echo $LPnId;?>">
					<input type="hidden" name="nScore" value="<?php echo $LPaData['nScore'];?>">
					<div class="JobListFeedbackScoreBox JqScoreBox" data-scored="<?php echo $LPaData['nIsScored'];?>">
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
							<!-- 點擊後,圖片換成scoreActive.png -->
							<div class="JobListFeedbackScore JqStar">
								<img src="images/<?php echo $LPsImg;?>.png" alt="">
							</div>
							<?php
						}
						?>
					</div>

					<div class="JobListFeedbackTit"><?php echo aRECORD['REVIEW'];?></div>
					<?php
					if($LPaData['nIsScored'] == 0)
					{
						#未撰寫評論
						?>
						<div class="JobListFeedbackContent">
							<input type="hidden" name="sContent0" value="">
							<div class="EmojiContentInput JqChat JqContent0" contenteditable="true"></div>
						</div>
						<div class="JobListFeedbackBtnBox">
							<div class="BtnAct JqSubmit" data-jid="<?php echo $LPnId;?>"><?php echo aRECORD['FINISH'];?></div>
						</div>
						<?php
					}
					else
					{
						#已撰寫
						?>
						<div class="JobListFeedbackContent active">
							<div class="JobListFeedbackContentTxt"><?php echo $LPaData['sScoreContent0'];?></div>
						</div>
						<?php
					}
					?>
				</form>
			</div>
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
						<a class="selfieBox boss BG" href="[[::sInfUrl::]]" style="background-image: url('[[::sHeadImage::]]');"></a>
					</td>
					<td class="JobListInfTit">[[::sName0::]]</td>
					<td class="JobListInfLike">
						<div class="LikeIconImg JqFavorite" data-jid="[[::nJid::]]" data-favorite="[[::nFavorite::]]">
							[[::sFavoriteImage::]]
						</div>
					</td>
					<td class="JobListInfBtnTd">
						<a class="JobListInfBtn detail" href="<?php echo $aUrl['sMyjob'].'&nId=[[::nJid::]]';?>"><?php echo aRECORD['DETAIL'];?></a>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="JobListContent">
			<div><?php echo aRECORD['WORKTIME'];?></div>
			<div>
				<span>[[::sStartTime::]]</span>
				<span>~</span>
				<span>[[::sEndTime::]]</span>
			</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aRECORD['WORKPLACE'];?></div>
			<div>[[::sArea::]]</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aRECORD['WORKTYPE'];?></div>
			<div>
				[[::sTypeHtml::]]
			</div>
		</div>
		<div class="JobListContent">
			[[::sContent0::]]
			<div class="JobListContentImg">
				[[::sImgUrl::]]
			</div>
		</div>
		<div class="JobListDate">[[::sCreateTime::]]</div>
		<div class="JobListFeedbackBox">
			<form action="[[::sScoreUrl::]]" method="POST" data-jid="[[::nJid::]]">
				<input type="hidden" name="nScore" value="[[::nScore::]]">
				<div class="JobListFeedbackScoreBox JqScoreBox" data-scored="[[::nIsScored::]]">
					[[::sScore::]]
				</div>

				<div class="JobListFeedbackTit"><?php echo aRECORD['REVIEW'];?></div>
				[[::sScoreHtml::]]
			</form>
		</div>
	</div>
</div>
<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
	require_once('inc/#Top.php');
?>
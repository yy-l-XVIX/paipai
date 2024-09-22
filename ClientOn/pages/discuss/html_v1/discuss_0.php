<?php $aData = json_decode($sData,true);?>
<!-- 討論區 -->
<div class="discussBox JqDiscuss JqAppend">
	<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1';?>">
	<input type="hidden" name="sDel" value="<?php echo $aUrl['sAct'].'&sJWT='.$sDelJWT;?>">


	<?php
	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}
	foreach ($aData as $LPnId => $LPaData)
	{	
		?>
		<div class="discussBlock JqBlock" data-id="<?php echo $LPnId;?>">
			<div class="discussArticleBox">
				<table class="discussArticleTable">
					<tbody>
						<tr>
							<td class="discussArticlePic">
								<!-- 若此人身份為雇主,selfieBox + boss -->
								<div class="selfieBox <?php echo $aMemberData[$LPaData['nUid']]['sRoleClass'];?> BG" style="background-image: url('<?php echo $aMemberData[$LPaData['nUid']]['sHeadImage'];?>');"></div>
							</td>
							<td class="discussArticleData">
								<div class="discussArticleTop">
									<div class="discussArticleName"><?php echo $aMemberData[$LPaData['nUid']]['sName0'];?></div>
									<?php
									if ($LPaData['nUid'] == $aUser['nId'])
									{
										// 2021-02-25 ios會擋到原本刪除按鈕 要求改成 ... 顯示
										?>
										<div class="discussArticleBtnMore PosRight JqMoreBox">
											<div class="JobListInfBtnMore JqMoreBtn">
												<i class="fas fa-times"></i>
											</div>
											<div class="JobListInfBtnMoreInner DisplayBlockNone JqMoreBlock JqStupidOut JqReplaceS" data-replace="<?php echo $aUrl['sAct'].'&sJWT='.$sDelJWT.'&nId='.$LPnId;?>" data-showctrl="delete">
												<div class="JobListInfBtnMoreInnerAhref"><?php echo aDISCUSS['REMOVE'];?></div>
											</div>
										</div>
										<?php
									}
									?>
								</div>
								<div class="discussArticleContent">
									<?php echo $LPaData['sContent0'];?>
									<?php
									if (!empty($LPaData['aImgUrl']))
									{
										?>
										<div>
											<?php
											foreach ($LPaData['aImgUrl'] as $LPsImgUrl)
											{
												?>
												<img src="<?php echo $LPsImgUrl;?>">
												<?php
											}
											?>
										</div>
										<?php
									}
									?>
								</div>
								<div class="discussArticleBot">
									<a class="discussArticleBtnView" href="<?php echo $aUrl['sDiscussDetail'].'&nId='.$LPnId;?>">
										<div class="more"><?php echo aDISCUSS['CHECKMSG'];?></div>
									</a>
									<div class="discussArticleDate"><?php echo $LPaData['sCreateTime'];?></div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
	?>
</div>
<div class="DisplayNone JqCopy">
	<div class="discussBlock JqBlock"  data-id="[[::nId::]]">
		<div class="discussArticleBox">
			<table class="discussArticleTable">
				<tbody>
					<tr>
						<td class="discussArticlePic">
							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="selfieBox [[::sRoleClass::]] BG" style="background-image: url('[[::sHeadImage::]]');"></div>
						</td>
						<td class="discussArticleData">
							<div class="discussArticleTop">
								<div class="discussArticleName">[[::sName0::]]</div>
								[[::sDeleteBtn::]]
							</div>
							<div class="discussArticleContent">
								[[::sContent0::]]
								<div class="">
									[[::aImgUrl::]]
								</div>
							</div>
							<div class="discussArticleBot">
								<a class="discussArticleBtnView JqMessageLink" href="<?php echo $aUrl['sDiscussDetail'].'&nId=[[::nId::]]'?>">
									<div class="more"><?php echo aDISCUSS['CHECKMSG'];?></div>
								</a>
								<div class="discussArticleDate">[[::sCreateTime::]]</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- 操作 -->
<?php
/*
<div class="WindowBox JqWindowBox JqDiscussAct">
	<div class="WindowSelectBox">
		<div class="WindowSelectTop">
			<div class="WindowSelectTopTit"><?php echo aDISCUSS['SELECTACT'];?></div>
		</div>
		<div class="WindowSelectItemBox TextAlignCenter">
			<div class="WindowSelectItem FontRed JqDelete" data-act=""><?php echo aDISCUSS['REMOVE'];?></div>
			<div class="WindowSelectItem JqClose"><?php echo aDISCUSS['CANCEL'];?></div>
		</div>
	</div>
	<div class="WindowBg"></div>
</div>
*/
?>
<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
	require_once('inc/#Top.php');
?>
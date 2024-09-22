<?php $aData = json_decode($sData,true);?>
<!-- 討論區-查看留言 -->
<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1&nId='.$nId;?>">
<input type="hidden" name="sReply" value="<?php echo $aUrl['sAct'].'&sJWT='.$sReplyJWT;?>">
<input type="hidden" name="sDel" value="<?php echo $aUrl['sAct'].'&sJWT='.$sDelJWT;?>">
<input type="hidden" name="sDelReply" value="<?php echo $aUrl['sAct'].'&sJWT='.$sDelReplyJWT;?>">
<input type="hidden" name="nId" value="<?php echo $nId;?>">
<div class="discussDetailBox">
	<div class="discussBlock JqBlock">
		<div class="discussArticleBox">
			<table class="discussArticleTable">
				<tbody>
					<tr>
						<td class="discussArticlePic">
							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="selfieBox <?php echo $aMemberData[$aData['nUid']]['sRoleClass'];?> BG" style="background-image: url('<?php echo $aMemberData[$aData['nUid']]['sHeadImage'];?>');"></div>
						</td>
						<td class="discussArticleData">
							<div class="discussArticleTop">
								<div class="discussArticleName"><?php echo $aMemberData[$aData['nUid']]['sName0'];?></div>
								<?php
								if ($aData['nUid'] == $aUser['nId'])
								{
									// 2021-02-25 ios會擋到原本刪除按鈕 要求改成 ... 顯示
									?>
									<div class="discussArticleBtnMore PosRight JqMoreBox">
										<div class="JobListInfBtnMore JqMoreBtn">
											<i class="fas fa-times"></i>
										</div>
										<div class="JobListInfBtnMoreInner DisplayBlockNone JqMoreBlock JqStupidOut JqReplaceS" data-replace="<?php echo $aUrl['sAct'].'&sJWT='.$sDelJWT.'&nId='.$nId;?>" data-showctrl="delete">
											<div class="JobListInfBtnMoreInnerAhref"><?php echo aDISCUSS['REMOVE'];?></div>
										</div>
									</div>
									<?php
								}
								?>
							</div>
							<div class="discussArticleContent">
								<?php echo $aData['sContent0'];?>
								<div class="">
									<?php
									foreach ($aData['aImgUrl'] as $LPsImgUrl)
									{
										?>
										<img src="<?php echo $LPsImgUrl;?>" alt="">
										<?php
									}
									?>
								</div>
							</div>
							<div class="discussArticleBot">
								<div class="discussArticleDate"><?php echo $aData['sCreateTime'];?></div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- 至少有一則留言才呈現 -->
		<div class="discussMsgBox JqAppend">
			<?php
			foreach ($aData['aReply'] as $LPnRid => $LPaReply)
			{
				?>
				<div class="discussMsgBlock">
					<table class="discussArticleTable JqBlock">
						<tbody>
							<tr>
								<td class="discussArticlePic">
									<!-- 若此人身份為雇主,selfieBox + boss -->
									<div class="selfieBox <?php echo $aMemberData[$LPaReply['nUid']]['sRoleClass'];?> BG" style="background-image: url('<?php echo $aMemberData[$LPaReply['nUid']]['sHeadImage'];?>');"></div>
								</td>
								<td class="discussArticleData">
									<div class="discussArticleTop">
										<div class="discussArticleName"><?php echo $aMemberData[$LPaReply['nUid']]['sName0'];?></div>
										<?php
										if ($LPaReply['nUid'] == $aUser['nId'])
										{
											// 2021-02-25 ios會擋到原本刪除按鈕 要求改成 ... 顯示
											?>
											<div class="discussArticleBtnMore PosRight JqMoreBox">
												<div class="JobListInfBtnMore JqMoreBtn">
													<i class="fas fa-times"></i>
												</div>
												<div class="JobListInfBtnMoreInner DisplayBlockNone JqMoreBlock JqStupidOut JqReplaceS" data-replace="<?php echo $aUrl['sAct'].'&sJWT='.$sDelReplyJWT.'&nId='.$LPnRid;?>" data-showctrl="delete">
													<div class="JobListInfBtnMoreInnerAhref"><?php echo aDISCUSS['REMOVE'];?></div>
												</div>
											</div>
											<?php
										}
										?>
									</div>
									<div class="discussArticleContent">
										<?php echo $LPaReply['sContent0'];?>
										<?php
										if (!empty($LPaReply['aImgUrl']))
										{
											?>
											<div>
												<?php
												foreach ($LPaReply['aImgUrl'] as $LPsImgUrl)
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
										<div class="discussArticleDate"><?php echo $LPaReply['sCreateTime'];?></div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

<div class="DisplayNone JqCopy">
	<div class="discussMsgBlock">
		<table class="discussArticleTable JqBlock">
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
							[[::aImgUrl::]]
						</div>
						<div class="discussArticleBot">
							<div class="discussArticleDate">[[::sCreateTime::]]</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- 操作 -->


<!-- 輸入訊息 -->
<div class="myjobIptBox JqMsgIptBox discussdetail">
	<div class="myjobIptContainer">
		<form id="JqReplyForm" enctype="multipart/form-data">
			<input type="hidden" name="nId" value="<?php echo $nId;?>">
			<input type="hidden" name="sContent0" value="">
			<table class="myjobIptTable">
				<tbody>
					<tr>
						<td class="myjobIptPic" rowspan="2">

							<!-- 若此人身份為雇主,selfieBox + boss -->
							<div class="selfieBox">
								<img src="<?php echo $aMemberData[$aUser['nId']]['sHeadImage'];?>" alt="">
							</div>
						</td>
						<td class="myjobIptMsg JqReplyContent JqMsgIptTxt">
							<div class="EmojiContentInput JqContent0" contenteditable="true"></div>
							<?php
							/*
							if(false)
							{
								?>
								<div class="EmojiBox JqEmojiBox">
									<div class="EmojiBtnSwitch JqBtnEmoji">
										<i class="far fa-laugh"></i>
									</div>
								</div>
								<?php
							}
							*/
							?>
						</td>
						<td class="enterMessagePhotoTd " rowspan="2">
							<input type="hidden" name="nImgCount" value="0" data-max="<?php echo $aSystem['aParam']['nPostImage'];?>">
							<div class="enterMessagePhoto JqFileBtnBox">
								<input type="file" class="JqFile" name="aFile[]" data-filebtn="0">
								<i class="fas fa-camera"></i>
							</div>
						</td>
						<td class="myjobIptTdBtn" rowspan="2">
							<div class="myjobIptBtn JqEditArea JqReply"><?php echo aDISCUSS['SEND'];?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="EmojiContentPhotoBox JqEmojiContentPhotoBox"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<div class="MsgIptBg JqMsgIptBg"></div>
</div>

<?php
	#Emoji
	require_once('inc/#EmojiPackage.php');
?>

<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
	require_once('inc/#Top.php');
?>
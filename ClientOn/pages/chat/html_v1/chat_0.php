<?php $aData = json_decode($sData,true);?>
<!-- 群組 -->

<div class="chatBox ">
	<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1';?>">
	<input type="hidden" name="sGroupType" value="chat">
	<input type="hidden" name="nGid" value="<?php echo $nGid;?>">
	<input type="hidden" name="nUid" value="<?php echo $aUser['nId'];?>">
	<input type="hidden" name="sName0" value="<?php echo $aMember[$aUser['nId']]['sName0'];?>">
	<input type="hidden" class="JqNewAnnounce" value="<?php echo $sAnnounce;?>">
	<input type="hidden" class="JqChange" value="<?php echo $sGroupName0;?>">
	<div class="JobListChatBox active">
		<?php
		// 問她參加嗎
		if ($aMember[$aUser['nId']]['nGroupStatus'] == 0)
		{
			?>
			<div class="TextAlignCenter">
				<div class="BtnAny JqDeny JqActBtn" data-act="<?php echo $sDenyACT;?>"><?php echo aCHAT['DENY'];?></div>
				<div class="BtnAny JqJoin JqActBtn" data-act="<?php echo $sJoinACT;?>"><?php echo aCHAT['JOIN'];?></div>
			</div>
			<?php
		}
		?>
		<div class="serviceChatBox JqShowArea JqAppend">
			<?php
			foreach ($aData as $LPnId => $LPaMessage)
			{
				if ($aUser['nId'] == $LPaMessage['nUid'])
				{
					?>
					<div class="serviceList Table UserMsg self">
						<div>
							<div>
								<div class="serviceListInf">
									<div class="serviceListName">
										<div class="serviceListNameTxt"><?php echo $aMember[$LPaMessage['nUid']]['sName0'];?></div>
									</div>
									<div class="serviceListBot">
										<div class="serviceListMsgBox">
											<div class="serviceListMsg"><?php echo $LPaMessage['sMsg'];?></div>
										</div>
										<div class="serviceListTime">
											<div class="serviceListTimeTxt"><?php echo $LPaMessage['sCreateTime'];?></div>
										</div>
									</div>
								</div>
								<div class="serviceListTdImg">
									<a class="serviceListImg BG" href="<?php echo $LPaMessage['sInfUrl'];?>" style="background-image: url('<?php echo $aMember[$aUser['nId']]['sHeadImage'];?>');"></a>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				else
				{
					// 若此會員是其他人才, class + other
					?>
					<div class="serviceList Table AdmMsg ">
						<div>
							<div>
								<div class="serviceListTdImg">
									<a class="serviceListImg BG" href="<?php echo $LPaMessage['sInfUrl'];?>" style="background-image: url('<?php echo $aMember[$LPaMessage['nUid']]['sHeadImage'];?>');"></a>
								</div>
								<div class="serviceListInf">
									<div class="serviceListName">
										<div class="serviceListNameTxt">
											<?php echo $aMember[$LPaMessage['nUid']]['sName0'];?>
										</div>
									</div>
									<div class="serviceListBot">
										<div class="serviceListMsgBox">
											<div class="serviceListMsg"><?php echo $LPaMessage['sMsg'];?></div>
										</div>
										<div class="serviceListTime">
											<div class="serviceListTimeTxt"><?php echo $LPaMessage['sCreateTime'];?></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
	if ($aMember[$aUser['nId']]['nGroupStatus'] == 1 || $aMember[$aUser['nId']]['nGroupStatus'] == 2)
	{
		// 輸入訊息
	?>
		<div class="myjobIptBox JqMsgIptBox">
			<div class="myjobIptContainer">
				<?php
				if ($aGroup['nOnline'] == 2)
				{
					echo '<div class="TextAlignCenter">'.aCHAT['BLOCK'].'</div>';
				}
				else
				{
				?>
				<table class="myjobIptTable">
					<tbody>
						<tr>
							<td class="myjobIptPic" rowspan="2">
								<div class="selfieBox">
									<img src="<?php echo $aMember[$aUser['nId']]['sHeadImage'];?>" alt="">
								</div>
							</td>
							<td class="myjobIptMsg JqMsgIptTxt">
								<div class="EmojiContentInput JqChat JqContent0" contenteditable="true"></div>
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
							<td class="enterMessagePhotoTd" rowspan="2">
								<form id="JqImageForm"  enctype="multipart/form-data" action="<?php echo $aUrl['sAct'].'&sJWT='.$sImgJWT;?>">
									<input type="hidden" name="nImgCount" value="0" data-max="<?php echo $aSystem['aParam']['nPostImage'];?>">
									<div class="enterMessagePhoto JqFileBtnBox">
										<input type="file" class="JqFile" name="aFile[]" data-filebtn="0" >
										<i class="fas fa-camera"></i>
									</div>
								</form>
							</td>
							<td class="myjobIptTdBtn" rowspan="2">
								<div class="myjobIptBtn JqEnterBtn JqSend"><?php echo aCHAT['SEND'];?></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="EmojiContentPhotoBox JqEmojiContentPhotoBox"></div>
							</td>
						</tr>
					</tbody>
				</table>
				<?php
				}
				?>
			</div>
			<div class="MsgIptBg JqMsgIptBg"></div>
		</div>
	<?php
	}
	?>
</div>


<?php
	#Emoji
	require_once('inc/#EmojiPackage.php');
?>
<div class="DisplayNone JqCopySelfMsg">
	<div class="serviceList Table UserMsg self">
		<div>
			<div>
				<div class="serviceListInf">
					<div class="serviceListName">
						<div class="serviceListNameTxt"><?php echo $aMember[$aUser['nId']]['sName0'];?></div>
					</div>
					<div class="serviceListBot">
						<div class="serviceListMsgBox">
							<div class="serviceListMsg">[[::sMsg::]]</div>
						</div>
						<div class="serviceListTime">
							<div class="serviceListTimeTxt">[[::sCreateTime::]]</div>
						</div>
					</div>
				</div>
				<div class="serviceListTdImg">
					<a class="serviceListImg BG" href="<?php echo $aUrl['sInf'];?>" style="background-image: url('<?php echo $aMember[$aUser['nId']]['sHeadImage'];?>');"></a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="DisplayNone JqCopyOtherMsg">
	<div class="serviceList Table AdmMsg ">
		<div>
			<div>
				<div class="serviceListTdImg">
					<a class="serviceListImg BG" href="[[::sInfUrl::]]" style="background-image: url('[[::sHeadImage::]]');"></a>
				</div>
				<div class="serviceListInf">
					<div class="serviceListName">
						<div class="serviceListNameTxt">
							[[::sName0::]]
						</div>
					</div>
					<div class="serviceListBot">
						<div class="serviceListMsgBox">
							<div class="serviceListMsg">[[::sMsg::]]</div>
						</div>
						<div class="serviceListTime">
							<div class="serviceListTimeTxt">[[::sCreateTime::]]</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="JpPageBottom"></div>
<input type="hidden" class="JqGroupMember" value='<?php echo json_encode($aMember);?>'>
<?php
	require_once('inc/#Down.php');
?>
<?php $aData = json_decode($sData,true);?>
<!--
	人才 - 工作清單
	雇主 - 刊登工作
-->
<header>
	<form >
		<div class="headerContainer TextAlignLeft">
			<a href="<?php echo $aUrl['sBack'];?>" class="headerIcon headerLeft">
				<i class="fas fa-arrow-left"></i>
			</a>
			<div class="myjobChooseBox">
				<?php
				if($sUserCurrentRole == 'staff')
				{
					#人才才呈現
					?>
					<div class="myjobChooseTopic"><?php echo aJOB['CHOOSEUSER'];?></div>
					<?php
				}
				else
				{
					#雇主呈現
					?>
					<div class="myjobPickBtn JqMyjobBtnPick"><?php echo aJOB['CHOOSE'];?></div>
					<?php
				}
				?>
				<div class="myjobChooseUserBox">
					<div class="myjobChooseUserBtn myjobChooseUserBtnPrev">
						<i class="fas fa-chevron-left"></i>
					</div>
					<div class="myjobChooseUserView JqMyjobBePickedBox">
						<?php
						for ($i=0; $i < $aJobData['nEmploye']; $i++)
						{
							if (isset($aJobData['aEmploye'][$i]))
							{
								$LPnUid = (int)$aJobData['aEmploye'][$i];
								//已選擇之人才
						?>

								<a href="<?php echo $aMemberData[$LPnUid]['sInfUrl'];?>" class="selfieBox JqMyjobBePicked BG" style="background-image: url('<?php echo $aMemberData[$LPnUid]['sHeadImage'];?>');"></a>
						<?php
							}
							else
							{
								//工作人數 - 已選擇之人才 = 數量
						?>
								<div class="myjobChooseUserEmpty JqMyjobNotBePicked"></div>
						<?php
							}
						}
						?>
					</div>
					<div class="myjobChooseUserBtn myjobChooseUserBtnNext">
						<i class="fas fa-chevron-right"></i>
					</div>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
	</form>
</header>

<div class="WindowBox JqWindowBox JqMyjobPickBox">
	<header>
		<div class="headerContainer">
			<div class="headerIcon headerLeft JqClose">
				<i class="fas fa-arrow-left"></i>
			</div>

			<div class="headerBtn headerRight0 JqHeadConfirm JqStupidOut DisplayInlineBlockNone" data-showctrl="kindremind"><?php echo aJOB['CONFIRM'];?></div>

		</div>
	</header>
	<div class="myjobPickListBox">
		<?php
		if (sizeof($aJobData['aEmploye']) == $aJobData['nEmploye'] )
		{
			echo '<div class="myjobPickList"><div class="TextAlignCenter">'.aERROR['EMPLOYEMAX'].'</div></div>';
		}
		else
		{
			foreach ($aMemberData as $LPnUid => $LPaMember)
			{
				// if ($LPaMember['nJoin'] == 1 || $LPnUid == $aJobData['nUid'] || !isset($LPaMember['nInGroup'])) # 已參加 & 雇主 & 已退出 跳過
				// {
				// 	continue;
				// }
				if ($LPnUid == $aJobData['nUid'] || !isset($LPaMember['nInGroup']))
				{
					continue;
				}
				?>
				<div class="myjobPickList">
					<table class="myjobPickTable">
						<tbody>
							<tr>
								<td class="myjobPickTdIcon">
									<?php
									if ($LPaMember['nJoin'] != 1)
									{
										?>
										<div class="myjobPickIconChoose">
											<label for="member<?php echo $LPnUid; ?>">
												<input type="checkbox" id="member<?php echo $LPnUid; ?>" class="JqMyjobCheckbox" data-img="<?php echo $LPaMember['sHeadImage']; ?>" data-name="<?php echo $LPaMember['sName0']; ?>" data-id="<?php echo $LPnUid; ?>">
											</label>
										</div>
										<?php
									}
									?>
								</td>
								<td class="myjobPickTdPic JqMyjobPickBtn">
									<div class="selfieBox BG" style="background-image: url('<?php echo $LPaMember['sHeadImage']; ?>');"></div>
								</td>
								<td class="myjobPickTdName JqMyjobPickBtn">
									<div><?php echo $LPaMember['sName0']; ?></div>
								</td>
								<td class="myjobPickTdIcon FontRed JqReplaceS JqStupidOut"  data-showctrl="2" data-replace="<?php echo $aUrl['sPageAct'].'&nUid='.$LPnUid; ?>">
									<div class="TextAlignCenter"><i class="fas fa-times-circle"></i></div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
			}
		}
		?>
	</div>
	<div class="WindowBg"></div>
</div>

<div class="myjobBox">
	<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1';?>">
	<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
	<input type="hidden" name="sPageAct" value="<?php echo $aUrl['sPageAct'];?>">
	<input type="hidden" name="sActJWT" value="<?php echo $sActJWT;?>">
	<input type="hidden" name="sDelJWT" value="<?php echo $sDelJWT;?>">
	<input type="hidden" name="sOutJWT" value="<?php echo $sOutJWT;?>">
	<input type="hidden" name="sKickOutJWT" value="<?php echo $sKickOutJWT;?>">
	<input type="hidden" name="sCloseJobJWT" value="<?php echo $sCloseJobJWT;?>">
	<input type="hidden" name="sGroupType" value="job">
	<input type="hidden" name="nGid" value="<?php echo $aJobData['nId'];?>">
	<input type="hidden" name="nUid" value="<?php echo $aUser['nId'];?>">
	<input type="hidden" name="sName0" value="<?php echo $aMemberData[$aUser['nId']]['sName0'];?>">
	<div class="JobListBlock JqPhotoOtherBox">
		<table class="JobListInf">
			<tbody>
				<tr>
					<td class="JobListInfPic">
						<a class="selfieBox boss BG" href="<?php echo $aMemberData[$aJobData['nUid']]['sInfUrl'];?>" style="background-image: url('<?php echo $aMemberData[$aJobData['nUid']]['sHeadImage'];?>');"></a>
					</td>
					<td class="JobListInfTit"><?php echo $aJobData['sName0'];?></td>

					<?php
					if($sUserCurrentRole == 'staff')
					{
						?>
						<td class="JobListInfLike">
							<div class="LikeIconImg JqFavorite" data-jid="<?php echo $aJobData['nId'];?>" data-favorite="<?php echo $aJobData['nFavorite'];?>">
								<?php
								if($aJobData['nFavorite'])
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
						if ($aJobData['nStatus'] == 1)//已結案
						{
							?>
							<div class="JobListInfBtn active" ><?php echo aJOB['CLOSEDJOB'];?></div>
							<?php
						}
						else if($sUserCurrentRole == 'staff')
						{
							#人才時呈現
							?>
							<div class="JobListInfBtn active JqOut" data-jid="<?php echo $aJobData['nId'];?>"><?php echo aJOB['OUTJOB'];?></div>
							<?php

						}
						else
						{
							#雇主時呈現
							?>
							<div class="JobListInfBtn active JqReplaceS JqStupidOut" data-showctrl="3" data-replace="<?php echo $aUrl['sPageAct'].'&nJid='.$aJobData['nId']; ?>" data-jid="<?php echo $aJobData['nId'];?>"><?php echo aJOB['CLOSEJOB'];?></div>
							<?php
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="JobListContent">
			<div><?php echo aJOB['WORKTIME'];?></div>
			<div>
				<span><?php echo $aJobData['sStartTime'];?></span>
				<span>~</span>
				<span><?php echo $aJobData['sEndTime'];?></span>
			</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aJOB['WORKPLACE'];?></div>
			<div><?php echo $aJobData['sArea'];?></div>
		</div>
		<div class="JobListContent">
			<div><?php echo aJOB['WORKTYPE'];?></div>
			<div>
				<?php
				foreach ($aJobData['aType0'] as $LPnType0 => $LPaType0)
				{
					?>
					<span><?php echo $LPaType0['sName0'];?></span>
					<?php
				}
				;?>
			</div>
		</div>
		<div class="JobListContent">
			<?php echo $aJobData['sContent0'];?>
			<div class="JobListContentImg">
				<?php
				if ($aJobData['sImgUrl'] != '')
				{
					?>
					<img src="<?php echo $aJobData['sImgUrl'];?>" alt="">
					<?php
				}
				?>
			</div>
		</div>
		<div class="JobListDate"><?php echo $aJobData['sCreateTime'];?></div>
		<div class="MarginBottom10"><?php echo aJOB['INTERESTED'];?></div>
		<div class="myjobBossBox">
			<div class="myjobBossBtn myjobBossBtnPrev">
				<i class="fas fa-chevron-left"></i>
			</div>
			<div class="myjobBossView">
				<?php
				// user in group
				foreach ($aMemberData as $LPnUid => $LPaMember)
				{

					if (!isset($LPaMember['nInGroup']) || $LPnUid == $aJobData['nUid'])
					{
						continue;
					}
				?>
					<a class="selfieBox BG" href="<?php echo $LPaMember['sInfUrl'];?>" style="background-image: url('<?php echo $LPaMember['sHeadImage'];?>');"></a>
				<?php
				}
				?>
				<div style="clear:both;"></div>
			</div>
			<div class="myjobBossBtn myjobBossBtnNext">
				<i class="fas fa-chevron-right"></i>
			</div>
		</div>
		<div class="JobListChatBox active">
			<div class="serviceChatBox JqChatBox JqAppend JqShowArea">
				<?php
				foreach ($aData as $LPnId => $LPaData)
				{
					if ($aUser['nId'] == $LPaData['nUid'])
					{
						?>
						<div class="serviceList Table UserMsg self">
							<div>
								<div>
									<div class="serviceListInf">
										<div class="serviceListName">
											<div class="serviceListNameTxt"><?php echo $aMemberData[$LPaData['nUid']]['sName0'];?></div>
										</div>
										<div class="serviceListBot">
											<div class="serviceListMsgBox">
												<div class="serviceListMsg"><?php echo $LPaData['sMsg'];?></div>
											</div>
											<div class="serviceListTime">
												<div class="serviceListTimeTxt"><?php echo $LPaData['sCreateTime'];?></div>
											</div>
										</div>
									</div>
									<div class="serviceListTdImg">
										<a class="serviceListImg BG" href="<?php echo $aMemberData[$LPaData['nUid']]['sInfUrl'];?>" style="background-image: url('<?php echo $aMemberData[$LPaData['nUid']]['sHeadImage'];?>');"></a>
									</div>
								</div>
							</div>
						</div>

						<?php
					}
					else
					{
						?>
						<div class="serviceList Table AdmMsg">
							<div>
								<div>
									<div class="serviceListTdImg">
										<a href="<?php echo $aMemberData[$LPaData['nUid']]['sInfUrl'];?>" class="serviceListImg BG" style="background-image: url('<?php echo $aMemberData[$LPaData['nUid']]['sHeadImage'];?>');"></a>
									</div>
									<div class="serviceListInf">
										<div class="serviceListName">
											<div class="serviceListNameTxt"><?php echo $aMemberData[$LPaData['nUid']]['sName0'];?></div>
										</div>
										<div class="serviceListBot">
											<div class="serviceListMsgBox">
												<div class="serviceListMsg"><?php echo $LPaData['sMsg'];?></div>
											</div>
											<div class="serviceListTime">
												<div class="serviceListTimeTxt"><?php echo $LPaData['sCreateTime'];?></div>
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
	</div>
</div>
<!-- 輸入訊息 -->
<div class="myjobIptBox JqMsgIptBox">
	<div class="myjobIptContainer">
		<table class="myjobIptTable">
			<tbody>
				<tr>
					<td class="myjobIptPic" rowspan="2">
						<div class="selfieBox JqMyHeadImage BG" style="background-image: url('<?php echo $aMemberData[$aUser['nId']]['sHeadImage'];?>');" data-headimg="<?php echo $aMemberData[$aUser['nId']]['sHeadImage'];?>"></div>
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
						<form id="JqImageForm"  enctype="multipart/form-data" action="<?php echo $aUrl['sPageAct'].'&sJWT='.$sImgJWT;?>">
							<input type="hidden" name="nImgCount" value="0" data-max="<?php echo $aSystem['aParam']['nPostImage'];?>">
							<div class="enterMessagePhoto JqFileBtnBox">
								<input type="file" class="JqFile" name="aFile[]" data-filebtn="0" >
								<i class="fas fa-camera"></i>
							</div>
						</form>
					</td>
					<td class="myjobIptTdBtn" rowspan="2">
						<div class="myjobIptBtn JqEnterBtn JqSend"><?php echo aJOB['SEND'];?></div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="EmojiContentPhotoBox JqEmojiContentPhotoBox"></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="myjobIptBg MsgIptBg JqMsgIptBg"></div>
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
						<div class="serviceListNameTxt"><?php echo $aMemberData[$aUser['nId']]['sName0'];?></div>
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
					<a class="serviceListImg BG" href="<?php echo $aMemberData[$aUser['nId']]['sInfUrl'];?>" style="background-image: url('<?php echo $aMemberData[$aUser['nId']]['sHeadImage'];?>"></a>
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
<div class="DisplayNone JqCopyInviteMsg">
	<div class="serviceList Table AdmMsg ">
		<div>
			<div>
				<div class="serviceListTdImg">
					<a class="serviceListImg BG" href="[[::sInfUrl::]]" style="background-image: url('[[::sHeadImage::]]');"></a>
				</div>
				<div class="serviceListInf">
					<div class="serviceListName">
						<div class="serviceListNameTxt">[[::sName0::]]</div>
					</div>
					<div class="serviceListBot">
						<div class="serviceListMsgBox">
							<div class="serviceListMsg">
								<div class="serviceListInviteQ"><?php echo aJOB['ASKWORK'];?></div>
								<div class="serviceListInviteBtnBox">
									<div class="serviceListInviteBtn JqInviteBtn JqMyjobBtnAccept" data-href="javascript:void(0);"  data-jwt="<?php echo $sAcceptJob;?>"><?php echo aJOB['YES'];?></div>
									<div class="serviceListInviteBtn JqInviteBtn JqMyjobBtnDeny" data-href="javascript:void(0);"  data-jwt="<?php echo $sRejectJob;?>"><?php echo aJOB['NO'];?></div>
								</div>
							</div>
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
<input type="hidden" name="nFirstJoin" value="<?php echo $nFirstJoin;?>">
<input type="hidden" class="JqGroupMember" value='<?php echo json_encode($aMemberData);?>'>
<?php
	require_once('inc/#Down.php');
?>
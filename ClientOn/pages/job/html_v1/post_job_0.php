<?php $aData = json_decode($sData,true);?>
<!-- 刊登工作 - 新增 -->
<form id="JqPostForm" enctype="multipart/form-data" action="<?php echo $aUrl['sAct'].'&sJWT='.$sJWT;?>">
	<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
	<input type="hidden" name="nId" value="<?php echo $nId;?>">
	<input type="hidden" name="sChangeCityJWT" value="<?php echo $sChangeCityJWT;?>">
	<div class="postJobBox JqMsgIptBox">
		<div class="postJobKindBox">
			<label for="nStatus0" class="postJobKindBtn JqPostJobBtnKind  <?php echo $aData['nStatus']== 0 ?'active':''?>">
				<input type="radio" id="nStatus0" value="0" name="nStatus" <?php echo $aData['nStatus']== 0 ?'checked':'';?>>
				<span><?php echo aPOSTJOB['POST'];?></span>
			</label>
			<label for="nStatus10" class="postJobKindBtn JqPostJobBtnKind  <?php echo $aData['nStatus']== 10 ?'active':''?>">
				<input type="radio" id="nStatus10" value="10" name="nStatus" <?php echo $aData['nStatus']== 10 ?'checked':'';?>>
				<span><?php echo aPOSTJOB['DRAFT'];?></span>
			</label>
		</div>
		<table class="infData">
			<tbody>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aPOSTJOB['NAME0'];?></div>
					</td>
					<td class="infDataCell2">
						<div class="Ipt">
							<input type="text" name="sName0" placeholder="<?php echo aPOSTJOB['NAME0'];?>..." value="<?php echo $aData['sName0'];?>" required autocomplete="off">
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aPOSTJOB['STARTTIME'];?></div>
					</td>
					<td class="infDataCell2">
						<div class="Ipt">
							<input type="text" name="sStartTime" class="JqStartTime" placeholder="<?php echo aPOSTJOB['STARTTIME'];?>" value="<?php echo $aData['sStartTime'];?>" autocomplete="off">
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aPOSTJOB['ENDTIME'];?></div>
					</td>
					<td class="infDataCell2">
						<div class="Ipt">
							<input type="text" name="sEndTime" class="JqEndTime" placeholder="<?php echo aPOSTJOB['ENDTIME'];?>" value="<?php echo $aData['sEndTime'];?>" autocomplete="off">
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aPOSTJOB['PLACE'];?></div>
					</td>
					<td class="infDataCell2">
						<div class="MarginBottom15">
							<div class="Sel">
								<select class="JqChangeCity">
									<?php
									foreach ($aCity as $LPnCid => $LPaCity)
									{
										?>
										<option value="<?php echo $LPnCid;?>" <?php echo $LPaCity['sSelect'];?> ><?php echo $LPaCity['sName0'];?></option>
										<?php
									}
									?>
								</select>
								<div class="SelDecro"></div>
							</div>
						</div>
						<div>
							<div class="Sel">
								<select name="nAid" class="JqChangeArea">
									<?php
									foreach ($aArea as $LPnAid => $LPaArea)
									{
										?>
										<option value="<?php echo $LPnAid;?>" <?php echo $LPaArea['sSelect'];?> ><?php echo $LPaArea['sName0'];?></option>
										<?php
									}
									?>
								</select>
								<div class="SelDecro"></div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aPOSTJOB['PEOPLE'];?></div>
					</td>
					<td class="infDataCell2">
						<div class="Ipt">
							<input type="number" name="nEmploye" placeholder="<?php echo aPOSTJOB['PEOPLE'];?>..." value="<?php echo $aData['nEmploye'];?>" inputmode="numeric" required>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aPOSTJOB['TYPE'];?></div>
					</td>
					<td class="infDataCell2">
						<div class="postJobTypeBtnBox">
							<?php
							foreach ($aJobType as $LPnId => $LPaJobType)
							{
							?>
								<div class="postJobTypeBtn">
									<label for="jobtype<?php echo $LPnId;?>">
										<input type="checkbox" name="aJobType[]" id="jobtype<?php echo $LPnId;?>" value="<?php echo $LPnId;?>" <?php echo $LPaJobType['sSelect'];?>>
										<div class="postJobTypeBtnText"><?php echo $LPaJobType['sName0'];?></div>
									</label>
								</div>
							<?php
							}
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aPOSTJOB['CONTENT0'];?></div>
					</td>
					<td class="infDataCell2 JqPostContent">
						<input type="hidden" name="sContent0" value="">
						<div class="EmojiContentInput infDataCell2Content JqContent0" contenteditable="true">
							<?php echo $aData['sContent0'];?>
						</div>
						<?php
						if(true)
						{
						?>
						<div class="EmojiBox JqEmojiBox">
							<div class="EmojiBtnSwitch JqBtnEmoji">
								<i class="far fa-laugh"></i>
							</div>
						</div>
						<?php
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			#Emoji
			require_once('inc/#EmojiPackage.php');
		?>
		<div class="postJobBlock">
			<div class="postJobImgTit"><?php echo aPOSTJOB['PHOTO'];?></div>
			<div class="FileImg">
				<img class="JqPreviewImage" data-file="0" src="">
			</div>
			<div class="FileBtnAdd JqFileActive">
				<input type="file" name="sFile" class="JqFile" data-filebtn="0" accept="image/*" />
				<div class="original"><?php echo UPLOADIMG;?></div>
				<div class="change"><?php echo CHANGEIMG;?></div>
			</div>
		</div>
		<div class="postJobBtnBox">
			<div class="BtnAct JqSubmit"><?php echo SUBMIT?></div>
		</div>
		<div class="MsgIptBg JqMsgIptBg"></div>
	</div>
</form>
<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nt" value="<?php echo NOWTIME;?>" />
	<input type="hidden" name="nId" value="<?php echo $aData['nId'];?>" />

	<div class="Information tab">
		<table class="InformationTit">
			<tbody>
				<tr>
					<td class="InformationTitCell JqBtnShowOnly active" style="width:calc(100%/7);" data-showctrl="1">
						<div class="InformationName"><?php echo aUSER['MEMBERDATA']; ?></div>
					</td>
					<td class="InformationTitCell JqBtnShowOnly" style="width:calc(100%/7);" data-showctrl="2">
						<div class="InformationName"><?php echo aUSER['MEMBERDETAIL']; ?></div>
					</td>
					<td class="InformationTitCell JqBtnShowOnly" style="width:calc(100%/7);" data-showctrl="3">
						<div class="InformationName"><?php echo aUSER['MEMBERID']; ?></div>
					</td>
					<td class="InformationTitCell JqBtnShowOnly" style="width:calc(100%/7);" data-showctrl="4">
						<div class="InformationName"><?php echo aUSER['MEMBERBANK']; ?></div>
					</td>
					<td class="InformationTitCell JqBtnShowOnly" style="width:calc(100%/7);" data-showctrl="5">
						<div class="InformationName"><?php echo aUSER['MEMBERPHOTO']; ?></div>
					</td>
					<td class="InformationTitCell JqBtnShowOnly" style="width:calc(100%/7);" data-showctrl="6">
						<div class="InformationName"><?php echo aUSER['MEMBERVIDEO']; ?></div>
					</td>
					<td class="InformationTitCell JqBtnShowOnly" style="width:calc(100%/7);" data-showctrl="7">
						<div class="InformationName"><?php echo aUSER['MEMBERTRANSACTION']; ?></div>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="InformationScroll">
			<div class="InformationTableBox active" data-show="1">
				<table>
					<tbody>
						<tr>
							<td><?php echo ACCOUNT;?></td>
							<td>
								<?php
								if($nId == 0)
								{
									?>
									<div class="Ipt">
										<input type="text" name="sAccount" value="<?php echo $aData['sAccount'];?>"  placeholder="<?php echo aUSER['ACCOUNTFORMAT'];?>">
									</div>
									<?php
								}
								else
								{
									?>
									<?php echo $aData['sAccount'];?>
									<?php
								}
								?>
							</td>
						</tr>
						<tr>
							<td><?php echo STATUS;?></td>
							<td>
								<div class="Sel">
									<select name="nStatus">
										<?php
										foreach ($aStatus as $LPnStatus => $LPaStatus)
										{
											?>
											<option value="<?php echo $LPnStatus;?>" <?php echo $LPaStatus['sSelect'];?> >
												<?php echo $LPaStatus['sTitle'];?>
											</option>
											<?php
										}
										?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['KIND'];?></td>
							<td>
								<?php
								foreach ($aKind as $LPnKid => $LPaKind)
								{
									?>
									<label for="nKid<?php echo $LPnKid;?>" class="IptRadio">
										<input type="checkbox" name="aKid[]" id="nKid<?php echo $LPnKid;?>" value="<?php echo $LPnKid;?>" <?php echo $LPaKind['sSelect'];?>>
										<span><?php echo $LPaKind['sTitle'];?></span>
									</label>
									<?php
								}
								?>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['EXPIRED0'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" class="JqExpired0" name="sExpired0" value="<?php echo $aData['sExpired0'];?>"  autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['EXPIRED1'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" class="JqExpired1" name="sExpired1" value="<?php echo $aData['sExpired1'];?>"  autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['NAME0'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>" >
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['NAME1'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" name="sName1" value="<?php echo $aData['sName1'];?>" >
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['IDNUMBER'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" name="sIdNumber" value="<?php echo $aData['sIdNumber'];?>" >
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['BIRTHDAY'];?></td>
							<td>
								<div class="Ipt">
									<input class="JqBirthday" type="text" name="sBirthday" value="<?php echo $aData['sBirthday'];?>" autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['AGE'];?></td>
							<td><?php echo $aData['nAge'];?></td>
						</tr>
						<tr>
							<td><?php echo aUSER['LOCATION'];;?></td>
							<td>
								<div class="Sel">
									<select name="nLid">
										<?php
										foreach ($aLocation as $LPnLid => $LPaLocation)
										{
											?>
											<option value="<?php echo $LPnLid;?>" <?php echo $LPaLocation['sSelect'];?> >
												<?php echo $LPaLocation['sTitle'];?>
											</option>
											<?php
										}
										?>
									</select>
								</div>
							</td>
						</tr>
						<?php
						if ($nId == 0)
						{
							?>
							<tr>
								<td><?php echo aUSER['PASSWORD'];?></td>
								<td>
									<div class="Ipt">
										<input type="password" name="sPassword"  placeholder="<?php echo aUSER['PASSWORDFORMAT'];?>">
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo aUSER['TRANSPASSWORD'];?></td>
								<td>
									<div class="Ipt">
										<input type="password" name="sTransPassword"  placeholder="<?php echo aUSER['TRANSPASSWORDFORMAT'];?>">
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo aUSER['PA'];?></td>
								<td>
									<div class="Ipt">
										<input type="text" name="sPa">
									</div>
								</td>
							</tr>

							<?php
						}
						if ($aAdm['nAdmType'] == 1)
						{
							?>
							<tr>
								<td><?php echo aUSER['HIDEMEMBER'];?></td>
								<td>
									<label for="nType99" class="IptRadio">
										<input type="radio" id="nType99" name="nType" <?php echo $aData['aType']['99']?> value="99">
										<span><?php echo aUSER['NOHIDE'];?></span>
									</label>
									<label for="nType1" class="IptRadio">
										<input type="radio" id="nType1" name="nType" <?php echo $aData['aType']['1']?> value="1">
										<span><?php echo aUSER['HIDE'];?></span>
									</label>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td><?php echo CREATETIME;?></td>
							<td><?php echo $aData['sCreateTime'];?></td>
						</tr>
						<tr>
							<td><?php echo UPDATETIME;?></td>
							<td><?php echo $aData['sUpdateTime'];?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="InformationTableBox " data-show="2">
				<table>
					<tbody>
						<tr>
							<td><?php echo aUSER['HEIGHT'];?></td>
							<td>
								<div class="Ipt">
									<input type="number" name="sHeight" value="<?php echo $aData['sHeight'];?>">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['WEIGHT'];?></td>
							<td>
								<div class="Ipt">
									<input type="number" name="sWeight" value="<?php echo $aData['sWeight'];?>">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['SIZE'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" name="sSize" value="<?php echo $aData['sSize'];?>" placeholder="<?php echo aUSER['SIZEFORMAT'];?>">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['CONTENT0'];?></td>
							<td>
								<div class="Textarea">
									<textarea name="sContent0" id="sContent0"><?php echo $aData['sContent0'];?></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['CONTENT1'];?></td>
							<td>
								<div class="Textarea">
									<textarea name="sContent1" id="sContent1"><?php echo $aData['sContent1'];?></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['PHONE'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" name="sPhone" value="<?php echo $aData['sPhone'];?>">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['WECHAT'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" name="sWechat" value="<?php echo $aData['sWechat'];?>">
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['EMAIL'];?></td>
							<td>
								<div class="Ipt">
									<input type="text" name="sEmail" value="<?php echo $aData['sEmail'];?>">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="InformationTableBox " data-show="3">
				<table>
					<tbody>
						<tr>
							<td><?php echo aUSER['IDFRONT'];?></td>
							<td>
								<div class="InlineBlockImg MarginBottom5"><img src="<?php echo $aData['sIdImgUrl0'];?>"></div>
							</td>
						</tr>
						<tr>
							<td><?php echo aUSER['IDBACK'];?></td>
							<td>
								<div class="InlineBlockImg MarginBottom5"><img src="<?php echo $aData['sIdImgUrl1'];?>"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="InformationTableBox " data-show="4">
				<table>
					<thead>
						<tr>
							<th><?php echo NO;?></th>
							<th><?php echo aUSER['BANKNAME'];?></th>
							<th><?php echo aUSER['BANKBRANCH'];?></th>
							<th><?php echo aUSER['BANKACCOUNTNAME'];?></th>
							<th><?php echo aUSER['BANKACCOUNT'];?></th>
							<th><?php echo aUSER['BANKIMAGE'];?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($aUserBank as $LPnBid => $LPaUserBank)
						{
							?>
							<tr>
								<td><?php echo $LPnBid;?></td>
								<td><?php echo $LPaUserBank['sBankName'];?> (<?php echo $LPaUserBank['sCode'];?>)</td>
								<td><?php echo $LPaUserBank['sName2'];?></td>
								<td><?php echo $LPaUserBank['sName1'];?></td>
								<td><?php echo $LPaUserBank['sName0'];?></td>
								<td>
									<div class="InlineBlockImg MarginBottom5"><img src="<?php echo $LPaUserBank['sImgUrl'];?>"></div>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="InformationTableBox " data-show="5">
				<table>
					<tbody>

						<?php
						foreach ($aUserPhoto as $LPsPhoto)
						{
							?>
							<tr>
								<td>
								<div class="InlineBlockImg MarginBottom5"><img src="<?php echo $LPsPhoto;?>"></div>
								</td>
							</tr>
							<?php
						}
						?>

					</tbody>
				</table>
			</div>
			<div class="InformationTableBox " data-show="6">
				<table>
					<tbody>
						<tr>
							<td>
								<?php
								foreach ($aUserVideo as $LPsVideo)
								{
									?>
									<div class="">
										<video controls="" autoplay="" name="media"><source src="<?php echo $LPsVideo;?>" type="video/mp4"></video>
									</div>
									<?php
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="InformationTableBox " data-show="7">
				<table>
					<thead>
						<tr>
							<th><?php echo NO;?></th>
							<th><?php echo aUSER['KIND'];?></th>
							<th><?php echo aUSER['PAYMENT'];?></th>
							<th><?php echo aUSER['AMOUNT'];?></th>
							<th><?php echo STATUS;?></th>
							<th><?php echo CREATETIME;?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($aData['aTransaction'] as $LPnId => $LPaLog)
						{
							?>
							<tr>
								<td><?php echo $LPnId;?></td>
								<td><?php echo $aKind[$LPaLog['nUkid']]['sTitle'];?></td>
								<td><?php echo $LPaLog['sPayTypeName'];?></td>
								<td><?php echo $LPaLog['nMoney'];?></td>
								<td><?php echo $aMoneyStatus[$LPaLog['nStatus']]['sText'];?></td>
								<td><?php echo $LPaLog['sUpdateTime'];?></td>

							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- 操作選項 -->
	<div class="EditBtnBox">
		<div class="EditBtn JqStupidOut" data-showctrl="0">
			<i class="far fa-save"></i>
			<span><?php echo CSUBMIT;?></span>
		</div>
		<a href="<?php echo $aUrl['sBack'];?>" class="EditBtn red">
			<i class="fas fa-times"></i>
			<span><?php echo CBACK;?></span>
		</a>
	</div>
</form>
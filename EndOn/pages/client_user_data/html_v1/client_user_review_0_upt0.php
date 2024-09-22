<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0" enctype="multipart/form-data">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nt" value="<?php echo NOWTIME;?>" />
	<input type="hidden" name="nId" value="<?php echo $aData['nId'];?>" />

	<div class="Information tab">
		<table class="InformationTit">
			<tbody>
				<tr>
					<td class="InformationTitCell JqBtnShowOnly active" style="width:calc(100%/7);" data-showctrl="1">
						<div class="InformationName"><?php echo aREVIEW['TITLENAME']; ?></div>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="InformationScroll">
			<div class="InformationTableBox active" data-show="1">
				<table>
					<thead>
						<tr>
							<th><?php echo aREVIEW['ITEMS'];?></th>
							<th><?php echo aREVIEW['DATAS'];?></th>
							<th><?php echo OPERATE;?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo aREVIEW['METHODS'];?></td>
							<td>
								<?php
								foreach ($aData['aKid'] as $LPnKid)
								{
									?>
									<span><?php echo $aKindData[$LPnKid]['sName0'];?></span>
									<?php
								}
								?>
							</td>
							<td></td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['REALNAME'];?></td>
							<td><?php echo $aData['sName1'];?></td>
							<td>
								<label for="pass0">
									<input class="JqPass" type="radio" id="pass0" name="aPending[0]" value="1" <?php echo $aDataPending['sName1'][1];?>>
									<span><?php echo aREVIEW['PASS'];?></span>
								</label>
								<label for="reject0">
									<input class="JqReject" type="radio" id="reject0" name="aPending[0]" value="99" <?php echo $aDataPending['sName1'][99];?>>
									<span><?php echo aREVIEW['DENY'];?></span>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['BIRTH'];?></td>
							<td>
								<?php echo $aData['sBirthday'];?>
								<?php echo aREVIEW['AGE'].': '.$aData['nAge'];?>
							</td>
							<td>
								<label for="pass1">
									<input class="JqPass" id="pass1" type="radio" name="aPending[1]" value="1" <?php echo $aDataPending['sBirthday'][1];?>>
									<span><?php echo aREVIEW['PASS'];?></span>
								</label>
								<label for="reject1">
									<input class="JqReject" id="reject1" type="radio" name="aPending[1]" value="99" <?php echo $aDataPending['sBirthday'][99];?>>
									<span><?php echo aREVIEW['DENY'];?></span>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['IDNUMBER'];?></td>
							<td><?php echo $aData['sIdNumber'];?></td>
							<td>
								<label for="pass2">
									<input class="JqPass" id="pass2" type="radio" name="aPending[2]" value="1" <?php echo $aDataPending['sIdNumber'][1];?>>
									<span><?php echo aREVIEW['PASS'];?></span>
								</label>
								<label for="reject2">
									<input class="JqReject" id="reject2" type="radio" name="aPending[2]" value="99" <?php echo $aDataPending['sIdNumber'][99];?>>
									<span><?php echo aREVIEW['DENY'];?></span>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['IDFRONT'];?></td>
							<td><img src="<?php echo $aData['sImageUrl0'];?>"></td>
							<td rowspan="2">
								<label for="pass3" >
									<input class="JqPass" id="pass3" type="radio" name="aPending[3]" value="1" <?php echo $aDataPending['sIdImage'][1];?>>
									<span><?php echo aREVIEW['PASS'];?></span>
								</label>
								<label for="reject3">
									<input class="JqReject" id="reject3" type="radio" name="aPending[3]" value="99" <?php echo $aDataPending['sIdImage'][99];?>>
									<span><?php echo aREVIEW['DENY'];?></span>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['IDBACK'];?></td>
							<td><img src="<?php echo $aData['sImageUrl1'];?>"></td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['BANKACCOUNT'];?></td>
							<td>
								<?php
								if (empty($aData['aBank']))
								{
									echo aREVIEW['NOBANKYET'];
								}
								foreach ($aData['aBank'] as $LPnId => $LPaBank)
								{
									?>
									<div>
										<?php echo $aBankName[$LPaBank['nBid']]['sName0'];?>
										(<?php echo $aBankName[$LPaBank['nBid']]['sCode'];?>)
									</div>
									<div><?php echo aREVIEW['BANKBRANCH'];?> : <?php echo $LPaBank['sName2'];?></div>
									<div><?php echo aREVIEW['ACCOUNT'];?> : <?php echo $LPaBank['sName0'];?></div>
									<div><?php echo aREVIEW['BANKNAME'];?> : <?php echo $LPaBank['sName1'];?></div>
									<div><img src="<?php echo $LPaBank['sImageUrl'];?>"></div>
									<?php
								}
								?>
							</td>
							<td>
								<label for="pass4">
									<input class="JqPass" id="pass4" type="radio" name="aPending[4]" value="1" <?php echo $aDataPending['sBankCard'][1];?>>
									<span><?php echo aREVIEW['PASS'];?></span>
								</label>
								<label for="reject4">
									<input class="JqReject" id="reject4" type="radio" name="aPending[4]" value="99" <?php echo $aDataPending['sBankCard'][99];?>>
									<span><?php echo aREVIEW['DENY'];?></span>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['PHOTOS'];?></td>
							<td>
								<?php
								if (empty($aPhoto))
								{
									echo aREVIEW['NOPHOTOYET'];
								}
								foreach ($aPhoto as $LPsPhotoUrl)
								{
									?>
									<img src="<?php echo $LPsPhotoUrl;?>">
									<?php
								}
								?>
							</td>
							<td>
								<label for="pass5" >
									<input class="JqPass" id="pass5" type="radio" name="aPending[5]" value="1" <?php echo $aDataPending['sPhoto'][1];?>>
									<span><?php echo aREVIEW['PASS'];?></span>
								</label>
								<label for="reject5">
									<input class="JqReject" id="reject5" type="radio" name="aPending[5]" value="99" <?php echo $aDataPending['sPhoto'][99];?>>
									<span><?php echo aREVIEW['DENY'];?></span>
								</label>
							</td>
						</tr>
						<tr>
							<td><?php echo aREVIEW['VIDEOS'];?></td>
							<td>
								<?php
								if (empty($aVideo))
								{
									echo aREVIEW['NOVIDEOYET'];
								}
								foreach ($aVideo as $LPsVideoUrl)
								{
									?>
									<video style="width: 100%;" controls="" autoplay="" name="media"><source src="<?php echo $LPsVideoUrl;?>" type="video/mp4"></video>
									<?php
								}
								?>

							</td>
							<td >
								<label for="pass6" >
									<input class="JqPass" id="pass6" type="radio" name="aPending[6]" value="1" <?php echo $aDataPending['sVideo'][1];?>>
									<span><?php echo aREVIEW['PASS'];?></span>
								</label>
								<label for="reject6">
									<input class="JqReject" id="reject6" type="radio" name="aPending[6]" value="99" <?php echo $aDataPending['sVideo'][99];?>>
									<span><?php echo aREVIEW['DENY'];?></span>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td>
								<div class="BtnAny JqPendingAll" data-class="JqPass"><?php echo aREVIEW['PASSALL'];?></div>
								<div class="BtnAny JqPendingAll" data-class="JqReject"><?php echo aREVIEW['DENYALL'];?></div>
							</td>
						</tr>
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
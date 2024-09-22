<?php
	$aData = json_decode($sData,true);
?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aMANUAL['ACCOUNT'];?></span>
			<div class="Ipt">
				<input type="text" name="sAccount" value="<?php echo $sAccount;?>" >
			</div>
		</div>
		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<?php
	if($sAccount != '')
	{
?>
		<div class="Information MarginBottom30">
			<div class="InformationScroll">
				<div class="InformationTableBox">
					<table>
						<thead>
							<tr>
								<th><?php echo aMANUAL['ACCOUNT'];?></th>
								<th><?php echo aMANUAL['MONEY'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo $aData['sAccount'];?></td>
								<td><?php echo $aData['nMoney'];?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- 編輯頁面 -->
		<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
			<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
			<input type="hidden" name="sAccount" value="<?php echo $sAccount;?>">
			<div class="Information ">
				<div class="InformationScroll">
					<div class="InformationTableBox">
						<table>
							<thead>
								<tr>
									<th><?php echo aMANUAL['MONEY'];?></th>
									<th><?php echo aMANUAL['TYPE3']['sTitle'];?></th>
									<th><?php echo aMANUAL['TYPE1']['sTitle'];?></th>
									<th><?php echo aMANUAL['MEMO'];?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="Ipt">
											<input type="number" name="nMoney">
										</div>
									</td>
									<td>
										<div class="Sel">
											<select name="nType3">
												<option value="-1" disabled selected><?php echo PLEASESELECT;?></option>
												<?php
													foreach($aType3 as $LPnStatus => $LPaDetail)
													{
												?>
														<option value="<?php echo $LPnStatus;?>" ><?php echo $LPaDetail['sText'];?></option>
												<?php
													}
												?>
											</select>
										</div>
									</td>
									<td>
										<div class="Sel">
											<select name="nType1">
												<option value="-1" disabled selected><?php echo PLEASESELECT;?></option>
												<?php
													foreach($aType1 as $LPnStatus => $LPaDetail)
													{
												?>
														<option value="<?php echo $LPnStatus;?>" ><?php echo $LPaDetail['sText'];?></option>
												<?php
													}
												?>
											</select>
										</div>
									</td>
									<td>
										<div class="Ipt">
											<input type="text" name="sMemo">
										</div>
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
<?php
	}
?>


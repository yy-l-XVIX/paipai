<?php $aData = json_decode($sData,true);?>
<!-- 純顯示資訊 -->
<form method="POST" action="<?php echo $aUrl['sAct'];?>" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>">
	<div class="MarginBottom20">
		<div class="Ipt">
			<input type="text" name="sName0" value="" placeholder="<?php echo NEWNAME;?>">
		</div>
		<div class="Ipt">
			<input type="text" name="sParam" value="" placeholder="<?php echo NEWPARAM;?>">
		</div>
		<div class="BtnAny JqStupidOut" data-showctrl="0"><?php echo CSUBMIT;?></div>
	</div>
	<div class="Information">
		<table class="InformationTit">
			<tbody>
				<tr>
					<td class="InformationTitCell" style="width:calc(100%/1);">
						<div class="InformationName"><?php echo $sHeadTitle; ?></div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="InformationScroll">
			<div class="InformationTableBox">
				<table>
					<thead>
						<tr>
							<th><?php echo PARAMNAME;?></th>
							<th><?php echo PARAMS;?></th>
							<th><?php echo LASTUPDATETIME;?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach (aPARAM as $LPsDefine => $LPsLangName)
						{
							if (is_array($LPsLangName))
							{
								foreach ($LPsLangName as $LPsKey => $LPsLang)
								{
									if ($LPsKey == 'TITLE')
									{
										echo '<td colspan="3">'.$LPsLang.'</td>';
										continue;
									}
									?>
									<tr>
										<td><?php echo $LPsLang;?></td>
										<td>
											<div class="Ipt">
												<input type="text" name="aParam[<?php echo $aData[$LPsKey]['sName0'];?>]" value="<?php echo $aData[$LPsKey]['sParam'];?>">
											</div>
										</td>
										<td><?php echo $aData[$LPsKey]['sUpdateTime'];?></td>
									</tr>
									<?php
								}
							}
							else
							{
								?>
								<tr>
									<td><?php echo $LPsLangName;?></td>
									<td>
										<div class="Ipt">
											<input type="text" name="aParam[<?php echo $aData[$LPsDefine]['sName0'];?>]" value="<?php echo $aData[$LPsDefine]['sParam'];?>">
										</div>
									</td>
									<td><?php echo $aData[$LPsDefine]['sUpdateTime'];?></td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</form>
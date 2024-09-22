<?php $aData = json_decode($sData,true);?>
<div class="serviceBox">
	<form action="<?php echo $aUrl['sAct'];?>" method="POST" id="JqPostForm">
		<table class="infData">
			<tbody>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit"><?php echo aSERVICE['QUESTIONTOPIC'];?></div>
					</td>
					<td class="infDataCell2">
						<div class="Sel">
							<select name="nKind">
								<?php
								foreach ($aData as $LPnLid => $LPsName0)
								{
									?>
									<option value="<?php echo $LPnLid;?>"><?php echo $LPsName0;?></option>
									<?php
								}
								?>
							</select>
							<div class="SelDecro"></div>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<div><?php echo aSERVICE['CONTENT'];?></div>
							<div class="FontRed"><?php echo aSERVICE['RULE'];?></div>
						</div>
					</td>
					<td class="infDataCell2">
						<div class="Textarea">
							<textarea name="sQuestion"></textarea>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="BtnActBox">
			<div class="BtnAct JqSubmit"><?php echo SUBMIT;?></div>
		</div>
	</form>
</div>
<!-- 忘記密碼 -->
<div class="accessBox">
	<div class="accessBlock">
		<form id="JqForgotForm">
			<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'].'&sJWT='.$sJWT;?>">
			<input type="hidden" name="sCodeAct" value="<?php echo $aUrl['sAct'].'&sJWT='.$sAjaxJWT;?>">
			<div class="accessTopic"><?php echo aFORGOT['PAGEINFO'];?></div>
			<div class="accessData">
				<table class="accessTable">
					<tbody class="accessTbody">
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aFORGOT['ACCOUNT'];?></div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" name="sAccount" placeholder="<?php echo aFORGOT['ACCOUNTREQUIRED'];?>">
								</div>
								<div class="Notice Format JqAccount"><?php echo aERROR['ACCOUNTFORMATE'];?></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="accessBtnBox">
				<a href="javascript:void(0)" class="BtnAct" id="JqSubmit"><?php echo SUBMIT;?></a>
			</div>
		</form>
	</div>
</div>
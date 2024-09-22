<?php $aData = json_decode($sData,true);?>
<!-- 新增銀行帳戶 -->
<form method="post" action="<?php echo $aUrl['sAct'];?>" id="JqPostForm" data-info="* <?php echo aERROR['INFO'];?>">
	<input type="hidden" name="sCodeJWT" value="<?php echo $sCodeJWT;?>">
	<input type="hidden" name="sActJWT" value="<?php echo $sJWT;?>">
	<input type="hidden" name="nSMSTime" value="<?php echo $aSystem['aParam']['nSMSTime'];?>">

	<div class="bankAddBox">
		<table class="infData">
			<tbody>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo ACCOUNT;?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<div class="infDataTxt"><?php echo $aUser['sAccount'];?></div>
					</td>
				</tr>
				<?php
				/* 有需要再打開 功能正常
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aCHANGE['VERIFY'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="number" placeholder="<?php echo aCHANGE['CODEREQUIRED'];?>" name="nVcode" required="required">

							<!-- 若點選獲取驗證碼,VeriBtn+active -->
							<div class="VeriBtn JqGetVcode JqNophone">
								<span class="VeriBtnGet"><?php echo aCHANGE['GETCODE'];?></span>
								<span class="VeriBtnAgain JqCounting"><?php echo aCHANGE['AGAIN'];?></span>
							</div>
						</div>
					</td>
				</tr>
				*/
				?>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aCHANGE['PASSWORDOLD'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="password" name="sTransPassword" placeholder="<?php echo aCHANGE['PASSWORDRULE'];?>" required>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aCHANGE['PASSWORD'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="password" name="sTransPassword0" placeholder="<?php echo aCHANGE['PASSWORDRULE'];?>" required>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aCHANGE['CONFIRMPASSWORD'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="password" name="sTransPassword1" placeholder="<?php echo aCHANGE['PASSWORDRULE'];?>" required>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="BtnActBox">
			<div class="BtnAct JqSubmit"><?php echo SUBMIT;?></div>
		</div>
	</div>
</form>
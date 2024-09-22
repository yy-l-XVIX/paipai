<?php $aData = json_decode($sData,true);?>
<!-- 更改密碼 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nt" value="<?php echo NOWTIME;?>" />

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPASSWORD['ACCOUNT'];?></span>
		<div class="Ipt"><input type="text" disabled value="<?php echo $aAdm['sAccount'];?>"></div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPASSWORD['NAME'];?></span>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aAdm['sName0'];?>" placeholder="<?php echo aPASSWORD['NAME'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPASSWORD['PASSWORD'];?></span>
		<div class="Ipt">
			<input type="password" name="sPassword" placeholder="<?php echo aPASSWORD['PASSWORD'];?>">
		</div>
		<i class="fas fa-question-circle lowupt_notice"></i>
		<span class=""><?php echo aPASSWORD['NOTE'];?></span>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPASSWORD['NEWPASSWORD'];?></span>
		<div class="Ipt">
			<input type="password" name="sNewPassword" placeholder="<?php echo aPASSWORD['NEWPASSWORD'];?>">
		</div>
	</div>
	<div class="Block">
		<span class="InlineBlockTit"><?php echo aPASSWORD['CONFIRMPASSWORD'];?></span>
		<div class="Ipt">
			<input type="password" name="sConfirmPassword" placeholder="<?php echo aPASSWORD['CONFIRMPASSWORD'];?>">
		</div>
	</div>

	<!-- 操作選項 -->
	<div class="EditBtnBox">
		<div class="EditBtn JqStupidOut" data-showctrl="0">
			<i class="far fa-save"></i>
			<span><?php echo aPASSWORD['SUBMIT'];?></span>
		</div>
		<a href="<?php echo $aUrl['sBack'];?>" class="EditBtn red">
			<i class="fas fa-times"></i>
			<span><?php echo aPASSWORD['CANCEL'];?></span>
		</a>
	</div>
</form>

<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nId" value="<?php echo $aData['nId'];?>" />

	<div class="Block MarginBottom20">
		<span class="BlockLineTit"><?php echo ACCOUNT;?></span>
		<span><?php echo $aData['sAccount'];?></span>
	</div>

	<div class="Block MarginBottom20">
		<span class="BlockLineTit"><?php echo aUSER['PASSWORD'];?></span>
		<div class="Ipt">
			<input type="password" name="sPassword" placeholder="<?php echo aUSER['PASSWORDFORMAT'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="BlockLineTit"><?php echo aUSER['TRANSPASSWORD'];?></span>
		<div class="Ipt">
			<input type="password" name="sTransPassword" placeholder="<?php echo aUSER['TRANSPASSWORDFORMAT'];?>">
		</div>
	</div>

	<!-- 操作選項 -->
	<div class="EditBtnBox">
		<!--
		      若要防呆,此div加 JqStupidOut class , 小孩不要放input[type=""submit]
		-->
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
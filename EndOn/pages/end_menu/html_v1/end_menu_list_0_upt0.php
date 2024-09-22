<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nt" value="<?php echo NOWTIME;?>" />
	<input type="hidden" name="nId" value="<?php echo $aData['nId'];?>" />
	<!-- Select -->
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aLIST['STATUS'];?></span>
		<div class="Sel">
			<select name="nOnline">
				<?php
				foreach ($aOnline as $LPnOnline => $LPaOnline)
				{
					?>
					<option value="<?php echo $LPnOnline;?>" <?php echo $LPaOnline['sSelect'];?> >
						<?php echo $LPaOnline['sTitle'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aLIST['MENU'];?></span>
		<div class="Ipt">
			<input type="text" value="<?php echo $aMenuKind[$aData['nMid']]['sMenuName0'];?>" disabled>
		</div>
	</div>

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aLIST['LISTNAME0'];?></span>
		<div class="Ipt">
			<input type="text" value="<?php echo $aData['sListName0'];?>" disabled>
		</div>
	</div>

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aLIST['LISTTABLE0'];?></span>
		<div class="Ipt">
			<input type="text" value="<?php echo $aData['sListTable0'];?>" disabled>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo SORT;?></span>
		<div class="Ipt">
			<input type="text" name="nSort" value="<?php echo $aData['nSort'];?>" >
		</div>
		<i class="fas fa-question-circle lowupt_notice"></i>
		<span><?php echo SORTRULE;?></span>
	</div>
	<div class="Block">
		<span class="InlineBlockTit"><?php echo aLIST['TYPE0'];?></span>
		<span>[ <?php echo $aType0[$aData['nType0']]['sTitle'];?> ]</span>
	</div>

	<!-- 操作選項 -->
	<div class="EditBtnBox">
		<div class="EditBtn JqStupidOut" data-showctrl="0">
			<i class="far fa-save"></i>
			<span><?php echo CSUBMIT;?></span>
		</div>
		<a href="<?php echo $aUrl['sBack'];?>" class="EditBtn red">
			<i class="fas fa-times"></i>
			<span><?php echo aLIST['CANCEL'];?></span>
		</a>
	</div>
</form>
<?php $aData = json_decode($sData,true);?>
<!-- 推廣連結 -->
<div class="promotionBox">
	<table class="promotionTable">
		<tr>
			<td class="promotionPic">

				<!-- 若此人身份為雇主,selfieBox + boss -->
				<a class="selfieBox" href="<?php echo $aUrl['sInf'].'&nId='.$aUser['nId'];?>">
					<img src="<?php echo $aData['sHeadImage'];?>" alt="">
				</a>
			</td>
			<td class="promotionData">
				<a href="?sFolder=center&sPage=inf">
					<div class="MarginBottom5">
						<span>
							<span><?php echo $aUser['sName0'];?></span>
							<span class="promotionKind"><?php echo $aData['sKindName'];?></span>
						</span>
					</div>
					<div>
						<span>
							<span><?php echo ACCOUNT?>:</span>
							<span><?php echo $aUser['sAccount'];?></span>
						</span>
					</div>
				</a>
			</td>
		</tr>
	</table>
	<div class="promotionQrBox">
		<div class="promotionQrInner">
			<div class="promotionQrImg">
				<img src="<?php echo $aData['sQrUrl'];?>" alt="">
			</div>
		</div>
		<div class="BtnActBox">
			<input type="hidden" class="JqCopyUrl" value="<?php echo $aData['sPromoUrl'];?>">
			<div class="BtnAct JqCopy"><?php echo COPYLINK;?></div>
		</div>
	</div>
</div>
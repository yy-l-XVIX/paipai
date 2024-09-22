<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="MarginBottom10">
			<?php
			foreach ($aDay as $LPsText => $LPaDate)
			{
				?>
				<span class="JqDate BtnKind <?php echo $LPaDate['sSelect'];?>" data-day="<?php echo $LPsText;?>" data-date0="<?php echo $LPaDate['sStartDay']?>" data-date1="<?php echo $LPaDate['sEndDay']?>">
					<?php echo aDAYTEXT[$LPsText];?>
				</span>
				<?php
			}
			?>
			<input type="hidden" name="sSelDay" value="<?php echo $sSelDay;?>">
		</div>
		<div class="Block MarginBottom20" >
			<div class="Ipt">
				<input type="text" name="sStartTime" class="JqStartTime" value="<?php echo $sStartTime;?>" autocomplete="off">
			</div>
			<span>~</span>
			<div class="Ipt">
				<input type="text" name="sEndTime" class="JqEndTime" value="<?php echo $sEndTime;?>" autocomplete="off">
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aPAYMENTONLINE['ORDER'];?></span>
			<div class="Ipt">
				<input type="text" name="sOrder" value="<?php echo $sOrder;?>" >
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aPAYMENTONLINE['USER'];?></span>
			<div class="Ipt">
				<input type="text" name="sMemberAccount" value="<?php echo $sMemberAccount;?>" >
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aPAYMENTONLINE['ADMINT'];?></span>
			<div class="Ipt">
				<input type="text" name="sAdmin" value="<?php echo $sAdmin;?>" >
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aPAYMENTONLINE['PAYMENT'];?></span>
			<div class="Sel">
				<select name="nKid">
					<option value="-1" ><?php echo PLEASESELECT;?></option>
					<?php
						foreach($aPayment as $LPnId => $LPaDetail)
						{
					?>
							<option value="<?php echo $LPnId;?>" <?php echo $LPaDetail['sSelect'];?> ><?php echo $LPaDetail['sName0'];?></option>
					<?php
						}
					?>
				</select>
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aPAYMENTONLINE['STATUS']['sTitle'];?></span>
			<div class="Sel">
				<select name="nStatus">
					<option value="-1" ><?php echo PLEASESELECT;?></option>
					<?php
						foreach($aStatus as $LPnId => $LPaDetail)
						{
					?>
							<option value="<?php echo $LPnId;?>" <?php echo $LPaDetail['sSelect'];?> ><?php echo $LPaDetail['sText'];?></option>
					<?php
						}
					?>
				</select>
			</div>
		</div>
		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<!-- 純顯示資訊 -->
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
						<th><?php echo aPAYMENTONLINE['ORDER'];?></th>
						<th><?php echo aPAYMENTONLINE['USER'];?></th>
						<th><?php echo aPAYMENTONLINE['PAYMENT'];?></th>
						<th><?php echo aPAYMENTONLINE['TUNNEL'];?></th>
						<th><?php echo aPAYMENTONLINE['MONEY'];?></th>
						<th><?php echo aPAYMENTONLINE['FEE'];?></th>
						<th><?php echo aPAYMENTONLINE['ADMINT'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnLid => $LPaDetail)
					{
						?>
						<tr>
							<td><?php echo $LPaDetail['sOrder'];?></td>
							<td><?php echo $aMemberData[$LPaDetail['nUid']]['sAccount'];?></td>
							<td><?php echo $LPaDetail['sPayment'];?></td>
							<td><?php echo $LPaDetail['sTunnel'];?></td>
							<td><?php echo number_format($LPaDetail['nMoney']);?></td>
							<td><?php echo number_format($LPaDetail['nFee']);?></td>
							<td><?php echo $aAdminData[$LPaDetail['nAdmin0']]['sAccount'];?></td>
							<td class="<?php echo $LPaDetail['aStatus']['sClass'];?>"><?php echo $LPaDetail['aStatus']['sText'];?></td>
							<td><?php echo $LPaDetail['sCreateTime'];?></td>
							<td><?php echo $LPaDetail['sUpdateTime'];?></td>
							<td>
								<?php
								if($LPaDetail['nStatus'] == 0)
								{
								?>
									<div class="TableBtnBg blue JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaDetail['sPass'];?>">
										<i class="fas fa-pen"></i>
									</div>
									<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaDetail['sCancel'];?>">
										<i class="fas fa-times"></i>
									</div>
								<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td ><?php echo aPAYMENTONLINE['PAGETOTALCOUNT'];?></td>
						<td ><?php echo number_format($aCountData['nPageCount']);?></td>
						<td ><?php echo aPAYMENTONLINE['PAGETOTALMONEY'];?></td>
						<td ><?php echo number_format($aCountData['nPageMoney']);?></td>
						<td colspan="7"></td>
					</tr>
					<tr>
						<td ><?php echo aPAYMENTONLINE['TOTALCOUNT'];?></td>
						<td ><?php echo number_format($aCountData['nTotalCount']);?></td>
						<td ><?php echo aPAYMENTONLINE['TOTALMONEY'];?></td>
						<td ><?php echo number_format($aCountData['nTotalMoney']);?></td>
						<td colspan="7"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>
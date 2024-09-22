<?php
	$aData = json_decode($sData,true);
?>
<div class="bankListBox">
	<?php
	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}

	foreach($aData as $LPnId => $LPaDetail)
	{
	?>
		<div class="bankListBlock" data-bid="<?php echo $LPnId;?>">
			<table class="bankListTop">
				<tbody>
					<tr>
						<td class="bankListName"><?php echo $LPaDetail['sBank'];?></td>
						<td class="bankListBtn">
							<?php
							if ($aUser['nStatus'] == 11 )
							{
								?>
								<a href="javascript:void(0);" class="JqDeleteBank" data-href="<?php echo $LPaDetail['sDelUrl'];?>"><?php echo DEL;?></a>
								<?php
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="bankListImg">
				<img src="<?php echo base64Pic($aImage[$LPnId]);?>" alt="">
			</div>
			<div class="bankListData">
				<div class="bankListDataRow">
					<span><?php echo aBANKLIST['CARDNUM'];?>:</span>
					<span><?php echo $LPaDetail['sName0'];?></span>
				</div>
				<div class="bankListDataRow">
					<span><?php echo aBANKLIST['CARDNAME'];?>:</span>
					<span><?php echo $LPaDetail['sName1'];?></span>
				</div>
				<div class="bankListDataRow">
					<span><?php echo aBANKLIST['BANKBRANCH'];?>:</span>
					<span><?php echo $LPaDetail['sName2'];?></span>
				</div>
			</div>
		</div>
	<?php
	}
	?>
</div>
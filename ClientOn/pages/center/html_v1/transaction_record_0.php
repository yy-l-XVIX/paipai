<?php $aData = json_decode($sData,true);?>
<!-- 交易紀錄 -->
<div class="HavePageBox">
	<div class="transactionRecordBox">
		<?php
		/*
		?>
		<div class="transactionRecordKindBtnBox">
			<?php
			foreach ($aType2 as $LPnType2 => $LPaType2)
			{
				?>
				<!-- 當頁+active -->
				<a class="transactionRecordKindBtn <?php echo $LPaType2['sSelect'];?>" href="<?php echo $aUrl['sPage'].'&nType2='.$LPnType2;?>">
					<div class="transactionRecordKindBtnTxt"><?php echo $LPaType2['sText'];?></div>
				</a>
				<?php
			}
			?>
		</div>
		*/
		?>
		<div class="transactionRecordSearchBox">
			<form action="<?php echo $aUrl['sPage'].'&nType2='.$nType2;?>" method="POST">
				<table class="FormSearchDateTable">
					<tbody>
						<tr>
							<td class="FormSearchDateTd">
								<div class="Ipt">
									<input class="JqStartTime" type="text" name="sStartTime" value="<?php echo $sStartTime;?>" autocomplete="off">
									<i class="fas fa-calendar-alt"></i>
								</div>
							</td>
							<td class="FormSearchDateTdTxt">
								<div class="FormSearchDateTxt"><?php echo aRECORD['TO'];?></div>
							</td>
							<td class="FormSearchDateTd">
								<div class="Ipt">
									<input class="JqEndTime" type="text" name="sEndTime" value="<?php echo $sEndTime;?>" autocomplete="off">
									<i class="fas fa-calendar-alt"></i>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="FormSearchTable">
					<tbody>
						<tr>
							<td style="width:100%;">
								<div class="Ipt">
									<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo aRECORD['SEARCHACCOUNT']?>">
								</div>
							</td>
							<td>
								<div class="FormSearchBtn">
									<input type="submit">
									<div class="FormSearchBtnTxt"><i class="fas fa-search"></i></div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		<div class="FormBox">
			<table class="FormTable">

				<thead>
					<tr>
						<th><?php echo aRECORD['DATE'];?></th>
						<th><?php echo aRECORD['MEMO'];?></th>
						<th><?php echo aRECORD['MONEY'];?></th>
						<th><?php echo aRECORD['BALANCE'];?></th>
						<th><?php echo aRECORD['STATUS'];?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnId => $LPaDetail)
					{
						?>
						<tr>
							<td class="FormTdDate TextAlignRight">
								<div class="WordBreakBreakAll">
									<div><?php echo date('Y-m-d',$LPaDetail['nCreateTime']);?></div>
									<div class="FormFontTime"><?php echo date('H:i:s',$LPaDetail['nCreateTime']);?></div>
								</div>
							</td>
							<td>
								<div class="WordBreakBreakAll"><?php echo $LPaDetail['sMemo'];?></div>
							</td>
							<td>
								<div class="WordBreakBreakAll">
									<?php
									if($LPaDetail['nMoney'] < 0)
									{
										#負數
										echo '<span class="FormFontNeg">'.number_format($LPaDetail['nMoney']).'</span>';
									}
									else
									{
										#正數
										echo '<span class="FormFontPos">'.number_format($LPaDetail['nMoney']).'</span>';
									}
									?>
								</div>
							</td>
							<td>
								<div class="WordBreakBreakAll"><?php echo number_format($LPaDetail['nAfter']);?></div>
							</td>
							<td>
								<div><?php echo $LPaDetail['sType2'];?></div>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td><?php echo aRECORD['SUBTOTAL'];?></td>
						<?php
						if($aTotalData['nSubTotal'] < 0)
						{
							#負數
							echo '<td class="FormFontNeg">'.number_format($aTotalData['nSubTotal']).'</td>';
						}
						else
						{
							#正數
							echo '<td class="FormFontPos">'.number_format($aTotalData['nSubTotal']).'</td>';
						}
						?>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td></td>
						<td><?php echo aRECORD['TOTAL'];?></td>
						<?php
						if($aTotalData['nTotal'] < 0)
						{
							#負數
							echo '<td class="FormFontNeg">'.number_format($aTotalData['nTotal']).'</td>';
						}
						else
						{
							#正數
							echo '<td class="FormFontPos">'.number_format($aTotalData['nTotal']).'</td>';
						}
						?>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>
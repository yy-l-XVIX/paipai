<div class="accountRecordBox">
	<?php
	foreach($aAccountRecord as $LPnId => $LPaData)
	{
	?>
		<div class="accountRecordBlock">
			<div class="accountRecordBlockTopic">
				<div class="accountRecordBlockTopicTxt WordBreakBreakAll"><?php echo $LPaData['sTitle']; ?></div>
			</div>
			<div class="accountRecordBlockListBox">
				<?php
				foreach($LPaData['aList'] as $LPsKey => $LPaPage)
				{
				?>
					<a href="<?php echo $LPaPage['sUrl']; ?>" class="accountRecordBlockList">
						<table class="accountRecordBlockListTable">
							<tbody>
								<tr>
									<td class="accountRecordBlockListTdTit">
										<div class="accountRecordBlockListTit WordBreakBreakAll"><?php echo $LPaPage['sTitle']; ?></div>
									</td>
									<td class="accountRecordBlockListTdDecro">
										<i class="fas fa-chevron-right"></i>
									</td>
								</tr>
							</tbody>
						</table>
					</a>
				<?php
				}
				?>
			</div>
		</div>
	<?php
	}
	?>
</div>
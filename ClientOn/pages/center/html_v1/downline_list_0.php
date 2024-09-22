<?php $aData = json_decode($sData,true);?>
<!-- 歸屬下線 -->
<div class="HavePageBox">
	<div class="downlineListBox">
		<div class="downlineListSearchBox">
			<form action="<?php echo $aUrl['sPage'];?>" method="POST">
				<table class="FormSearchTable">
					<tbody>
						<tr>
							<td style="width:100%;">
								<div class="Ipt">
									<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo aDOWNLINE['SEARCHACCOUNT'];?>">
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
			<?php
			if (empty($aData))
			{
				echo '<div class="NoData">'.NODATAYET.'</div>';
			}
			else
			{
				?>
				<table class="FormTable">
					<thead>
						<tr>
							<th><?php echo aDOWNLINE['ACCOUNT'];?></th>
							<th><?php echo aDOWNLINE['METHOD'].'/'.aDOWNLINE['EXPIRETIME'];?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($aData as $LPnUid => $LPaData)
						{
							?>
							<tr>
								<td>
									<div class="WordBreakBreakAll"><?php echo $LPaData['sAccount'];?></div>
								</td>
								<td class="TextAlignLeft">
									<?php

									foreach ($LPaData['aKid'] as $LPnKid)
									{

										$LPnExpired = $LPaData['nExpired0']; #staff
										if ($LPnKid == 1)
										{
											$LPnExpired = $LPaData['nExpired1']; #boss
										}
										?>
										<div class="WordBreakBreakAll downlineListKind">
											<span class="downlineListRole <?php echo $LPnKid == 1?'boss':'staff';?>"><?php echo $aKindData[$LPnKid]['sName0'];?></span>
											<?php
											if ($LPnExpired > NOWTIME)
											{
												#未到期
												echo '<span>'.date('Y-m-d',$LPnExpired).'</span>';
											}
											else
											{
												#已到期
												if ($LPnExpired == 0)
												{

													echo '<span class="FormFontRed">'.aDOWNLINE['UNPAY'].'</span>';
												}
												else
												{
													echo '<span class="FormFontRed">'.date('Y-m-d',$LPnExpired).'('.aDOWNLINE['EXPIRED'].')</span>';
												}
											}
											?>
										</div>
										<?php
									}
									?>
									<?php
									/*
									if ($LPaData['nExpired0'] > 0)
									{
										?>
										<div class="WordBreakBreakAll downlineListKind">
											<span class="downlineListRole staff"><?php echo aDOWNLINE['STAFF'];?></span>
											<?php
											if($LPaData['nExpired0'] > NOWTIME)
											{
												#到期
												echo '<span>'.date('Y-m-d',$LPaData['nExpired0']).'</span>';
											}
											else
											{
												#已到期
												echo '<span class="FormFontRed">'.date('Y-m-d',$LPaData['nExpired0']).'('.aDOWNLINE['EXPIRED'].')</span>';
											}
											?>
										</div>
										<?php
									}
									if ($LPaData['nExpired1'] > 0)
									{
										?>
										<div class="WordBreakBreakAll downlineListKind">
											<span class="downlineListRole boss"><?php echo aDOWNLINE['BOSS'];?></span>
											<?php
											if($LPaData['nExpired1'] > NOWTIME)
											{
												#到期
												echo '<span>'.date('Y-m-d',$LPaData['nExpired1']).'</span>';
											}
											else
											{
												#已到期
												echo '<span class="FormFontRed">'.date('Y-m-d',$LPaData['nExpired1']).'('.aDOWNLINE['EXPIRED'].')</span>';
											}
											?>
										</div>
										<?php
									}
									*/
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<?php
			}
			?>
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>
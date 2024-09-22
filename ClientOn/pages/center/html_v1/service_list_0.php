<?php $aData = json_decode($sData,true);?>
<!-- 購買紀錄 -->
<div class="HavePageBox">
	<div class="transactionRecordKindBtnBox">
		<a class="BtnAny2" href="<?php echo $aUrl['sService'];?>"><?php echo aSERVICELIST['TITLE'];?></a>
	</div>
	<div class="rechargeListBox">
		<div class="rechargeListSearchBox">
			<form action="<?php echo $aUrl['sPage'];?>" method="POST">
				<table class="FormSearchDateTable">
					<tbody>
						<tr>
							<td class="FormSearchDateTd">
								<div class="Ipt">
									<input class="JqStartTime" type="text" name="sStartTime" value="<?php echo $sStartTime;?>">
									<i class="fas fa-calendar-alt"></i>
								</div>
							</td>
							<td class="FormSearchDateTdTxt">
								<div class="FormSearchDateTxt"><?php echo aSERVICELIST['TO'];?></div>
							</td>
							<td class="FormSearchDateTd">
								<div class="Ipt">
									<input class="JqEndTime" type="text" name="sEndTime" value="<?php echo $sEndTime;?>">
									<i class="fas fa-calendar-alt"></i>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="FormSearchTable">
					<tbody>
						<tr>
							<td style="width:50%;">
								<div class="Sel">
									<select name="nKid" >
										<option value="0"><?php echo aSERVICELIST['SELECTKIND'];?></option>
										<?php
										foreach ($aKind as $LPnKid => $LPaKind)
										{
											?>
											<option value="<?php echo $LPnKid;?>" <?php echo $LPaKind['sSelect'];?>><?php echo $LPaKind['sName0'];?></option>
											<?php
										}
										?>
									</select>
									<div class="SelDecro"></div>
								</div>
							</td>
							<td style="width:50%;">
								<div class="Ipt">
									<input type="text" name="sContent" value="<?php echo $sContent;?>" placeholder="<?php echo aSERVICELIST['CONTENT'];?>">
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
			<table class="FormTable noFoot">
				<input type="hidden" name="sPage" value="<?php echo $aUrl['sPage'];?>">
				<thead>
					<tr>
						<th><?php echo aSERVICELIST['TIME'];?></th>
						<th style="width:38%;"><?php echo aSERVICELIST['QUESTIONTOPIC'].'/'.aSERVICELIST['QUESTION'];?></th>
						<th style="width:38%;"><?php echo aSERVICELIST['RESPONSE'];?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (empty($aData))
					{
					?>
						<tr>
							<td colspan="4" style="border-radius: 0 0 20px 20px;"><?php echo NODATAYET;?></td>
						</tr>
					<?php
					}
					foreach($aData as $LPnId => $LPaDetail)
					{
					?>
						<tr>
							<td class="FormTdDate TextAlignRight">
								<div class="WordBreakBreakAll">
									<div><?php echo $LPaDetail['sCreateDate'];?></div>
									<div class="FormFontTime"><?php echo $LPaDetail['sCreateTime'];?></div>
								</div>
							</td>
							<td class="TextAlignLeft">
								<div class="WordBreakBreakAll">
									<div><?php echo $aKind[$LPaDetail['nKid']]['sName0'];?></div>	
									<div><?php echo $LPaDetail['sQuestion'];?></div>	
								</div>
							</td>
							<td class="FormFontRed TextAlignLeft">
								<div class="WordBreakBreakAll"><?php echo $LPaDetail['sResponse'];?></div>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>
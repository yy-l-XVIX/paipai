<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage']?>" method="post" class="MarginBottom20">
	<div class="InlineBlock MarginRight5">
		<div class="InlineBlockTit"><?php echo ACCOUNT;?></div>
		<div class="Ipt">
			<input type="text" name="sAccount" value="<?php echo $sAccount;?>">
		</div>
		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<div class="familyBox">
	<div class="familOneBox">
		<?php
		foreach($aData['aAncestor'] as $LPnId => $LPaData)
		{
		?>
			<a href="<?php echo $aUrl['sPage'].'&sAccount='.$LPaData['sAccount'];?>" class="familOne <?php echo $LPaData['sClass'];?>"  style="margin-left:<?php echo ($LPnId)*100; ?>px;">
				<div class="familBlock">
					<div class="familBlockInner">
						<div class="familBlockLine">
							<div class="familBlockLineTit <?php echo $LPaData['sClass'];?>"><?php echo ACCOUNT;?></div>
							<div class="familBlockLineTxt <?php echo $LPaData['sClass'];?>"><?php echo $LPaData['sAccount'];?></div>
						</div>
						<div class="familBlockLine">
							<div class="familBlockLineTit <?php echo $LPaData['sClass'];?>"><?php echo aLINK['NAME'];?></div>
							<div class="familBlockLineTxt <?php echo $LPaData['sClass'];?>"><?php echo $LPaData['sName0'];?></div>
						</div>
						<div class="familBlockLine">
							<div class="familBlockLineTit <?php echo $LPaData['sClass'];?>"><?php echo aLINK['MONEY'];?></div>
							<div class="familBlockLineTxt <?php echo $LPaData['sClass'];?>"><?php echo $LPaData['nMoney'];?></div>
						</div>
						<div class="familBlockLine">
							<div class="familBlockLineTit <?php echo $LPaData['sClass'];?>"><?php echo aLINK['TEAMFIRST'];?></div>
							<div class="familBlockLineTxt <?php echo $LPaData['sClass'];?>"><?php echo $LPaData['nTeamFirst'];?></div>
						</div>
						<div class="familBlockLine">
							<div class="familBlockLineTit <?php echo $LPaData['sClass'];?>"><?php echo aLINK['TEAMSECOND'];?></div>
							<div class="familBlockLineTxt <?php echo $LPaData['sClass'];?>"><?php echo $LPaData['nTeamSecond'];?></div>
						</div>
					</div>
				</div>
			</a>
		<?php
		}
		?>
	</div>

	<?php
	if(count($aData['aSon']))
	{
	?>
		<div class="familyTable Table" style="width:calc(100% - <?php echo ($aData['nAncestor'])*100; ?>px);margin-left:<?php echo ($aData['nAncestor'])*100; ?>px;">
			<div>
				<?php
				$nCount = 1;
				$nTrAmount = 6; #一行數量
				foreach($aData['aSon'] as $LPnId => $LPaData)
				{
					if($nCount%$nTrAmount==1)
					{
						echo '<div>';
					}
				?>
					<a href="<?php echo $aUrl['sPage'].'&sAccount='.$LPaData['sAccount'];?>" class="familBlock" style="width:calc(100%/<?php echo $nTrAmount; ?>);">
						<div class="familBlockInner">
							<div class="familBlockLine">
								<div class="familBlockLineTit"><?php echo ACCOUNT;?></div>
								<div class="familBlockLineTxt"><?php echo $LPaData['sAccount'];?></div>
							</div>
							<div class="familBlockLine">
								<div class="familBlockLineTit"><?php echo aLINK['NAME'];?></div>
								<div class="familBlockLineTxt"><?php echo $LPaData['sName0'];?></div>
							</div>
							<div class="familBlockLine">
								<div class="familBlockLineTit"><?php echo aLINK['MONEY'];?></div>
								<div class="familBlockLineTxt"><?php echo $LPaData['nMoney'];?></div>
							</div>
							<div class="familBlockLine">
								<div class="familBlockLineTit"><?php echo aLINK['TEAMFIRST'];?></div>
								<div class="familBlockLineTxt"><?php echo $LPaData['nTeamFirst'];?></div>
							</div>
							<div class="familBlockLine">
								<div class="familBlockLineTit"><?php echo aLINK['TEAMSECOND'];?></div>
								<div class="familBlockLineTxt"><?php echo $LPaData['nTeamSecond'];?></div>
							</div>
						</div>
					</a>
				<?php
					if($nCount%$nTrAmount==0)
					{
						echo '</div>';
					}
					$nCount ++;
				}
				if($aData['nSon']%$nTrAmount!=0)
				{
					for($nAddBlock=1;$nAddBlock<=($nTrAmount-$aData['nSon']%$nTrAmount);$nAddBlock++)
					{
						echo '<div></div>';
					}
					echo '</div>';
				}
				?>
			</div>
		</div>
	<?php
	}
	?>
</div>
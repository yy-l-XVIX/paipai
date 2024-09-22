<?php $aData = json_decode($sData,true);?>
<?php
if (false)
{
?>
<!-- 首頁 -->
<div class="indexView">
	<span class="indexViewTopic"><?php echo aINDEX['ONLINE'];?></span>
	<?php
	foreach ($aData['aKind'] as $LPnKid => $LPaKind)
	{
		?>
		<div class="indexViewKind">
			<div class="indexViewKindTit"><?php echo $LPaKind['sName0'];?></div>
			<div class="indexViewKindNum"><?php echo $LPaKind['nCount'];?></div>
		</div>
		<?php
	}
	?>
</div>
<?php
}
?>
<div class="indexChooseBox">
	<table class="indexChooseTable">
		<tbody>
			<?php
			$LPnI = 1;
			foreach ($aData['aLocation'] as $LPnId => $LPaLocation)
			{
				if($LPnI%2==1)
				{
					echo '<tr>';
				}
			?>
				<td>
					<a href="<?php echo $aUrl['sList'].'&nLid='.$LPaLocation['nLid'];?>" class="indexChooseBlock BG" style="<?php echo $LPaLocation['sImgUrl'];?>;">
						<div class="indexChooseTxt"><?php echo $LPaLocation['sName0'];?></div>
					</a>
				</td>
			<?php
				if($LPnI%2==0)
				{
					echo '</tr>';
				}
				$LPnI++;
			}
			if(($LPnI-1)%2==1)
			{
				echo '<td></td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
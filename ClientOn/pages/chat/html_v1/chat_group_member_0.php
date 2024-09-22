<?php $aData = json_decode($sData,true);?>
<!-- 聊天 -->
<header>
	<form action="<?php echo $aUrl['sPage'];?>" method="POST">
		<div class="headerContainer TextAlignLeft">
			<a href="<?php echo $aUrl['sBack'];?>" class="headerIcon headerLeft">
				<i class="fas fa-arrow-left"></i>
			</a>
			<div class="headerFindIpt Ipt">
				<input type="text" name="sName0" placeholder="<?php echo SEARCH;?>" value="<?php echo $sName0;?>">
			</div>
			<div class="headerFindBtn headerRight0">
				<input type="submit">
				<i class="fas fa-search"></i>
			</div>
		</div>
	</form>
</header>
<div class="chatGroupBox JqAppend">
	<table class="chatGroupUptTable">
		<tbody>
			<?php
			#一列5個,不足5個要補齊td,
			$LPnI = 1;
			foreach ($aData as $LPnUid => $LPaMember)
			{
				if ($LPnI % 5 == 1)
				{
					echo '<tr>';
				}
				?>
				<td>
					<a href="<?php echo $LPaMember['sInfUrl'];?>" class="chatGroupUptUserBtn">

						<div class="selfieBox JqBtnSize <?php echo $LPaMember['sRole']; //若此人身份為雇主,selfieBox + boss?> BG" style="background-image: url('<?php echo $LPaMember['sHeadImage'];?>');"></div>
						<div class="chatGroupUptName"><?php echo $LPaMember['sName0'];?></div>
					</a>
				</td>
				<?php
				if ($LPnI % 5 == 0)
				{
					echo '</tr>';
				}
				$LPnI++;
			}
			if(sizeof($aData) % 5 != 0)
			{
				$nAdd1 = 0;
				for($nAdd=$nAdd1;$nAdd<(5-(sizeof($aData)%5));$nAdd++)
				{
					echo '<td></td>';
				}
			}
			else
			{
				?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>

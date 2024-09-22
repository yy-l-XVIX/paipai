<?php $aData = json_decode($sData,true);?>
<!-- 編輯群組 -->
<input type="hidden" class="JqChange" value="<?php echo $aData['sHeadName'];?>">
<input type="hidden" class="JqBackUrl" value="<?php echo $aUrl['sBack'];?>">
<div class="chatGroupUptBox">
	<table class="chatGroupUptTable">
		<tbody>
			<?php
			#一列5個,不足5個要補齊td,最後一個會員下一個為"新增按鈕"
			$LPnI = 1;
			foreach ($aData['aMember'] as $LPnUid => $LPaMember)
			{
				if ($LPnUid == $aUser['nId'] && $aData['nType0'] == 0)
				{
					// 私聊不顯示自己
					continue;
				}
				if ($LPnI % 5 == 1)
				{
					echo '<tr>';
				}
				?>
				<td>
					<a href="<?php echo $LPaMember['sInfUrl'];?>" class="chatGroupUptUserBtn">
						<!-- 若此人身份為雇主,selfieBox + boss -->
						<div class="selfieBox JqBtnSize <?php echo $LPaMember['sRole'];?> BG" style="background-image: url('<?php echo $LPaMember['sHeadImage'];?>');"></div>
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

			if(sizeof($aData['aMember']) % 5 != 0)
			{
				$nAdd1 = 0;
				if ($aData['nType0'] == 1)
				{
					$nAdd1 = 1;
					echo	'<td>';
					echo	'<a href="'.$aUrl['sAdd'].'" class="chatGroupUptAddUser JqBtnAddSize">
							<span>+</span>
						 </a>';
					echo	'</td>';
				}
				for($nAdd=$nAdd1;$nAdd<(5-(sizeof($aData['aMember'])%5));$nAdd++)
				{
					echo '<td></td>';
				}
			}
			else
			{
				?>
				<tr>
					<td>
						<?php
						if ($aData['nType0'] == 1)
						{
							?>
							<a href="<?php echo $aUrl['sAdd'];?>" class="chatGroupUptAddUser JqBtnAddSize">
								<span>+</span>
							</a>
							<?php
						}
						?>
					</td>
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
<?php
if ($bMoreMember)
{
	?>
	<div class="chatGroupUptSeeBox">
		<a class="chatGroupUptSeeBtn" href="<?php echo $aUrl['sMoreMember'];?>">
			<span><?php echo aGROUP['CHECKMEMBER'];?></span>
			<i class="fas fa-chevron-right"></i>
		</a>
	</div>
	<?php
}
?>


	<!-- 卷軸到底後,Loading時出現, class + active -->
	<div class="LoadingBox">
		<div class="Loading">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
		</div>
	</div>
</div>
<?php
if ($aData['nType0'] != 0)
{
	?>
	<table class="infData">
		<tbody>
			<tr>
				<td class="infDataCell1">
					<a class="infDataTit" href="<?php echo $aUrl['sAdd'];?>"><?php echo aGROUP['NAME0'];?></a>
				</td>
				<td class="infDataCell2">
					<a class="infDataArrow" href="<?php echo $aUrl['sAdd'];?>">
						<span class="chatGroupUptDontknow"><?php echo $aData['sName0'];?></span>
						<i class="fas fa-chevron-right"></i>
					</a>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<a class="infDataTit" href="<?php echo $aUrl['sAnnounce'];?>"><?php echo aGROUP['ANNOUNCE'];?></a>
				</td>
				<td class="infDataCell2">
					<a class="infDataArrow" href="<?php echo $aUrl['sAnnounce'];?>">
						<span class="chatGroupUptDontknow"><?php echo $aData['sContent0'];?></span>
						<i class="fas fa-chevron-right"></i>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
?>
<div class="chatGroupUptBtnBox">
	<?php
	// 建立群組會員才有權限
	if ($aData['nUid'] == $aUser['nId'] && $aData['nType0'] != 0)
	{
		?>
		<div class="chatGroupUptBtn JqGroupBtnPick">
			<?php echo aGROUP['KICKMEMBER'];?>
		</div>
		<?php
	}
	?>
	<div class="chatGroupUptBtn MarginBottom5 JqActStupidOut" data-msg="<?php echo aGROUP['SUREDELMSG'];?>" data-act="<?php echo $aData['sDelMsgUrl'];?>">
		<?php echo aGROUP['DELMSG'];?>
	</div>
	<?php
	if ($aData['nType0'] != 0)
	{
		?>
		<div class="chatGroupUptBtn MarginBottom5 JqActStupidOut" data-msg="<?php echo aGROUP['SURELEAVE'];?>" data-act="<?php echo $aData['sExitGroupUrl'];?>">
			<?php echo aGROUP['LEAVEGROUP'];?>
		</div>
		<?php
	}
	?>
</div>
<?php
if ($aData['nUid'] == $aUser['nId'])
{
	?>
	<div class="WindowBox JqWindowBox JqGroupPickBox">
		<header>
			<div class="headerContainer">
				<div class="headerIcon headerLeft JqClose">
					<i class="fas fa-arrow-left"></i>
				</div>
			</div>
		</header>
		<div class="myjobPickListBox">
			<?php
			foreach ($aData['aMember'] as $LPnUid => $LPaMember)
			{
				if ($LPnUid == $aData['nUid'])
				{
					continue;
				}
				?>
				<div class="myjobPickList">
					<table class="myjobPickTable">
						<tbody>
							<tr>
								<td class="myjobPickTdPic JqMyjobPickBtn">
									<div class="selfieBox BG" style="background-image: url('<?php echo $LPaMember['sHeadImage']; ?>');"></div>
								</td>
								<td class="myjobPickTdName JqMyjobPickBtn">
									<div><?php echo $LPaMember['sName0']; ?></div>
								</td>
								<td class="myjobPickTdIcon FontRed JqActStupidOut" data-msg="<?php echo aGROUP['SUREDELETE'];?>" data-act="<?php echo $aData['sKickOutUrl'].'&nUid='.$LPnUid; ?>">
									<div><i class="fas fa-times-circle"></i></div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
			}
			?>
		</div>
		<div class="WindowBg"></div>
	</div>
	<?php
}
?>

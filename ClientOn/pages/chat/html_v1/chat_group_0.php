<?php $aData = json_decode($sData,true);?>
<!-- 聊天 -->
<header>
	<form action="<?php echo $aUrl['sPage'];?>" method="POST">
		<div class="headerContainer TextAlignLeft">

			<div class="headerFindIpt Ipt">
				<input type="text" name="sName0" placeholder="<?php echo aCHAT['NAME'];?>" value="<?php echo $sName0;?>">
			</div>
			<div class="headerFindBtn headerRight0">
				<input type="submit">
				<i class="fas fa-search"></i>
			</div>
		</div>
	</form>
</header>
<div class="chatGroupBtnBox">
	<a class="chatGroupBtn" href="<?php echo $aUrl['sGroupAdd'];?>"><?php echo aCHAT['ADD'];?></a>
</div>
<input type="hidden" name="sFetch" value="<?php echo $aUrl['sFetch'];?>">
<input type="hidden" name="nPageNo" value="<?php echo ($aPage['nNowNo']+1);?>">
<div class="chatGroupBox JqAppend">
	<?php
	foreach ($aData as $LPnId => $LPaData)
	{
		?>
		<div class="chatGroupList">
			<table class="chatGroupTable">
				<tbody>
					<tr>
						<td class="chatGroupTdPic">
							<a class="" href="<?php echo $LPaData['sInsUrl'];?>">
							<div class="chatGroupFolder">
								<table class="chatGroupFolderTable">
									<tbody><?php echo $LPaData['sGroupImgHtml'];?></tbody>
								</table>
								<div class="chatGroupFolderNotice DisplayBlockNone JqCheckGroupMessage <?php echo $LPaData['sSelfNotice'];?>" data-gid="<?php echo $LPaData['nId'];?>">N</div>
							</div>
							</a>
						</td>
						<td class="chatGroupTdName">
							<a class=" " href="<?php echo $LPaData['sInsUrl'];?>">
								<div><?php echo $LPaData['sName0']; ?></div>
							</a>
						</td>
						<td class="chatGroupTdPic">
							<div class="BtnAny JqStupidOut JqReplaceS" style="float: right;" data-showctrl="0" data-replace="<?php echo $LPaData['sDelUrl'];?>"><?php echo aCHAT['DELCHAT'];?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
	?>
</div>
<div class="DisplayNone JqCopy">
	<div class="chatGroupList">
		<table class="chatGroupTable">
			<tbody>
				<tr>
					<td class="chatGroupTdPic">
						<a class="" href="[[::sInsUrl::]]">
						<div class="chatGroupFolder">

							[[::sGroupImgHtml::]]

							<div class="chatGroupFolderNotice DisplayBlockNone JqCheckGroupMessage [[::sSelfNotice::]]" data-gid="[[::nId::]]">N</div>
						</div>
						</a>
					</td>
					<td class="chatGroupTdName">
						<a class=" " href="[[::sInsUrl::]]">
							<div>[[::sName0::]]</div>
						</a>
					</td>
					<td class="chatGroupTdPic">
						<div class="BtnAny JqStupidOut JqReplaceS" style="float: right;" data-showctrl="0" data-replace="[[::sDelUrl::]]"><?php echo aCHAT['DELCHAT'];?></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
	require_once('inc/#Top.php');
?>
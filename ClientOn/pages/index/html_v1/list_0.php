<?php $aData = json_decode($sData,true);?>
<header>
	<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="JqSearchForm">
		<div class="headerContainer TextAlignLeft">
			<input type="hidden" name="sArea" value="<?php echo $sArea;?>">
			<input type="hidden" name="sType" value="<?php echo $sType;?>">
			<input type="hidden" name="nFavorite" value="<?php echo $nFavorite;?>">
			<a href="<?php echo $aUrl['sIndex'];?>" class="headerIcon headerLeft">
				<i class="fas fa-arrow-left"></i>
			</a>
			<div class="headerFindIpt Ipt">
				<input type="text" placeholder="<?php echo aLIST['PLACEHOLDER'];?>" name="sName0" value="<?php echo $sName0;?>" inputmode="search">
			</div>
			<div class="headerFindBtn headerRight0">
				<input type="submit">
				<i class="fas fa-search"></i>
			</div>
		</div>
	</form>
</header>

<!-- 搜尋工作 -->
<div class="findFilterBox">
	<div class="findFilterTopic">
		<span><?php echo aLIST['TOTAL'];?></span>
		<span><?php echo $aPage['nDataAmount'];?></span>
		<span><?php echo aLIST['PIECE'];?></span>
	</div>
	<div class="findFilterKindCell">

			<!-- 若此過濾種類內有勾選項目,findFilterKindBtn + active -->
			<div class="findFilterKindBtn JqFindFilterBtnKind <?php echo $sType!=''?'active':'';?>" data-filter="Type"><?php echo aLIST['TYPE'];?></div>

			<!-- 若此過濾種類內有勾選項目,findFilterKindBtn + active -->
			<div class="findFilterKindBtn JqFindFilterBtnKind <?php echo $sArea!=''?'active':'';?>" data-filter="Area"><?php echo aLIST['LOCATION'];?></div>
			<?php
			if($sUserCurrentRole == 'staff')
			{
				?>
				<!-- 若種類被選取,findFilterKindBtn + active -->
				<div class="findFilterKindBtn JqFavoriteFilter <?php echo $nFavorite==1?'active':'';?>"><?php echo aLIST['FAVORITE'];?></div>
				<?php
			}
			?>
	</div>
	<div style="clear:both;"></div>
</div>
<?php
/*
<!-- 過濾器 -->
<div class="WindowBox JqWindowBox JqSelectBox" data-filter="Area">
	<div class="WindowSelectBox">
		<div class="WindowSelectTop">
			<div class="WindowSelectTopBtn left JqSelectBtnClear"><?php echo aLIST['CLEAR'];?></div>
			<div class="WindowSelectTopTit JqApply"><?php echo aLIST['APPLY'];?><?php #echo aLIST['PLEASESELECT'];?></div>
			<div class="WindowSelectTopBtn right JqClose"><?php echo aLIST['CANCEL'];?></div>
		</div>
		<div class="WindowSelectItemBox">
			<?php
			foreach ($aCityArea as $LPnCid => $LPaCity)
			{
			?>
				<div class="WindowSelectItem city JqCityKind" data-cityctrl="<?php echo $LPnCid; ?>">
					<div class="WindowSelectItemTxt JqSelectItemTxt"><?php echo $LPaCity['sName0'];?></div>
					<div class="WindowSelectItemSetting">
						<input type="checkbox" id="city<?php echo $LPnCid; ?>" value="<?php echo $LPnCid; ?>" class="JqCityCheck">
						<label for="city<?php echo $LPnCid; ?>">
							<span>全選</span>
						</label>
						<span class="WindowSelectItemSettingBtnMore JqBtnCityKind">
							<span class="more">+</span>
							<span class="less">-</span>
						</span>
					</div>
				</div>
				<div class="WindowSelectItemDetailBox DisplayBlockNone JqCityKindBox" data-city="<?php echo $LPnCid; ?>">
					<?php
					foreach ($LPaCity['aArea'] as $LPnAid => $LPaArea)
					{
					?>
						<div class="WindowSelectItem">
							<div class="WindowSelectItemTxt JqSelectItemTxt"><?php echo $LPaArea['sText'];?></div>
							<div class="WindowSelectItemBtn">
								<label for="area<?php echo $LPnAid; ?>">
									<input type="checkbox" id="area<?php echo $LPnAid; ?>" value="<?php echo $LPnAid; ?>" <?php echo $LPaArea['sSelect'];?> class="JqCityCheckbox">
								</label>
							</div>
						</div>
			<?php
					}
				echo '</div>';
			}
			?>
		</div>
		<div class="WindowSelectBtnBox">
			<div class="WindowSelectBtn"></div>
		</div>
	</div>
	<div class="WindowBg"></div>
</div>
<div class="WindowBox JqWindowBox JqSelectBox" data-filter="Type">
	<div class="WindowSelectBox">
		<div class="WindowSelectTop">
			<div class="WindowSelectTopBtn left JqSelectBtnClear"><?php echo aLIST['CLEAR'];?></div>
			<div class="WindowSelectTopTit JqApply"><?php echo aLIST['APPLY'];?><?php #echo aLIST['PLEASESELECT'];?></div>
			<div class="WindowSelectTopBtn right JqClose"><?php echo aLIST['CANCEL'];?></div>
		</div>
		<div class="WindowSelectItemBox">
			<?php
			foreach ($aType as $LPnId => $LPaType)
			{
				?>
				<div class="WindowSelectItem">
					<div class="WindowSelectItemTxt JqSelectItemTxt"><?php echo $LPaType['sName0'];?></div>
					<div class="WindowSelectItemBtn">
						<label for="list<?php echo $LPnId; ?>">
							<input type="checkbox" id="list<?php echo $LPnId; ?>" value="<?php echo $LPnId; ?>" <?php echo $LPaType['sSelect'];?>>
						</label>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<div class="WindowSelectBtnBox">
			<div class="WindowSelectBtn "></div>
		</div>
	</div>
	<div class="WindowBg"></div>
</div>
*/
?>

<?php
// 2021-04-14 客人要求不顯示
// if (!empty($aOnlineMember))
if (false)
{
	?>
	<div class="onlineBox">
		<div class="onlineContainer">
			<div class="onlineUserBox">
				<div class="onlineUserBtn onlineUserBtnPrev">
					<i class="fas fa-chevron-left"></i>
				</div>
				<div class="onlineUserView">
					<?php
					foreach ($aOnlineMember as $LPnUid => $LPaMember)
					{
					?>
						<a class="selfieBox BG" href="<?php echo $LPaMember['sUserInfoUrl'];?>" style="background-image: url('<?php echo isset($aHeadImage[$LPnUid]) ? $aHeadImage[$LPnUid]['sHeadImage'] : DEFAULTHEADIMG;?>');">
							<?php
							if($LPaMember['nKid'] == 3)
							{
								?>
								<!-- 若為下班selfieStatus + off , 若為工作中 selfieStatus + ing -->
								<div class="selfieStatus <?php echo $LPaMember['sStatusClass'];?>"></div>
								<?php
							}
							?>
						</a>
					<?php
					}
					?>
					<div style="clear:both;"></div>
				</div>
				<div class="onlineUserBtn onlineUserBtnNext">
					<i class="fas fa-chevron-right"></i>
				</div>
			</div>
			<div class="onlineBtnBox">
				<a href="<?php echo $aUrl['sOnline'];?>" class="onlineBtn">
					<span><?php echo aLIST['CHECKALL'];?></span>
					<i class="fas fa-chevron-right"></i>
				</a>
			</div>
		</div>
	</div>
	<?php
}
?>

<div class="findListBox JqAppend">
	<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
	<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].$sPageVar.'&run_page=1&nFetch=1';?>">
	<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
	<input type="hidden" name="sActJWT" value="<?php echo $sActJWT;?>">
	<input type="hidden" name="sDelJWT" value="<?php echo $sDelJWT;?>">
	<input type="hidden" name="sJoinJWT" value="<?php echo $sJoinJWT;?>">
	<input type="hidden" name="sOutJWT" value="<?php echo $sOutJWT;?>">
	<?php
	if (empty($aData))
	{
		echo '<div class="NoData">'.NODATAYET.'</div>';
	}
	foreach ($aData as $LPnId => $LPaData)
	{
		?>
		<div class="JobListBlock">
			<table class="JobListInf">
				<tbody>
					<tr>
						<td class="JobListInfPic">
							<a class="selfieBox boss BG" href="<?php echo $LPaData['sUserInfoUrl'];?>" style="background-image: url('<?php echo isset($aHeadImage[$LPaData['nUid']]) ? $aHeadImage[$LPaData['nUid']]['sHeadImage'] : DEFAULTHEADIMG;?>');"></a>
						</td>
						<td class="JobListInfTit"><?php echo $LPaData['sName0'];?></td>
						<?php
						if($sUserCurrentRole == 'staff')
						{
						?>
							<td class="JobListInfLike">
								<div class="LikeIconImg JqFavorite" data-jid="<?php echo $LPnId;?>" data-favorite="<?php echo $LPaData['nFavorite'];?>">
									<?php
									if($LPaData['nFavorite'] == 1)
									{
										#已收藏工作時呈現
										echo '<img src="images/likeActive.png" alt="">';
									}
									else
									{
										#尚未收藏工作時呈現
										echo '<img src="images/like.png" alt="">';
									}
									?>
								</div>
							</td>
						<?php
						}
						?>
						<td class="JobListInfBtnTd">
							<?php
							if($LPaData['nStatus'] == 1)
							{
								?>
								<div class="JobListInfBtn active"><?php echo aLIST['CLOSE'];?></div>
								<?php
							}
							else
							{
								if($sUserCurrentRole == 'staff')
								{
									#人才時呈現
									if($LPaData['nJoin'] == 1)
									{
										?>
										<!-- 已應徵工作時呈現 -->
										<div class="JobListInfBtn active JqOut" data-jid="<?php echo $LPnId;?>"><?php echo aLIST['OUT'];?></div>
										<?php
									}
									else
									{
										// if ($aUser['nStatus'] == 1)
										if (true)
										{
											?>
											<!-- 尚未應徵工作時呈現 -->
											<div class="JobListInfBtn JqListStupidOut" data-showctrl="1" data-jid="<?php echo $LPnId;?>">
												<?php echo aLIST['JOIN'];?>
											</div>
											<?php
										}
										else
										{
											?>
											<!-- 尚未應徵工作時呈現 -->
											<div class="JobListInfBtn JqJoin" data-jid="<?php echo $LPnId;?>"><?php echo aLIST['JOIN'];?></div>
											<?php
										}
									}
								}
								else
								{
									?>
									<a class="JobListInfBtn detail" href="<?php echo $LPaData['sDetailUrl'];?>"><?php echo aLIST['DETAIL'];?></a>
									<?php
								}
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="JobListContent">
				<div><?php echo aLIST['WORKTIME'];?></div>
				<div>
					<span><?php echo $LPaData['sStartTime'];?></span>
					<span>~</span>
					<span><?php echo $LPaData['sEndTime'];?></span>
				</div>
			</div>
			<div class="JobListContent">
				<div><?php echo aLIST['WORKPLACE'];?></div>
				<div><?php echo $LPaData['sArea'];?></div>
			</div>
			<div class="JobListContent">
				<div><?php echo aLIST['WORKTYPE'];?></div>
				<div>
					<?php
					foreach ($LPaData['aType0'] as $LPsType0)
					{
						$LPnType0 = (int)$LPsType0;
						if (!isset($aType[$LPnType0])) {
							continue;
						}
						?>
						<span><?php echo $aType[$LPnType0]['sName0'];?></span>
						<?php
					}
					?>
				</div>
			</div>
			<div class="JobListContent">
				<?php echo $LPaData['sContent0'];?>
				<div class="JobListContentImg">
					<?php
					if ($LPaData['sImgUrl'] != '')
					{
						?>
						<img src="<?php echo $LPaData['sImgUrl'];?>" alt="">
						<?php
					}
					?>
				</div>
			</div>
			<div class="JobListDate"><?php echo $LPaData['sCreateTime'];?></div>

		</div>
		<?php
	}
	?>
</div>
<div class="DisplayNone JqCopy">
	<div class="JobListBlock">
		<table class="JobListInf">
			<tbody>
				<tr>
					<td class="JobListInfPic">
						<a class="selfieBox boss BG" href="[[::sUserInfoUrl::]]" style="background-image: url('[[::sHeadImage::]]');"></a>
					</td>
					<td class="JobListInfTit">[[::sName0::]]</td>
					<?php
					if($sUserCurrentRole == 'staff')
					{
						?>
						<td class="JobListInfLike">
							<div class="LikeIconImg JqFavorite" data-jid="[[::nId::]]" data-favorite="[[::nFavorite::]]">
								[[::sFavoriteImage::]]
							</div>
						</td>
						<?php
					}
					?>
					<td class="JobListInfBtnTd">
						<!-- # 已應徵/結案active 工作詳情detail -->
						[[::sActBtn::]]
					</td>
				</tr>
			</tbody>
		</table>
		<div class="JobListContent">
			<div><?php echo aLIST['WORKTIME'];?></div>
			<div>
				<span>[[::sStartTime::]]</span>
				<span>~</span>
				<span>[[::sEndTime::]]</span>
			</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aLIST['WORKPLACE'];?></div>
			<div>[[::sArea::]]</div>
		</div>
		<div class="JobListContent">
			<div><?php echo aLIST['WORKTYPE'];?></div>
			<div>[[::sTypeHtml::]]</div>
		</div>
		<div class="JobListContent">
			[[::sContent0::]]
			<div class="JobListContentImg">
				[[::sImgUrl::]]
			</div>
		</div>
		<div class="JobListDate">[[::sCreateTime::]]</div>
	</div>
</div>

<?php
	#卷軸到底後,Loading時出現, class + active
	require_once('inc/#Loading.php');
	require_once('inc/#Top.php');
?>
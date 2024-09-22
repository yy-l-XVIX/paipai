<?php
	$sSQL = '	SELECT 	1
			FROM 	'.CLIENT_SERVICE.'
			WHERE nStatus = 0';
	$Result = $oPdo->prepare($sSQL);
	sql_query($Result);
	$nServiceCount = $Result->rowCount();
?>
<nav class="JqNav">
	<div class="navContainer">
		<div class="navWebName">
			<div>
				<a href="<?php echo sys_web_encode($aMenuToNo['pages/index/php/_index_0.php']);?>">
					<?php echo $aSystem['sTitle'];?>
				</a>
			</div>
		</div>
		<div class="navMenu">
			<?php
			$aPageSelect = explode('/', $aRequire['Require']);
			foreach ($aSystem['aNav'] as $LPnKid => $LPaKind)
			{
				if (!isset($aMenuCtrl[$LPaKind['sMenuTable0']]))
				{
				     continue;
				}
				$LPaKind['sActive'] = '';
				if ($aPageSelect['1'] == $LPaKind['sMenuTable0'])
				{
				     $LPaKind['sActive'] = 'active';
				}
				?>
				<div class="navMenuBlock">
					<!-- 停留此主目錄 navMenuTopic + active -->
					<div class="navMenuTopic JqNavMenuBtn  <?php echo $LPaKind['sActive'];?>">
						<i class="fas fa-cogs MarginRight10"></i>
						<span class="navMenuTopicName"><?php echo aMENULANG['aKIND'][$LPaKind['sMenuTable0']];?></span>
						<span class="AfterArrow right"></span>
					</div>
					<div class="navMenuListBox">
						<?php
						foreach ($LPaKind['aList'] as $LPnLid => $LPaList)
						{
							if (!isset($aMenuCtrl[$LPaKind['sMenuTable0']][$LPaList['sListTable0']]))
							{
							     continue;
							}
							if ($LPaList['nType0'] == 1) # 附屬功能不顯示
							{
								continue;
							}
							$LPaList['sActive'] = '';
							if (strpos($aPageSelect['3'], $LPaList['sListTable0']) !== false)
							{
								$LPaList['sActive'] = 'active';
							}
							$LPsUrl = 'javascript:void(0);';
							if (isset($aMenuToNo['pages/'.$LPaKind['sMenuTable0'].'/php/_'.$LPaList['sListTable0'].'.php']))
							{
							     $LPsUrl = sys_web_encode($aMenuToNo['pages/'.$LPaKind['sMenuTable0'].'/php/_'.$LPaList['sListTable0'].'.php']);
							}
							?>
							<!-- 停留此子目錄 navMenuList + active -->
							<div class="navMenuList <?php echo $LPaList['sActive'];?>">
								<?php
								if ($LPaList['sListTable0'] == 'client_service_0' && $nServiceCount > 0)
								{
									?>
									<a class="navMenuListName" href="<?php echo $LPsUrl;?>">
										<span><?php echo aMENULANG['aLIST'][$LPaList['sListTable0']];?></span>
										<span class="FontRed">(<?php echo $nServiceCount;?>)</span>
									</a>
									<?php
								}
								else
								{
									?>
									<a class="navMenuListName" href="<?php echo $LPsUrl;?>">
										<?php echo aMENULANG['aLIST'][$LPaList['sListTable0']];?>
									</a>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</nav>
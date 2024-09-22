<?php $aData = json_decode($sData,true);?>
<!-- 選擇方案 -->
<div class="chooseBox">
	<div class="chooseContainer">
		<?php

		foreach ($aData as $LPnLid => $LPaData)
		{
			?>
			<div class="chooseBlock">
				<div class="chooseTit"><?php echo $LPaData['sName1']; ?></div>
				<div class="chooseSubTit">
					<span><?php echo $LPaData['sContent0']; ?></span>
				</div>
				<div class="chooseBtnBox">
					<?php
					if (!isset($aJWT['aUser'])) // 註冊
					{
						?>
						<a href="<?php echo $LPaData['sUrl'];?>" class="BtnAct"><?php echo aCHOOSE['SELECTMETHOD'];?></a>
						<?php
					}
					else if ($LPaData['nFreeStartTime'] <= NOWTIME && $LPaData['nFreeEndTime'] >= NOWTIME) // 免費使用期間
					{

						if (in_array($LPnLid,$aJWT['aUser']['aKid']))
						{
							?>
							<div>
								<span><?php echo date('Y-m-d H:i:s',$LPaData['nFreeStartTime']);?></span>
								<span>~</span>
								<span><?php echo date('Y-m-d H:i:s',$LPaData['nFreeEndTime']);?></span>
								<span><?php echo aCHOOSE['FREEUSE'];?></span>
							</div>
							<?php
						}
						else
						{
							?>
							<div class="MarginBottom10">
								<span><?php echo date('Y-m-d H:i:s',$LPaData['nFreeStartTime']);?></span>
								<span>~</span>
								<span><?php echo date('Y-m-d H:i:s',$LPaData['nFreeEndTime']);?></span>
								<span><?php echo aCHOOSE['FREEUSE'];?></span>
							</div>
							<button class="BtnAct JqSubmit" data-url="<?php echo $aUrl['sAct1'].'&nLid='.$LPaData['nLid'];?>"><?php echo aCHOOSE['OPEN'];?></button>
							<?php
						}
					}
					else if ($LPaData['sExpired'] == '')
					{
						if ($LPaData['nType0'] == 1)
						{
							?>
							<button class="BtnAct JqSubmit" data-url="<?php echo $aUrl['sAct0'].'&nLid='.$LPaData['nLid'];?>"><?php echo aCHOOSE['FREETRY'];?> <?php echo $LPaData['nFreeDays'];?> <?php echo aCHOOSE['DAYS'];?></button>
							<?php
						}
						else
						{
							?>
							<!-- 尚未購買 -->
							<a href="<?php echo $LPaData['sUrl'];?>" class="BtnAct"><?php echo aCHOOSE['SELECTMETHOD'];?></a>
							<?php
						}
					}
					else
					{
						?>
						<!-- 已購買 -->
						<a href="<?php echo $LPaData['sUrl'];?>" class="BtnAct"><?php echo aCHOOSE['EXPIREDDATE'];?> <?php echo $LPaData['sExpired'];?></a>
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
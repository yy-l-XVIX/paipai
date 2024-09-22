<?php $aData = json_decode($sData,true);?>

<!-- 個人資訊-基本資料 -->
<div class="infProfileBox">
	<div class="infProfileTop">
		<!-- 若此人身份為雇主,selfieBox + boss -->
		<div class="infProfileImg selfieBox <?php echo $sSelfieBoxClass;?>">
			<img src="<?php echo $sHeadImage;?>" data-file="0" class="JqPreviewImage">
		</div>
		<div class="infProfileName">
			<span><?php echo $aData['sName0'];?></span>
			<span class="infProfileKind">
				<div><?php echo $aData['sKindName0']; ?></div>
			</span>
		</div>

	</div>

	<table class="infData">
		<tbody>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['NAME0'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['sName0'];?></div>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['LOCATION'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['sLocationName0'];?></div>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['HEIGHT'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['sHeight'];?></div>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['WEIGHT'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['sWeight'];?></div>
				</td>
			</tr>
			<?php
			/*
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['AGE'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['nAge'];?></div>
				</td>
			</tr>
			*/
			?>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['SIZE'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['sSize'];?></div>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['CONTENT0'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['sContent0'];?></div>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSHARE['CONTENT1'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt"><?php echo $aData['sContent1'];?></div>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- 會員自行選擇顯示/隱藏 -->
	<table class="infData">
		<tbody>
			<!-- phone -->
			<?php
			if ($aData['nType0']==1)
			{
				?>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aSHARE['PHONE'];?></span>
						</div>
					</td>
					<td class="infDataCell2 infDataChooseCell">
						<div class="infDataTxt"><?php echo $aData['sPhone'];?></div>
					</td>
				</tr>
				<?php
			}
			?>
			<!-- wechat -->
			<?php
			if ($aData['nType1']==1)
			{
				?>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aSHARE['WECHAT'];?></span>
						</div>
					</td>
					<td class="infDataCell2 infDataChooseCell">
						<div class="infDataTxt"><?php echo $aData['sWechat'];?></div>
					</td>
				</tr>
				<?php
			}
			?>
			<!-- email -->
			<?php
			if ($aData['nType2']==1)
			{
				?>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aSHARE['EMAIL'];?></span>
						</div>
					</td>
					<td class="infDataCell2 infDataChooseCell">
						<div class="infDataTxt"><?php echo $aData['sEmail'];?></div>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<!-- 圖片 -->
	<div class="infPhotoBox MarginBottom10">
		<table class="infPhotoTable">
			<tbody>
				<?php
				$nPhotoCount = 1;
				$nTdAmount = 3;
				foreach ($aImage as $LPnId => $LPsImgUrl)
				{
					if($nPhotoCount%$nTdAmount == 1)
					{
						echo '<tr>';
					}
				?>
					<td class="infPhotoTd">
						<div class="infPhotoBlock JqPhotoBtnZoom">
							<img src="<?php echo $LPsImgUrl;?>" alt="">
						</div>
					</td>
				<?php
					if($nPhotoCount%$nTdAmount == 0)
					{
						echo '</tr>';
					}
					$nPhotoCount ++;
				}
				if($nPhotoCount%$nTdAmount != 0)
				{
					for($nAdd=1;$nAdd<=($nTdAmount-($nPhotoCount%$nTdAmount));$nAdd++)
					{
						echo '<td class="infPhotoTd"></td>';
					}
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- 影片 -->
	<div class="infVideoBox">
		<?php
		foreach ($aVideo as $LPsVideoUrl)
		{
			?>
			<div class="infVideoVideo">
				<video src="<?php echo $LPsVideoUrl;?>#t=0.001" controls playsinline></video>
			</div>
			<?php
		}
		?>
	</div>
</div>
<div class="WindowBox JqWindowBox JqPhotoZoomBox">
	<header>
		<div class="headerContainer">
			<div class="headerIcon headerLeft JqClose">
				<i class="fas fa-arrow-left"></i>
			</div>
		</div>
	</header>
	<div class="infPhotoWindowBox">
		<img src="" alt="" class="JqZoomImg">
	</div>
	<div class="WindowBg"></div>
</div>
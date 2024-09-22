<?php $aData = json_decode($sData,true);?>
<!-- 推薦點數 -->
<div class="FormBox">
	<input type="hidden" name="sPage" value="<?php echo $aUrl['sPage'];?>">
	<table class="FormTable">
		<thead>
			<tr>
				<th>
					<div class="Sel">
						<select name="nType0" class="JqChange">
							<option value="0"><?php echo aRECORD['SELECTTYPE0'];?></option>
							<?php
							foreach ($aType0 as $LPnType0 => $LPaType0)
							{
								?>
								<option value="<?php echo $LPnType0;?>" <?php echo $LPaType0['sSelect'];?>><?php echo $LPaType0['sText'];?></option>
								<?php
							}
							?>
						</select>
						<i class="fas fa-chevron-down"></i>
					</div>
				</th>
				<th><?php echo aRECORD['DATE'];?></th>
				<th><?php echo aRECORD['POINT'];?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($aData as $LPnId => $LPaData)
			{
			?>
				<tr>
					<td><?php echo $aType0[$LPaData['nType0']]['sText'];?></td>
					<td><?php echo $LPaData['sCreateTime'];?></td>
					<td class="FormFontRed"><?php echo $LPaData['nDelta'];?></td>
				</tr>
			<?php
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td><?php echo aRECORD['TOTAL'];?></td>
				<td class="FormFontRed"><?php echo $nTotal;?></td>
			</tr>
		</tfoot>
	</table>
	<?php echo $aPageList['sHtml'];?>
	<?php
	/*
	<table class="FormPageBox">
		<tbody>
			<tr>
				<td>
					<a class="FormPageBtn" href="javascript:void(0);">第一頁</a>
				</td>
				<td>
					<a class="FormPageBtn" href="javascript:void(0);">上一頁</a>
				</td>
				<td>
					<a class="FormPageBtn" href="javascript:void(0);">下一頁</a>
				</td>
				<td>
					<a class="FormPageBtn" href="javascript:void(0);">最末頁</a>
				</td>
			</tr>
		</tbody>
	</table>
	*/
	?>
</div>
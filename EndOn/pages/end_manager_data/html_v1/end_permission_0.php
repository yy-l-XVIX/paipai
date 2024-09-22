<?php $aData = json_decode($sData,true);?>
<!-- 新增按鈕 -->
<div class="Block MarginBottom10">
      <a href="<?php echo $aUrl['sIns'];?>" class="BtnAdd"><?php echo INS.$sHeadTitle;?></a>
</div>
<!-- 純顯示資訊 -->
<div class="Information">
      <table class="InformationTit">
		<tbody>
			<tr>
				<td class="InformationTitCell" style="width:calc(100%/1);">
					<div class="InformationName"><?php echo $sHeadTitle; ?></div>
				</td>
			</tr>
		</tbody>
	</table>
      <div class="InformationScroll">
            <div class="InformationTableBox">
                  <table>
                        <thead>
                              <tr>
                                    <th><?php echo aPERMISSION['NAME0'];?></th>
                                    <th><?php echo aPERMISSION['CREATETIME'];?></th>
                                    <th><?php echo aPERMISSION['UPDATETIME'];?></th>
                                    <th><?php echo aPERMISSION['OPERATE'];?></th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php
                              foreach ($aData as $LPnId => $LPaData)
                              {
                                    ?>
                                    <tr>
                                          <td><?php echo $LPaData['sName0'];?></td>
                                          <td><?php echo $LPaData['sCreateTime'];?></td>
                                          <td><?php echo $LPaData['sUpdateTime'];?></td>
                                          <td>
                                                <a href="<?php echo $LPaData['sUptUrl'];?>" class="TableBtnBg">
                                                      <i class="fas fa-pen"></i>
                                                </a>
                                                <div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaData['sDelUrl'];?>">
                                                      <i class="fas fa-times"></i>
                                                </div>
                                          </td>
                                    </tr>
                                    <?php
                              }
                              ?>
                        </tbody>
                  </table>
            </div>
      </div>
</div>
<?php echo $aPageList['sHtml'];?>
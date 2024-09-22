<?php $aData = json_decode($sData,true);?>
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
                        <tbody>
                               <tr>
                                    <td><?php echo aSYNC['ALL'];?></td>
                                    <td>
                                          <div class="TableBtnBg JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $aData['sAll'];?>">
                                                <i class="fas fa-pen"></i>
                                          </div>
                                    </td>
                              </tr>
                              <tr>
                                    <td><?php echo aSYNC['MANUKIND'];?></td>
                                    <td>
                                          <div class="TableBtnBg JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $aData['sMenuKind'];?>">
                                                <i class="fas fa-pen"></i>
                                          </div>
                                    </td>
                              </tr>
                              <tr>
                                    <td><?php echo aSYNC['MANULIST'];?></td>
                                    <td>
                                          <div class="TableBtnBg JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $aData['sMenuList'];?>">
                                                <i class="fas fa-pen"></i>
                                          </div>
                                    </td>
                              </tr>
                              <tr>
                                    <td><?php echo aSYNC['LOGCODE'];?></td>
                                    <td>
                                          <div class="TableBtnBg JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $aData['sLogCode'];?>">
                                                <i class="fas fa-pen"></i>
                                          </div>
                                    </td>
                              </tr>
                        </tbody>
                  </table>
            </div>
      </div>
</div>

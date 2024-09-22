<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="MarginBottom20">
      <div>
            <div class="Ipt">
                  <input type="text" name="sAccount" placeholder="<?php echo aLOG['ACCOUNT'];?>" value="<?php echo $sAccount;?>">
            </div>
            <div class="Ipt">
                  <input type="text" name="sIp" placeholder="<?php echo aLOG['IP'];?>" value="<?php echo $sIp;?>">
            </div>
            <div class="Ipt">
                  <input type="text" name="sStartTime" class="JqStartTime" value="<?php echo $sStartTime;?>">
            </div>
            <span>~</span>
            <div class="Ipt">
                  <input type="text" name="sEndTime" class="JqEndTime" value="<?php echo $sEndTime;?>">
            </div>
            <input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
      </div>
</form>
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
                                    <th>No.</th>
                                    <th><?php echo aLOG['ACCOUNT']?></th>
                                    <th><?php echo aLOG['ACT'];?></th>
                                    <th><?php echo aLOG['WHOM'];?></th>
                                    <th><?php echo aLOG['IP']?></th>
                                    <th><?php echo aLOG['TIME'];?></th>
                                    <th><?php echo aLOG['PLACT'];?></th>
                                    <th></th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php
                              foreach ($aData as $LPnId => $LPaData)
                              {
                                    ?>
                                    <tr>
                                         <td><?php echo $LPnId;?></td>
                                         <td><?php echo $LPaData['sWho'];?></td>
                                         <td><?php echo $aSystem['aLogNums'][$LPaData['nLogCode']];?></td>
                                         <td><?php echo $LPaData['sWhom'];?></td>
                                         <td><?php echo $LPaData['nKid'];?></td>
                                         <td><?php echo $LPaData['sCreateTime'];?></td>
                                         <td><?php echo $LPaData['sIp'];?> </td>
                                         <td></td>
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
<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="MarginBottom20">
      <div>
            <div class="Ipt">
                  <input type="text" name="sAccount" placeholder="<?php echo aLOGIN['ACCOUNT'];?>" value="<?php echo $sAccount;?>">
            </div>
            <div class="Ipt">
                  <input type="text" name="sIp" placeholder="<?php echo aLOGIN['IP'];?>" value="<?php echo $sIp;?>">
            </div>
            <div class="Ipt">
                  <input type="text" name="sStartTime" class="JqStartTime" value="<?php echo $sStartTime;?>">
            </div>
            <span>~</span>
            <div class="Ipt">
                  <input type="text" name="sEndTime" class="JqEndTime" value="<?php echo $sEndTime;?>">
            </div>
            <input type="submit" class="BtnAny" value="<?php echo aLOGIN['SEARCH'];?>">
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
                                    <th><?php echo aLOGIN['ACCOUNT'];?></th>
                                    <th><?php echo aLOGIN['STATUS'];?></th>
                                    <th><?php echo aLOGIN['CREATETIME'];?></th>
                                    <th><?php echo aLOGIN['DEVICE'];?></th>
                                    <th><?php echo aLOGIN['USERAGENT'];?></th>
                                    <th><?php echo aLOGIN['IP'];?></th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php
                              foreach ($aData as $LPnId => $LPaData)
                              {
                                    ?>
                                    <tr>
                                         <td><?php echo $LPnId;?></td>
                                         <td><?php echo $LPaData['sAccount'];?></td>
                                         <td class="<?php echo $aStatus[$LPaData['nStatus']]['sClass'];?>"><?php echo $aStatus[$LPaData['nStatus']]['sTitle'];?></td>
                                         <td><?php echo $LPaData['sCreateTime'];?></td>
                                         <td><?php echo $LPaData['sDevice'];?> </td>
                                         <td>
                                                <div><?php echo $LPaData['sBrowser'];?></div>
                                                <div><?php echo $LPaData['sBrowserVersion'];?></div>
                                         </td>
                                         <td><?php echo $LPaData['sIp'];?></td>
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
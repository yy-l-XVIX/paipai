<?php $aData = json_decode($sData,true);?>
<!-- 純顯示資訊 -->

<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0">
      <input type="hidden" name="t" value="<?php echo NOWTIME;?>">
      <div class="Block MarginBottom10">
            <div class="Ipt">
                  <input type="password" name="sPassword" value="" placeholder="<?php echo aTRUNCATE['PASSWORD'];?>">
            </div>
            <div class="BtnAny JqStupidOut" data-showctrl="0"><?php echo CSUBMIT;?></div>
      </div>
      <div class="Block">
            <div class="BlockTit MarginBottom5"> admroot / mmg001 <?php echo aTRUNCATE['NODEL']?></div>
            <div class="BtnAny MarginBottom5 JqCheckAll"><?php echo aTRUNCATE['ALL'];?></div>
            <div class="GridBlockBox JqControlBlock">
            <?php
            foreach($aData as $LPsKind => $LPaTable)
            {
                  ?>
                  <div class="GridBlock">
                        <div class="GridBlockTopic"><?php echo aTRUNCATE[strtoupper($LPsKind)];?></div>
                        <label class="GridBlockList JqSubAll" for="<?php echo $LPsKind;?>">
                              <input type="checkbox" id="<?php echo $LPsKind;?>">
                              <span><?php echo aTRUNCATE['ALL'];?></span>
                        </label>
                        <?php
                        foreach($LPaTable as $LPsTable => $LPbTrue)
                        {
                              ?>
                              <label class="GridBlockList" for="<?php echo $LPsTable;?>">
                                    <input type="checkbox" value="1" name="<?php echo 'aPost['.$LPsTable.']';?>" id="<?php echo $LPsTable;?>">
                                    <span><?php echo $LPsTable;?></span>
                                    <?php
                                    if ($LPsTable == 'client_user_data')
                                    {
                                          echo '<div class="Ipt"><input type="text" name="sUserAccount" value="" placeholder="'.aTRUNCATE['INPUTUSER'].'"></div>';
                                    }
                                    if ($LPsTable == 'end_manager_data')
                                    {
                                          echo '<div class="Ipt"><input type="text" name="sManagerAccount" value="" placeholder="'.aTRUNCATE['INPUTMANAGER'].'"></div>';
                                    }
                                    ?>
                              </label>
                              <?php
                        }
                        ?>
                  </div>
            <?php
            }
            ?>
            </div>
      </div>


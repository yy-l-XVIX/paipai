<?php
if($nS == 1)
{
?>
<!DOCTYPE html>
<html>
      <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $aSystem['sTitle'];?></title>
            <link rel="stylesheet" href="plugins/css/components/jumpMsg.css?t=20200710v2">
      </head>
      <body>
<?php
}
?>
      <?php
      foreach($aJumpMsg as $LPnId => $LPaData)
      {
      ?>
      <div class="jumpMsgBox JqJumpMsgBox <?php echo (!empty($LPaData['sBoxClass']))?$LPaData['sBoxClass']:'';echo ($LPaData['sShow'] == 1)?'active':''; ?>" data-showmsg="<?php echo $LPnId; ?>" data-hide="<?php echo $LPnId; ?>">
            <div class="jumpMsgContainer">
                  <div class="jumpMsgInner">
                        <?php
                        # 標題
                        if(!empty($LPaData['sTitle']))
                        {
                        ?>
                              <div class="jumpMsgTop">
                                    <div class="jumpMsgTit"><?php echo $LPaData['sTitle']; ?></div>
                              </div>
                        <?php
                        }
                        ?>
                        <div class="jumpMsgContent">

                              <?php
                              # Icon
                              if(!empty($LPaData['sIcon']))
                              {
                              ?>
                                    <div class="jumpMsgIcon">

                                          <?php
                                          # 打勾
                                          if($LPaData['sIcon'] == 'success')
                                          {
                                          ?>
                                                <div class="jumpMsgIconRight">
                                                      <i class="fas fa-check"></i>
                                                </div>
                                          <?php
                                          }
                                          ?>

                                          <?php
                                          # 叉叉
                                          if($LPaData['sIcon'] == 'error')
                                          {
                                          ?>
                                                <div class="jumpMsgIconError">
                                                      <i class="fas fa-times"></i>
                                                </div>
                                          <?php
                                          }
                                          ?>
                                    </div>
                              <?php
                              }
                              ?>

                              <?php
                              # 純顯示文字 ( ex.訊息,動作結果 )
                              if(!empty($LPaData['sMsg']))
                              {
                              ?>
                                    <div class="jumpMsgContentTxt JqJumpMsgContentTxt"><?php echo $LPaData['sMsg']; ?></div>
                              <?php
                              }
                              ?>

                              <?php
                              # 大量文字 ( ex.後台新增之公告 )
                              if(!empty($LPaData['sArticle']))
                              {
                              ?>
                                    <div class="jumpMsgContentArticle"><?php echo $LPaData['sArticle']; ?></div>
                              <?php
                              }
                              ?>
                        </div>
                        <?php
                        # 按鈕
                        if(!empty($LPaData['aButton']))
                        {
                        ?>
                              <table class="jumpMsgBtnBox">
                                    <tbody>
                                          <tr>
                                                <?php
                                                foreach($LPaData['aButton'] as $LPnButtonKey => $LPaButtonData)
                                                {
                                                ?>
                                                      <td class="jumpMsgBtnCell" style="width:calc(100%/<?php echo count($LPaData['aButton']); ?>);max-width:calc(100%/<?php echo count($LPaData['aButton']); ?>);min-width:calc(100%/<?php echo count($LPaData['aButton']); ?>);">
                                                            <?php
                                                            if(strpos($LPaButtonData['sClass'],'JqClose') !== false)
                                                            {
                                                                  # 關閉
                                                            ?>
                                                                  <div class="jumpMsgBtn WordBreakBreakAll <?php echo (!empty($LPaButtonData['sClass']))?$LPaButtonData['sClass']:''; ?>">
                                                                        <span class="jumpMsgBtnTxt"><?php echo $LPaButtonData['sText']; ?></span>
                                                                  </div>
                                                            <?php
                                                            }
                                                            else if(strpos($LPaButtonData['sClass'],'submit') !== false)
                                                            {
                                                                  # submit form
                                                            ?>
                                                                  <div class="jumpMsgBtn WordBreakBreakAll">
                                                                        <div class="jumpMsgBtnTxt submit" onclick="$('form[data-form=\'<?php echo $LPnId;?>\']').submit();">確認</div>
                                                                  </div>
                                                            <?php
                                                            }
                                                            else
                                                            {
                                                                  # 換頁
                                                            ?>
                                                                  <a class="jumpMsgBtn WordBreakBreakAll JqRedirectClose <?php echo (!empty($LPaButtonData['sClass']))?$LPaButtonData['sClass']:''; ?>" href="<?php echo $LPaButtonData['sUrl']; ?>">
                                                                        <span class="jumpMsgBtnTxt"><?php echo $LPaButtonData['sText']; ?></span>
                                                                  </a>
                                                            <?php
                                                            }
                                                            ?>
                                                      </td>
                                                <?php
                                                }
                                                ?>
                                          </tr>
                                    </tbody>
                              </table>
                        <?php
                        }
                        ?>
                  </div>
            </div>
            <div class="jumpMsgBg <?php echo ($LPaData['nClicktoClose'] == 1)?'JqClose':''; ?>"></div>
      </div>
      <?php
      }
      ?>
<?php
if($nS == 1)
{
?>
      </body>
</html>
<?php
}
?>
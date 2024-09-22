<!-- 
<div class="EmojiBox JqEmojiBox">
      <div class="EmojiBtnSwitch JqBtnEmoji">
            <i class="far fa-laugh"></i>
      </div>      
</div> 
-->
<div class="EmojiImgBox DisplayBlockNone JqEmojiImgBox">
      <table class="EmojiImgTable">
            <tbody>
                  <?php
                  $nTdCount = 10;
                  for($ff=1;$ff<=36;$ff++)
                  {
                        if($ff%$nTdCount==1)
                        {
                              echo '<tr>';
                        }
                  ?>
                        <td class="EmojiImgTd" style="min-width: calc(100%/<?php echo $nTdCount; ?>);width: calc(100%/<?php echo $nTdCount; ?>);">
                              <div class="EmojiImg">
                                    <img src="images/emoji/<?php echo $ff; ?>.png" alt="" class="JqEmojiImage">
                              </div>
                        </td>
                  <?php
                        if($ff%$nTdCount==0)
                        {
                              echo '</tr>';
                        }
                  }
                  if(($ff-1)%$nTdCount != 0)
                  {
                        for($nAdd=1;$nAdd<=($nTdCount-(($ff-1)%$nTdCount));$nAdd++)
                        {
                              echo '<td></td>';
                        }
                        echo '</tr>';
                  }
                  ?>
            </tbody>
      </table>
</div>
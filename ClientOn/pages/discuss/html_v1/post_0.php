<!-- 新增貼文 -->
<div class="discussPostBoxOuter">
	<form id="JqPostForm" enctype="multipart/form-data">
		<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
		<div class="discussPostBox">
			<div class="discussPostWrite">
				<div class="discussPostWriteList">
					<div class="discussPostTit"><?php echo aPOST['DISCUSS'];?></div>
					<div class="discussPostMsg">
						<input type="hidden" name="sContent0" value="">
						<div class="EmojiContentInput postJobMsgContent JqReplyContent JqContent0" contenteditable="true"></div>
						<div class="EmojiBox JqEmojiBox">
							<div class="EmojiBtnSwitch JqBtnEmoji">
								<i class="far fa-laugh"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="discussPostWriteList">
					<div>
						<?php
							#Emoji
							require_once('inc/#EmojiPackage.php');
						?>
					</div>
				</div>
				<div class="discussPostWriteList JqFileBox">
					<div class="discussPostTit"><?php echo aPOST['IMAGE'];?></div>
					<?php
					for($i=0;$i<$aSystem['aParam']['nPostImage'];$i++)
					{
						?>
						<div class="discussPostMsg ">
							<div class="FileImg ">
								<img class="JqPreviewImage" data-file="<?php echo $i;?>" src="">
							</div>
							<div class="FileBtnAdd JqFileActive ">
								<input type="file" name="aFile[]" class="JqFile" data-filebtn="<?php echo $i;?>" accept="image/*" />
								<div class="original"><?php echo UPLOADIMG;?></div>
								<div class="change"><?php echo CHANGEIMG;?></div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>

			<div class="discussPostBtnBox">
				<a href="javascript:void(0)" class="BtnAct JqSubmit"><?php echo aPOST['FINISH'];?></a>
			</div>
		</div>

	</form>


</div>
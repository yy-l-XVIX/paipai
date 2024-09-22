<footer>
	<table class="footerContainer">
		<tbody>
			<tr>
				<input type="hidden" class="JqCheckMessageFetch" value="<?php echo $sCheckMessageFetch; // from SetArray.php?>">
				<?php
				foreach($aFooter as $LPsKey => $LPaData)
				{
					$sClass = '';
					if($LPsKey==$aPageRequire[1])
					{
						$sClass = 'active';
					}
				?>
					<td class="<?php echo $sClass; ?>">
						<a href="<?php echo $LPaData['sUrl']; ?>" class="footerA">
							<?php
							if(($LPsKey == 'job') || ($LPsKey == 'chat'))
							{
								?>
								<!-- 如果有通知 , >=100 顯示99+ -->
								<div class="footerMsg FontNumber JqNewMessage DisplayBlockNone" data-type="<?php echo $LPsKey;?>">N</div>
								<?php
							}
							?>
							<div class="footerIcon"><?php echo $LPaData['sIcon']; ?></i></div>
							<div class="footerTit"><?php echo $LPaData['sText']; ?></div>
						</a>
					</td>
				<?php
				}
				?>
			</tr>
		</tbody>
	</table>
</footer>
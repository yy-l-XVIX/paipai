<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/inc/#Unload.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/inc/lang/'.$aSystem['sLang'].'/post.php');

	$nId 		= filter_input_int('nId',	INPUT_REQUEST,0);
	$sContent0 	= isset($_POST['sContent0']) ? nl2br($_POST['sContent0']) : '';

	/**
	 * 回傳陣列 JSON
	 * @var Int nStatus
	 * 	回傳狀態值
	 * 	1 => 正常 其餘待補
	 * @var String sMsg
	 * 	回傳訊息
	 * @var Array aData
	 * 	回傳陣列
	 * @var Int nAlertType
	 * 	回傳訊息提示類型
	 * 	0 => 不需提示框
	 * @var String sUrl
	 * 	回傳後導頁檔案
	 */
	$aReturn = array(
		'nStatus'		=> 1,
		'sMsg'		=> '',
		'aData'		=> array(),
		'nAlertType'	=> 0,
		'sUrl'		=> sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_0.php']),
	);
	$aEditLog = array();

	# img 標籤轉換代號 <img src="images/emoji/01.png"> => [:01:] (?)
	$sContent0 = str_replace('<img class="EmojiImgIcon" src="images/emoji/', '[:', $sContent0);
	$sContent0 = str_replace('.png">', ':]', $sContent0);

	if ($aJWT['a'] == 'INS')
	{
		if ($sContent0 == '' && empty($_FILES['aFile'])) // 沒上傳圖&&沒傳字讓他失敗
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aPOST['EMPTYCONTENT'];
		}
		if ($aReturn['nStatus'] == 1)
		{
			$oPdo->beginTransaction();

			$aSQL_Array = array(
				'nUid' 		=> (int) $aUser['nId'],
				'nOnline' 		=> (int) 1,
				'sContent0' 	=> (string) $sContent0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);

			$sSQL = 'INSERT INTO '.CLIENT_DISCUSS.' ' . sql_build_array('INSERT', $aSQL_Array );
			$Result = $oPdo->prepare($sSQL);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);
			$nLastId = $oPdo->lastInsertId();

			$aEditLog[CLIENT_DISCUSS]['aOld'] = array();
			$aEditLog[CLIENT_DISCUSS]['aNew'] = $aSQL_Array;
			$aEditLog[CLIENT_DISCUSS]['aNew']['nId'] = $nLastId;

			// 上圖多個
			if (!empty($_FILES['aFile']))
			{
				for ($i=0; $i < $aSystem['aParam']['nPostImage']; $i++)
				{
					if (isset($_FILES['aFile']['name'][$i]) && $_FILES['aFile']['name'][$i] != '' && $_FILES['aFile']['error'][$i] != 4)
					{
						$aFile['sTable'] = CLIENT_DISCUSS;
						$aFile['aFile'] = array(
							'name'	=> $_FILES['aFile']['name'][$i],
							'type'	=> $_FILES['aFile']['type'][$i],
							'tmp_name'	=> $_FILES['aFile']['tmp_name'][$i],
							'error'	=> $_FILES['aFile']['error'][$i],
							'size'	=> $_FILES['aFile']['size'][$i],
						);
						$aFileInfo = goImage($aFile);

						if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
						{

							// $oPdo->rollback();
							$aReturn['nStatus'] = 0;
							$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
							// $aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/discuss/php/_post_0.php']);
							echo json_encode($aReturn);
							exit;
						}
						else
						{
							$aTmp = explode('.',$aFileInfo['sFilename']);
							$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
							$sFname = $aFileInfo['sFilename'];
						}

						$aSQL_Array = array(
							'nKid'		=> (int) $nLastId,
							'sTable'		=> (string) CLIENT_DISCUSS,
							'sFile'		=> (string) $sFname,
							'nType0'		=> (int) 0,
							'nCreateTime'	=> (int) NOWTIME,
							'sCreateTime'  	=> (string) NOWDATE,
						);
						$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
						$Result = $oPdo->prepare($sSQL);
						sql_build_value($Result, $aSQL_Array);
						sql_query($Result);
						$nImageLastId = $oPdo->lastInsertId();

						#紀錄動作 - 新增
						$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
						$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;
					}
				}
			}

			// 上圖
			// if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
			// {
			// 	$aFile['sTable'] = CLIENT_DISCUSS;
			// 	$aFile['aFile'] = $_FILES['sFile'];
			// 	$aFileInfo = goImage($aFile);

			// 	if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
			// 	{

			// 		$oPdo->rollback();
			// 		$aReturn['nStatus'] = 0;
			// 		$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
			// 		$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/discuss/php/_post_0.php']);
			// 		echo json_encode($aReturn);
			// 		exit;
			// 	}
			// 	else
			// 	{
			// 		$aTmp = explode('.',$aFileInfo['sFilename']);
			// 		$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
			// 		$sFname = $aFileInfo['sFilename'];
			// 	}

			// 	$aSQL_Array = array(
			// 		'nKid'		=> (int) $nLastId,
			// 		'sTable'		=> (string) CLIENT_DISCUSS,
			// 		'sFile'		=> (string) $sFname,
			// 		'nType0'		=> (int) 0,
			// 		'nCreateTime'	=> (int) NOWTIME,
			// 		'sCreateTime'  	=> (string) NOWDATE,
			// 	);

			// 	$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
			// 	$Result = $oPdo->prepare($sSQL);
			// 	sql_build_value($Result, $aSQL_Array);
			// 	sql_query($Result);
			// 	$nImageLastId = $oPdo->lastInsertId();

			// 	#紀錄動作 - 新增
			// 	$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
			// 	$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;
			// }

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nLastId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100501,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);
			$oPdo->commit();

			$aReturn['sMsg'] = INSV;
		}
	}

	if ($aJWT['a'] == 'REPLY')
	{
		// error_log(print_r($_FILES['aFile'],true));
		if ($sContent0 == '' && empty($_FILES['aFile'])) // 沒上傳圖&&沒傳字讓他失敗
		{
			$aReturn['nStatus'] = 0;
			$aReturn['sMsg'] = aPOST['EMPTYCONTENT'];
		}
		if ($aReturn['nStatus'] == 1)
		{
			$sSQL = '	SELECT 	nId,
							sContent0,
							nOnline
					FROM 	'.CLIENT_DISCUSS.'
					WHERE nId = :nId
					AND 	nOnline = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_query($Result);
			$aData = $Result->fetch(PDO::FETCH_ASSOC);
			if ($aData === false)
			{
				$aReturn['sMsg'] = NODATA;
				$aReturn['nStatus'] = 0;
			}
			else
			{
				$aSQL_Array = array(
					'nDid'		=> (int) $nId,
					'nUid' 		=> (int) $aUser['nId'],
					'nOnline' 		=> (int) 1,
					'sContent0' 	=> (string) $sContent0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);

				$sSQL = 'INSERT INTO '.CLIENT_DISCUSS_REPLY.' ' . sql_build_array('INSERT', $aSQL_Array );
				$Result = $oPdo->prepare($sSQL);
				sql_build_value($Result, $aSQL_Array);
				sql_query($Result);
				$nLastId = $oPdo->lastInsertId();

				$aEditLog[CLIENT_DISCUSS_REPLY]['aOld'] = array();
				$aEditLog[CLIENT_DISCUSS_REPLY]['aNew'] = $aSQL_Array;
				$aEditLog[CLIENT_DISCUSS_REPLY]['aNew']['nId'] = $nLastId;

				// 上圖多個
				if (!empty($_FILES['aFile']))
				{
					for ($i=0; $i < $aSystem['aParam']['nPostImage']; $i++)
					{
						if (isset($_FILES['aFile']['name'][$i]) && $_FILES['aFile']['name'][$i] != '' && $_FILES['aFile']['error'][$i] != 4)
						{
							$aFile['sTable'] = CLIENT_DISCUSS_REPLY;
							$aFile['aFile'] = array(
								'name'	=> $_FILES['aFile']['name'][$i],
								'type'	=> $_FILES['aFile']['type'][$i],
								'tmp_name'	=> $_FILES['aFile']['tmp_name'][$i],
								'error'	=> $_FILES['aFile']['error'][$i],
								'size'	=> $_FILES['aFile']['size'][$i],
							);
							$aFileInfo = goImage($aFile);

							if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
							{

								// $oPdo->rollback();
								$aReturn['nStatus'] = 0;
								$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
								// $aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/discuss/php/_post_0.php']);
								echo json_encode($aReturn);
								exit;
							}
							else
							{
								$aTmp = explode('.',$aFileInfo['sFilename']);
								$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
								$sFname = $aFileInfo['sFilename'];
							}

							$aSQL_Array = array(
								'nKid'		=> (int) $nLastId,
								'sTable'		=> (string) CLIENT_DISCUSS_REPLY,
								'sFile'		=> (string) $sFname,
								'nType0'		=> (int) 0,
								'nCreateTime'	=> (int) NOWTIME,
								'sCreateTime'  	=> (string) NOWDATE,
							);
							$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
							$Result = $oPdo->prepare($sSQL);
							sql_build_value($Result, $aSQL_Array);
							sql_query($Result);
							$nImageLastId = $oPdo->lastInsertId();

							#紀錄動作 - 新增
							$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
							$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;
						}
					}
				}

				// 上圖
				// if (isset($_FILES['sFile']) && $_FILES['sFile']['name']<>'')
				// {
				// 	$aFile['sTable'] = CLIENT_DISCUSS_REPLY;
				// 	$aFile['aFile'] = $_FILES['sFile'];
				// 	$aFileInfo = goImage($aFile);

				// 	if(isset($aFileInfo['nState']) && $aFileInfo['nState'] != 0)
				// 	{

				// 		// $oPdo->rollback();
				// 		$aReturn['nStatus'] = 0;
				// 		$aReturn['sMsg'] .= aIMGERROR[strtoupper($aFileInfo['error'])].'<br>';
				// 		// $aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/discuss/php/_post_0.php']);
				// 		echo json_encode($aReturn);
				// 		exit;
				// 	}
				// 	else
				// 	{
				// 		$aTmp = explode('.',$aFileInfo['sFilename']);
				// 		$aFileInfo['sFilename'] = str_replace(end($aTmp),'png',$aFileInfo['sFilename']);
				// 		$sFname = $aFileInfo['sFilename'];
				// 	}

				// 	$aSQL_Array = array(
				// 		'nKid'		=> (int) $nLastId,
				// 		'sTable'		=> (string) CLIENT_DISCUSS_REPLY,
				// 		'sFile'		=> (string) $sFname,
				// 		'nType0'		=> (int) 0,
				// 		'nCreateTime'	=> (int) NOWTIME,
				// 		'sCreateTime'  	=> (string) NOWDATE,
				// 	);

				// 	$sSQL = 'INSERT INTO ' . CLIENT_IMAGE_CTRL . ' ' . sql_build_array('INSERT', $aSQL_Array );
				// 	$Result = $oPdo->prepare($sSQL);
				// 	sql_build_value($Result, $aSQL_Array);
				// 	sql_query($Result);
				// 	$nImageLastId = $oPdo->lastInsertId();

				// 	#紀錄動作 - 新增
				// 	$aEditLog[CLIENT_IMAGE_CTRL]['aNew'] = $aSQL_Array;
				// 	$aEditLog[CLIENT_IMAGE_CTRL]['aNew']['nId'] = $nImageLastId;
				// }

				$aActionLog = array(
					'nWho'		=> (int) $aUser['nId'],
					'nWhom'		=> (int) 0,
					'sWhomAccount'	=> (string) '',
					'nKid'		=> (int) $nLastId,
					'sIp'			=> (string) USERIP,
					'nLogCode'		=> (int) 7100502,
					'sParam'		=> (string) json_encode($aEditLog),
					'nType0'		=> (int) 0,
					'nCreateTime'	=> (int) NOWTIME,
					'sCreateTime'	=> (string) NOWDATE,
				);
				DoActionLog($aActionLog);

				$aReturn['sMsg'] = INSV;
				$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_detail_0.php']).'&nId='.$nId;
			}
		}
	}

	if ($aJWT['a'] == 'DEL')
	{

		$sSQL = '	SELECT 	nId,
						sContent0,
						nOnline
				FROM 	'.CLIENT_DISCUSS.'
				WHERE nUid = :nUid
				AND 	nId = :nId
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aData === false)
		{
			$aReturn['sMsg'] = NODATA;
			$aReturn['nStatus'] = 0;
		}
		else
		{
			$aSQL_Array = array(
				'nOnline' 		=> (int) 99,
				'nUpdateTime' 	=> (int) NOWTIME,
				'sUpdateTime' 	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_DISCUSS.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE nId = :nId
					AND 	nOnline = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_DISCUSS]['aOld'] = $aData;
			$aEditLog[CLIENT_DISCUSS]['aNew'] = $aSQL_Array;

			$aSQL_Array = array(
				'nOnline' 		=> (int) 99,
				'nUpdateTime' 	=> (int) NOWTIME,
				'sUpdateTime' 	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_DISCUSS_REPLY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE nDid = :nDid
					AND 	nOnline = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nDid', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100503,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['sMsg'] = DELV;
			$aReturn['aData']['nId'] = $nId;
		}
	}

	if ($aJWT['a'] == 'DELREPLY')
	{
		$sSQL = '	SELECT 	nId,
						nDid,
						sContent0,
						nOnline
				FROM 	'.CLIENT_DISCUSS_REPLY.'
				WHERE nUid = :nUid
				AND 	nId = :nId
				AND 	nOnline = 1
				LIMIT 1';
		$Result = $oPdo->prepare($sSQL);
		$Result->bindValue(':nUid', $aUser['nId'], PDO::PARAM_INT);
		$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
		sql_query($Result);
		$aData = $Result->fetch(PDO::FETCH_ASSOC);
		if ($aData === false)
		{
			$aReturn['sMsg'] = NODATA;
			$aReturn['nStatus'] = 0;
		}
		else
		{
			$aSQL_Array = array(
				'nOnline' 		=> (int) 99,
				'nUpdateTime' 	=> (int) NOWTIME,
				'sUpdateTime' 	=> (string) NOWDATE,
			);
			$sSQL = '	UPDATE '.CLIENT_DISCUSS_REPLY.' SET ' . sql_build_array('UPDATE', $aSQL_Array ).'
					WHERE nId = :nId
					AND 	nOnline = 1
					LIMIT 1';
			$Result = $oPdo->prepare($sSQL);
			$Result->bindValue(':nId', $nId, PDO::PARAM_INT);
			sql_build_value($Result, $aSQL_Array);
			sql_query($Result);

			$aEditLog[CLIENT_DISCUSS_REPLY]['aOld'] = $aData;
			$aEditLog[CLIENT_DISCUSS_REPLY]['aNew'] = $aSQL_Array;

			$aActionLog = array(
				'nWho'		=> (int) $aUser['nId'],
				'nWhom'		=> (int) 0,
				'sWhomAccount'	=> (string) '',
				'nKid'		=> (int) $nId,
				'sIp'			=> (string) USERIP,
				'nLogCode'		=> (int) 7100504,
				'sParam'		=> (string) json_encode($aEditLog),
				'nType0'		=> (int) 0,
				'nCreateTime'	=> (int) NOWTIME,
				'sCreateTime'	=> (string) NOWDATE,
			);
			DoActionLog($aActionLog);

			$aReturn['aData']['nId'] = $nId;
			$aReturn['sMsg'] = DELV;
			$aReturn['sUrl'] = sys_web_encode($aMenuToNo['pages/discuss/php/_discuss_detail_0.php']).'&nId='.$aData['nDid'];
		}
	}

	echo json_encode($aReturn);
	exit;
?>
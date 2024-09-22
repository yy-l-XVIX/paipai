<?php
	#set_time_limit(0);
	#ignore_user_abort(true);
	#ini_set('error_log', dirname(dirname(dirname(__FILE__))).'/error_log.txt');
	# require
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) .'/System/System.php');
	#require結束
	#error_log('SocketServer.php start');
	#參數宣告區
	// 自動儲存時間
	define('SAVETIME', 10);
	// 自動儲存訊息數
	define('SAVECOUNT', 5);
	$nTime = NOWTIME;
	$sNull = NULL;
	$nGid = 0;
	$nMsgCount = 0; #訊息數量總計
	$aRoom = array();
	$aClientData = array();
	$aSaveMsg = array(
		'chat'=> array(),
		'job'=> array(),
	); #尚未儲存訊息
	$aServer = array(
		'sIP'		=> '192.168.15.97',
		'sPort'	=> '8080',
		'nMax'	=> 0, #最大連線數
		'nPeople'	=> array(), #在線人數
	);
	$aWebFile = array(
		'chat' => '/practice/t_paipai/ClientTest/?_chat_0.RLCt',
		'job'	 => '/practice/t_paipai/ClientTest/?_my_job_0.RLJb',
	);
	#宣告結束

	#程式邏輯區
	#設定網路模式、socket類型和通訊協定
	$oSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_set_option($oSocket, SOL_SOCKET, SO_REUSEADDR, 1);
	socket_bind($oSocket, 0, $aServer['sPort']);

	#監聽入口
	socket_listen($oSocket);

	#連接的所有清單
	$aClient = array($oSocket);

	#啟動無限迴圈
	while (true)
	{
		$aChange = $aClient;
		socket_select($aChange, $sNull, $sNull, 0, 10);

		#新的連接進場
		if (in_array($oSocket, $aChange))
		{
			#啟用新的連接
			$oNewSocket = socket_accept($oSocket);
			$aClient[] = $oNewSocket;

			#進行交握動作
			$sHead = socket_read($oNewSocket, 1024);
			hand_shake($sHead, $oNewSocket, $aServer['sIP'], $aServer['sPort']);

			$sSk = array_search($oSocket, $aChange);
			unset($aChange[$sSk]);
		}

		#詢問每個client socket連接
		foreach ($aChange as $sK => $LPsocket)
		{
			#當前連接如有資訊傳送，處理對應動作
			while(@socket_recv($LPsocket, $buffer, 1024, 0) >= 1)
			{
				#解密動作
				$sDTxt = deWscode($buffer);
				$aMessage = json_decode($sDTxt, true);
				#echo date('Y-m-d H:i:s ').'#74 Recive data => '.$sDTxt."\n";
				if (!$aMessage)
				{
					break 2;
				}
				switch ($aMessage['sType'])
				{
					case 'join':
						$aClientData[$sK] = array(
							'oSocket'	=> $LPsocket,
							'nUid'	=> $aMessage['nUid'],
							'sName0'	=> $aMessage['sName0'],
							'nGroupId'	=> $aMessage['nGroupId'],
							'sGroupType'=> $aMessage['sGroupType'],
						);
						$aRoom[$aMessage['sGroupType'].$aMessage['nGroupId']][$aMessage['nUid']] = $LPsocket;
						#echo date('Y-m-d H:i:s ').'#90 New ClientData => '.print_r($aClientData,true)."\n";
						#echo date('Y-m-d H:i:s ').'#91 New Room member => '.print_r($aRoom,true)."\n";

						$aSendMessage = array(
							'sType'	=> 'join',
							'nUid' 	=> $aMessage['nUid'],
						);
						$sSendMessage = enWscode(json_encode($aSendMessage));
						sendMsg($sSendMessage,$aMessage['nGroupId'],0,$aMessage['sGroupType']);
						break;
					case 'chat':
						$aSendMessage = array(
							'sType'	=> 'chat',
							'nUid' 	=> $aMessage['nUid'],
							'sName0'	=> $aClientData[$sK]['sName0'],
							'sMsg'	=> $aMessage['sMsg'],
							'sCreateTime'=> date('Y-m-d H:i:s'),
						);
						$sSendMessage = enWscode(json_encode($aSendMessage));
						sendMsg($sSendMessage,$aMessage['nGroupId'],$aMessage['nTargetUid'],$aMessage['sGroupType']);

						// save message
						$aMessage['sCreateTime'] = $aSendMessage['sCreateTime'];
						$nMsgCount ++;
						$aSaveMsg[$aMessage['sGroupType']][] = $aMessage;
						break;
				}
				break 2; //exist this loop
			}

			#檢查當前連接是否離線
			$buffer = @socket_read($LPsocket, 1024, PHP_NORMAL_READ);
			if ($buffer === false)
			{
				#echo date('Y-m-d H:i:s ').'#124 Client closed => '.print_r($aClient[$sK],true)."\n";

				unset($aRoom[$aClientData[$sK]['sGroupType'].$aClientData[$sK]['nGroupId']][$aClientData[$sK]['nUid']]);
				unset($aClientData[$sK]);
				unset($aClient[$sK]);
				break;
			}
		}

		$nLPTime = time() - $nTime;
		if($nLPTime >= SAVETIME || $nMsgCount >= SAVECOUNT)
		{
			# 訊息寫入DB

			$ch = curl_init('http://demo801.monopoly168.com/Project/t_paipai/EndTest/inc/tool/SaveMsg.php');
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aSaveMsg));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_TIMEOUT,1);
			$result = curl_exec($ch);
			curl_close($ch);
			$nTime = time();
			$nMsgCount = 0;
			$aSaveMsg = array(
				'chat' => array(),
				'job' => array(),
			);
		}
	}
	//關閉socket
	socket_close($oSocket);

	#發送訊息 (message, groupid , target uid , group type)
	function sendMsg($sMessage,$nGroupId = 0,$nTargetUid = 0,$sGroupType='')
	{
		global $aClient;
		global $aRoom;

		/**
		 * $nGid => 房間ID 如末指定即為全體廣播
		 * 有指定的話就是對該房間的所有人進行區域性廣播
		 */
		if($nGroupId == 0)
		{
			foreach($aClient as $LPoSocket)
			{
				@socket_write($LPoSocket['oSocket'],$sMessage,strlen($sMessage));
			}
		}
		else
		{
			if (!isset($aRoom[$sGroupType.$nGroupId]))
			{
				return false; // 房間不存在
			}

			if ($nTargetUid != 0) // 指定發送對象
			{
				@socket_write($aRoom[$sGroupType.$nGroupId][$nTargetUid],$sMessage,strlen($sMessage));
			}
			else
			{
				// 群組傳送
				foreach($aRoom[$sGroupType.$nGroupId] as $v)
				{
					@socket_write($v,$sMessage,strlen($sMessage));
				}
			}

		}
		return true;
	}

	//解碼用
	function deWscode($text)
	{
		$length = ord($text[1]) & 127;

		if($length == 126)
		{
			$masks = substr($text, 4, 4);
			$data = substr($text, 8);
		}
		elseif($length == 127)
		{
			$masks = substr($text, 10, 4);
			$data = substr($text, 14);
		}
		else
		{
			$masks = substr($text, 2, 4);
			$data = substr($text, 6);
		}
		$text = "";
		for ($i = 0; $i < strlen($data); ++$i)
		{
			$text .= $data[$i] ^ $masks[$i%4];
		}
		return $text;
	}

	//轉碼處理
	function enWscode($text)
	{
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($text);

		if($length <= 125)
		{
			$sHead = pack('CC', $b1, $length);
		}
		elseif($length > 125 && $length < 65536)
		{
			$sHead = pack('CCn', $b1, 126, $length);
		}
		elseif($length >= 65536)
		{
			$sHead = pack('CCNN', $b1, 127, $length);
		}

		return $sHead.$text;
	}

	#交握方式
	function hand_shake($sHead,$client_conn, $host, $port)
	{
		$headers = array();
		$aLink = preg_split("/\r\n/", $sHead);

		foreach($aLink as $v)
		{
			$v = chop($v);
			if(preg_match('/\A(\S+): (.*)\z/', $v, $matches))
			{
				$headers[$matches[1]] = $matches[2];
			}
		}

		$secKey = isset($headers['Sec-WebSocket-Key']) ? $headers['Sec-WebSocket-Key'] : '';
		$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		$upgrade =
		"HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
		"Upgrade: websocket\r\n" .
		"Connection: Upgrade\r\n" .
		'WebSocket-Origin: '. $host ."\r\n" .
		'WebSocket-Location: ws://'. $host .':'. $port ."/chat.php\r\n".
		'Sec-WebSocket-Accept:'. $secAccept ."\r\n\r\n";

		socket_write($client_conn,$upgrade,strlen($upgrade));
	}
?>
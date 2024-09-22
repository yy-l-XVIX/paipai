<?php
	ini_set('error_log', dirname(__FILE__).'/error_log.txt');
      /*php start*/

            require_once(dirname(dirname(__file__)) .'/System/System.php');
            require_once('inc/#Define.php');
            require_once('inc/#DefineTable.php');
            require_once('inc/#Function.php');
            require_once('process/priority_process.php');
            require_once(dirname(dirname(__file__)) .'/System/ConnectBase.php');
            require_once('inc/lang/'.$aSystem['sLang'].'/define.php');
            require_once('inc/#IsLogin.php');
      /*php end*/
      /*header start*/
            require_once('inc/#Page.php');

      /*body end*/
?>
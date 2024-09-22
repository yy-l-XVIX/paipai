<?php
	require_once(dirname(__FILE__).'/lang/'.$_GET['sLang'].'/JsDefine.php');

	echo 'var aJSDEFINE = new Array();';
	foreach(aJSDEFINE as $LPsKey => $LPsDefine)
	{
		echo 'aJSDEFINE[\''.$LPsKey.'\'] = \'' . $LPsDefine . '\';';
	}
?>
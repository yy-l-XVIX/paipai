<?php
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) .'/System/Connect/UserClass.php');

	if ($aJWT['a'] == 'UPT')
	{
		# 清除 cookie
		$oAdm = new oUser();
		$oAdm->updateCookie(array('sSid'=>$aJWT['sSid']));
		exit;
	}
?>
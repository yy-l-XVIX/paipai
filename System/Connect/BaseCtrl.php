<?php
	# 建立連線
	function oPdo_Connection($aDB)
	{
		# 建立資料庫連線
		try
		{
			$oPdoOptions = array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
			);
			$DSN = 'mysql:host='. $aDB['dbHost'] .';dbname='. $aDB['dbName'] .';';
			$oPdo = new PDO($DSN, $aDB['dbUser'], $aDB['dbPassword'], $oPdoOptions);
		}
		catch (PDOException $e)
		{
			echo 'DB Connection failed: ' . $e->getMessage() . PHP_EOL;
			exit;
		}

		return $oPdo;
	}

	function sql_build_array($query, $assoc_ary = false)
	{
		if ( !is_array($assoc_ary) )
		{
			return false;
		}

		$query = strtoupper(trim($query));

		$fields = $values = array();

		if ( $query == 'INSERT' )
		{
			foreach ( $assoc_ary as $key => $var )
			{
				$fields[] = $key;
				$values[] = ':'.$key;
			}

			$query = ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
		}
		else if ($query == 'UPDATE' || $query == 'SELECT')
		{
			$values = array();
			foreach ($assoc_ary as $key => $var)
			{
				$values[] = $key.' = :'.$key ;
			}
			$query = implode(($query == 'UPDATE') ? ', ' : ' AND ', $values);
		}

		return $query;
	}

	function sql_query($rs_name)
	{
		global	$oPdo;

		$rs_name->execute();
		$aTemp_Info = $rs_name->errorInfo();

		if (!(isset($aTemp_Info[0]) && ($aTemp_Info[0] == '00000')))
		{
			$sSQL_Code = isset($aTemp_Info[1]) ? $aTemp_Info[1] : '';
			$sSQL_Message = isset($aTemp_Info[2]) ? $aTemp_Info[2] : '';

			$sError_Msg = 'SQL Error : ['. $sSQL_Code  .']'. $sSQL_Message;
			echo $sError_Msg;
			error_log($sError_Msg);
			$oPdo->rollBack();
			exit;
		}
	}

	function sql_build_value($rs_name, $assoc_ary = false)
	{
		if(!is_array($assoc_ary))
		{
			return false;
		}

		foreach($assoc_ary as $k => $v)
		{
			if (is_int($v) && $v > 0)
			{
				$rs_name->bindValue(':'. $k, $v, PDO::PARAM_INT);
			}
			else
			{
				$rs_name->bindValue(':'. $k, $v, PDO::PARAM_STR);
			}
		}
	}

	function sql_limit($nStart = 0, $nCount = 1)
	{
		$sLimit = '';

		if ($nCount == 1)
		{
			$sLimit = 'LIMIT '. $nCount;
		}
		else
		{
			$sLimit = 'LIMIT '. $nStart .','. $nCount;
		}
		$sLimit = $sLimit .' ';

		return $sLimit;
	}
?>
<?php
//header('Content-Type:text/html; charset=utf8');
include "../lib/mMysql.php";
//include "../lib/mMongo.php";
$db = new Database();

	function _getDate()
	{
		return date("Y-m-d H:i:s",time());
	}
	function a(){	return func_get_args(); }
	function s($p){	 return "'".$p."'";   }
	function sql()
	{
		$param=func_get_args();
		$txt="(";
		for($i=0; $i<count($param); $i++)
		{
			if((string)$param[$i]=="date")
			{	$txt.="'"._getDate()."'"; }
			else
				$txt.=(string)$param[$i];
			if($i!=count($param)-1)$txt.=",";
		}
		$txt.=")";
		return $txt;
	}

    function stringToArray($str, $param) {
		$list = explode($param, $str);
		$p = array();
		foreach ($list as $value) {
			array_push($p, urlencode($value));
//			array_push($p, $value);
		}
		return $p;
	}

	function resetHeat($num) {
		$sql = "select max(heart) as value from event";
		$rst = $GLOBALS['db']->Select($sql);
		$max = 0;
		while($rec = mysqli_fetch_assoc($rst)) {
			foreach ($rec as $key => $value) {
				$max = $value;
			}
		}
		$heat = round($num/$max, 2) * 100;
		if ($heat > 97) $heat = 97;
		return $heat;
	}

	function getJsonBySql($sql)
	{
		$rst=$GLOBALS['db']->Select($sql);
		$all=array();
		while($rec = mysqli_fetch_assoc($rst))
		{
			$p=new obj();
			foreach($rec as $key => $value)
			{
				if(!is_numeric($key)){
					if ("labels" == $key) {
						$p->$key = stringToArray($value, " ");
					} elseif ("value" == $key) {
						$p->$key = urlencode(resetHeat($value));
					} else {
						$p->$key = urlencode($value);
//						$p->$key = $value;
					}
				}
			}
			array_push($all,$p);
		}
		return $all;
	}

	function getArrayBySql($sql) {
		$rst = $GLOBALS['db']->Select($sql);
		$all = array();
		while ($rec = mysqli_fetch_assoc($rst)) {
			foreach ($rec as $key => $value) {
				array_push($all, urlencode($value));
			}
		}
		return $all;
	}

	function run($sql,&$ID=-1)
	{
		$rst=$GLOBALS['db']->Select($sql);
		if($ID!=-1)$ID=$GLOBALS['db']->GetInsertID();
		return $rst;
	}
	function haveRecord($sql)
	{
		$rst=$GLOBALS['db']->Select($sql);
		$row=mysql_fetch_row($rst);
		if(empty($row))
		{
			return 0;
		}
		else
		{
			return $row[0];
		}
	}
	function queryAll($sql1,$sql2,&$id=-1)
	{
		return $GLOBALS['db']->QueryAll($sql1,$sql2,$id);
	}
	function queryAllEx($sql1,$sql2,&$data,$user,&$id=-1)
	{
		return $GLOBALS['db']->QueryAllEx($sql1,$sql2,$data,$user,$id);
	}
	function queryAllSql(&$sql)
	{
		return $GLOBALS['db']->QueryAllSql($sql);
	}

	function getArrayByMongoArrays($colName, $query = "", $fields = "") {}
?>




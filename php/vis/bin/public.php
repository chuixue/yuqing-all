<?php
	header('Content-Type:text/html; charset=utf8');
	include "../lib/mMysql.php";
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
	
	function getJsonBySql($sql)
	{
		$db=new Database();
		$rst=$db->Select($sql);
		$all=array();
		while($rec = mysql_fetch_array($rst))
		{
			$p=new obj();
			foreach($rec as $key => $value)
			{
				if(!is_numeric($key)){
					$p->$key=urlencode($value);
					//print "$key => $value\n";
				}
			}
			array_push($all,$p);
		}
		return $all;
	}
	function run($sql,&$ID=-1)
	{
		$db=new Database();
		$rst=$db->Select($sql);
		if($ID!=-1)$ID=$db->GetInsertID();
		return $rst;
	}
	function haveRecord($sql)
	{
		$db=new Database();
		$rst=$db->Select($sql);			
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
		$db=new Database();
		return $db->QueryAll($sql1,$sql2,$id);
	}
	function queryAllEx($sql1,$sql2,&$data,$user,&$id=-1)
	{
		$db=new Database();
		return $db->QueryAllEx($sql1,$sql2,$data,$user,$id);
	}
	function queryAllSql(&$sql)
	{
		$db=new Database();
		return $db->QueryAllSql($sql);
	}
?>




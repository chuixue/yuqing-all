<?php
	header('Content-Type:text/html; charset=utf8');
//mMysql.php
/*
 * Created on 2015-4-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
	
	include "mConfig.php";
	class Database
	{
		public $cfg;
		public $dbHost="";    //数据库地址
 		public $dbUser="";    //用户
 		public $dbPasswd="";  //密码
 		public $dbName="";    //数据库名
 		public $dbPort="";    //端口
		public $conn;		  //数据库连接
		public $selDB=false;	
		public $debug=false;
		
		function __construct($path="") 
		{
       		$this->cfg=$path!=""?new Config():new Config(1);
			$this->SetData($this->cfg->dbHost,$this->cfg->dbUser,$this->cfg->dbPasswd,$this->cfg->dbPort,$this->cfg->dbName);
			$this->connect();
   		}
   		//设置数据
		function SetData($host,$user,$passwd,$port=3306,$dbname="")
		{
			$this->dbHost=$host;
       		$this->dbUser=$user;
       		$this->dbPasswd=$passwd;
       		$this->dbPort=$port;
			$this->dbName=$dbname;
		}
   		//连接数据库
    	public function connect($dbname="", $charset='utf8')
    	{
            $this->conn=@mysql_connect($this->dbHost.":".$this->dbPort, $this->dbUser, $this->dbPasswd);
            if(!$this->conn)
            {
                $this->DealError(101);
                return false;
            }
            $db=($dbname=="") ? $this->dbName : $dbname;
            if($db!="")@mysql_select_db($db, $this->conn);
        	@mysql_query("set names ".$charset);
			mysql_query("set names 'utf8' ");
			mysql_query("set character_set_client=utf8");
			mysql_query("set character_set_results=utf8");    
	        return $this->conn;
        }
        //选择数据库
        public function SelectDB($dbname="")
        {	
			if($dbname=="")$dbname=$this->dbName;
        	$rst=@mysql_select_db($dbname,$this->conn);
        	if(!$rst)$this->DealError(103);
			$this->selDB=true;	
        	return $rst; 
        }
        //执行
        public function Execute($sql, $charset='utf8')
        {
        	return $this->Select($sql,$charset);
        }
        //查询
        public function Select($sql, $charset='utf8')
        { 
			if(!$this->selDB)
			{
				$this->SelectDB();
				$this->selDB=true;	
			}
        	@mysql_query("set character set ".$charset);
			if($this->debug)echo "<pre>Query:".$sql .".</pre>";
        	$rst = mysql_query($sql,$this->conn);
        	if($rst==false) $this->DealError(202); else{ if($this->debug)echo "OK!";}
        	return $rst;
        }
		public function GetInsertID()
		{
			return mysql_insert_id();	
		}
        public function GetAllTable($tbName)
        {
        	return $this->Select("select * from ".$tbName);
        }
		public function DealError($error_numb,$error_text="",$sql="") 
  		{
			$e=@mysql_error();
			$eu=mb_convert_encoding($e, "UTF-8", "GBK"); 
  			if(trim($e)!="")echo $eu."<br/>";
  			if($error_numb<200)exit();
  		}
		
		function __destruct()
		{
       		if($this->conn)mysql_close();
   		}
		function CreateDB($dbName="")
		{
			$this->selDB=true;	
			$dbn = $dbName!=""? $dbn: $this->dbName;
			$sql='CREATE DATABASE IF NOT EXISTS '.$dbn.' DEFAULT CHARSET utf8 COLLATE utf8_general_ci';
			$this->Execute($sql);
			$db=($dbName=="") ? $this->dbName : $dbName;
           	if($db!="")@mysql_select_db($db, $this->conn);
		}	
		function CreateTB()
		{
			$cfg=$this->cfg;
			for($i=0; $i<count($cfg->tbList); $i++)
			{
				$sql=$cfg->tbList[$i]->GetSql();
				$this->Execute($sql);		
			}
		}
		function QueryAll($sql1,$sql2,&$id1=-1)
		{
			$this->Select('SET AUTOCOMMIT=0'); // 设置为不自动提交查询  
			$this->Select('START TRANSACTION'); // 开始查询，这里也可以使用BEGIN  
			$rst1=$this->Select($sql1);  
			$rst=false;
			if($rst1!=false)
			{
				$id1=mysql_insert_id();
				$rst2=$this->Select($sql2);  
				if($rst2==false)
				{
					$id1=-1;
					mysql_query('ROLLBACK');
				}
				else
					$rst=true;
			}
			mysql_query('COMMIT');  
			return $rst;
		}
		function QueryAllSql(&$sql)
		{
			$this->Select('SET AUTOCOMMIT=0'); // 设置为不自动提交查询  
			$this->Select('START TRANSACTION'); // 开始查询，这里也可以使用BEGIN  
			$RSTS=array();
			for($i=0;$i<count($sql);$i++)
			{
				$RSTS[$i]=$this->Select($sql[$i]);  
			}
			$rst=true;
			for($i=0;$i<count($rst);$i++)
			{
				if($RSTS[$i]==false){ $rst=false; break; }
			}
			if(!$rst)mysql_query('ROLLBACK');
			mysql_query('COMMIT');  
			return $rst;
		}
		function QueryAllEx($sql1,$sql2,&$data,$user,&$id1=-1)
		{
			$this->Select('SET AUTOCOMMIT=0'); // 设置为不自动提交查询  
			$this->Select('START TRANSACTION'); // 开始查询，这里也可以使用BEGIN  
			$rst1=$this->Select($sql1);  
			$rst=false;
			if($rst1!=false)
			{
				$id1=mysql_insert_id();
				for($i=0; $i<count($data); $i++)
				{
					$sql2.="(".$id1.",".($i+1).",'".$data[$i]."','".$user."')";
					if($i<count($data)-1)$sql2.=",";
				}
				$rst2=$this->Select($sql2);  
				if($rst2==false)
				{
					$id1=-1;
					mysql_query('ROLLBACK');
				}
				else
					$rst=true;
			}
			mysql_query('COMMIT');
			return $rst;
		}
		
	}
 
 
 
?>
